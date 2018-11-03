<?php

namespace Carnage\Phactor\Zend;

use Carnage\Phactor\Auth\AnonUser;
use Carnage\Phactor\Auth\AuthorisationDelegator;
use Carnage\Phactor\Identity\Generator;
use Carnage\Phactor\Message\GenericBus;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthBusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $subscriptions = $config['message_subscriptions'];
        $rbac = $config['message_rbac'];

        $wrappedBus = new GenericBus(
            $container->get('Log'),
            $subscriptions,
            $container->get(MessageHandlerManager::class),
            $container->get(Generator::class)
        );

        $auth = $container->get(AuthenticationServiceInterface::class);

        $user = new AnonUser();

        if ($auth->hasIdentity()) {
            //@TODO handle instance where this identity doesn't implement our interface (provide config to wrap it)
            $user = $auth->getIdentity();
        }

        return new AuthorisationDelegator($wrappedBus, $rbac, $user);
    }
}