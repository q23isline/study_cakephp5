<?php
declare(strict_types=1);

namespace App\Infrastructure\SampleUsers;

use App\Model\Entity\SampleUser;
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
            $result[] = $this->buildEntity($record);
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

        return $this->buildEntity($record);
    }

    /**
     * DB へユーザー保存
     *
     * @param array{type: string, name: string, birthDay: \DateTime, height: string, gender: string} $command
     * @return array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}
     */
    public function saveUser(array $command): array
    {
        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $entity = $model->newEmptyEntity();
        $entity->name = $command['name'];
        $entity->birth_day = new Date($command['birthDay']->format('Y-m-d'));
        $entity->height = $command['height'];
        $entity->gender = $command['gender'];
        $saved = $model->saveOrFail($entity);

        return $this->buildEntity($saved);
    }

    /**
     * DB へユーザー更新
     *
     * @param array{type: string, id: int, name: string, birthDay: \DateTime, height: string, gender: string} $command
     * @return array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}
     */
    public function updateUser(array $command): array
    {
        /** @var \App\Model\Table\SampleUsersTable $model */
        $model = TableRegistry::getTableLocator()->get('SampleUsers');
        $entity = $model->get($command['id']);
        $entity->name = $command['name'];
        $entity->birth_day = new Date($command['birthDay']->format('Y-m-d'));
        $entity->height = $command['height'];
        $entity->gender = $command['gender'];
        $saved = $model->saveOrFail($entity);

        return $this->buildEntity($saved);
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
     * エンティティを組み立てる
     *
     * @param \App\Model\Entity\SampleUser $entity
     * @return array{id: int, name: string, birthDay: \DateTime, height: string, gender: string}
     */
    private function buildEntity(SampleUser $entity): array
    {
        $result = [
            'id' => $entity->id,
            'name' => $entity->name,
            'birthDay' => new DateTime($entity->birth_day->format('Y-m-d')),
            'height' => $entity->height,
            'gender' => $entity->gender,
        ];

        return $result;
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
