<?php

namespace SweetTooth\Bundle\ChannelBundle\Provider;

use SweetTooth\Bundle\ChannelBundle\Provider\AbstractSweetToothConnector;

class CustomerConnector extends AbstractSweetToothConnector
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        // TODO: Config this
        // return 'orocrm.magento.connector.customer.label';
        
        return "Sweet Tooth Customer";
    }

    /**
     * {@inheritdoc}
     */
    public function getImportEntityFQCN()
    {
        return self::CUSTOMER_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getImportJobName()
    {
        return 'sweettooth_customer_import';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConnectorSource()
    {
        return $this->transport->getCustomers();
    }
}
