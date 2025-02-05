<?php
declare(strict_types=1);

namespace App\Infrastructure\SampleUsers;

use Cake\I18n\Date;
use Cake\ORM\TableRegistry;
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
        $conditions = null;
        if ($command['filterName'] !== null) {
            $conditions['name LIKE'] = '%' . $command['filterName'] . '%';
        }

        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $count = $model->find()->where($conditions)->count();

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
        $conditions = null;
        if ($command['filterName'] !== null) {
            $conditions['name LIKE'] = '%' . $command['filterName'] . '%';
        }

        $offset = ($command['pageNumber'] - 1) * $command['pageSize'];

        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $query = $model->find()->where($conditions)->offset($offset)->limit($command['pageSize']);

        if ($command['sort'] !== null) {
            $sortColumn = $this->removeLeadingHyphen($command['sort']);
            $order = $this->startsWithHyphen($command['sort']) ? 'DESC' : 'ASC';
            $query = $query->orderBy([
                $sortColumn => $order,
                'id' => 'ASC', // 並び順が一定になるように ID をつけとく
            ]);
        }

        /** @var array<\App\Model\Entity\SampleUser> $records */
        $records = $query;

        $result = [];
        foreach ($records as $record) {
            $result[] = [
                'id' => $record->id,
                'name' => $record->name,
                'birthDay' => new DateTime($record->birth_day->format('Y-m-d')),
                'height' => $record->height,
                'gender' => $record->gender,
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
        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $query = $model->find()->select(['id'])->where(['id' => $id])->first();

        /** @var \App\Model\Entity\SampleUser|null $record */
        $record = $query;

        if ($record === null) {
            return false;
        }

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
        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $record = $model->get($id);

        $result = [
            'id' => $record->id,
            'name' => $record->name,
            'birthDay' => new DateTime($record->birth_day->format('Y-m-d')),
            'height' => $record->height,
            'gender' => $record->gender,
        ];

        return $result;
    }

    /**
     * DB へユーザー保存
     *
     * @param array{type: string, name: string, birthDay: \DateTime, height: string, gender: string} $command
     * @return int
     */
    public function saveUser(array $command): int
    {
        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $entity = $model->newEmptyEntity();
        $entity->name = $command['name'];
        $entity->birth_day = new Date($command['birthDay']->format('Y-m-d'));
        $entity->height = $command['height'];
        $entity->gender = $command['gender'];
        $saved = $model->saveOrFail($entity);

        // 保存後に採番された ID を返す
        return $saved->id;
    }

    /**
     * DB へユーザー更新
     *
     * @param array{type: string, id: int, name: string, birthDay: \DateTime, height: string, gender: string} $command
     * @return void
     */
    public function updateUser(array $command): void
    {
        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $entity = $model->get($command['id']);
        $entity->name = $command['name'];
        $entity->birth_day = new Date($command['birthDay']->format('Y-m-d'));
        $entity->height = $command['height'];
        $entity->gender = $command['gender'];
        $model->saveOrFail($entity);
    }

    /**
     * DB へユーザー削除
     *
     * @param int $id
     * @return void
     */
    public function deleteUser(int $id): void
    {
        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $entity = $model->get($id);
        $model->deleteOrFail($entity);
    }

    /**
     * 先頭がハイフンかどうか
     *
     * @param string $str
     * @return bool
     */
    private function startsWithHyphen(string $str): bool
    {
        return isset($str[0]) && $str[0] === '-';
    }

    /**
     * 先頭のハイフンを除いた文字列を取得する
     *
     * @param string $str
     * @return string
     */
    private function removeLeadingHyphen(string $str): string
    {
        return $this->startsWithHyphen($str) ? substr($str, 1) : $str;
    }
}
