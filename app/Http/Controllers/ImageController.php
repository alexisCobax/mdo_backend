<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ImagesHelper;

class ImageController extends Controller
{
    public function index()
    {

        return view('images');

    }
    public function upload(Request $request)
    {
        // Validar el formulario si es necesario
        $foo = new ImagesHelper();

        return $foo->uploadMultipleImages($request, 'images');
    }
}
