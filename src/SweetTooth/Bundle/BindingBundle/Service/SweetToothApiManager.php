<?php

namespace SweetTooth\Bundle\BindingBundle\Sync;

// TOOD: Do I always have to use the 'as' at the end of this? Fatal class not found otherwise it seems.
use SweetTooth\Bundle\BindingBundle\Broker\ContactBroker as ContactBroker;

use Doctrine\ORM\EntityManager;

use SweetTooth;

class SweetToothApiManager
{
    protected $em;

    protected $apiKey;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(
        EntityManager $em
    ) {
        $this->em = $em;
        $this->initApiKey();
    }

    /**
     * Initializes the SweetTooth ApiKey for the global library
     * 
     * @return $this
     */
    protected function initApiKey()
    {
        $channel = $this->em->getRepository('OroIntegrationBundle:Channel')->findOneBy(
            array('type' => 'sweettooth')
        );

        // Do nothing
        if (!$channel || !$channel->getTransport()->getApiKey()) {
            return;
        }

        $this->apiKey = $channel->getTransport()->getApiKey();

        // SweetTooth::setApiKey('sk_DrRXkNMNnLW1Z4VhGstfw8V4');
        SweetTooth::setApiKey($this->apiKey);

        return $this;
    }

    /**
     * Checks if api key has been set
     * 
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->apiKey ? true : false;
    }
}
