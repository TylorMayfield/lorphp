<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;

class Permission extends Model {
    protected static $table = 'permissions';
    
    public static function getAllForRole(string $role_id): array {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT p.name
            FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = ?
        ", [$role_id]);
        
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function hasPermission(string $role_id, string $permission): bool {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT COUNT(*) as count
            FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = ? AND p.name = ?
        ", [$role_id, $permission]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }
}
