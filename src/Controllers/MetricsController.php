<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Services\DashboardService;
use LorPHP\Core\Database;

use LorPHP\Core\JsonView;

class MetricsController extends Controller {
    protected $dashboardService;
    protected $db;

    public function __construct() {
        parent::__construct();
        // Only require auth for query endpoint
        if ($_SERVER['REQUEST_URI'] === '/metrics/query') {
            $this->requireAuth();
        }
        $this->dashboardService = new DashboardService();
        $this->db = Database::getInstance();
    }

    /**
     * Grafana Simple JSON endpoint for time series data
     */
    public function query() {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $from = strtotime($requestData['range']['from']);
        $to = strtotime($requestData['range']['to']);
        
        $response = [];
        
        foreach ($requestData['targets'] as $target) {
            switch ($target['target']) {
                case 'active_clients':
                    $response[] = $this->getActiveClientsMetrics($from, $to);
                    break;
                case 'total_users':
                    $response[] = $this->getTotalUsersMetrics($from, $to);
                    break;
                case 'recent_contacts':
                    $response[] = $this->getRecentContactsMetrics($from, $to);
                    break;
            }
        }
        
        JsonView::render($response);
    }

    /**
     * Grafana health check endpoint
     */
    public function health() {
        JsonView::render(['status' => 'success']);
    }

    /**
     * Return available metrics
     */
    public function search() {
        JsonView::render([
            'active_clients',
            'total_users',
            'recent_contacts'
        ]);
    }

    private function getActiveClientsMetrics($from, $to) {
        $stats = $this->dashboardService->getStats($this->user);
        return [
            'target' => 'active_clients',
            'datapoints' => [
                [$stats['activeClients'], time() * 1000]
            ]
        ];
    }

    private function getTotalUsersMetrics($from, $to) {
        $stats = $this->dashboardService->getStats($this->user);
        return [
            'target' => 'total_users',
            'datapoints' => [
                [$stats['organizationUsers'], time() * 1000]
            ]
        ];
    }

    private function getRecentContactsMetrics($from, $to) {
        $stats = $this->dashboardService->getStats($this->user);
        return [
            'target' => 'recent_contacts',
            'datapoints' => [
                [$stats['recentContacts'], time() * 1000]
            ]
        ];
    }
}
