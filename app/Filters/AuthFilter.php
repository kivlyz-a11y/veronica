<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('is_logged_in')) {
            $session->set('redirect_url', current_url());
            return redirect()->to(site_url('auth/login'))->with('error', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        // Check RBAC roles if specified in route filter argument
        if (!empty($arguments)) {
            $userRole = $session->get('user_role');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to(site_url('auth/login'))->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
