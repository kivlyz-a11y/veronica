<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\ServiceModel;
use App\Models\SystemSettingModel;

class HomeController extends BaseController
{
    public function index()
    {
        helper('veronika');
        $serviceModel = new ServiceModel();
        $subcatModel  = new \App\Models\ServiceSubcategoryModel();

        $services = $serviceModel->getActiveServices();
        foreach ($services as &$service) {
            $service['subcategories'] = $subcatModel->getByServiceId($service['id']);
        }

        $data = [
            'title'    => 'SI VERONIKA - Pengadilan Agama Penajam',
            'services' => $services,
        ];

        return view('public/home', $data);
    }
}
