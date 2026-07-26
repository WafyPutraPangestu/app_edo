<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_request_id',
        'user_id',
        'invoice_number',
        'total_amount',
        'status',
        'payment_method',
        'payment_date',
        'snap_token',
        'midtrans_transaction_id'
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function releaseRequest()
    {
        return $this->belongsTo(ReleaseRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
