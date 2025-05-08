<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;
use LorPHP\Core\OrganizationScoped;

class Package extends Model {
    use OrganizationScoped;
    
    protected $table = 'packages';
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
        'description' => [
            'type' => 'string'
        ],
        'price' => [
            'type' => 'decimal',
            'rules' => [
                'required' => true,
                'min' => 0
            ]
        ]
    ];

    /**
     * Get all clients assigned to this package
     * @return array
     */
    public function getClients(): array {
        try {
            $db = Database::getInstance();
            $sql = "SELECT c.* FROM clients c 
                   JOIN client_packages cp ON c.id = cp.client_id 
                   WHERE cp.package_id = ?";
            $stmt = $db->query($sql, [$this->id]);
            
            $clients = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $client = new Client();
                foreach ($row as $key => $value) {
                    $client->__set($key, $value);
                }
                $clients[] = $client;
            }
            return $clients;
        } catch (\Exception $e) {
            error_log("Error getting package clients: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Assign this package to a client
     * @param string $clientId
     * @return bool
     */
    public function assignToClient(string $clientId): bool {
        try {
            $db = Database::getInstance();
            return $db->insert('client_packages', [
                'client_id' => $clientId,
                'package_id' => $this->id,
                'assigned_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            error_log("Error assigning package to client: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove this package from a client
     * @param string $clientId
     * @return bool
     */
    public function removeFromClient(string $clientId): bool {
        try {
            $db = Database::getInstance();
            return $db->delete('client_packages', [
                'client_id' => $clientId,
                'package_id' => $this->id
            ]);
        } catch (\Exception $e) {
            error_log("Error removing package from client: " . $e->getMessage());
            return false;
        }
    }
}
