<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ServiceModel;
use App\Models\ServiceSubcategoryModel;
use App\Services\Audit\AuditService;

class ServiceController extends BaseController
{
    protected ServiceModel $serviceModel;
    protected ServiceSubcategoryModel $subcatModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
        $this->subcatModel  = new ServiceSubcategoryModel();
        $this->auditService = new AuditService();
    }

    public function index()
    {
        helper(['form', 'veronika']);

        $services = $this->serviceModel->findAll();
        foreach ($services as &$srv) {
            $srv['subcategories'] = $this->subcatModel->where('service_id', $srv['id'])->findAll();
        }

        $data = [
            'title'    => 'Manajemen Jenis Layanan - SI VERONIKA',
            'services' => $services,
        ];

        return view('admin/services/index', $data);
    }

    public function store()
    {
        $name = trim($this->request->getPost('name'));
        $slug = url_title(strtolower($name));

        $this->serviceModel->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $this->request->getPost('description'),
            'icon'        => $this->request->getPost('icon') ?: 'bi-info-circle',
            'status'      => 'active',
        ]);

        return redirect()->back()->with('message', 'Jenis layanan berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $name = trim($this->request->getPost('name'));
        $slug = url_title(strtolower($name));

        $this->serviceModel->update($id, [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $this->request->getPost('description'),
            'icon'        => $this->request->getPost('icon') ?: 'bi-info-circle',
            'status'      => $this->request->getPost('status') ?: 'active',
        ]);

        return redirect()->back()->with('message', 'Jenis layanan berhasil diperbarui.');
    }

    public function storeSubcategory()
    {
        $name = trim($this->request->getPost('name'));
        $slug = url_title(strtolower($name));

        $this->subcatModel->insert([
            'service_id'  => (int)$this->request->getPost('service_id'),
            'name'        => $name,
            'slug'        => $slug,
            'description' => $this->request->getPost('description'),
            'status'      => 'active',
        ]);

        return redirect()->back()->with('message', 'Sub-layanan berhasil ditambahkan.');
    }

    public function deleteSubcategory(int $id)
    {
        $this->subcatModel->delete($id);
        return redirect()->back()->with('message', 'Sub-layanan berhasil dihapus.');
    }
}
