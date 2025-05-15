<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Core\Database;
use LorPHP\Core\FormBuilder;
use LorPHP\Models\Client;

class ClientController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $clients = $this->user->getOrganizationClients([
            'search' => $search,
            'status' => $status
        ]);
        
        return $this->view('clients/index', [
            'title' => 'Clients - CRM Dashboard',
            'clients' => $clients,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function create() {
        $form = FormBuilder::createClientForm();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($form->validate()) {
                $client = new Client();
                $client->organization_id = $this->user->organization_id;
                $client->name = $_POST['name'];
                $client->email = $_POST['email'] ?? '';
                $client->phone = $_POST['phone'] ?? '';
                $client->notes = $_POST['notes'] ?? '';
                
                try {
                    if ($client->save()) {
                        $this->withSuccess('Client created successfully');
                        return $this->redirectTo('/clients');
                    }
                } catch (\Exception $e) {
                    $form->addError('form', 'Failed to create client. Please try again.');
                }
            }
        }
        
        return $this->view('clients/create', [
            'title' => 'New Client',
            'form' => $form
        ]);
    }

    public function addContact($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectTo("/clients/{$id}");
        }
        
        $client = $this->getClientById($id);
        if (!$client) {
            return $this->redirectTo('/clients');
        }
        
        $type = $_POST['type'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        if ($client->addContact($this->user->id, $type, $notes)) {
            $this->withSuccess('Contact added successfully');
        } else {
            $this->withError('Failed to add contact');
        }
        
        return $this->redirectTo("/clients/{$id}");
    }

    public function show($id) {
        $client = $this->getClientById($id);
        
        if (!$client) {
            $this->withError('Client not found');
            return $this->redirectTo('/clients');
        }

        $contacts = $client->getContacts();
        
        return $this->view('clients/view', [
            'title' => $client->name . ' - Client Details',
            'client' => $client,
            'contacts' => $contacts
        ]);
    }
    
    public function edit($id) {
        $client = $this->getClientById($id);
        
        if (!$client) {
            $this->withError('Client not found');
            return $this->redirectTo('/clients');
        }
        
        return $this->view('clients/edit', [
            'title' => 'Edit ' . $client->name,
            'client' => $client
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectTo("/clients/{$id}");
        }

        $client = $this->getClientById($id);
        if (!$client) {
            $this->withError('Client not found');
            return $this->redirectTo('/clients');
        }

        // Update client fields
        $client->name = $_POST['name'] ?? $client->name;
        $client->email = $_POST['email'] ?? $client->email;
        $client->phone = $_POST['phone'] ?? $client->phone;
        $client->notes = $_POST['notes'] ?? $client->notes;

        if (!$client->save()) {
            $errors = $client->getErrors();
            $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Failed to update client';
            $this->withError($errorMessage);
            return $this->redirectTo("/clients/{$id}/edit");
        }

        $this->withSuccess('Client updated successfully');
        return $this->redirectTo("/clients/{$id}");
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectTo("/clients/{$id}");
        }

        $client = $this->getClientById($id);
        if (!$client) {
            $this->withError('Client not found');
            return $this->redirectTo('/clients');
        }

        try {
            $db = Database::getInstance();
            if ($db->delete('clients', ['id' => $client->id])) {
                $this->withSuccess('Client deleted successfully');
                return $this->redirectTo('/clients');
            }
        } catch (\Exception $e) {
            error_log("Error deleting client: " . $e->getMessage());
        }

        $this->withError('Failed to delete client');
        return $this->redirectTo("/clients/{$id}");
    }

    private function getClientById($id) {
        $row = Client::findOne([
            'id' => $id,
            'organization_id' => $this->user->organization_id
        ]);
        
        if (!$row) {
            return null;
        }

        $client = new Client();
        foreach ($row as $key => $value) {
            $client->__set($key, $value);
        }
        return $client;
    }
}
