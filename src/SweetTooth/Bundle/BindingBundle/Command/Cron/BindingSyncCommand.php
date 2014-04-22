<?php

namespace SweetTooth\Bundle\BindingBundle\Command\Cron;

use SweetTooth\Bundle\BindingBundle\Entity\ContactBinding;

use Oro\Bundle\CronBundle\Command\CronCommandInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Oro\Bundle\ImapBundle\Sync\ImapEmailSynchronizer;
use Oro\Bundle\CronBundle\Command\Logger\OutputLogger;

class BindingSyncCommand extends ContainerAwareCommand implements CronCommandInterface
{
    /**
     * {@internaldoc}
     */
    public function getDefaultDefinition()
    {
        return '*/30 * * * *';
    }

    /**
     * {@internaldoc}
     */
    protected function configure()
    {
        $this
            ->setName('oro:cron:sweettooth:sync')
            ->setDescription('Sweet Tooth Contact and Order sync');
    }

    /**
     * {@internaldoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $synchronizer = $this->getContainer()->get('sweettooth_binding.binding_synchronizer');
        $synchronizer->sync();
    }
}
