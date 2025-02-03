<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SampleUsersFixture
 */
class SampleUsersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'birth_day' => '2025-02-03',
                'height' => 1.5,
                'gender' => 'L',
                'created' => '2025-02-03 22:40:34',
                'modified' => '2025-02-03 22:40:34',
            ],
        ];
        parent::init();
    }
}
