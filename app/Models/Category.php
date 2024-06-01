<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public $table = "categories";

    public function wallapaper()
    {
        return $this->hasOne(wallapaper::class, 'category_id', 'id');
    }
}
