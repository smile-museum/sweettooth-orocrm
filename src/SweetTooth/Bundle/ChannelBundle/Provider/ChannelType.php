<?php

namespace SweetTooth\Bundle\ChannelBundle\Provider;

use Oro\Bundle\IntegrationBundle\Provider\ChannelInterface;

class ChannelType implements ChannelInterface
{
    const TYPE = 'sweettooth';

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        // TODO: Add translation file
        // return 'orocrm.magento.channel_type.label';
        
        return "Sweet Tooth";
    }
}
