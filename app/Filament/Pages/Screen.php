<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Eq3wMicrolmsCourseDate;
class Screen extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.screen';


    protected function getHeaderActions(): array
    {


        return [

        ];
    }


    protected function getViewData(): array
    {



        $user = auth()->user(); // get the authenticated user

        $mogo = "spasst";


        return compact('user', 'mogo');
    }




function setNewCoursedates(){


    $latestEntries = MicrolmsCourseDate::select('eq3w_microlms_course_dates.*')
        ->join(\DB::raw('(SELECT MAX(id) as latest_id, course_id FROM eq3w_microlms_course_dates GROUP BY course_id) as latest'),
            function($join) {
                $join->on('eq3w_microlms_course_dates.id', '=', 'latest.latest_id');
            })
        ->get();
}

}
