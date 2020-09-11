<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationProduct;

class ProductController extends Controller
{
    private $productsModel;

    public function __construct(Product $productsModel)
    {
        $this->productsModel = $productsModel;
    }

    public function getAll() {
        try {
            $products = $this->productsModel->all();
            if($products && count($products) > 0) {
                return response()->json($products, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get($id) {
        try {
            $product = $this->productsModel->find($id);
            if($product) {
                return response()->json($product, Response::HTTP_OK);
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
            ValidationProduct::RULE_PRODUCT
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
                    $product = $this->productsModel->create($requestData);

                    return response()->json($product, Response::HTTP_CREATED);
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
            ValidationProduct::RULE_PRODUCT_UPDATE
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
                    $product = $this->productsModel->find($id)
                        ->update($requestData);

                    return response()->json($product, Response::HTTP_CREATED);
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
            $product = $this->productsModel->find($id)->delete();

            return response()->json(null, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
