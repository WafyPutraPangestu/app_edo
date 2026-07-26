<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestCharge extends Model
{
    use HasFactory;

    protected $fillable = ['release_request_id', 'local_charge_id', 'amount'];

    public function releaseRequest()
    {
        return $this->belongsTo(ReleaseRequest::class);
    }

    public function localCharge()
    {
        return $this->belongsTo(LocalCharge::class);
    }
}
