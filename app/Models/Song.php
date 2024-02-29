<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;
    protected $fillable = [
        'artwork',
        'title',
        'file',
        'artist',
        'composer',
        'album',
        'track_number',
        'genres',
        'playtime',
    ];
    protected $casts = [
      'genres' => 'array',
    ];

    public function albums() {
        return $this->belongsToMany(Album::class, 'album_song');
    }
}
