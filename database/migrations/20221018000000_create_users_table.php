<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');

        $table->addColumn('login_id', 'string', [
            'comment' => 'ログインID',
            'null' => false,
        ]);
        $table->addColumn('login_pw', 'string', [
            'comment' => 'ログインパスワード',
            'null' => false,
        ]);

        $table->addColumn('name', 'string', [
            'comment' => '名前',
        ]);

        $table->addTimestamps();

        $table->addIndex('login_id', [
            'name' => 'uniq_user_login_id',
            'unique' => true,
        ]);

        $table->create();
    }
}
