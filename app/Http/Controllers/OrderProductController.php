<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationOrderProduct;

class OrderProductController extends Controller
{
    private $orderProductsModel;

    public function __construct(OrderProduct $orderProductsModel)
    {
        $this->orderProductsModel = $orderProductsModel;
    }

    public function getAll() {
        try {
            $orderProducts = $this->orderProductsModel
                             ->all()
                             ->type;
            if($orderProducts && count($orderProducts) > 0) {
                return response()->json($orderProducts, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get($id) {
        try {
            $orderProduct = $this->orderProductsModel
                            ->select('orderProducts.*','types.name as type_name')
                            ->leftJoin('types', 'types.id', '=', 'orderProducts.type_id')
                            ->find($id);
            if($orderProduct) {
                return response()->json($orderProduct, Response::HTTP_OK);
            } else {
                return response()->json(null, Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            dd($e);
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request) {
        $validator = Validator::make(
            $request->all(),
            ValidationOrderProduct::RULE_ORDER_PRODUCT
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
                    $orderProduct = $this->orderProductsModel->create($requestData);

                    return response()->json($orderProduct, Response::HTTP_CREATED);
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
            ValidationOrderProduct::RULE_ORDER_PRODUCT_UPDATE
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
                    $orderProduct = $this->orderProductsModel->find($id)
                        ->update($requestData);

                    return response()->json($orderProduct, Response::HTTP_CREATED);
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
            $orderProduct = $this->orderProductsModel->find($id)->delete();

            return response()->json(null, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
