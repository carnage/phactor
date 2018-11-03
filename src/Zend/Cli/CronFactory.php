<?php

namespace Carnage\Phactor\Zend\Cli;


use Carnage\Phactor\Message\Bus;
use Carnage\Phactor\Message\DelayedMessage\DelayedMessageBus;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CronFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $delayedMessageBus = $container->get(Bus::class);

        if (!($delayedMessageBus instanceof DelayedMessageBus)) {
            throw new \RuntimeException('Your application isn\'t configured to use a delayed message bus');
        }

        return Cron::build($delayedMessageBus);
    }
}
