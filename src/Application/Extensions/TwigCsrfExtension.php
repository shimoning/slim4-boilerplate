<?php
declare(strict_types=1);

namespace App\Application\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Slim\Csrf\Guard;

class TwigCsrfExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var Guard
     */
    protected $csrf;

    /**
     * @param Guard $csrf
     */
    public function __construct(Guard $csrf)
    {
        $this->csrf = $csrf;
    }

    /**
     * @return array
     */
    public function getGlobals(): array
    {
        // CSRF token name and value
        return [
            'csrf'   => [
                'name' => [
                    'name' => $this->csrf->getTokenNameKey(),
                    'value' => $this->csrf->getTokenName(),
                ],
                'value' => [
                    'name' => $this->csrf->getTokenValueKey(),
                    'value' => $this->csrf->getTokenValue(),
                ],
            ]
        ];
    }
}
