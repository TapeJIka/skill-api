<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class SongResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'artwork'=> URL::signedRoute('song.artwork',['song' => $this->id]),
            'title'=> $this->title,
            'file'=>  URL::signedRoute('song.file',['song' => $this->id]),
            'artist'=> $this->artist,
            'composer'=> $this->composer,
            'album'=> $this->album,
            'track_number'=> $this->track_number,
            'genres'=> $this->genres,
            'playtime'=> $this->playtime,
        ];
    }
}
