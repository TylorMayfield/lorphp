<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Core\RoleMiddleware;
use LorPHP\Models\User;
use LorPHP\Models\Organization;

class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireAdmin();
    }

    private function requireAdmin() {
        $user = $this->app->getState('user');
        if (!$user) {
            error_log('Admin access denied: No user in state');
            $this->redirectTo('/login');
            exit;
        }

        $role = $user->getRole();
        error_log('Admin access attempt - User: ' . $user->email . ' Role: ' . ($role ?? 'none'));
        
        // Ensure user is admin
        if (!$user->hasRole('admin')) {
            error_log('Admin access denied: Not an admin');
            $this->redirectTo('/dashboard');
            exit;
        }
    }

    public function index() {
        return $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'users' => User::all(),
            'organizations' => Organization::all()
        ]);
    }

    public function users() {
        return $this->view('admin/users', [
            'title' => 'Manage Users',
            'users' => User::all()
        ]);
    }

    public function organizations() {
        return $this->view('admin/organizations', [
            'title' => 'Manage Organizations',
            'organizations' => Organization::all()
        ]);
    }

    public function toggleUserStatus($id) {
        // User must be admin to toggle status
        $currentUser = $this->app->getState('user');
        if (!$currentUser || !$currentUser->hasRole('admin')) {
            $this->withError('Unauthorized: Admin access required');
            return $this->redirectTo('/dashboard');
        }
        
        $user = User::findById($id);
        if (!$user) {
            $this->withError('User not found');
            return $this->redirectTo('/admin/users');
        }

        // Don't allow disabling the admin account
        $adminConfig = $this->app->getConfig('admin.admin_account');
        if ($user->email === $adminConfig['email']) {
            $this->withError('Cannot disable the admin account');
            return $this->redirectTo('/admin/users');
        }

        $user->active = $user->active ? 0 : 1;
        if ($user->save()) {
            $this->withSuccess('User status updated successfully');
        } else {
            $this->withError('Failed to update user status');
        }

        return $this->redirectTo('/admin/users');
    }
}
