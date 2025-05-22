<?php

namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Interfaces\ClientInterface;


/**
 * Class Client
 * Represents the Client entity.
 *
 * @property string $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property bool $isActive
 * @property string $modifiedBy
 * @property string $name
 * @property string $email
 * @property string $organizationId
 */
class Client extends Model implements ClientInterface
{
    protected static string $tableName = 'clients';
    protected static array $fillable = ['id', 'createdAt', 'updatedAt', 'isActive', 'modifiedBy', 'name', 'email', 'organizationId', 'organization', 'contacts', 'packages'];
}
