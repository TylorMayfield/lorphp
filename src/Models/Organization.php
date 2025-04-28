<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;

class Organization extends Model {
    protected $table = 'organizations';
    
    public $id;
    public $name;
    public $created_at;
    
    protected $schema = [
        'name' => [
            'type' => 'string',
            'rules' => [
                'required' => true,
                'min' => 2,
                'max' => 100
            ]
        ]
    ];
    
    public function save(): bool {
        try {
            $db = Database::getInstance();
            
            $data = [
                'name' => $this->name
            ];
            
            if (!isset($this->id)) {
                $this->id = $db->insert($this->table, $data);
                return $this->id > 0;
            }
            
            return $db->update($this->table, $data, ['id' => $this->id]);
        } catch (\Exception $e) {
            error_log("Error saving organization: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find an organization by ID
     * @param int $id
     * @return Organization|null
     */
    public static function findById($id) {
        $db = Database::getInstance();
        $orgData = $db->findOne('organizations', ['id' => $id]);
        
        if (!$orgData) {
            return null;
        }
        
        $org = new self();
        foreach ($orgData as $key => $value) {
            $org->$key = $value;
        }
        
        return $org;
    }
    
    /**
     * Get organization's clients with optional conditions
     * @param array $conditions
     * @return array
     */
    public function getClients($conditions = []) {
        $db = Database::getInstance();
        $allConditions = array_merge(['organization_id' => $this->id], $conditions);
        return $db->getAll('clients', $allConditions);
    }
    
    public function getUsers() {
        try {
            $db = Database::getInstance();
            $sql = "SELECT * FROM users WHERE organization_id = ?";
            $stmt = $db->query($sql, [$this->id]);
            
            $users = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $user = new User();
                foreach ($row as $key => $value) {
                    $user->$key = $value;
                }
                $users[] = $user;
            }
            
            return $users;
        } catch (\Exception $e) {
            error_log("Error getting users: " . $e->getMessage());
            return [];
        }
    }
}
