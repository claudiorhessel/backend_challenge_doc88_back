<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    private $clientsModel;

    public function __construct(Clients $clientsModel)
    {
        $this->clientsModel = $clientsModel;
    }

    public function getAll() {
        $clients = $this->clientsModel->all();

        return response()->json($clients);
    }

    public function get($id) {
        $client = $this->clientsModel->find($id);

        return response()->json($client);
    }

    public function store(Request $request) {
        $client = $this->clientsModel->create($request->all());

        return response()->json($client);
    }

    public function update($id, Request $request) {
        $client = $this->clientsModel->find($id)
            ->update($request->all());

        return response()->json($client);
    }

    public function destroy($id) {
        $client = $this->clientsModel->find($id)->destroy();

        return response()->json($client);
    }
}
