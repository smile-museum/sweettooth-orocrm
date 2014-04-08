<?php

namespace SweetTooth\Bundle\ChannelBundle\Provider\Transport;

use SweetTooth\Bundle\ChannelBundle\Provider\RESTTransport as BaseRESTTransport;

use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Oro\Bundle\SecurityBundle\Encoder\Mcrypt;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\IntegrationBundle\Provider\SOAPTransport as BaseSOAPTransport;

use OroCRM\Bundle\MagentoBundle\Provider\Iterator\CartsBridgeIterator;
use OroCRM\Bundle\MagentoBundle\Provider\Iterator\OrderBridgeIterator;
use OroCRM\Bundle\MagentoBundle\Provider\Iterator\CustomerBridgeIterator;
use OroCRM\Bundle\MagentoBundle\Provider\Iterator\OrderSoapIterator;
use OroCRM\Bundle\MagentoBundle\Provider\Iterator\RegionSoapIterator;
use OroCRM\Bundle\MagentoBundle\Provider\Iterator\StoresSoapIterator;
use OroCRM\Bundle\MagentoBundle\Provider\Iterator\WebsiteSoapIterator;
use OroCRM\Bundle\MagentoBundle\Provider\Iterator\CustomerSoapIterator;
use OroCRM\Bundle\MagentoBundle\Provider\Iterator\CustomerGroupSoapIterator;

/**
 * Magento SOAP transport
 * used to fetch and pull data to/from Magento instance
 * with sessionId param using SOAP requests
 *
 * @package OroCRM\Bundle\MagentoBundle
 */
// class RestTransport extends BaseSOAPTransport implements MagentoTransportInterface
class RestTransport extends BaseRESTTransport implements SweetToothTransportInterface
{
    const ACTION_CUSTOMER_LIST = 'customerCustomerList';
    const ACTION_CUSTOMER_INFO = 'customerCustomerInfo';
    const ACTION_PING          = 'oroPing';

    const ACTION_ORO_CUSTOMER_LIST = 'oroCustomerList';

    public function __construct(Mcrypt $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function init(Transport $transportEntity)
    {
        parent::init($transportEntity);

        $apiKey  = $this->settings->get('api_key', false);
        error_log("INIT: ");

        if (!$apiKey) {
            throw new InvalidConfigurationException(
                "Sweet Tooth REST transport requires 'api_key' setting to be defined."
            );
        }

        throw new InvalidConfigurationException(
            "TODO: Implement Sweet Tooth sync"
        );

        /** @var string sessionId returned by Magento API login method */
        // $this->sessionId = $this->call('login', ['username' => $apiUser, 'apiKey' => $apiKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function call($action, $params = [])
    {
        if (null !== $this->sessionId) {
            $params = array_merge(['sessionId' => $this->sessionId], (array)$params);
        }

        if ($this->isWsiMode) {
            $result = parent::call($action, [(object) $params]);
            $result = $this->parseWSIResponse($result);
        } else {
            $result = parent::call($action, $params);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomers()
    {
        $settings = $this->settings->all();

        if ($this->isExtensionInstalled()) {
            return new CustomerBridgeIterator($this, $settings);
        } else {
            return new CustomerSoapIterator($this, $settings);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'orocrm.magento.transport.soap.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFormType()
    {
        return 'sweet_tooth_channel_soap_transport_setting_form_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsEntityFQCN()
    {
        return 'SweetTooth\\Bundle\\ChannelBundle\\Entity\\SweetToothRestTransport';
    }
}
