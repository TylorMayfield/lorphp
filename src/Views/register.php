<?php
/**
 * Registration page view
 */

// Set the layout
$this->setLayout('base');

// Render the registration form as a string
$formContent = $this->renderPartialToString('forms/register-form', [
    'values' => $value ?? [],
    'error' => $error ?? null
]);

// Use the auth-container partial for consistent layout
$this->partial('components/auth-container', [
    'title' => 'Create your account',
    'subtitle' => 'Already have an account?',
    'linkText' => 'Sign in',
    'linkUrl' => '/login',
    'error' => $error ?? null,
    'content' => $formContent
]);
