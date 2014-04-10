<?php

namespace SweetTooth\Bundle\BindingBundle\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use OroCRM\Bundle\ContactBundle\Entity\Contact as OroContact;
use Symfony\Component\DependencyInjection\Container;

class ContactPostPersist
{
    protected $container;

    /**
     * Constructor
     */
    public function __construct(
        Container $container
    ) {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        // The broker handles binding creation for us
        return $this->postUpdate($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        /** @var $entity \OroCRM\Bundle\ContactBundle\Entity\Contact */
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // perhaps you only want to act on some "PinbarTab" entity
        if ($entity instanceof OroContact) {
            $broker = $this->container->get('sweettooth_binding.contact_broker');
            $broker->update($entity->getId());
        }
    }
}
