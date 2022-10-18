<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;
use App\Application\Settings\SettingsInterface;
use Slim\Views\Twig;

use App\Domain\User\UserRepository;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;

    public function __construct(
        LoggerInterface $logger,
        SettingsInterface $settings,
        Twig $twig,
        UserRepository $userRepository,
    ) {
        parent::__construct($logger, $settings, $twig);
        $this->userRepository = $userRepository;
    }
}
