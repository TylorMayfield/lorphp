<?php

namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Interfaces\PackageInterface;


/**
 * Class Package
 * Represents the Package entity.
 *
 * @property string $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property bool $isActive
 * @property string $modifiedBy
 * @property string $name
 */
class Package extends Model implements PackageInterface
{
    protected static string $tableName = 'packages';
    protected static array $fillable = ['id', 'createdAt', 'updatedAt', 'isActive', 'modifiedBy', 'name', 'clients'];
}
