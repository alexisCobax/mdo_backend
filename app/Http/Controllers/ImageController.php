<?php

namespace App\Http\Controllers;

use App\Helpers\ImagesHelper;
use Illuminate\Http\Request;

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
