<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalCharge extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tarif'];

    // Relasi: Tarif ini dipakai di tagihan mana saja
    public function requestCharges()
    {
        return $this->hasMany(RequestCharge::class);
    }
}
