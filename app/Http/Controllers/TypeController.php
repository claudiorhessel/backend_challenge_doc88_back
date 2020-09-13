<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationType;

class TypeController extends Controller
{
    private $typesModel;

    public function __construct(Type $typesModel)
    {
        $this->typesModel = $typesModel;
    }

    public function getAll()
    {
        try {
            $types = $this->typesModel->all();
            if($types && count($types) > 0) {
                return response()->json($types, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get($id)
    {
        try {
            $type = $this->typesModel->find($id);
            if($type) {
                return response()->json($type, Response::HTTP_OK);
            } else {
                return response()->json(null, Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ValidationType::RULE_TYPE
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                $type = $this->typesModel->create($request->all());

                return response()->json($type, Response::HTTP_CREATED);
            } catch(QueryException $e) {
                return response()->json(['error' => 'Erro de conexão com o banco de dados'],
                                Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ValidationType::RULE_TYPE_UPDATE
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                $type = $this->typesModel
                             ->find($id)
                             ->update($request->all());

                return response()->json($type, Response::HTTP_CREATED);
            } catch(QueryException $e) {
                return response()->json(['error' => 'Erro de conexão com o banco de dados'],
                                Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function destroy($id)
    {
        try {
            $type = $this->typesModel->find($id)->delete();

            return response()->json(null, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
