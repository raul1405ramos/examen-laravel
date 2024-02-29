<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Producto extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cantidad_disponible',
        'categoria_id',
        'marca_id'

    ];

    public function categoria(){ //Producto pertenece a una categoria
    return $this->belongsTo(Categoria::class);

    }

    public function marca(){ //Producto pertenece a una marca
        return $this->belongsTo(Marca::class);
    
        }

    public function compras(){ //Producto pertenece a una o varias marcas
        return $this->belongsToMany(Compra::class)->withPivot('precio', 'cantidad', 'subtotal')->withTimestamps();

    }    
}
