<?php
namespace LorPHP\Core\Traits;

use LorPHP\Core\Database;

trait RateLimiter {
    protected function getRateLimit(string $key, int $maxAttempts = 60, int $decayMinutes = 1): array {
        $db = Database::getInstance();
        
        // Clean up old entries first
        $this->cleanupRateLimits();
        
        // Get current timestamp
        $now = time();
        $expiresAt = $now + ($decayMinutes * 60);
        
        // Check if we have an existing rate limit record
        $stmt = $db->query(
            "SELECT * FROM rate_limits WHERE `key` = ? AND expires_at > ?",
            [$key, $now]
        );
        $rateLimit = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$rateLimit) {
            // Create new rate limit record
            $db->insert('rate_limits', [
                'key' => $key,
                'attempts' => 1,
                'expires_at' => $expiresAt
            ]);
            
            return [
                'attempts' => 1,
                'remaining' => $maxAttempts - 1,
                'success' => true
            ];
        }
        
        // Increment attempts
        $attempts = $rateLimit['attempts'] + 1;
        $db->query(
            "UPDATE rate_limits SET attempts = ? WHERE `key` = ?",
            [$attempts, $key]
        );
        
        return [
            'attempts' => $attempts,
            'remaining' => max(0, $maxAttempts - $attempts),
            'success' => $attempts <= $maxAttempts
        ];
    }
    
    protected function cleanupRateLimits(): void {
        $db = Database::getInstance();
        $db->query("DELETE FROM rate_limits WHERE expires_at < ?", [time()]);
    }
    
    protected function tooManyAttempts(string $key, int $maxAttempts = 60, int $decayMinutes = 1): bool {
        $rateLimit = $this->getRateLimit($key, $maxAttempts, $decayMinutes);
        return !$rateLimit['success'];
    }
}
