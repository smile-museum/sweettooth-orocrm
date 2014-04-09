<?php

namespace SweetTooth\Bundle\BindingBundle\Broker;

use OroCRM\Bundle\MagentoBundle\Entity\Customer;
use SweetTooth\Bundle\BindingBundle\Entity\ContactBinding;
use SweetTooth\Bundle\BindingBundle\Entity\MagentoOrderBinding;
use SweetTooth\Bundle\BindingBundle\Broker\BrokerAbstract;

use Doctrine\ORM\EntityManager;

use SweetTooth;
use SweetTooth_Customer;
use SweetTooth_Activity;
use SweetTooth_RecordExistsError;

use Stripe;

class MagentoOrderBroker extends BrokerAbstract
{
    protected $_bindingModelKey = 'SweetToothBindingBundle:MagentoOrderBinding';

    public function test($localId)
    {
        // $localObject = $this->em->getRepository('SweetToothBindingBundle:ContactBinding')->findOneBy(array('local_id'=>$localId));
        
        $localObject = $this->em->getRepository('OroCRMMagentoBundle:Order')->find(1);

        $customer_id = $localObject->getCustomer()->getContact()->getId();

        error_log("Customer Id " . $customer_id);

        // $binding = $this->doUpdate(2);
        // $binding = $this->doCreate(7);
    }

    protected function _retrieveRemoteObject($remoteId)
    {
        return SweetTooth_Activity::retrieve($remoteId);
    }

    protected function _createRemoteObject($localId, $localObject = null) 
    {
        $remoteObject = null;

        // Load local model if not passed in
        if (!$localObject) {
            $localObject = $this->em->getRepository('OroCRMMagentoBundle:Order')->find($localId);
        }

        $localContactId = $localObject->getCustomer()->getContact()->getId();

        // TODO: Could optimize here with a call that will only load the customer binding if it already has a remote customer id saved
        // Right now it's making a fetch to ST to load the remote customer id (extra network call)
        $contactBroker = $this->container->get('sweettooth_binding.contact_broker');
        $customerBinding = $contactBroker->retrieve($localContactId);

        // Propagate the exception if there was a failure loading the remote customer
        if (!$customerBinding->getRemoteId()) {
            throw new Exception("Failed to load remote Sweet Tooth Customer. Details: " . $customerBinding->getErrorMessage());
            // throw new SweetTooth_Error("Failed to load remote Sweet Tooth Customer. Details: " . $customerBinding->getErrorMessage());
        }

        $remoteObject = SweetTooth_Activity::create(array(
            // 'customer_id'   => 'cus_8X9NEdS9AfQlnM',
            'customer_id'   => $customerBinding->getRemoteId(),
            'verb'          => 'oro_order',
            'object'    => array(
                'external_id'    => $localObject->getId(),
                'total_amount'     => $localObject->getTotalAmount()
            )
        ));

        return $remoteObject;
    }

    protected function _updateRemoteObject($remoteId, $localId, $localObject = null) 
    {
        $remoteObject = null;

        // Load local model if not passed in
        if (!$localObject) {
            $localObject = $this->em->getRepository('OroCRMMagentoBundle:Order')->find($localId);
        }

        $remoteObject = $this->_retrieveRemoteObject($remoteId);

        $orderData = array(
            'total_amount' => $localObject->getTotalAmount()
        );

        $remoteObject->object = $orderData;
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
        $binding = new MagentoOrderBinding();
        $binding->setLocalId($localId);

        $this->em->persist($binding);
        $this->em->flush();
        return $binding;
    }
}
