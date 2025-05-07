<?php
namespace LorPHP\Core;

use LorPHP\Models\Permission;

class RoleMiddleware {
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
