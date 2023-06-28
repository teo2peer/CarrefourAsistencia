<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    use Searchable;

    public function toSearchableArray()
    {
        $array = $this->toArray();
        return $array;
    }
}