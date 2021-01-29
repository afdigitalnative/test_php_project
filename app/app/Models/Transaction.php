<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['account_id', 'amount'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->{$transaction->getKeyName()} = (string) Str::uuid();
        });
    }

    public function getKeyType()
    {
        return 'string';
    }
}
