<?php

namespace App\Http\Controllers;
use  App\Http\Responses\ApiResponse;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Iluminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $categorias = Categoria::all();
            return ApiResponse::success('Lista de Categorías', 200, $categorias);
           // throw new Exception("Error al obtener categorias");
        } catch(Exception $e){
            return ApiResponse::error('Error al obtener la lista de categorias: '.$e->getMessage(), 500);
        }

       
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|unique:categorias'
            ]);

            $categoria = Categoria::create($request->all());
            return ApiResponse::success('Categoría creada exactamente', 201, $categoria);
        } catch (ValidationException $e){
          return ApiResponse::error('Error de validación', 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            return ApiResponse::success('Categoría obtenida exitosamente', 200, $categoria);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoría no encontrada', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $request->validate([
                'nombre' => ['required', Rule::unique('categorias')->ignore($categoria)]
            ]);
            $categoria->update($request-> all());
            return ApiResponse::success('Categoría actualizada exitosamente', 200, $categoria);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoría no encontrada', 404);
        } catch (Exception $e){
            return ApiResponse::error('Error'.$e->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();
            return ApiResponse::success('Categoría eliminada exitosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoría no encontrada', 404);
        }
    }

    public function productosPorCategoria($id)
    {
        try {
            $categoria = Categoria::with('productos')->findOrFail($id);
            return ApiResponse::success('Categoría y lista de Productos', 200, $categoria);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::success('Categoría no encontrada', 404);
        }
        
    }
}
