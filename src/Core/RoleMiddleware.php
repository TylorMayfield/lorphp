<?php
namespace LorPHP\Core;

use LorPHP\Models\Permission;
use LorPHP\Models\User;

class RoleMiddleware {
    /**
     * Check if user is authenticated
     * @return bool
     */
    public static function isAuthenticated(): bool {
        return !is_null(Application::getInstance()->getState('user'));
    }

    /**
     * Get the current authenticated user
     * @return \LorPHP\Models\User|null
     */
    public static function getCurrentUser(): ?\LorPHP\Models\User {
        return Application::getInstance()->getState('user');
    }

    /**
     * Check if user has specified role
     * @param string $role
     * @return bool
     */
    public static function hasRole(string $role): bool {
        $user = Application::getInstance()->getState('user');
        if (!$user || !isset($user->role)) {
            return false;
        }
        
        return strtolower($user->role) === strtolower($role);
    }

    /**
     * Check if user has required permission
     * @param string $permission
     * @return bool
     */
    public static function hasPermission(string $permission): bool {
        $user = Application::getInstance()->getState('user');
        if (!$user || !isset($user->role_id)) {
            return false;
        }
        
        return Permission::hasPermission($user->role_id, $permission);
    }
    
    /**
     * Require a specific permission to access a resource
     * @param string $permission
     * @throws \Exception
     */
    public static function requirePermission(string $permission): void {
        if (!self::hasPermission($permission)) {
            throw new \Exception('Unauthorized: Insufficient permissions');
        }
    }
}
