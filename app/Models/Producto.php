<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Producto extends Model
{
    use Searchable;

    protected $fillable = ['nombre', 'categoria_id', 'descripcion', 'precio', 'EAN', 'incentivo'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function especificaciones()
    {
        return $this->hasMany(Especificacion::class);
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function searchableAs()
    {
        return 'productos_index';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['categoria'] = $this->categoria->nombre;
        $array['especificaciones'] = $this->especificaciones->pluck('valor')->implode(' ');

        return $array;
    }
}