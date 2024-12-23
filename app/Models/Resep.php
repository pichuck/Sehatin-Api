<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    //

    protected $table = 'resep';
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
            // Simpan file ke storage
            $this->attributes['foto'] = $value->store('resep', 'public');
        } else {
            // Set langsung jika sudah berupa string
            $this->attributes['foto'] = $value;
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