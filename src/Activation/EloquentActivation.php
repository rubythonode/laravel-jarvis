<?php

namespace SecTheater\Jarvis\Activation;

use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\User\EloquentUser;

class EloquentActivation extends Model
{
    protected $table = 'activations';
    protected $guarded = [];
    protected $casts = [
      'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(EloquentUser::class, 'user_id', 'id');
    }
}
