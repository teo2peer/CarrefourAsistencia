<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenProducto extends Model
{
    protected $fillable = ['producto_id', 'url'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}