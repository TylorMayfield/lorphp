<?php

namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Interfaces\OrganizationInterface;
use LorPHP\Models\User;
use LorPHP\Models\Client;


/**
 * Class Organization
 * Represents the Organization entity.
 *
 * @property string $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property bool $isActive
 * @property string $modifiedBy
 * @property string $name
 */
class Organization extends Model implements OrganizationInterface
{
    protected static string $tableName = 'organizations';
    protected static $fillable = ['id', 'createdAt', 'updatedAt', 'isActive', 'modifiedBy', 'name', 'users', 'clients'];

    /**
     * Get related users
     * @return User[]
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get related clients
     * @return Client[]
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get the users
     * @return User
     */
    public function getUsers()
    {
        return $this->getAttribute('users');
    }

    /**
     * Set the users
     * @param User $users
     * @return void
     */
    public function setUsers($users): void
    {
        $this->setAttribute('users', $users);
    }

    /**
     * Get the clients
     * @return Client
     */
    public function getClients()
    {
        return $this->getAttribute('clients');
    }

    /**
     * Set the clients
     * @param Client $clients
     * @return void
     */
    public function setClients($clients): void
    {
        $this->setAttribute('clients', $clients);
    }
}
