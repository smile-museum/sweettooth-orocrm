<?php

namespace SweetTooth\Bundle\ContactBundle\Extension;

use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\DataGridBundle\Datasource\DatasourceInterface;
use Oro\Bundle\DataGridBundle\Datasource\ResultRecord;
use Oro\Bundle\DataGridBundle\Datasource\ResultRecordInterface;
use Oro\Bundle\SearchBundle\Engine\Indexer;
use Oro\Bundle\SearchBundle\Extension\Pager\IndexerQuery;

use Doctrine\ORM\EntityManager;
use SweetTooth\Bundle\BindingBundle\Broker\ContactBroker;

use SweetTooth;
use SweetTooth_PointsTransaction;

class PointsHistoryDatasource implements DatasourceInterface
{
    const TYPE = 'points_history';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    protected $contactId;

    /**
     * @param Indexer $indexer
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        // TODO: Pass in customer broker service here, see SearchDatasource
    }

    /**
     * {@inheritDoc}
     */
    public function process(DatagridInterface $grid, array $config)
    {
        $grid->setDatasource(clone $this);
    }

    /**
     * @return ResultRecordInterface[]
     */
    public function getResults()
    {
        error_log("CONTACT ID: " . $this->getContactId());
        // Return no results if customer filter not specified
        if (!$this->getContactId()) {
            return [];
        }

        $broker = new ContactBroker($this->entityManager);
        $binding = $broker->retrieve($this->getContactId(), false);

        // Return no results if customer has not been created in ST yet
        if (!$binding->getRemoteId()) {
            return [];
        }

        SweetTooth::setApiKey('sk_DrRXkNMNnLW1Z4VhGstfw8V4');
        $pointsTransactions = SweetTooth_PointsTransaction::all(array(
          'customer_id' => $binding->getRemoteId()
        ));

        $results = [];
        foreach ($pointsTransactions->items as $transaction) {
            $results[] = array(
                "description"   => $transaction->comment,
                // TODO: Add custom renderer
                "points_change" => ($transaction->points_change > 0 ? '+' : '-') . abs($transaction->points_change),
                "created"       => new \DateTime($transaction->created)
            );
        }

        $rows = [];
        foreach ($results as $result) {
            $rows[] = new ResultRecord($result);
        }

        return $rows;
    }

    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
    }

    public function getContactId()
    {
        return $this->contactId;
    }
}
