<?php

namespace SweetTooth\Bundle\BindingBundle\Broker;
// require_once Mage::getBaseDir('lib') . '/sweettooth-php/lib/SweetTooth.php';
use SweetTooth;

use SweetTooth\Bundle\BindingBundle\Entity\ContactBinding;
use Doctrine\ORM\EntityManager;

abstract class BrokerAbstract
{
    /**
     * Abstract. Set this in the child class to specify the binding model
     *
     * Example: 'OroCRMMagentoBundle:Customer'
     * 
     * @var string
     */
    protected $_bindingModelKey;

    // TODO: Comment these guys
    abstract protected function _retrieveRemoteObject($remoteId);
    abstract protected function _createRemoteObject($localId, $localObject = null);
    abstract protected function _updateRemoteObject($remoteId, $localId, $localObject = null);
    abstract protected function _deleteRemoteObject($remoteId);


    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(
        EntityManager $em
    ) {
        $this->em = $em;
        // $apiKey = $this->container->get('oro_config.global')->get('sweet_tooth_binding.api_key');
        // SweetTooth::setApiKey($apiKey);
        SweetTooth::setApiKey('sk_DrRXkNMNnLW1Z4VhGstfw8V4');
        // Mage::helper('stcore')->initSweetToothLib();
    }

    /**
     * Retrieves the binding object with the remote object loaded
     * 
     * @param  int  $localId                  id of the local model
     * @param  boolean $loadRemoteObject      if false, will not load the remote object. Default true
     * @return ST_Core_Model_Broker_Abstract  binding object
     */
    public function retrieve($localId, $loadRemoteObject = true)
    {
        // We'll never queue a retrieve call, so we just call the syncronous _retrieve directly
        return $this->_retrieve($localId, $loadRemoteObject);
    }

    public function create($localId, $localObject = null, $forceSyncronous = false)
    {
        $sendInBackground = true;

        // If we're sending calls in the background, just save this model with in_sync
        // being false so it gets picked up by the cron later
        if ($sendInBackground && !$forceSyncronous) {
            $binding = $this->retrieve($localId, false);
            $binding->setInSync(false);
            $this->em->persist($binding);
            $this->em->flush();
            return $binding;
        }

        return $this->_create($localId, $localObject);
    }

    public function update($localId, $localObject = null, $forceSyncronous = false)
    {
        $sendInBackground = true;

        // If we're sending calls in the background, just save this model with in_sync
        // being false so it gets picked up by the cron later
        if ($sendInBackground && !$forceSyncronous) {
            $binding = $this->retrieve($localId, false);
            $binding->setInSync(false);
            $this->em->persist($binding);
            $this->em->flush();
            return $this;
        }

        return $this->_update($localId, $localObject);
    }

    public function delete($localId, $forceSyncronous = false)
    {
        $sendInBackground = true;

        // If we're sending calls in the background, just save this model with in_sync
        // being false so it gets picked up by the cron later
        if ($sendInBackground && !$forceSyncronous) {
            $binding = $this->retrieve($localId, false);
            $binding->setStageDelete(true);
            $this->em->persist($binding);
            $this->em->flush();
            return $binding;
        }

        return $this->_delete($localId);
    }

    /**
     * These are helper methods to force each crud operation to be syncronous.
     *
     * These are the equivalent to calling the regular methods with the $forceSyncronous flag on.
     */
    public function doRetrieve($localId, $loadRemoteObject = true)
    {
        // We'll never queue a retrieve call, so we just call the syncronous _retrieve directly
        return $this->_retrieve($localId, $loadRemoteObject);
    }

    public function doCreate($localId, $localObject = null)
    {
        return $this->_create($localId, $localObject);
    }

    public function doUpdate($localId, $localObject = null)
    {
        return $this->_update($localId, $localObject);
    }

    public function doDelete($localId)
    {
        return $this->_delete($localId);
    }

    /**
     * Below are the syncronous CRUD operations, all public ones
     * are first checked if they should be queued based on the send_in_background
     * configuration options.
     */

    public function _retrieve($localId, $loadRemoteObject = true)
    {
        // Fail silently on corrupt bindings because they are usually from db integrity problems.
        // I've seen this a few times
        //   1. Guest order gets added tothe stcore_order_binding table
        if (!$localId) {
            return null;
        }
        
        $binding = $this->_loadBinding($localId);

        // Return binding object without loading remote object
        if (!$loadRemoteObject) {
            return $binding;
        }

        $remoteId = $binding->getRemoteId();
        if (!$remoteId) {
            return $this->_create($localId);
        }

        try {
            $remoteObject = $this->_retrieveRemoteObject($remoteId);
            $binding->setRemoteObject($remoteObject);
        } catch (SweetTooth_Error $e) {
            // We don't set any in_sync flags on retrieve right now.
            // Since we're not necessarily out of sync if this fails.
            // Perhaps at some point we track errors that happen on retrieve
            // along with some caching if we need the performance boost.
            // Mage::helper('stcore')->logException($e);
            $binding->setErrorMessage($e->getMessage());

            $this->em->persist($binding);
            $this->em->flush();
        }

        return $binding;
    }

    protected function _create($localId, $localObject = null)
    {
        // Ignore corrupt bindings
        if (!$localId) {
            return null;
        }

        $binding = $this->_loadBinding($localId);

        // Check if remote object is already created
        if ($binding->getRemoteId()) {
            // TODO: Watch out for circular calls?
            return $this->_update($localId, $localObject);
        }

        try {
            $remoteObject = $this->_createRemoteObject($localId, $localObject);
            $binding->addSuccess($remoteObject);
        } catch (Exception $e) {
            $binding->addException($e);
        }

        $this->em->persist($binding);
        $this->em->flush();

        return $binding;
    }

    protected function _update($localId, $localObject = null)
    {
        // Ignore corrupt bindings
        if (!$localId) {
            return null;
        }

        $binding = $this->_loadBinding($localId);

        // Create remote object if not created yet
        if (!$binding->getRemoteId()) {
            return $this->_create($localId, $localObject);
        }

        $remoteId = $binding->getRemoteId();

        try {
            $remoteObject = $this->_updateRemoteObject($remoteId, $localId, $localObject);
            $binding->addSuccess($remoteObject);
        } catch (Exception $e) {
            $binding->addException($e);
        }

        $this->em->persist($binding);
        $this->em->flush();

        return $binding;
    }

    /**
     * TODO: This function is untested and still needs work.
     * For example the deleted_at flag is set locally, and the in_sync flag is set to false.
     * We should not have to set the deleted_at flag again after this call.
     */
    protected function _delete($localId)
    {
        $binding = $this->_loadBinding($localId);

        // Check if already deleted
        if ($binding->deletedAt()) {
            return $binding;
        }

        // TODO: If a remote object does not exist yet and is deleted, we should not create it then delete it

        // If no remote object exists yet, just mark the local one as deleted
        if (!$binding->getRemoteId()) {
            $binding->setDeletedAt(new \DateTime());
            $this->em->persist($binding);
            $this->em->flush();
            return $binding;
        }

        $remoteId = $binding->getRemoteId();

        try {
            $this->_deleteRemoteObject($remoteId);
            $binding->addSuccess($remoteObject)
                ->setDeletedAt(new \DateTime());
        } catch (Exception $e) {
            $binding->addException($e);
        }

        $this->em->persist($binding);
        $this->em->flush();

        return $binding;
    }
}
