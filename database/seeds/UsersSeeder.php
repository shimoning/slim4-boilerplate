<?php

use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    public function run(): void
    {
        // ユーザ
        $users = $this->table('users');
        $users->insert([
            [
                'login_id' => 'user_1',
                'login_pw' => \password_hash('Password@1234', PASSWORD_BCRYPT),
                'name' => 'User Name',
            ],
            // ...
        ])->save();
    }
}
