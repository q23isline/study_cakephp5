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
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('sessions');
        $table->addColumn('id', 'char', [
            'default' => null,
            'limit' => 40,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('data', 'binary', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('expires', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
