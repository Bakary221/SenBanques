<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Client extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profession',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comptesBancaires() {
        return $this->hasMany(CompteBancaire::class);
    }
}
