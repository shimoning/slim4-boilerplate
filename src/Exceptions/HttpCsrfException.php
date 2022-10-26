<?php

declare(strict_types=1);

namespace App\Exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpCsrfException extends HttpSpecializedException
{
    protected $code = 419;
    protected $message = 'Page Expired.';
    protected string $title = '419 Page Expired';
    protected string $description = 'The page has expired.';
}
