<?php

namespace App\Repositories;

use App\Http\Requests\SeriesFormRequest;
use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use Illuminate\Support\Facades\DB;

class EloquentSeriesRepository implements SeriesRepository
{
    public function add(SeriesFormRequest $request): Series
    {
        return DB::transaction(function() use ($request){

            $serie = Series::create($request->all());
            $season = [];
            for ($i = 1; $i <= $request->seasonQty; $i++) {
                $season[] = [
                    'series_id' => $serie->id,
                    'number' => $i,
                ];
            }
            Season::insert($season);

            $episodes = [];
            foreach ($serie->seasons as $season) {
                for ($j = 1; $j < $request->episodesPerSeason; $j++) {
                    $episodes[] = [
                        'season_id' => $season->id,
                        'number' => $j,
                    ];
                };
            }
            Episode::insert($episodes);

            return $serie;

        });
    }
}