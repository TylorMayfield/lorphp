<?php
namespace LorPHP\Core;

use LorPHP\Core\Traits\RateLimiter;
use LorPHP\Core\Traits\RateLimitHeaders;

class RateLimitMiddleware {
    use RateLimiter, RateLimitHeaders;
    
    /**
     * Handle rate limiting for API and sensitive routes
     * 
     * @return bool|void Returns void if successful, false if rate limit exceeded
     */
    public static function handle() {
        $instance = new self();
        return $instance->handleRateLimit();
    }
    
    private function handleRateLimit() {
        $app = Application::getInstance();
        $request = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Get user if authenticated
        $user = $app->getState('user');
        $userId = $user ? $user->id : null;
        
        // Different rate limits for different endpoints
        $limits = $this->getRateLimits($request, $method);
        
        if (!$limits) {
            return; // No rate limiting for this route
        }
        
        // Create unique key based on IP and optionally user ID
        $key = $userId 
            ? "rate_limit:{$ip}:{$userId}:{$limits['key']}"
            : "rate_limit:{$ip}:{$limits['key']}";
            
        $rateLimit = $this->getRateLimit($key, $limits['max_attempts'], $limits['decay_minutes']);
        $this->setRateLimitHeaders($rateLimit);
        
        if (!$rateLimit['success']) {
            header('HTTP/1.1 429 Too Many Requests');
            header('Retry-After: ' . $limits['decay_minutes'] * 60);
            echo json_encode([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => $limits['decay_minutes'] * 60
            ]);
            exit;
        }
    }
    
    private function getRateLimits(string $request, string $method): ?array {
        // Define rate limits for different routes
        $patterns = [
            // Login attempts - stricter limits
            '#^/login$#' => [
                'POST' => [
                    'key' => 'login',
                    'max_attempts' => 5,
                    'decay_minutes' => 15
                ]
            ],
            // Registration - prevent spam
            '#^/register$#' => [
                'POST' => [
                    'key' => 'register',
                    'max_attempts' => 3,
                    'decay_minutes' => 60
                ]
            ],
            // API endpoints - general rate limiting
            '#^/api/#' => [
                'GET' => [
                    'key' => 'api_get',
                    'max_attempts' => 60,
                    'decay_minutes' => 1
                ],
                'POST' => [
                    'key' => 'api_post',
                    'max_attempts' => 30,
                    'decay_minutes' => 1
                ]
            ],
            // Metrics endpoint - prevent abuse
            '#^/metrics/#' => [
                'GET' => [
                    'key' => 'metrics',
                    'max_attempts' => 30,
                    'decay_minutes' => 1
                ]
            ],
            // Client data endpoints
            '#^/clients/#' => [
                'GET' => [
                    'key' => 'clients_get',
                    'max_attempts' => 60,
                    'decay_minutes' => 1
                ],
                'POST' => [
                    'key' => 'clients_post',
                    'max_attempts' => 30,
                    'decay_minutes' => 1
                ]
            ]
        ];
        
        foreach ($patterns as $pattern => $methods) {
            if (preg_match($pattern, $request) && isset($methods[$method])) {
                return $methods[$method];
            }
        }
        
        return null;
    }
}
