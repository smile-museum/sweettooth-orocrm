<?php

namespace SweetTooth\Bundle\ContactBundle\EventListener\Datagrid;

use Doctrine\ORM\EntityManager;

use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;

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
     * Build before
     *
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        // Nothing to do here
    }

    /**
     * Add required filters to datasource
     *
     * @param BuildAfter $event
     */
    public function onBuildAfter(BuildAfter $event)
    {
        /** @var OrmDatasource $ormDataSource */
        $dataSource = $event->getDatagrid()->getDatasource();
        $parameters = $event->getParameters();

        if (!empty($parameters['contactId'])) {
            $dataSource->setContactId($parameters['contactId']);
        }
    }
}
