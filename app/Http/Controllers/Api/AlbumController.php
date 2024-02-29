<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlbumRequest;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\SongResource;
use App\Models\Album;
use App\Models\PlayedSongs;
use App\Models\Song;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query_builder = QueryBuilder::for(Album::class)
            ->defaultSort('id')
            ->allowedSorts([
                'id',
                'title',
            ])->allowedFilters([
                AllowedFilter::exact('id'),
                'title',
            ]);
        if(request()->page) {
            return AlbumResource::collection($query_builder->paginate(request()->pagination ?? 10));
        }
        return AlbumResource::collection($query_builder->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AlbumRequest $request)
    {
        $validated = $request->validated();
        return new AlbumResource(Album::create($validated));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new AlbumResource(Album::find($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AlbumRequest $request,$id)
    {
        $validated = $request->validated();
        return new AlbumResource(Album::find($id)->update($validated));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $album = Album::find($id);
        $album->delete();
        return new AlbumResource($album);
    }

    public function addSong(Request $request)
    {
        $validated = $request->validate([
            'album_id' => 'required',
            'song_id' => 'required|array',
        ]);

        $album = Album::find($validated['album_id']);

        $album->songs()->sync($validated['song_id']);

        return new AlbumResource($album);

    }

    public function albumSongs ($id) {
        $album  = Album::find($id);
        $songs = $album->songs;
        return $songs;
    }
}
