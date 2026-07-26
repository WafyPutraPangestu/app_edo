<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleaseDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_request_id',
        'qr_code_string',
        'pdf_path',
        'issued_date'
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'datetime',
        ];
    }

    public function releaseRequest()
    {
        return $this->belongsTo(ReleaseRequest::class);
    }
}
