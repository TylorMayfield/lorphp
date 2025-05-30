<?php
// Project routes for LorPHP
namespace LorPHP;

use LorPHP\Core\Application;

// Get the router instance from the application
$router = Application::getInstance()->router;

// Define routes
$router->get('/', 'HomeController@index');
$router->get('/offline', function() {
    return (new \LorPHP\Core\View())->render('offline');
});
$router->get('/test', function() {
    return (new \LorPHP\Core\View())->render('test');
});

// Auth routes
$router->get('/login', 'LoginController@index');
$router->post('/login', 'LoginController@index');
$router->get('/register', 'RegisterController@index');
$router->post('/register', 'RegisterController@index');
$router->get('/dashboard', 'DashboardController@index');
$router->post('/logout', 'AuthController@logout');

// Grafana Metrics Routes
$router->post('/metrics/query', 'MetricsController@query');
$router->get('/metrics/search', 'MetricsController@search');
$router->get('/metrics/health', 'MetricsController@health');

// Settings Routes
$router->get('/settings', 'SettingsController@index');
$router->post('/settings/update', 'SettingsController@update');

// Admin Routes
$router->get('/admin', 'AdminController@index');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/organizations', 'AdminController@organizations');
$router->post('/admin/users/{id}/toggle-status', 'AdminController@toggleUserStatus');

// CRM Routes
$router->get('/clients', 'ClientController@index');
$router->get('/clients/create', 'ClientController@create');
$router->post('/clients', 'ClientController@create');
$router->get('/clients/{id}', 'ClientController@show');
$router->get('/clients/{id}/edit', 'ClientController@edit');
$router->post('/clients/{id}/update', 'ClientController@update');
$router->post('/clients/{id}/delete', 'ClientController@delete');
$router->post('/clients/{id}/contacts', 'ClientController@addContact');

// Package Routes
$router->get('/packages', 'PackageController@index');
$router->get('/packages/create', 'PackageController@create');
$router->post('/packages', 'PackageController@create');
$router->get('/packages/{id}', 'PackageController@show');
$router->get('/packages/{id}/edit', 'PackageController@edit');
$router->post('/packages/{id}/update', 'PackageController@update');
$router->post('/packages/{id}/delete', 'PackageController@delete');
$router->post('/packages/{id}/assign', 'PackageController@assignToClient');
$router->post('/packages/{id}/clients/{clientId}/remove', 'PackageController@removeFromClient');
