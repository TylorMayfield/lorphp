<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client = new Client();
            $client->organization_id = $this->user->organization_id; // UUID
            $client->name = $_POST['name'] ?? '';
            $client->email = $_POST['email'] ?? '';
            $client->phone = $_POST['phone'] ?? '';
            $client->notes = $_POST['notes'] ?? '';
            
            if ($client->save()) {
                $this->withSuccess('Client created successfully');
                return $this->redirectTo('/clients');
            }
            
            return $this->view('clients/create', [
                'title' => 'New Client',
                'error' => 'Failed to create client'
            ]);
        }
        
        return $this->view('clients/create', [
            'title' => 'New Client'
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
        $client = Client::findOne([
            'id' => $id,
            'organization_id' => $this->user->organization_id
        ]);

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
}
