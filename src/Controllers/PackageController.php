<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Core\Database;
use LorPHP\Core\FormBuilder;
use LorPHP\Models\Package;
use LorPHP\Models\Client;

class PackageController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    public function index() {
        $packages = $this->user->getOrganizationPackages();
        
        return $this->view('packages/index', [
            'title' => 'Packages',
            'packages' => $packages
        ]);
    }

    public function create() {
        $form = FormBuilder::createPackageForm();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($form->validate()) {
                $package = new Package();
                $package->organization_id = $this->user->organization_id;
                $package->name = $_POST['name'];
                $package->description = $_POST['description'] ?? '';
                $package->price = floatval($_POST['price']);
                
                if ($package->save()) {
                    $this->withSuccess('Package created successfully');
                    return $this->redirectTo('/packages');
                }
                
                $form->addError('form', 'Failed to create package');
            }
        }
        
        return $this->view('packages/create', [
            'title' => 'New Package',
            'form' => $form
        ]);
    }

    public function show($id) {
        $package = $this->getPackageById($id);
        if (!$package) {
            $this->withError('Package not found');
            return $this->redirectTo('/packages');
        }

        // Get all assigned clients for this package
        $clients = $package->getClients();

        // Get all clients that don't have this package assigned
        $allClients = $this->user->getOrganizationClients();
        $assignedClientIds = array_map(function($client) {
            return $client->id;
        }, $clients);

        $availableClients = array_filter($allClients, function($client) use ($assignedClientIds) {
            return !in_array($client->id, $assignedClientIds);
        });
        
        return $this->view('packages/view', [
            'title' => $package->name,
            'package' => $package,
            'clients' => $clients,
            'availableClients' => $availableClients
        ]);
    }

    public function edit($id) {
        $package = $this->getPackageById($id);
        if (!$package) {
            $this->withError('Package not found');
            return $this->redirectTo('/packages');
        }
        
        return $this->view('packages/edit', [
            'title' => 'Edit ' . $package->name,
            'package' => $package
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectTo("/packages/{$id}");
        }

        $package = $this->getPackageById($id);
        if (!$package) {
            $this->withError('Package not found');
            return $this->redirectTo('/packages');
        }

        $package->name = $_POST['name'] ?? $package->name;
        $package->description = $_POST['description'] ?? $package->description;
        $package->price = $_POST['price'] ?? $package->price;

        if ($package->save()) {
            $this->withSuccess('Package updated successfully');
            return $this->redirectTo("/packages/{$id}");
        }

        $this->withError('Failed to update package');
        return $this->redirectTo("/packages/{$id}/edit");
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectTo("/packages/{$id}");
        }

        $package = $this->getPackageById($id);
        if (!$package) {
            $this->withError('Package not found');
            return $this->redirectTo('/packages');
        }

        try {
            $db = Database::getInstance();
            // First delete from client_packages
            $db->delete('client_packages', ['package_id' => $package->id]);
            // Then delete the package
            if ($db->delete('packages', ['id' => $package->id])) {
                $this->withSuccess('Package deleted successfully');
                return $this->redirectTo('/packages');
            }
        } catch (\Exception $e) {
            error_log("Error deleting package: " . $e->getMessage());
        }

        $this->withError('Failed to delete package');
        return $this->redirectTo("/packages/{$id}");
    }

    public function assignToClient($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectTo("/packages/{$id}");
        }

        $package = $this->getPackageById($id);
        if (!$package) {
            $this->withError('Package not found');
            return $this->redirectTo('/packages');
        }

        $clientId = $_POST['client_id'] ?? '';
        if (!$clientId) {
            $this->withError('No client selected');
            return $this->redirectTo("/packages/{$id}");
        }

        // Verify client belongs to organization
        $client = Client::findOne([
            'id' => $clientId,
            'organization_id' => $this->user->organization_id
        ]);

        if (!$client) {
            $this->withError('Invalid client selected');
            return $this->redirectTo("/packages/{$id}");
        }

        if ($package->assignToClient($clientId)) {
            $this->withSuccess('Package assigned to client successfully');
        } else {
            $this->withError('Failed to assign package to client');
        }

        return $this->redirectTo("/packages/{$id}");
    }

    public function removeFromClient($id, $clientId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectTo("/packages/{$id}");
        }

        $package = $this->getPackageById($id);
        if (!$package) {
            $this->withError('Package not found');
            return $this->redirectTo('/packages');
        }

        // Verify client belongs to organization
        $client = Client::findOne([
            'id' => $clientId,
            'organization_id' => $this->user->organization_id
        ]);

        if (!$client) {
            $this->withError('Invalid client');
            return $this->redirectTo("/packages/{$id}");
        }

        if ($package->removeFromClient($clientId)) {
            $this->withSuccess('Package removed from client successfully');
        } else {
            $this->withError('Failed to remove package from client');
        }

        return $this->redirectTo("/packages/{$id}");
    }
    
    private function getPackageById($id) {
        $row = Package::findOne([
            'id' => $id,
            'organization_id' => $this->user->organization_id
        ]);
        
        if (!$row) {
            return null;
        }

        $package = new Package();
        foreach ($row as $key => $value) {
            $package->__set($key, $value);
        }
        return $package;
    }
}
