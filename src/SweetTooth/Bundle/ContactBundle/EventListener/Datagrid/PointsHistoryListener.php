<?php

namespace SweetTooth\Bundle\ContactBundle\EventListener\Datagrid;

use Doctrine\ORM\EntityManager;

use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;

use SweetTooth\Bundle\BindingBundle\Broker\ContactBroker;

class PointsHistoryListener
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Remove useless fields in case of filtering
     *
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();
        $parameters = $event->getParameters();

        // if (!empty($parameters['contactId'])) {
        //     $this->removeColumn($config, 'contactName');
        // }

        // if (!empty($parameters['accountId'])) {
        //     $this->removeColumn($config, 'accountName');
        // }
    }

    /**
     * Add required filters
     *
     * @param BuildAfter $event
     */
    public function onBuildAfter(BuildAfter $event)
    {
        /** @var OrmDatasource $ormDataSource */
        $dataSource = $event->getDatagrid()->getDatasource();
        // $queryBuilder = $ormDataSource->getQueryBuilder();
        $parameters = $event->getParameters();

        if (!empty($parameters['contactId'])) {
            $dataSource->setContactId($parameters['contactId']);
            // $binding = $broker->retrieve($parameters['contactId'], false);
            // if ($binding->getRemoteId()) {
            // }
        }

        // if (!empty($parameters['userId'])) {
        //     $user = $this->entityManager->find('OroUserBundle:User', $parameters['userId']);
        //     $queryBuilder
        //         ->andWhere('call.owner = :user')
        //         ->setParameter('user', $user);
        // }

        // if (!empty($parameters['contactId'])) {
        //     $contact = $this->entityManager->find('OroCRMContactBundle:Contact', $parameters['contactId']);
        //     $queryBuilder
        //         ->andWhere('call.relatedContact = :contact')
        //         ->setParameter('contact', $contact);
        // }

        // if (!empty($parameters['accountId'])) {
        //     $account = $this->entityManager->find('OroCRMAccountBundle:Account', $parameters['accountId']);
        //     $queryBuilder
        //         ->andWhere('(call.relatedAccount = :account OR :account MEMBER OF contact.accounts)')
        //         ->setParameter('account', $account);
        // }

        // if (array_key_exists('callIds', $parameters)) {
        //     $callIds = $parameters['callIds'];
        //     if (!is_array($callIds)) {
        //         $callIds = explode(',', $callIds);
        //     }
        //     $queryBuilder->andWhere($queryBuilder->expr()->in('call.id', $callIds));
        // }
    }
}
