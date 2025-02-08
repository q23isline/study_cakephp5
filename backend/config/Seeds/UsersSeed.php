<?php
declare(strict_types=1);

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\I18n\DateTime;
use Migrations\BaseSeed;

/**
 * Users seed.
 */
class UsersSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/4/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'email' => 'admin@example.com',
                'password' => $this->_setPassword('admin00'),
                'created' => DateTime::now(),
                'modified' => DateTime::now(),
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }

    /**
     * パスワードを生成する
     *
     * @param string $password パスワード
     * @return string ハッシュ化されたパスワード
     */
    private function _setPassword(string $password): string
    {
        $hasher = new DefaultPasswordHasher();

        return $hasher->hash($password);
    }
}
