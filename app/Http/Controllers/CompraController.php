<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Compra;
use App\Models\Producto;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompraController extends Controller
{
    public function index(){
        # code
    }

    public function store(Request $request)
    {
        try {
            $productos = $request->input('productos');

            if(empty($productos)){
                return ApiResponse::error('No se proporcionaron productos', 400);
            }

            $validator = Validator::make($request->all(),[
                'productos' => 'required|array',
                'productos.*.producto_id' => 'required|integer|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1'
            ]);

            if($validator->fails()){
                return ApiResponse::error('Datos inválidos en la lista de productos', 400, $validator->errors());
            }

            $productoIds = array_column($productos, 'producto_id');

            if(count($productoIds)!== count(array_unique($productoIds))){
                return ApiResponse::error('No se permiten productos duplicados para la compra', 400);
            }

            $totalPagar= 0;
            $subtotal = 0;
            $compraItems = [];

            foreach($productos as $producto){
                $productoB = Producto::find($producto['producto_id']);
                if(!$productoB){
                    return ApiResponse::error('Producto no encontrado', 404);
                }

                if($productoB->cantidad_disponible<$producto['cantidad']){
                    return ApiResponse::error('El producto no tiene suficiente cantidad disponible', 404);
                }

                $productoB->cantidad_disponible -= $producto['cantidad'];
                $productoB->save();

                $subtotal = $productoB->precio * $producto['cantidad'];
                $totalPagar += $subtotal;

                $compraItems[] = [
                    'producto_id' => $productoB-> id,
                    'precio' => $productoB->precio,
                    'cantidad' => $producto['cantidad'],
                    'subtotal' => $subtotal
                ];
            }

            $compra = Compra::create([
                'subtotal' => $totalPagar,
                'total' => $totalPagar
            ]);

            $compra->productos()->attach($compraItems);

            return ApiResponse::success('Compra realizada exitosamente',201, $compra);

        } catch (QueryException $e) {
            return ApiResponse::error('Error en la consulta de base de datos', 500);
        }catch(Exception $e){
            return ApiResponse::error('Error inesperado', 500);
        }
    }

    public function show($id)
    {
        # code
    }
}