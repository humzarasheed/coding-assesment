<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
    ];

    /**
     * Get the sender associated with the request.
     */
    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    /**
     * Get the receiver associated with the request.
     */
    public function receiver()
    {
        return $this->hasOne(User::class, 'id', 'receiver_id');
    }
}
