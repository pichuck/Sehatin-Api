<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    //
    protected $table = 'artikel';
    protected $fillable = [
        'judul', 
        'user_id', 
        'isi', 
        'category', 
        'foto'
    ];


    public function getFotoAttribute($value)
    {
        return url('storage/' . $value);
    }

    public function setFotoAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->attributes['foto'] = $value->store('artikel', 'public');
        } else {
            $this->attributes['foto'] = $value; // Store the string as is
        }
    }
    

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'penulis', 'id');
    }

}