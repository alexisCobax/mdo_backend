<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ConfiguracionesGenerales;

class NywdController extends Controller
{

    function login(Request $request)
    {
        try {

            $response = Http::post('https://developer.nywd.com/api/v1/account/login', [
                'Username' => $request->username,
                'Password' => $request->password
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new Exception('Error logging in: ' . $response->status());
            }
        } catch (Exception $e) {
            echo 'Error logging in: ',  $e->getMessage(), "\n";
        }
    }

    function refreshToken(Request $request)
    {
        try {
            $response = Http::post('https://developer.nywd.com/api/v1/account/token/refresh', [
                'RefreshToken' => $request->refreshToken,
                'AccessToken' => $request->accessToken
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new Exception('Error refreshing token: ' . $response->status());
            }
        } catch (Exception $e) {
            echo 'Error refreshing token: ',  $e->getMessage(), "\n";
        }
    }

    function getProductBySKU(Request $request)
    {
        try {
            $response = Http::withToken($request->bearerToken())->get("https://developer.nywd.com/api/v1/products/{$request->sku}");

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new Exception('Error fetching product: ' . $response->status());
            }
        } catch (Exception $e) {
            echo 'Error fetching product: ',  $e->getMessage(), "\n";
        }
    }

    function getProductBrands(Request $request)
    {
        try {
            $response = Http::withToken($request->bearerToken())->get('https://developer.nywd.com/api/v1/products/brands');

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new Exception('Error fetching product brands: ' . $response->status());
            }
        } catch (Exception $e) {
            echo 'Error fetching product brands: ',  $e->getMessage(), "\n";
        }
    }

    function getProductCategories(Request $request)
    {
        try {
            $response = Http::withToken($request->bearerToken())->get('https://developer.nywd.com/api/v1/products/categories');

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new Exception('Error fetching product categories: ' . $response->status());
            }
        } catch (Exception $e) {
            echo 'Error fetching product categories: ',  $e->getMessage(), "\n";
        }
    }

    function getProducts(Request $request)
    {
        try {
            $response = Http::withToken($request->bearerToken())->get('https://developer.nywd.com/api/v1/products', [
                'page' => $request->page,
                'pagesize' => $request->pageSize,
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new Exception('Error fetching products: ' . $response->status());
            }
        } catch (Exception $e) {
            echo 'Error fetching products: ',  $e->getMessage(), "\n";
        }
    }
}
