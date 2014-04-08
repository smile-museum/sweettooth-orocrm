<?php

namespace SweetTooth\Bundle\BindingBundle\Sync;

// TOOD: Do I always have to use the 'as' at the end of this? Fatal class not found otherwise it seems.
use SweetTooth\Bundle\BindingBundle\Broker\ContactBroker as ContactBroker;
use SweetTooth\Bundle\BindingBundle\Broker\MagentoOrderBroker as MagentoOrderBroker;

use Doctrine\ORM\EntityManager;

class BindingSynchronizer
{
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
    }

    /**
     * Syncs bindings
     */
    public function sync()
    {
        // $localObject = $this->em->getRepository('OroCRMMagentoBundle:Order')->find(2);
        // $localObject->setGiftMessage("test");

        // $this->em->persist($localObject);
        // $this->em->flush();

        // error_log("Id " . $localObject->getId());

        $this->syncContacts();
        $this->syncMagentoOrders();
    }

    protected function syncContacts()
    {
        $bindings = $this->em->getRepository('SweetToothBindingBundle:ContactBinding')->findBy(
            array('in_sync' => false)
        );

        foreach ($bindings as $binding) {
            $broker = new ContactBroker($this->em);
            $broker->doUpdate($binding->getLocalId());
            error_log('Done sync: ' . $binding->getLocalId());
        }
    }

    protected function syncMagentoOrders()
    {
        $bindings = $this->em->getRepository('SweetToothBindingBundle:MagentoOrderBinding')->findBy(
            array('in_sync' => false)
        );

        foreach ($bindings as $binding) {
            $broker = new MagentoOrderBroker($this->em);

            // $broker->test(1);
            // return;

            $broker->doUpdate($binding->getLocalId());
            error_log('Synced order binding ' . $binding->getLocalId());
        }
    }
}
