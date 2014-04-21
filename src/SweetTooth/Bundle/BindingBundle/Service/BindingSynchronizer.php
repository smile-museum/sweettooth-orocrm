<?php

namespace SweetTooth\Bundle\BindingBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\ORM\EntityManager;


class BindingSynchronizer extends ContainerAware
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
        // Make sure all the entities (Contacts, Magento Orders) that exist in
        // the system before ST is installed have bindings to be synced on cron.
        // TODO: Add this as a config option since some merchants may want to start
        // their loyalty program from today onward 
        $this->generateCustomerBindings();
        $this->generateMagentoOrderBindings();

        $this->syncContacts();
        $this->syncMagentoOrders();
    }

    public function generateCustomerBindings()
    {
        $bindingTable = 'sweettooth_contact_binding';
        $localObjectTable = 'orocrm_contact';

        $query = "insert into {$bindingTable} (local_id)
             select c.id from {$localObjectTable} as c left outer join {$bindingTable} as b on c.id = b.local_id 
             where b.local_id IS NULL";

        // TOOD: Check error here (also, is createNativeQuery better practice?)
        $result = $this->em->getConnection()->query($query);

        return $result;
    }

    public function generateMagentoOrderBindings()
    {
        $bindingTable = 'sweettooth_magento_order_binding';
        $localObjectTable = 'orocrm_magento_order';

        $query = "insert into {$bindingTable} (local_id)
             select c.id from {$localObjectTable} as c left outer join {$bindingTable} as b on c.id = b.local_id 
             where b.local_id IS NULL";

        // TOOD: Check error here (also, is createNativeQuery better practice?)
        $result = $this->em->getConnection()->query($query);

        return $result;
    }

    protected function syncContacts()
    {
        $bindings = $this->em->getRepository('SweetToothBindingBundle:ContactBinding')->findBy(
            array('in_sync' => false)
        );

        foreach ($bindings as $binding) {
            $broker = $this->container->get('sweettooth_binding.contact_broker');
            $broker->doUpdate($binding->getLocalId());
        }
    }

    protected function syncMagentoOrders()
    {
        $bindings = $this->em->getRepository('SweetToothBindingBundle:MagentoOrderBinding')->findBy(
            array('in_sync' => false)
        );

        foreach ($bindings as $binding) {
            $broker = $this->container->get('sweettooth_binding.magento_order_broker');
            $broker->doUpdate($binding->getLocalId());
        }
    }
}
