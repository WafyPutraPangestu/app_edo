<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleaseRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'surat_kuasa_path',
        'awb_path',
        'awb_number',
        'flight_number',
        'origin',
        'destination',
        'quantity',
        'gross_weight',
        'goods_description',
        'status',
        'rejection_note'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function requestCharges()
    {
        return $this->hasMany(RequestCharge::class);
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
    public function releaseDocument()
    {
        return $this->hasOne(ReleaseDocument::class);
    }
}
