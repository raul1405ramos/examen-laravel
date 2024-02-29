<?php

namespace App\Http\Controllers;
use  App\Http\Responses\ApiResponse;
use App\Models\Marca;
use Exception;
use Illuminate\Http\Request;
use Iluminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        try{
            $marca = Marca::all();
            return ApiResponse::success('Lista de Marcas', 200, $marca);
           // throw new Exception("Error al obtener categorias");
        } catch(Exception $e){
            return ApiResponse::error('Error al obtener la lista de marcas: '.$e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|unique:marcas'
            ]);

            $marca = Marca::create($request->all());
            return ApiResponse::success('Marca creada exactamente', 201, $marca);
        } catch (ValidationException $e){
          return ApiResponse::error('Error de validación', 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $marca = Marca::findOrFail($id);
            return ApiResponse::success('Marca obtenida exitosamente', 200, $marca);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca no encontrada', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $request->validate([
                'nombre' => ['required', Rule::unique('marcas')->ignore($marca)]
            ]);
            $marca->update($request-> all());
            return ApiResponse::success('Marca actualizada exitosamente', 200, $marca);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca no encontrada', 404);
        } catch (Exception $e){
            return ApiResponse::error('Error'.$e->getMessage(), 422);
        }
    }

 /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $marca->delete();
            return ApiResponse::success('Marca eliminada exitosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoría no encontrada', 404);
        }
    }

    public function productosporMarca($id)
    {
        try {
            $marca = Marca::with('productos')->findOrFail($id);
            return ApiResponse::success('Marca y lista de Productos', 200, $marca);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::success('Marca no encontrada', 404);
        }

    }
}


    

   
