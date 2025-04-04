<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['user_id', 'content', 'date_for'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
