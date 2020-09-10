<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationClient;

class ClientController extends Controller
{
    private $clientsModel;

    public function __construct(Clients $clientsModel)
    {
        $this->clientsModel = $clientsModel;
    }

    public function getAll() {
        try {
            $clients = $this->clientsModel->all();
            if($clients && count($clients) > 0) {
                return response()->json($clients, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    public function get($id) {
        try {
            $client = $this->clientsModel->find($id);
            if($client && count($client) > 0) {
                return response()->json($client, Response::HTTP_OK);
            } else {
                return response()->json(null, Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    public function store(Request $request) {
        $validator = Validator::make(
            $request->all(),
            ValidationClient::RULE_CLIENT
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                $client = $this->clientsModel->create($request->all());

                return response()->json($client, Response::HTTP_CREATED);
            } catch(QueryException $e) {
                return response()->json(['error' => 'Erro de conexão com o banco de dados'], array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR));
            }
        }
    }

    public function update($id, Request $request) {
        try {
            $client = $this->clientsModel->find($id)
                ->update($request->all());

            return response()->json($client, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    public function destroy($id) {
        try {
            $client = $this->clientsModel->find($id)->delete();

            return response()->json(null, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
