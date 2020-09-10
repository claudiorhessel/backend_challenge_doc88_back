<?php

namespace App\Http\Controllers;

use App\Models\Pastel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationPastel;

class PastelController extends Controller
{
    private $pastelsModel;

    public function __construct(Pastel $pastelsModel)
    {
        $this->pastelsModel = $pastelsModel;
    }

    public function getAll() {
        try {
            $pastels = $this->pastelsModel->all();
            if($pastels && count($pastels) > 0) {
                return response()->json($pastels, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get($id) {
        try {
            $pastel = $this->pastelsModel->find($id);
            if($pastel) {
                return response()->json($pastel, Response::HTTP_OK);
            } else {
                return response()->json(null, Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request) {
        $validator = Validator::make(
            $request->all(),
            ValidationPastel::RULE_PASTEL
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $photoName = $photo->getClientOriginalName();
                    $destinationPath = rtrim(app()->basePath('public/images'));
                    $photo->move($destinationPath, $photoName);

                    $requestData = $request->all();
                    $requestData['photo'] = $photoName;
                    $pastel = $this->pastelsModel->create($requestData);

                    return response()->json($pastel, Response::HTTP_CREATED);
                } else {
                    return response()->json(['error' => 'Erro com a foto.'], Response::HTTP_BAD_REQUEST);
                }
            } catch(QueryException $e) {
                return response()->json(['error' => 'Erro de conexão com o banco de dados'],
                                Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function update($id, Request $request) {
        $validator = Validator::make(
            $request->all(),
            ValidationPastel::RULE_PASTEL_UPDATE
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $photoName = $photo->getClientOriginalName();
                    $destinationPath = rtrim(app()->basePath('public/images'));
                    $photo->move($destinationPath, $photoName);

                    $requestData = $request->all();
                    $requestData['photo'] = $photoName;
                    $pastel = $this->pastelsModel->find($id)
                        ->update($requestData);

                    return response()->json($pastel, Response::HTTP_CREATED);
                } else {
                    return response()->json(['error' => 'Erro com a foto.'], Response::HTTP_BAD_REQUEST);
                }
            } catch(QueryException $e) {
                return response()->json(['error' => 'Erro de conexão com o banco de dados'],
                                Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function destroy($id) {
        try {
            $pastel = $this->pastelsModel->find($id)->delete();

            return response()->json(null, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
