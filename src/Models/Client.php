<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;

class Client extends Model {
    protected $table = 'clients';
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
        ],
        'email' => [
            'type' => 'string',
            'rules' => [
                'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ]
        ],
        'phone' => [
            'type' => 'string',
            'rules' => [
                'pattern' => '/^[0-9+\-\(\)\s]*$/'
            ]
        ],
        'status' => [
            'type' => 'string',
            'rules' => [
                'required' => true
            ]
        ]
    ];

    public function getContacts() {
        try {
            $db = Database::getInstance();
            $sql = "SELECT c.*, u.name as user_name 
                   FROM contacts c 
                   LEFT JOIN users u ON c.user_id = u.id 
                   WHERE c.client_id = ? 
                   ORDER BY c.contact_date DESC";
            $stmt = $db->query($sql, [$this->id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting contacts: " . $e->getMessage());
            return [];
        }
    }

    public function addContact($userId, $type, $notes = '') {
        try {
            $db = Database::getInstance();
            $data = [
                'client_id' => $this->id,
                'user_id' => $userId,
                'type' => $type,
                'notes' => $notes,
                'contact_date' => date('Y-m-d H:i:s')
            ];
            
            if ($db->insert('contacts', $data)) {
                // Update last_contact_date
                $this->last_contact_date = date('Y-m-d H:i:s');
                return $db->update('clients', 
                    ['last_contact_date' => $this->last_contact_date],
                    ['id' => $this->id]
                );
            }
            return false;
        } catch (\Exception $e) {
            error_log("Error adding contact: " . $e->getMessage());
            return false;
        }
    }
}
