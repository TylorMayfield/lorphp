<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class CreatePermissionsTables extends Migration {
    private $roleIds = [
        'admin' => '5c6eec33-1150-4e0b-bd2d-a8c0f4a8fd8f',
        'manager' => '7615d1a5-fbaa-4be3-9a53-3900987729c8',
        'user' => '07ce4626-4c87-4e33-93a5-2894ee3f9f31'
    ];

    public function up() {
        // Create roles table with UUIDs
        $this->createTable('roles', function(Schema $table) {
            $table->uuid();
            $table->string('name');
            $table->string('description', true); // nullable
            $table->timestamps();
            $table->unique('name');
        });

        // Create permissions table with UUIDs
        $this->createTable('permissions', function(Schema $table) {
            $table->uuid();
            $table->string('name');
            $table->string('description', true); // nullable
            $table->timestamps();
            $table->unique('name');
        });

        // Create role_permissions pivot table with UUIDs
        $this->createTable('role_permissions', function(Schema $table) {
            $table->string('role_id');
            $table->string('permission_id');
            $table->timestamp('created_at', 'CURRENT_TIMESTAMP');
            $table->foreignKey('role_id', 'roles(id)', 'CASCADE');
            $table->foreignKey('permission_id', 'permissions(id)', 'CASCADE');
        });

        $roles = [
            ['id' => $this->roleIds['admin'], 'name' => 'admin', 'description' => 'System Administrator with full access'],
            ['id' => $this->roleIds['manager'], 'name' => 'manager', 'description' => 'Organization Manager'],
            ['id' => $this->roleIds['user'], 'name' => 'user', 'description' => 'Standard User']
        ];

        // Insert roles with fixed UUIDs
        foreach ($roles as $role) {
            $this->db->query("
                INSERT INTO roles (id, name, description, created_at, updated_at) 
                VALUES (?, ?, ?, datetime('now'), datetime('now'))",
                [$role['id'], $role['name'], $role['description']]
            );
        }

        // Create permissions map
        $permissionIds = [];
        $permissionsList = [
            'view_dashboard' => 'Access to view dashboard',
            'view_organizations' => 'View all organizations',
            'manage_organizations' => 'Create/Edit/Delete organizations',
            'view_users' => 'View user listings',
            'manage_users' => 'Create/Edit/Delete users',
            'disable_users' => 'Enable/Disable user accounts',
            'view_clients' => 'View client listings',
            'manage_clients' => 'Create/Edit/Delete clients',
            'view_settings' => 'Access settings page',
            'manage_settings' => 'Modify system settings'
        ];

        // Insert permissions
        foreach ($permissionsList as $name => $description) {
            $id = $this->generateUuid();
            $permissionIds[$name] = $id;
            $this->db->query(
                "INSERT INTO permissions (id, name, description, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))",
                [$id, $name, $description]
            );
        }

        // Role permission mappings
        $rolePermissions = [
            'admin' => array_values($permissionIds), // Admin gets all permissions
            'manager' => [
                $permissionIds['view_dashboard'],
                $permissionIds['view_users'],
                $permissionIds['view_clients'],
                $permissionIds['manage_clients'],
                $permissionIds['view_settings'],
                $permissionIds['manage_settings']
            ],
            'user' => [
                $permissionIds['view_dashboard'],
                $permissionIds['view_clients'],
                $permissionIds['manage_clients'],
                $permissionIds['view_settings']
            ]
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $roleName => $permissions) {
            foreach ($permissions as $permissionId) {
                $this->db->query(
                    "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                    [$this->roleIds[$roleName], $permissionId]
                );
            }
        }

        // Set admin role for admin user
        $this->db->query(
            "UPDATE users SET role_id = ? WHERE email = ?",
            [$this->roleIds['admin'], 'admin@lorphp.local']
        );
        
        // Set default role for other users
        $this->db->query(
            "UPDATE users SET role_id = ? WHERE role_id IS NULL AND email != ?",
            [$this->roleIds['user'], 'admin@lorphp.local']
        );
    }

    public function down() {
        $this->dropTable('role_permissions');
        $this->dropTable('permissions');
        $this->dropTable('roles');
    }

    private function generateUuid(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
