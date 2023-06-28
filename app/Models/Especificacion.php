<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especificacion extends Model
{
    protected $fillable = ['producto_id', 'nombre', 'valor'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}