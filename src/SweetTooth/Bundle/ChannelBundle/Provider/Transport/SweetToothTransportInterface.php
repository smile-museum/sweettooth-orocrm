<?php

namespace SweetTooth\Bundle\ChannelBundle\Provider\Transport;

use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;

use OroCRM\Bundle\MagentoBundle\Provider\Iterator\UpdatedLoaderInterface;

interface SweetToothTransportInterface extends TransportInterface
{
    /**
     * Retrieve customers from magento
     *
     * @return UpdatedLoaderInterface|\Iterator
     */
    public function getCustomers();
}
