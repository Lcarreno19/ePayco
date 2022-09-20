<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_wallets extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id',
        'operation',
        'type',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
