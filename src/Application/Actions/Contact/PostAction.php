<?php

declare(strict_types=1);

namespace App\Application\Actions\Contact;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

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

        return $this->redirect(
            $this->fullUrlFor('contact.complete'),
            200,
        );
    }
}
