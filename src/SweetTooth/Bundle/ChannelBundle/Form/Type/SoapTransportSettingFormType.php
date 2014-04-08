<?php

namespace SweetTooth\Bundle\ChannelBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;
use Oro\Bundle\FormBundle\Form\DataTransformer\ArrayToJsonTransformer;
use Oro\Bundle\IntegrationBundle\Manager\TypesRegistry;

use SweetTooth\Bundle\ChannelBundle\Form\EventListener\SoapSettingsFormSubscriber;
use SweetTooth\Bundle\ChannelBundle\Form\EventListener\SoapConnectorsFormSubscriber;

class SoapTransportSettingFormType extends AbstractType
{
    const NAME = 'sweet_tooth_channel_soap_transport_setting_form_type';

    /** @var TransportInterface */
    protected $transport;

    /** @var SoapSettingsFormSubscriber */
    protected $subscriber;

    /** @var TypesRegistry */
    protected $registry;

    public function __construct(
        TransportInterface $transport,
        SoapSettingsFormSubscriber $subscriber,
        TypesRegistry $registry
    ) {
        $this->transport  = $transport;
        $this->subscriber = $subscriber;
        $this->registry   = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->subscriber);

        $builder->add(
            'apiKey',
            'text',
            ['label' => 'API Key', 'required' => true]
        );

        // TODO: Add connection checker
        // $builder->add('check', 'button', ['label' => 'Check connection']);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => $this->transport->getSettingsEntityFQCN()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
