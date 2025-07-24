<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Restrict CRUD operations to admin only
        if (session()->get('role') !== 'admin') {
            $uri = $request->getUri()->getPath();
            
            // List of restricted paths/operations
            $restrictedPaths = [
                'barang/create',
                'barang/store',
                'barang/edit',
                'barang/update',
                'barang/delete',
                'barang/update-stok-langsung',
                'barang-masuk/create',
                'barang-masuk/store',
                'barang-masuk/edit',
                'barang-masuk/update',
                'barang-masuk/delete',
                'barang-keluar/create',
                'barang-keluar/store',
                'barang-keluar/edit',
                'barang-keluar/update',
                'barang-keluar/delete',
            ];

            // Check if current path is restricted
            foreach ($restrictedPaths as $path) {
                if (strpos($uri, $path) === 0) {
                    return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melakukan operasi ini.');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 