<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateSessions extends BaseMigration
{
    public bool $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('sessions');

        $table->addColumn('id', 'char', [
            'limit' => 40,
            'null' => false,
        ]);

        $table->addColumn('created', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => false,
        ]);

        $table->addColumn('modified', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'update' => 'CURRENT_TIMESTAMP',
            'null' => false,
        ]);

        $table->addColumn('data', 'binary', [
            'default' => null,
            'limit' => 8000,
            'null' => true,
        ]);

        $table->addColumn('expires', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
            'signed' => false,
        ]);

        $table->addPrimaryKey(['id']);

        $table->create();
    }
}
