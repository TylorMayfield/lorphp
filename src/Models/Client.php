<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;
use LorPHP\Core\OrganizationScoped;
use LorPHP\Core\Traits\Auditable;

class Client extends Model {
    use OrganizationScoped, Auditable;
    
    protected $isAuditable = true;
    
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
                'email' => true
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

    /**
     * Get all packages assigned to this client
     * @return array
     */
    public function getPackages(): array {
        try {
            $db = Database::getInstance();
            $sql = "SELECT p.* FROM packages p 
                   JOIN client_packages cp ON p.id = cp.package_id 
                   WHERE cp.client_id = ?";
            $stmt = $db->query($sql, [$this->id]);
            
            $packages = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $package = new Package();
                foreach ($row as $key => $value) {
                    $package->__set($key, $value);
                }
                $packages[] = $package;
            }
            return $packages;
        } catch (\Exception $e) {
            error_log("Error getting client packages: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if this client has a specific package
     * @param string $packageId
     * @return bool
     */
    public function hasPackage(string $packageId): bool {
        try {
            $db = Database::getInstance();
            $stmt = $db->query(
                "SELECT 1 FROM client_packages WHERE client_id = ? AND package_id = ?",
                [$this->id, $packageId]
            );
            return (bool)$stmt->fetch();
        } catch (\Exception $e) {
            error_log("Error checking client package: " . $e->getMessage());
            return false;
        }
    }

    public function addPackage($packageId) {
        try {
            if ($this->hasPackage($packageId)) {
                return true;
            }

            $db = Database::getInstance();
            return $db->insert('client_packages', [
                'client_id' => $this->id,
                'package_id' => $packageId,
                'assigned_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            error_log("Error adding package to client: " . $e->getMessage());
            return false;
        }
    }

    public function removePackage($packageId) {
        try {
            $db = Database::getInstance();
            return $db->delete('client_packages', [
                'client_id' => $this->id,
                'package_id' => $packageId
            ]);
        } catch (\Exception $e) {
            error_log("Error removing package from client: " . $e->getMessage());
            return false;
        }
    }
}
