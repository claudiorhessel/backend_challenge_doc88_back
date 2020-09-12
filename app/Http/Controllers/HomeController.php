<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function notFound() {
        return response()->json(['error' => 'Rota não encontrada.'], Response::HTTP_NOT_FOUND);
    }
}
