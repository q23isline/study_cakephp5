<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseSeed;

/**
 * SampleUsers seed.
 */
class SampleUsersSeed extends BaseSeed
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
        $data = [];
        for ($i = 0; $i < 52; $i++) {
            $data[] = [
                'name' => $this->randomName(),
                'birth_day' => $this->randomDate(),
                'height' => rand(140, 200) . '.' . rand(0, 9),
                'gender' => (string)rand(1, 2),
                'created' => DateTime::now(),
                'modified' => DateTime::now(),
            ];
        }

        $table = $this->table('sample_users');
        $table->insert($data)->save();
    }

    /**
     * ランダムな名前を生成
     *
     * @return string
     */
    private function randomName(): string
    {
        $lastNames = ['佐藤', '鈴木', '高橋', '田中', '伊藤', '渡辺', '山本', '中村', '小林', '加藤'];
        $firstNamesMale = ['太郎', '一郎', '健太', '翔', '直樹', '悠斗', '颯太', '大和', '陽翔', '陸'];
        $firstNamesFemale = ['花子', '美咲', 'さくら', '結衣', '葵', '玲奈', '七海', '莉子', '楓', '優奈'];

        $lastName = $lastNames[array_rand($lastNames)];
        if (rand(1, 2) === 1) {
            $firstName = $firstNamesMale[array_rand($firstNamesMale)];
        } else {
            $firstName = $firstNamesFemale[array_rand($firstNamesFemale)];
        }

        return $lastName . ' ' . $firstName;
    }

    /**
     * ランダムな日付を生成
     *
     * @return string
     */
    private function randomDate(): string
    {
        $startTimestamp = strtotime('1950-01-01');
        $endTimestamp = strtotime('2005-12-31');
        $randomTimestamp = rand($startTimestamp, $endTimestamp);

        return date('Y-m-d', $randomTimestamp);
    }
}
