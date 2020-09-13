<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationClient;

class ClientController extends Controller
{
    private $clientsModel;

    public function __construct(Client $clientsModel)
    {
        $this->clientsModel = $clientsModel;
    }

    public function getAll(Request $request)
    {
        $filters = $request->all();
        $where = array();
        $orderBy = 'id';
        $position = 'asc';
        $perPage = '5';
        if(count($filters) > 0) {
            foreach($filters as $key => $value) {
                switch($key) {
                    case 'name':
                    case 'email':
                    case 'address':
                    case 'complement':
                    case 'neighborhood':
                    case 'phone':
                    case 'cep':
                        $where[] = [$key, 'like', '%'.$value.'%'];
                        break;
                    case 'birth_date':
                        $where[] = [$key, '=', $value];
                        break;
                    case 'order_by':
                        $orderBy = $value;
                        break;
                    case 'position':
                        $position = $value;
                        break;
                    case 'per_page':
                        $perPage = $value;
                        break;
                }
            }
        }

        try {
            $clients = $this->clientsModel
                            ->where($where)
                            ->orderBy($orderBy, $position)
                            ->simplePaginate($perPage);
            if($clients && count($clients) > 0) {
                return response()->json($clients, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    public function getById($id)
    {
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

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ValidationClient::RULE_CLIENT,
            ValidationClient::MESSAGE_CLIENT
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

    public function update($id, Request $request)
    {
        try {
            $client = $this->clientsModel->find($id)
                ->update($request->all());

            return response()->json($client, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    public function destroy($id)
    {
        try {
            $client = $this->clientsModel->find($id)->delete();

            return response()->json(null, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
