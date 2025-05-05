<?php
namespace LorPHP\Core;

class RoleMiddleware {
    private static $roles = [
        'admin' => ['view_users', 'manage_users', 'view_clients', 'manage_clients', 'manage_organization'],
        'manager' => ['view_users', 'view_clients', 'manage_clients'],
        'user' => ['view_clients', 'manage_clients'] 
    ];
    
    /**
     * Check if user has required permission
     * @param string $permission
     * @return bool
     */
    public static function hasPermission(string $permission): bool {
        $user = Application::getInstance()->getState('user');
        if (!$user || !isset($user->role)) {
            return false;
        }
        
        $userRole = $user->role ?? 'user';
        return in_array($permission, self::$roles[$userRole] ?? []);
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
