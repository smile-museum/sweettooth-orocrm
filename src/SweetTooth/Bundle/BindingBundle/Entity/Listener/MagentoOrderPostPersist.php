<?php

namespace SweetTooth\Bundle\BindingBundle\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use OroCRM\Bundle\MagentoBundle\Entity\Order as MagentoOrder;

use Symfony\Component\DependencyInjection\Container;

class MagentoOrderPostPersist
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
        /** @var $entity \OroCRM\Bundle\MagentoBundle\Entity\Order */
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // perhaps you only want to act on some "PinbarTab" entity
        if ($entity instanceof MagentoOrder) {
            $broker = $this->container->get('sweettooth_binding.magento_order_broker');
            $broker->update($entity->getId());
        }
    }
}
