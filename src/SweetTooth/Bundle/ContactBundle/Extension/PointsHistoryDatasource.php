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
use Symfony\Component\DependencyInjection\Container;

use SweetTooth;
use SweetTooth_PointsTransaction;

class PointsHistoryDatasource implements DatasourceInterface
{
    const TYPE = 'points_history';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Service container
     */
    protected $container;

    /**
     * Contact's id
     */
    protected $contactId;

    /**
     * Sweet Tooth API Key
     */
    protected $apiKey;

    /**
     * Constructor
     */
    public function __construct(
        EntityManager $entityManager,
        Container $container
    ) {
        $this->entityManager = $entityManager;
        $this->container = $container;

        $this->apiKey = $this->container->get('oro_config.global')->get('sweet_tooth_binding.api_key');

        SweetTooth::setApiKey($this->apiKey);
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
        // Return no results if customer filter not specified
        if (!$this->getContactId()) {
            return [];
        }

        $broker = $this->container->get('sweettooth_binding.contact_broker');
        $binding = $broker->retrieve($this->getContactId(), false);

        // Return no results if customer has not been created in ST yet
        if (!$binding->getRemoteId()) {
            return [];
        }

        // SweetTooth::setApiKey('sk_DrRXkNMNnLW1Z4VhGstfw8V4');
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

    /**
     * This gets set by the grid listener so we can query Sweet Tooth
     * for the appropriate contact points info
     * @param int $contactId
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * Returns the previously set contact id
     * @return int contact id
     */
    public function getContactId()
    {
        return $this->contactId;
    }
}
