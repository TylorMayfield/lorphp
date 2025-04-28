<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;

class User extends Model {
    // Table name for database operations
    protected $table = 'users';
    
    // User properties
    public $id;
    public $organization_id;
    public $name;
    public $email;
    public $password;
    public $created_at;
    
    private $organization = null;
    
    protected $schema = [
        'name' => [
            'type' => 'string',
            'rules' => [
                'required' => true,
                'min' => 3,
                'max' => 50
            ]
        ],
        'email' => [
            'type' => 'string',
            'rules' => [
                'required' => true,
                'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ]
        ]
    ];
    
    /**
     * Save user to database
     * 
     * @return bool Whether the save was successful
     */
    public function save(): bool {
        try {
            $db = Database::getInstance();
            
            // Hash the password if it's not already hashed
            if (!$this->password) {
                return false;
            }
            
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
            ];
            
            // Insert new user
            if (!isset($this->id)) {
                $this->id = $db->insert($this->table, $data);
                return $this->id > 0;
            }
            
            // Update existing user
            return true;
        } catch (\Exception $e) {
            error_log("Error saving user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set password with hashing
     * 
     * @param string $password Plain text password
     * @return void
     */
    public function setPassword(string $password): void {
        // Hash the password for security
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Generate a JWT token for the user
     * 
     * @return string The JWT token
     */
    private function generateJWT(): string {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'sub' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 72) // 72 hours
        ];

        $secret = $_ENV['JWT_SECRET'] ?? 'your-256-bit-secret';

        $base64Header = $this->base64UrlEncode(json_encode($header));
        $base64Payload = $this->base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', 
            $base64Header . "." . $base64Payload, 
            $secret, 
            true
        );
        
        $base64Signature = $this->base64UrlEncode($signature);
        
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    /**
     * Validate a JWT token
     * 
     * @param string $token The JWT token to validate
     * @return bool|array False if invalid, user data if valid
     */
    public static function validateJWT(string $token) {
        $secret = $_ENV['JWT_SECRET'] ?? 'your-256-bit-secret';
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }

        [$base64Header, $base64Payload, $signature] = $parts;
        
        $signature = self::base64UrlDecode($signature);
        $validSignature = hash_hmac('sha256', 
            $base64Header . "." . $base64Payload, 
            $secret, 
            true
        );
        
        if (!hash_equals($signature, $validSignature)) {
            return false;
        }
        
        $payload = json_decode(self::base64UrlDecode($base64Payload), true);
        
        if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }

    /**
     * Base64Url encode
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64Url decode
     */
    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Authenticate user with email and password
     * 
     * @param string $email User email
     * @param string $password Plain text password to verify
     * @return bool Whether authentication was successful
     */
    public function authenticate(string $email, string $password): ?string {
        try {
            $db = Database::getInstance();
            
            // Find the user by email
            $sql = "SELECT * FROM {$this->table} WHERE email = ?";
            $stmt = $db->query($sql, [$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user) {
                return null; // User not found
            }
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set properties from database
                $this->id = $user['id'];
                $this->name = $user['name'];
                $this->email = $user['email'];
                $this->created_at = $user['created_at'];
                
                // Generate and return JWT token
                return $this->generateJWT();
            }
            
            return null; // Invalid password
        } catch (\Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a user by their ID
     * @param int $id
     * @return User|null
     */
    public static function findById($id) {
        $db = Database::getInstance();
        $userData = $db->findOne('users', ['id' => $id]);
        
        if (!$userData) {
            return null;
        }
        
        $user = new self();
        foreach ($userData as $key => $value) {
            $user->$key = $value;
        }
        
        // Load the organization relationship
        if ($user->organization_id) {
            $user->loadOrganization();
        }
        
        return $user;
    }
    
    /**
     * Load the organization relationship
     */
    private function loadOrganization() {
        if ($this->organization === null && $this->organization_id) {
            $this->organization = Organization::findById($this->organization_id);
        }
        return $this->organization;
    }
    
    /**
     * Get the user's organization clients
     * @param array $conditions Optional conditions to filter clients
     * @return array
     */
    public function getOrganizationClients($conditions = []) {
        if (!$this->organization) {
            $this->loadOrganization();
        }
        return $this->organization ? $this->organization->getClients($conditions) : [];
    }
}
