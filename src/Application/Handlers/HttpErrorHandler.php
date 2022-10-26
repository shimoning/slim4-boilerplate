<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\CallableResolverInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use App\Exceptions\HttpCsrfException;
use DI\Container;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Slim\Views\Twig;
use Throwable;

class HttpErrorHandler extends SlimErrorHandler
{
    protected Container $container;

    public function __construct(
        Container $container,
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        ?LoggerInterface $logger = null
    ) {
        $this->container = $container;
        parent::__construct($callableResolver, $responseFactory, $logger);
    }

    /**
     * @inheritdoc
     */
    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        $statusCode = 500;
        $error = new ActionError(
            ActionError::SERVER_ERROR,
            'An internal error has occurred while processing your request.'
        );

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $error->setDescription($exception->getMessage());

            if ($exception instanceof HttpNotFoundException) {
                $error->setType(ActionError::RESOURCE_NOT_FOUND);
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $error->setType(ActionError::NOT_ALLOWED);
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $error->setType(ActionError::UNAUTHENTICATED);
            } elseif ($exception instanceof HttpForbiddenException) {
                $error->setType(ActionError::INSUFFICIENT_PRIVILEGES);
            } elseif ($exception instanceof HttpBadRequestException) {
                $error->setType(ActionError::BAD_REQUEST);
            } elseif ($exception instanceof HttpNotImplementedException) {
                $error->setType(ActionError::NOT_IMPLEMENTED);
            } else if ($exception instanceof HttpCsrfException) {
                $error->setType(ActionError::CSRF_ERROR);
            }
        }

        if (
            !($exception instanceof HttpException)
            && $exception instanceof Throwable
            && $this->displayErrorDetails
        ) {
            $error->setDescription($exception->getMessage());
        }

        $response = $this->responseFactory->createResponse($statusCode);
        if (
            ! \method_exists($exception, 'getRequest') ||
            $exception->getRequest()->getHeaderLine('X-Requested-With') === 'XMLHttpRequest'
        ) {
            $payload = new ActionPayload($statusCode, null, $error);
            $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);
            $response->getBody()->write($encodedPayload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            if ($statusCode === 419) {
                return $response->withStatus(200)->withHeader('Location', $_SERVER['HTTP_REFERER']);
            }

            /** @var Twig $twig */
            $twig = $this->container->get(Twig::class);
            $response->getBody()->write($twig->fetch('errors/default', [
                'statusCode' => $statusCode,
                'error' => $error,
            ]));
            return $response;
        }
    }
}
