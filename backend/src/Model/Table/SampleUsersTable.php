<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SampleUsers Model
 *
 * @method \App\Model\Entity\SampleUser newEmptyEntity()
 * @method \App\Model\Entity\SampleUser newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\SampleUser> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SampleUser get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\SampleUser findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\SampleUser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\SampleUser> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SampleUser|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\SampleUser saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\SampleUser>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SampleUser>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SampleUser>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SampleUser> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SampleUser>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SampleUser>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SampleUser>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SampleUser> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SampleUsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('sample_users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->date('birth_day')
            ->requirePresence('birth_day', 'create')
            ->notEmptyDate('birth_day');

        $validator
            ->decimal('height')
            ->requirePresence('height', 'create')
            ->notEmptyString('height');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 1)
            ->requirePresence('gender', 'create')
            ->notEmptyString('gender');

        return $validator;
    }
}
