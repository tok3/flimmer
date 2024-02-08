<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Storage;

class Document extends Model
{
    protected $fillable = [
        'customer_id',
        'file_path',
        'comments'
    ];

    protected static function booted(): void
    {
        self::deleting(function (Document $customerDocument) {
            Storage::disk('public')->delete($customerDocument->file_path);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

}
