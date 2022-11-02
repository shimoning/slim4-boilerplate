<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

use Psr\Log\LoggerInterface;
use App\Application\Settings\SettingsInterface;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Symfony\Component\Mailer\MailerInterface;

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    protected LoggerInterface $logger;
    protected SettingsInterface $settings;
    protected Twig $twig;
    protected Messages $message;
    protected MailerInterface $mailer;

    protected Request $request;
    protected Response $response;

    protected array $args;

    public function __construct(
        LoggerInterface $logger,
        SettingsInterface $settings,
        Twig $twig,
        Messages $message,
        MailerInterface $mailer,
    ) {
        $this->logger = $logger;
        $this->settings = $settings;
        $this->twig = $twig;
        $this->message = $message;
        $this->mailer = $mailer;
    }

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * @return array|object
     */
    protected function getFormData()
    {
        return $this->request->getParsedBody();
    }

    /**
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @return mixed
     */
    protected function resolveQuery(string $name, $default = '')
    {
        return $this->request->getQueryParams()[$name] ?? $default;
    }

    /**
     * @return mixed
     */
    protected function resolvePostBody(string $name, $default = '')
    {
        return $this->request->getParsedBody()[$name] ?? $default;
    }

    protected function setFlashMessage(string $key, $message)
    {
        $this->message->addMessage($key, $message);
    }

    /**
     * @param array|object|null $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($payload->getStatusCode());
    }

    protected function respondWithValidationError($message): Response
    {
        $this->setFlashMessage('validation-error', $message);
        return $this->redirect($_SERVER['HTTP_REFERER'], 302);
    }

    /**
     * ルート名からURLをフルで取得する
     *
     * @param string $routeName
     * @param array $data
     * @param array $queryParams
     * @return string
     */
    protected function fullUrlFor(
        string $routeName,
        array $data = [],
        array $queryParams = [],
    ): string {
        return RouteContext::fromRequest($this->request)
            ->getRouteParser()
            ->fullUrlFor(
                $this->request->getUri(),
                $routeName,
                $data,
                $queryParams,
            );
    }

    /**
     * リダイレクトを行う
     *
     * @param string $location
     * @param integer $status
     * @return Response
     */
    protected function redirect(string $location, int $status = 302): Response
    {
        return $this->response
            ->withStatus($status)
            ->withHeader('Location', $location);
    }

    /**
     * Short-hand for Twig render
     */
    protected function render(string $template, array $data = []): Response
    {
        return $this->twig->render($this->response, $template, $data);
    }
}
