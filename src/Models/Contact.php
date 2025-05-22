<?php

namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Interfaces\ContactInterface;


/**
 * Class Contact
 * Represents the Contact entity.
 *
 * @property string $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property bool $isActive
 * @property string $modifiedBy
 * @property string $name
 * @property string $email
 * @property string $clientId
 */
class Contact extends Model implements ContactInterface
{
    protected static string $tableName = 'contacts';
    protected static array $fillable = ['id', 'createdAt', 'updatedAt', 'isActive', 'modifiedBy', 'name', 'email', 'clientId', 'client'];
}
