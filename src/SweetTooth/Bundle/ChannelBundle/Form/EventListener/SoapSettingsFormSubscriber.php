<?php

namespace SweetTooth\Bundle\ChannelBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Oro\Bundle\SecurityBundle\Encoder\Mcrypt;

class SoapSettingsFormSubscriber implements EventSubscriberInterface
{
    /** @var Mcrypt */
    protected $encryptor;

    public function __construct(Mcrypt $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSet',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        ];
    }

    /**
     * Populate websites choices if exist in entity
     *
     * @param FormEvent $event
     */
    public function preSet(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($data === null) {
            return;
        }

        // $modifier = $this->getModifierWebsitesList($data->getWebsites());
        // $modifier($form);

        // if ($data->getId()) {
            // change label for apiKey field
            // $options = $event->getForm()->get('apiKey')->getConfig()->getOptions();
            // $options = array_merge($options, ['label' => 'New SOAP API Key', 'required' => false]);
            // $form->add('apiKey', 'text', $options);
        // }
    }

    /**
     * Pre submit event listener
     * Encrypt passwords and populate if empty
     * Populate websites choices from hidden fields
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = (array)$event->getData();

        // $oldPassword = $event->getForm()->get('apiKey')->getData();
        // if (empty($data['apiKey']) && $oldPassword) {
        //     // populate old password
        //     $data['apiKey'] = $oldPassword;
        // } elseif (isset($data['apiKey'])) {
        //     $data['apiKey'] = $this->encryptor->encryptData($data['apiKey']);
        // }

        $event->setData($data);
    }
}
