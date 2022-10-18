<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use Psr\Log\LoggerInterface;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

class PdoUserRepository implements UserRepository
{
    private \PDO $pdo;
    protected LoggerInterface $logger;

    /**
     * @param \PDO $pdo
     * @param LoggerInterface $logger
     */
    public function __construct(
        \PDO $pdo,
        LoggerInterface $logger,
    ) {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    /**
     * fetch all
     *
     * @return User[]
     */
    public function findAll(): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM users;');
        $statement->execute();
        $users = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(array($this, 'entity'), $users);
    }

    /**
     * fetch one by primary key
     *
     * @param int $id
     * @return User
     */
    public function findById(int $id): User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE id = :id;');
        $statement->execute(['id' => $id]);
        $user = $statement->fetch(\PDO::FETCH_ASSOC);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        return $this->entity($user);
    }

    /**
     * convert Entity
     *
     * @param array $user
     * @return User
     */
    public function entity($user): User
    {
        return new User(
            $user['id'] ?? null,
            $user['login_id'] ?? null,
            $user['login_pw'] ?? null,
            $user['name'] ?? '',
            $user['created_at'] ?? null,
            $user['updated_at'] ?? null,
        );
    }
}
