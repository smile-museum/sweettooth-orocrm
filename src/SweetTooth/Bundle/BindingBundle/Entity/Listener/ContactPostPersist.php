<?php

namespace SweetTooth\Bundle\BindingBundle\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use OroCRM\Bundle\ContactBundle\Entity\Contact as OroContact;
use SweetTooth\Bundle\BindingBundle\Broker\ContactBroker as ContactBroker;

class ContactPostPersist
{
    public function postPersist(LifecycleEventArgs $args)
    {
        // The broker handles binding creation for us
        return $this->postUpdate($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        /** @var $entity \OroCRM\Bundle\MagentoBundle\Entity\Customer */
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // perhaps you only want to act on some "PinbarTab" entity
        if ($entity instanceof OroContact) {
            $broker = new ContactBroker($entityManager);
            $broker->update($entity->getId());
        }
    }
}
