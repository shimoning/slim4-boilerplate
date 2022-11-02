<?php

declare(strict_types=1);

namespace App\Application\Actions\Contact;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;
use Symfony\Component\Mime\Email;

class PostAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        if (
            empty($data['email']) ||
            !preg_match('/^[a-z0-9._+^~-]+@[a-z0-9.-]+$/i', $data['email'])
        ) {
            return $this->respondWithValidationError('メールアドレスの入力に誤りがあります。');
        } else if (empty($data['name']) || empty($data['message'])) {
            return $this->respondWithValidationError('空欄があります。必須項目を全て入力して下さい。');
        }

        // sendmail
        $email = (new Email)
            ->from($data['email'])
            ->to($this->settings->get('app')['APP_CONTACT_EMAIL'])
            ->subject('サイトからのお問い合わせ | Slim4 Boilerplate')
            ->text($this->twig->fetch(
                'emails/contact.txt.twig',
                [
                    'data' => $data,
                    'remote_addr' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                ],
            ));

        $this->mailer->send($email);

        return $this->redirect(
            $this->fullUrlFor('contact.complete'),
            200,
        );
    }
}
