<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SongRequest;
use App\Http\Resources\PlayedSongsResource;
use App\Http\Resources\SongResource;
use App\Models\PlayedSongs;
use App\Models\Song;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use NunoMaduro\Collision\SolutionsRepositories\NullSolutionsRepository;
use Owenoj\LaravelGetId3\GetId3;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        if (Schema::hasTable('played_songs')){
//            $genres = DB::table('played_songs')
//                ->join('songs', 'played_songs.song_id','=','songs.id')
//                ->select('songs.genres')
//                ->whereJsonLength('songs.genres','>', 0)
//                ->get();
//            $list_of_genres = array();
//            foreach ($genres as $genre){
//                foreach ($genre as $g){
//                    array_push($list_of_genres, json_decode($g));
//                }
//            }
//            dd(array_count_values($list_of_genres));
//        }
//        $played = PlayedSongs::all();
//        dd($played);
//        $played->songs()->select('genres')->count()->get();
//        dd($played);

        $query_builder = QueryBuilder::for(Song::class)
            ->defaultSort('id')
            ->allowedSorts([
                'id',
                'title',
                'artist',
                'album',
                'genres',
                'playtime',
            ])->allowedFilters([
                AllowedFilter::exact('id'),
                'title',
                'artist',
                'album',
                'genres',
                'playtime',
            ]);
        if(request()->page) {
            return SongResource::collection($query_builder->paginate(request()->pagination ?? 10));
        }
        return SongResource::collection($query_builder->get());
    }

    /**
     * Upload a song, and getting song info,putting song cover and file to storage with hash names
     */
    public function upload(SongRequest $request)
    {
        $validated = $request->validated();
        $song = $validated['file'];
        $track = new GetId3($validated['file']);
        $songCover = $track->getArtwork(true);
        if(isset($songCover)){
            $validated['artwork'] = $songCover->hashName();
            $songCover->store('songCover');
        }
        $validated['file'] = $song->hashName();
        $validated['title'] = $track->getTitle();
        $validated['artist'] = $track->getArtist();
        $validated['composer'] = $track->getComposer();
        $validated['album'] = $track->getAlbum();
        $validated['track_number'] = $track->getTrackNumber();
        if (!$track->getGenres() == []){
            foreach ($track->getGenres() as $genre){
                $validated['genres'] = $genre;
            }
        }
//        $validated['genres'] = $track->getGenres();
        $validated['playtime'] = $track->getPlaytime();
        $song->store('songs');

        return new SongResource(Song::create($validated));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new SongResource(Song::find($id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $song = Song::find($id);
        $song->file = Storage::disk('local')->delete('songs/'.$song->file);
        $song->artwork = Storage::disk('local')->delete('songCover/'.$song->artwork);
        $song->delete();
        return new SongResource($song);
    }

    public function playedSong($id)
    {
        $validated = [
          'song_id' => $id,
        ];
        PlayedSongs::create($validated);
        return response()->json([
            'data' => 'yes'
        ]);

    }

    public function statistic()
    {
        if (Schema::hasTable('played_songs')){
            $songs_played = DB::table('played_songs')->count();
            $playtimes = DB::table('played_songs')
                ->join('songs', 'played_songs.song_id','=','songs.id')
                ->select('songs.playtime')
                ->get();
            $total_playtime = 0;
            foreach ($playtimes as $playtime ){
                foreach ($playtime as $p){
                    $p = str_replace(':', ',', $p);
                    $total_playtime += $p;
                }
            }
            dd($total_playtime);
        }


    }

    public function getArtwork(Request $request, Song $song)
    {
        $song->artwork = Storage::disk('local')->path('songCover/'.$song->artwork);
        return response()->file($song->artwork);
    }

    public function getSong(Request $request, Song $song)
    {
        $song->file = Storage::disk('local')->path('songs/'.$song->file);
        return response()->file($song->file);
    }


}
