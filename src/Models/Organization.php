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

            error_log("[Organization Debug] Running query: " . $sql . " with params: " . print_r($params, true));
            $stmt = $db->query($sql, $params);
            
            $clients = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $client = new Client();
                foreach ($row as $key => $value) {
                    $client->__set($key, $value);  // Use __set to ensure proper attribute setting
                }
                $clients[] = $client;
            }
            error_log("[Organization Debug] Found " . count($clients) . " clients");
            return $clients;
        } catch (\Exception $e) {
            error_log("Error getting clients: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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
            error_log("Error getting users: " . $e->getMessage());
            return [];
        }
    }
}
