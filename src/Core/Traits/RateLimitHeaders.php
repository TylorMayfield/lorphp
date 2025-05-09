<?php
namespace LorPHP\Core\Traits;

trait RateLimitHeaders {
    protected function setRateLimitHeaders(array $rateLimit): void {
        header('X-RateLimit-Limit: ' . ($rateLimit['attempts'] + $rateLimit['remaining']));
        header('X-RateLimit-Remaining: ' . $rateLimit['remaining']);
        
        if (!$rateLimit['success']) {
            header('X-RateLimit-Reset: ' . (time() + 60)); // Reset after one minute
        }
    }
}
