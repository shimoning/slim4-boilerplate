<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;

use Slim\App;
use Slim\Factory\AppFactory;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use Slim\Csrf\Guard;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Slim\Flash\Messages;

use Slim\Views\Twig;
use App\Application\Extensions\TwigCsrfExtension;
use App\Exceptions\HttpCsrfException;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        App::class => function (ContainerInterface $container) {
            return AppFactory::createFromContainer($container);
        },
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        \PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');

            $host = $dbSettings['DB_HOST'];
            $port = $dbSettings['DB_PORT'];
            $dbname = $dbSettings['DB_DATABASE'];
            $charset = $dbSettings['DB_CHARSET'];
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

            return new \PDO($dsn, $dbSettings['DB_USERNAME'], $dbSettings['DB_PASSWORD']);
        },
        Guard::class => function (App $app, Messages $m) {
            return (new Guard($app->getResponseFactory()))
                ->setPersistentTokenMode(true)
                ->setFailureHandler(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($m) {
                    $request = $request->withAttribute('csrf_status', false);
                    $m->addMessage('csrf_error', true);

                    throw new HttpCsrfException($request);
                });
        },
        Messages::class => function () {
            return new Messages();
        },
        Twig::class => function (ContainerInterface $c, Guard $csrf, Messages $m) {
            $settings = $c->get(SettingsInterface::class);
            $twig = Twig::create(__DIR__ . '/../templates', $settings->get('twig'));
            $twig->addExtension(new TwigCsrfExtension($csrf));

            $environment = $twig->getEnvironment();
            $environment->addGlobal('flash', $m);
            return $twig;
        },
        MailerInterface::class => function () {
            // TODO: support other protocol
            return new Mailer(Transport::fromDsn('sendmail://default'));
        },
    ]);
};
