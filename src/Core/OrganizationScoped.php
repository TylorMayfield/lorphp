<?php
namespace LorPHP\Core;

trait OrganizationScoped {
    /**
     * Apply organization scope to any query
     * @param array $conditions
     * @return array
     */
    protected function scopeToOrganization(array $conditions = []): array {
        $user = Application::getInstance()->getState('user');
        if (!$user || !$user->organization_id) {
            return [];
        }
        return array_merge(['organization_id' => $user->organization_id], $conditions); // UUID
    }
    
    /**
     * Override findById to enforce organization scope
     * @param int $id
     * @return static|null
     */
    public static function findById($id) {
        $instance = new static();
        $user = Application::getInstance()->getState('user');
        
        if (!$user || !$user->organization_id) {
            return null;
        }
        
        $data = $instance->db()->findOne(
            $instance->table, 
            ['id' => $id, 'organization_id' => $user->organization_id] // UUID
        );
        
        if (!$data) {
            return null;
        }
        
        foreach ($data as $key => $value) {
            $instance->$key = $value;
        }
        
        return $instance;
    }
    
    /**
     * Override save to enforce organization scope
     * @return bool
     */
    public function save(): bool {
        $user = Application::getInstance()->getState('user');
        if (!$user || !$user->organization_id) {
            return false;
        }
        
        // Ensure organization_id is set
        $this->organization_id = $user->organization_id;
        
        return parent::save();
    }
}
