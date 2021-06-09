<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerNFe extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customer_nfe';

    protected $fillable = [
        'customer_id',
        'nfe_id',
    ];

    public $timestamps = false;

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function nfe() {
        return $this->belongsTo(NFe::class);
    }
}
