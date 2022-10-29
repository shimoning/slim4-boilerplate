<?php

declare(strict_types=1);

namespace App\Application\Actions\Contact;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class ShowFormAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->render('pages/contact-form.twig');
    }
}
