<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function product(){

        $busca = request('search');

        return view('products', ['busca' => $busca]);
    }
}
