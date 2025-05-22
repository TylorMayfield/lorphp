<?php

namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Interfaces\UserInterface;


/**
 * Class User
 * Represents the User entity.
 *
 * @property string $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property bool $isActive
 * @property string $modifiedBy
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 */
class User extends Model implements UserInterface
{
    protected static string $tableName = 'users';
    protected static array $fillable = ['id', 'createdAt', 'updatedAt', 'isActive', 'modifiedBy', 'name', 'email', 'password', 'role', 'clients', 'organizations'];
}
