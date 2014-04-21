<?php

namespace SweetTooth\Bundle\BindingBundle\Service\Broker;

use OroCRM\Bundle\MagentoBundle\Entity\Customer;
use SweetTooth\Bundle\BindingBundle\Entity\ContactBinding;

use Doctrine\ORM\EntityManager;

use SweetTooth;
use SweetTooth_Customer;
use SweetTooth_RecordExistsError;

use Stripe;

class ContactBroker extends BrokerAbstract
{
    protected $_bindingModelKey = 'SweetToothBindingBundle:ContactBinding';

    public function test()
    {
        // $localObject = $this->em->getRepository('SweetToothBindingBundle:ContactBinding')->findOneBy(array('local_id'=>$localId));
        
        error_log('Testtt');

        // $binding = $this->doUpdate(2);
        // $binding = $this->doCreate(7);
    }

    protected function _retrieveRemoteObject($remoteId)
    {
        return SweetTooth_Customer::retrieve($remoteId);
    }

    protected function _createRemoteObject($localId, $localObject = null) 
    {
        $remoteObject = null;

        // Load local model if not passed in
        if (!$localObject) {
            $localObject = $this->em->getRepository('OroCRMContactBundle:Contact')->find($localId);
        }

        try {
            $remoteObject = SweetTooth_Customer::create(array(
                'first_name'    => $localObject->getFirstName(),
                'last_name'     => $localObject->getLastName(),
                'email'         => $localObject->getPrimaryEmail(),
            ));

        } catch (SweetTooth_RecordExistsError $e) {
            // If a customer with this email already exists on ST, we match and fetch it
            $remoteObject = SweetTooth_Customer::retrieve($e->recordId);
        }

        return $remoteObject;
    }

    protected function _updateRemoteObject($remoteId, $localId, $localObject = null) 
    {
        $remoteObject = null;

        // Load local model if not passed in
        if (!$localObject) {
            $localObject = $this->em->getRepository('OroCRMContactBundle:Contact')->find($localId);
        }

        $remoteObject = $this->_retrieveRemoteObject($remoteId);

        $remoteObject->first_name = $localObject->getFirstName();
        $remoteObject->last_name = $localObject->getLastName();
        $remoteObject->email = $localObject->getPrimaryEmail();
        $remoteObject->save();

        return $remoteObject;
    }

    protected function _deleteRemoteObject($remoteId)
    {
        $remoteObject = SweetTooth_Customer::retrieve($remoteId);
        $remoteObject->delete();

        return $remoteObject;
    }

    protected function _loadBinding($localId)
    {
        $binding = $this->em->getRepository($this->_bindingModelKey)
            ->findOneBy(array('local_id'=>$localId));

        if ($binding) {
            return $binding;
        }

        // TODO: Find out how we can instantiate this class
        // with the bindingModelKey
        $binding = new ContactBinding();
        $binding->setLocalId($localId);

        $this->em->persist($binding);
        $this->em->flush();
        return $binding;
    }
}
