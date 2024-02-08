<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;

class GroupChat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.group-chat';



    protected function getViewData(): array
    {
        $user = auth()->user();  // den authentifizierten Benutzer abrufen
        $mogo = "spasst";

        return compact('user', 'mogo');
    }
}
