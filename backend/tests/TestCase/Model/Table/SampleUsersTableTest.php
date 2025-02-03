<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SampleUsersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SampleUsersTable Test Case
 */
class SampleUsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SampleUsersTable
     */
    protected $SampleUsers;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.SampleUsers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SampleUsers') ? [] : ['className' => SampleUsersTable::class];
        $this->SampleUsers = $this->getTableLocator()->get('SampleUsers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SampleUsers);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SampleUsersTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
