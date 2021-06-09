<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'CNPJ',
        'name',
    ];

    public function nfe() {
        return $this->hasMany(NFe::class, 'CNPJ');
    }
}
