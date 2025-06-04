<?php
namespace LorPHP\Database\Seeders;

use LorPHP\Models\Role;

class RoleSeeder
{
    public static function run()
    {
        require_once __DIR__ . '/../../src/Core/Traits/HasUuid.php';
        require_once __DIR__ . '/../../src/Interfaces/UserInterface.php';
        require_once __DIR__ . '/../../src/Models/User.php';
        require_once __DIR__ . '/../../src/Controllers/AuthController.php';

        // Helper for UUID generation
        $uuidGen = function() {
            $obj = new class {
                use \LorPHP\Core\Traits\HasUuid;
                public function uuid() {
                    return $this->generateUuid();
                }
            };
            return $obj->uuid();
        };

        // Seed roles
        $roles = [
            ['id' => $uuidGen(), 'name' => 'user', 'description' => 'Default user role', 'is_active' => true],
            ['id' => $uuidGen(), 'name' => 'admin', 'description' => 'Administrator role', 'is_active' => true],
            ['id' => $uuidGen(), 'name' => 'moderator', 'description' => 'Moderator role', 'is_active' => true],
            ['id' => $uuidGen(), 'name' => 'default_admin', 'description' => 'Default admin role for initial setup', 'is_active' => true],
        ];

        foreach ($roles as $roleData) {
            if (!Role::findByName($roleData['name'])) {
                $role = new Role();
                $role->id = $roleData['id'];
                $role->name = $roleData['name'];
                $role->description = $roleData['description'];
                $role->is_active = $roleData['is_active'];
                $role->save();
            }
        }        // Create LOR Organization first
        require_once __DIR__ . '/../../src/Interfaces/OrganizationInterface.php';
        require_once __DIR__ . '/../../src/Models/Organization.php';
        $orgClass = '\LorPHP\Models\Organization';
        $orgName = 'LOR Organization';
        $db = \LorPHP\Core\Database::getInstance();
        $orgData = $db->findOne('organizations', ['name' => $orgName]);
        if ($orgData) {
            $org = new $orgClass();
            $org->fill($orgData);
            error_log('[RoleSeeder] Loaded org: ' . print_r($orgData, true));
        } else {
            $org = new $orgClass();
            $org->id = $uuidGen();
            $org->name = $orgName;
            $org->is_active = true;
            error_log('[RoleSeeder] About to save org, id: ' . var_export($org->id, true) . ' type: ' . gettype($org->id));
            $org->save();
            error_log('[RoleSeeder] Created org: ' . print_r(['id' => $org->id, 'name' => $org->name], true));
        }

        // Add default LOR user
        $lorEmail = 'lor@localhost.com';
        $lorName = 'LOR';
        $lorPassword = 'changeme';

        $lorRole = null;
        foreach ($roles as $roleData) {
            if ($roleData['name'] === 'admin' || $roleData['name'] === 'default_admin') {
                $lorRole = $roleData['id'];
                break;
            }
        }

        if ($lorRole) {
            $userClass = '\\LorPHP\\Models\\User';
            $user = $userClass::findByEmail($lorEmail);
            if (!$user) {
                $user = new $userClass();
                $user->id = $uuidGen();
                $user->name = $lorName;
                $user->email = $lorEmail;
                $user->is_active = true;
                $user->password = \LorPHP\Controllers\AuthController::hashPassword($lorPassword);
            }
            // Always set role and organization to ensure correct linkage
            $user->role_id = $lorRole;
            // Always assign the correct org UUID, even if user exists
            $user->organization_id = $org->id;
            error_log('[RoleSeeder] Assigning org to user: ' . print_r(['user_email' => $user->email, 'org_id' => $org->id, 'org_id_type' => gettype($org->id)], true));
            $user->save();
        }
    }
}
