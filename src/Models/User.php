<?php
namespace LorPHP\Models;

use LorPHP\Core\Model;
use LorPHP\Core\Database;

class User extends Model {
    protected $table = 'users';
    protected $useUuid = true;
    protected $timestamps = true;
    
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
     * Set the user's password, hashing it before storage.
     *
     * @param string $password The plain text password.
     * @return void
     */
    public function setPassword(string $password): void {
        // Hash the password using PHP's default hashing algorithm (currently bcrypt)
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Get the organization this user belongs to
     * @return Organization|null
     */
    public function getOrganization(): ?Organization {
        error_log("[User Debug] Getting organization");
        error_log("[User Debug] Current attributes: " . print_r($this->attributes, true));
        $org = $this->loadRelation('organization', Organization::class, 'organization_id');
        error_log("[User Debug] Loaded organization: " . ($org ? "Found" : "Not found"));
        return $org;
    }

    /**
     * Get the user's organization clients
     * @param array $conditions Optional conditions to filter clients
     * @return array
     */
    public function getOrganizationClients($conditions = []) {
        $org = $this->getOrganization();
        return $org ? $org->getClients($conditions) : [];
    }

    /**
     * Base64URL encode
     * 
     * @param string $data
     * @return string
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64URL decode
     * 
     * @param string $data
     * @return string
     */
    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    /**
     * Authenticate user with email and password
     * 
     * @param string $email
     * @param string $password
     * @return string|false JWT token if successful, false otherwise
     */
    public function authenticate(string $email, string $password): ?string {
        try {
            $db = Database::getInstance();
            
            $sql = "SELECT * FROM {$this->table} WHERE email = ?";
            $stmt = $db->query($sql, [$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user) {
                return null;
            }
            
            if (password_verify($password, $user['password'])) {
                foreach ($user as $key => $value) {
                    $this->$key = $value;
                }
                return $this->generateJWT();
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
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
     * @return array|false False if invalid, payload if valid
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

    public function save(): bool {
        try {
            $db = Database::getInstance();
            
            // Hash the password if it's not already hashed
            if (!$this->password) {
                return false;
            }
            
            // Generate UUID for new users
            if (!isset($this->id)) {
                $this->id = sprintf(
                    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                    mt_rand(0, 0xffff),
                    mt_rand(0, 0x0fff) | 0x4000,
                    mt_rand(0, 0x3fff) | 0x8000,
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                );
            }
            
            $data = [
                'id' => $this->id,
                'organization_id' => $this->organization_id,
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
            ];
            
            // Insert new user
            if (!isset($this->created_at)) {
                return $db->insert($this->table, $data) !== false;
            }
            
            // Update existing user
            return $db->update($this->table, $data, ['id' => $this->id]);
        } catch (\Exception $e) {
            error_log("Error saving user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate a GUID/UUID v4
     * @return string
     */
    public static function generateGUID(): string {
        if (function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        }
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
