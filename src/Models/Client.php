<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;

class Client extends Model {
    protected $table = 'clients';
    
    public $id;
    public $organization_id;
    public $name;
    public $email;
    public $phone;
    public $status;
    public $notes;
    public $last_contact_date;
    public $created_at;
    
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
        ]
    ];
    
    public function save(): bool {
        try {
            $db = Database::getInstance();
            
            $data = [
                'organization_id' => $this->organization_id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => $this->status ?? 'active',
                'notes' => $this->notes,
                'last_contact_date' => $this->last_contact_date
            ];
            
            if (!isset($this->id)) {
                $this->id = $db->insert($this->table, $data);
                return $this->id > 0;
            }
            
            return $db->update($this->table, $data, ['id' => $this->id]);
        } catch (\Exception $e) {
            error_log("Error saving client: " . $e->getMessage());
            return false;
        }
    }
    
    public function getContacts() {
        try {
            $db = Database::getInstance();
            $sql = "SELECT c.*, u.name as user_name FROM contacts c 
                   JOIN users u ON c.user_id = u.id 
                   WHERE c.client_id = ? 
                   ORDER BY c.contact_date DESC";
            $stmt = $db->query($sql, [$this->id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting contacts: " . $e->getMessage());
            return [];
        }
    }
    
    public function addContact($userId, $type, $notes) {
        try {
            $db = Database::getInstance();
            $data = [
                'client_id' => $this->id,
                'user_id' => $userId,
                'type' => $type,
                'notes' => $notes
            ];
            
            $contactId = $db->insert('contacts', $data);
            
            // Update last contact date
            $this->last_contact_date = date('Y-m-d H:i:s');
            $db->update($this->table, 
                ['last_contact_date' => $this->last_contact_date],
                ['id' => $this->id]
            );
            
            return $contactId > 0;
        } catch (\Exception $e) {
            error_log("Error adding contact: " . $e->getMessage());
            return false;
        }
    }
}
