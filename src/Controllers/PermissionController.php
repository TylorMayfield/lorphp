<?php

namespace LorPHP\Controllers;

use LorPHP\Core\Controller;
use LorPHP\Models\Permission;
use LorPHP\Core\JsonView;

class PermissionController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->model = new Permission();
    }

    public function index()
    {
        // List all permission
        $items = $this->model->all();
        return JsonView::render(['data' => $items]);
    }

    public function create()
    {
        // Handle POST to create new permission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getRequestData();
            $item = $this->model->create($data);
            return JsonView::render(['data' => $item], 201);
        }
        
        // Show create form for GET
        return $this->view('permission/create');
    }

    public function show($id)
    {
        // Show single permission
        $item = $this->model->find($id);
        if (!$item) {
            return JsonView::render(['error' => 'Permission not found'], 404);
        }
        return JsonView::render(['data' => $item]);
    }

    public function edit($id)
    {
        // Show edit form
        $item = $this->model->find($id);
        if (!$item) {
            return JsonView::render(['error' => 'Permission not found'], 404);
        }
        return $this->view('permission/edit', ['item' => $item]);
    }

    public function update($id)
    {
        // Handle update
        $item = $this->model->find($id);
        if (!$item) {
            return JsonView::render(['error' => 'Permission not found'], 404);
        }

        $data = $this->getRequestData();
        $updated = $this->model->update($id, $data);
        return JsonView::render(['data' => $updated]);
    }

    public function delete($id)
    {
        // Handle delete
        $item = $this->model->find($id);
        if (!$item) {
            return JsonView::render(['error' => 'Permission not found'], 404);
        }

        $this->model->delete($id);
        return JsonView::render(['message' => 'Permission deleted successfully']);
    }

    private function getRequestData()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }
    public function users($id)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return JsonView::render(['error' => 'Permission not found'], 404);
        }
        
        $related = $item->users();
        return JsonView::render(['data' => $related]);
    }
}
}
