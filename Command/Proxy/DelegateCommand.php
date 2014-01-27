<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Doctrine\Bundle\DoctrineBundle\Command\Proxy;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command Delegate.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class DelegateCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

    /**
     * @return \Symfony\Component\Console\Command\Command
     */
    abstract protected function createCommand();

    /**
     * @return string
     */
    protected function getMinimalVersion()
    {
        return '2.3.0-DEV';
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        if (version_compare(\Doctrine\ORM\Version::VERSION, $this->getMinimalVersion()) >= 0) {
            $this->command = $this->createCommand();
            $help          = $this->command->getHelp();
            $difinition    = $this->command->getDefinition();
            $description   = $this->command->getDescription();

            $this->setHelp($help);
            $this->setDefinition($difinition);
            $this->setDescription($description);
        }

        $this->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (version_compare(\Doctrine\ORM\Version::VERSION, $this->getMinimalVersion()) < 0) {
            throw new \RuntimeException(sprintf('"%s" requires doctrine-orm "%s" or newer', $this->getName(), $this->getMinimalVersion()));
        }

        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));

        $this->command->setHelperSet($this->getHelperSet());
        $this->command->execute($input, $output);
    }
}
