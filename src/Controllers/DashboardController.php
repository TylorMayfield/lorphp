<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Services\DashboardService;

class DashboardController extends Controller {
    protected $dashboardService;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->dashboardService = new DashboardService();
    }    
    
    public function index() {
        // Redirect admin users to admin dashboard
        if ($this->user && $this->user->getRole() === 'admin') {
            return $this->redirectTo('/admin');
        }

        // Use $this->user which is set in the parent Controller class
        $stats = $this->dashboardService->getStats($this->user);
        $recentClients = $this->dashboardService->getRecentClients($this->user);
        $recentContacts = $this->dashboardService->getRecentContacts($this->user);
        $recentPackages = $this->dashboardService->getRecentPackages($this->user);
        
        return $this->view('dashboard', [
            'title' => 'Dashboard - LorPHP',
            'stats' => $stats,
            'recentClients' => $recentClients,
            'recentContacts' => $recentContacts,
            'recentPackages' => $recentPackages
        ]);
    }
}
