<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NFe extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'nfes';

    public $timestamps = false;

    protected $fillable = [
        'nfe_code',
        'generated_date',
        'delivery_price',
        'company_id',
    ];

    protected $attributes = [
        'delivery_price' => 0.00,
    ];

    public function company() {
        return $this->belongsTo(Company::class, 'CNPJ');
    }
}
