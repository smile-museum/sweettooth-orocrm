<?php

namespace SweetTooth\Bundle\ChannelBundle\Provider;

use Oro\Bundle\IntegrationBundle\Provider\ConnectorInterface;

/**
 * Interface MagentoConnectorInterface
 *
 * @package OroCRM\Bundle\MagentoBundle\Provider
 * This interface should be implemented by magento related connectors
 * Contains just general constants
 */
interface SweetToothConnectorInterface extends ConnectorInterface
{
    // TODO: Add ST Customer Binding
    const CUSTOMER_TYPE           = 'OroCRM\\Bundle\\MagentoBundle\\Entity\\Customer';
}
