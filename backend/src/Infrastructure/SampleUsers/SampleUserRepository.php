<?php
declare(strict_types=1);

namespace App\Infrastructure\SampleUsers;

use DateTime;

final class SampleUserRepository
{
    /**
     * DB からユーザー件数取得
     *
     * @param array{filterName: null|string, sort: null|string, pageNumber: int, pageSize: int} $command
     * @return int
     */
    public function countUsers(array $command): int
    {
        // 実際はコマンド変数から SQL クエリを組み立てる
        $query = $command;
        // 実際はモデルを利用してデータを取得する
        $count = $query;
        $count = 4;

        return $count;
    }

    /**
     * DB からユーザー取得
     *
     * @param array{filterName: null|string, sort: null|string, pageNumber: int, pageSize: int} $command
     * @return array<array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}>
     */
    public function findUsers(array $command): array
    {
        // 実際はコマンド変数から SQL クエリを組み立てる
        $query = $command;
        // 実際はモデルを利用してデータを取得する
        $records = $query;

        $result = [];
        foreach ($records as $record) {
            $result[] = [
                'id' => (int)($record['id'] ?? 1),
                'name' => $record['name'] ?? '田中 太郎',
                'birthDay' => new DateTime($record['birth_day'] ?? '2000-01-01'),
                'height' => $record['height'] ?? '170.0',
                'gender' => $record['gender'] ?? '1',
            ];
        }

        return $result;
    }

    /**
     * ユーザーが存在するか
     *
     * @param int $id
     * @return bool
     */
    public function isExistUser(int $id): bool
    {
        // 実際はモデルを利用してデータを取得する
        $record = $id;
        $record = $record;

        return true;
    }

    /**
     * DB へユーザー取得
     *
     * @param int $id
     * @return array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}
     */
    public function findUser(int $id): array
    {
        // 実際はモデルを利用してデータを取得する
        $record = $id;

        $result = [
            'id' => (int)($record['id'] ?? 1),
            'name' => (string)($record['name'] ?? '田中 太郎'),
            'birthDay' => new DateTime($record['birth_day'] ?? '2000-01-01'),
            'height' => (string)($record['height'] ?? '170.0'),
            'gender' => (string)($record['gender'] ?? '1'),
        ];

        return $result;
    }
}
