<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateSampleUsers extends BaseMigration
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
        $table = $this->table('sample_users');
        $table->addColumn('id', 'integer', [
            'autoIncrement' => true,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('birth_day', 'date', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('height', 'decimal', [
            'default' => null,
            'null' => false,
            'precision' => 4,
            'scale' => 1,
        ]);
        $table->addColumn('gender', 'string', [
            'default' => null,
            'limit' => 1,
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
        $table->addIndex([
            'name',
        
            ], [
            'name' => 'BY_NAME',
            'unique' => false,
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
