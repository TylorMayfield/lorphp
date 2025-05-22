<?php

namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Interfaces\PermissionInterface;


/**
 * Class Permission
 * Represents the Permission entity.
 *
 * @property string $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property bool $isActive
 * @property string $modifiedBy
 * @property string $name
 */
class Permission extends Model implements PermissionInterface
{
    protected static string $tableName = 'permissions';
    protected static array $fillable = ['id', 'createdAt', 'updatedAt', 'isActive', 'modifiedBy', 'name', 'users'];
}
