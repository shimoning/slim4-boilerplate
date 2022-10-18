<?php

declare(strict_types=1);

namespace App\Application\Actions\Top;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\Action;

class ShowAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->render('pages/top.twig');
    }
}
