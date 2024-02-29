<?php

namespace App\Http\Controllers;
use  App\Http\Responses\ApiResponse;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;
use Iluminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $productos = Producto::with('marca', 'categoria') -> get();
            return ApiResponse::success('Lista de productos', 200, $productos);
        } catch (Exception $e) {
            return ApiResponse::error('Error al obtener la lista de productos: '.$e->getMessage(), 500);

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|unique:productos',
                'precio'=> 'required|numeric',
                'cantidad_disponible'=> 'required|integer',
                'categoria_id'=> 'required|exists:categorias,id',
                'marca_id'=> 'required|exists:marcas,id',

            ]);
            $productos = Producto::create($request->all());
            return ApiResponse::success('Producto creado exitosamente', 201, $productos);

        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();

            if(isset($errors['categoria_id'])){
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);
            }

            if(isset($errors['marca_id'])){
                $errors['marca'] = $errors['marca_id'];
                unset($errors['marca_id']);
            }

            return ApiResponse::error('Errores de validación', 422, $errors);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {  
        $productos = Producto::with('marca', 'categoria')->findOrFail($id);
        return ApiResponse::success('Producto obtenido exitosamente', 201, $productos);

        } catch (ModelNotFoundException $e) {
        return ApiResponse::error('Producto no encontrado', 404);
        }

        }

    public function update(Request $request, string $id)
    {
        try {
            $productos = Producto::findOrFail($id);
            $request->validate([
                'nombre' => 'required|unique:productos,nombre,'.$productos->id,
                'precio'=> 'required|numeric',
                'cantidad_disponible'=> 'required|integer',
                'categoria_id'=> 'required|exists:categorias,id',
                'marca_id'=> 'required|exists:marcas,id',

            ]);

            $productos->update($request->All());
            return ApiResponse::success('Producto actualizado exitosamente', 200, $productos);
        }catch(ValidationException $e){
            $errors = $e->validator->errors()->toArray();

            if(isset($errors['categoria_id'])){
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);
            }
    
            if(isset($errors['marca_id'])){
                $errors['marca'] = $errors['marca_id'];
                unset($errors['marca_id']);
            }
            return ApiResponse::error('Errores de validación', 422, $errors);
        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Producto no encontrado', 404);
        }
    }
    
    public function destroy($id)
    {
        try {
            $productos = Producto::findOrFail($id);
            $productos->delete();
            return ApiResponse::success('producto Eliminado exitosamente', 200);
        } catch (ModelNotFoundException) {
            return ApiResponse::error('producto no encontrado', 404);
        }
    }

}