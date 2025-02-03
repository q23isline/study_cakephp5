<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SampleUser Entity
 *
 * @property int $id
 * @property string $name
 * @property \Cake\I18n\Date $birth_day
 * @property string $height
 * @property string $gender
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class SampleUser extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'birth_day' => true,
        'height' => true,
        'gender' => true,
        'created' => true,
        'modified' => true,
    ];
}
