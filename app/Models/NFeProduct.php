<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NFeProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'nfe_product';
    
    protected $fillable = [
        'nfe_id',
        'product_id',
        'quantity',
    ];

    public $timestamps = false;

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function nfe() {
        return $this->belongsTo(NFe::class);
    }
}
