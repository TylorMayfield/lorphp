<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;

class Organization extends Model {
    protected $table = 'organizations';
    protected $useUuid = true;
    protected $timestamps = true;
    
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
    
    public function getClients($conditions = []) {
        try {
            $db = Database::getInstance();
            $params = [$this->id];  // Start with organization_id parameter
            $sql = "SELECT * FROM clients WHERE organization_id = ?";

            if (!empty($conditions['search'])) {
                $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
                $searchTerm = "%" . $conditions['search'] . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if (!empty($conditions['status'])) {
                $sql .= " AND status = ?";
                $params[] = $conditions['status'];
            }

            if (!empty($conditions['limit'])) {
                $sql .= " LIMIT " . (int)$conditions['limit'];
            }

            $stmt = $db->query($sql, $params);
            
            $clients = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $client = new Client();
                foreach ($row as $key => $value) {
                    $client->__set($key, $value);  // Use __set to ensure proper attribute setting
                }
                $clients[] = $client;
            }
            return $clients;
        } catch (\Exception $e) {
            return [];
        }
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
            return [];
        }
    }
    
    /**
     * Get all organizations in the system
     * @return array
     */
    public static function all(): array {
        $db = Database::getInstance();
        $results = $db->query("SELECT * FROM organizations ORDER BY created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(function($data) {
            $org = new self();
            foreach ($data as $key => $value) {
                $org->$key = $value;
            }
            return $org;
        }, $results);
    }
}
