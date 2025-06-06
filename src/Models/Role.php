<?php

/**
 * This file is auto-generated by LorPHP.
 * Generated on: 2025-06-04 15:58:32
 * 
 * WARNING: Do not edit this file manually.
 * Any changes will be overwritten when the file is regenerated.
 */
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Interfaces\RoleInterface;
use LorPHP\Models\Permission;
use LorPHP\Models\User;

/**
 * Class Role
 * Represents the Role entity.
 *
 * @property string $id
 * @property string $created_at
 * @property string $updated_at
 * @property bool $is_active
 * @property string $modified_by
 * @property string $name
 * @property string $description
 */
class Role extends Model implements RoleInterface
{
    protected static string $tableName = 'roles';
    protected static $fillable = ['id', 'id', 'created_at', 'created_at', 'updated_at', 'updated_at', 'is_active', 'is_active', 'modified_by', 'modified_by', 'name', 'name', 'description', 'description', 'permissions', 'permissions', 'users', 'users'];
                
                
    /**
     * Find a record by its name
     * @param string $name The name to search for
     * @return static|null The record if found, null otherwise
     */
    public static function findByName(string $name): ?static
    {
        $db = \LorPHP\Core\Database::getInstance();
        $data = $db->findOne(static::$tableName, ['name' => $name]);
        
        if ($data) {
            $model = new static();
            $model->fill($data);
            return $model;
        }
        
        return null;
    }
                
    /**
     * Get related permissions
     * @return Permission[]
     */
    public function permissions(): array
    {
        return $this->manyToMany(Permission::class);
    }
                
    public function getPermissions()
    {
        return $this->permissions();
    }

    public function setPermissions($permissions): void
    {
        $this->permissions = $permissions;
    }
                
    /**
     * Get related users
     * @return User[]
     */
    public function users(): array
    {
        return $this->hasMany(User::class);
    }
                
    public function getUsers()
    {
        return $this->users();
    }

    public function setUsers($users): void
    {
        $this->users = $users;
    }
                
    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }
                
    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function setCreated_at($created_at): void
    {
        $this->created_at = $created_at;
    }
                
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    public function setUpdated_at($updated_at): void
    {
        $this->updated_at = $updated_at;
    }
                
    public function getIs_active()
    {
        return $this->is_active;
    }

    public function setIs_active($is_active): void
    {
        $this->is_active = $is_active;
    }
                
    public function getModified_by()
    {
        return $this->modified_by;
    }

    public function setModified_by($modified_by): void
    {
        $this->modified_by = $modified_by;
    }
                
    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }
                
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }


    public function save(): bool {
        return parent::save();
    }}
