<?php

namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Models\User;

class SettingsController extends Controller {
    public function __construct() {
        parent::__construct();
        // Ensure user is authenticated
        if (!$this->app->getState('user')) {
            $this->redirectTo('/login');
            exit;
        }
    }

    public function index() {
        $user = $this->app->getState('user');
        
        return $this->view('settings/index', [
            'title' => 'Account Settings',
            'user' => $user
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectTo('/settings');
        }

        $user = $this->app->getState('user');
        $data = $_POST;
        $errors = [];

        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif ($data['email'] !== $user->email) {
            // Check if email is already taken
            $existingUser = User::findByEmail($data['email']);
            if ($existingUser && $existingUser->id !== $user->id) {
                $errors['email'] = 'Email is already taken';
            }
        }

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }

        // Validate password if provided
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            } elseif ($data['password'] !== $data['password_confirm']) {
                $errors['password'] = 'Passwords do not match';
            }
        }

        if (!empty($errors)) {
            return $this->view('settings/index', [
                'title' => 'Account Settings',
                'user' => $user,
                'errors' => $errors,
                'old' => $data
            ]);
        }

        // Update user
        $user->name = $data['name'];
        $user->email = $data['email'];
          if (!empty($data['password'])) {
            $user->setPassword($data['password']); // This uses the proper hashing method
        }

        if ($user->save()) {
            // Update session state
            $this->app->setState('user', $user);
            $this->app->setState('flash_success', 'Profile updated successfully');
        } else {
            $this->app->setState('flash_error', 'Failed to update profile');
        }

        return $this->redirectTo('/settings');
    }
}
