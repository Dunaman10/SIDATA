<?php

namespace App\Filament\Resources\MentorStudentResource\Pages;

use App\Filament\Resources\MentorStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMentorStudents extends ListRecords
{
    protected static string $resource = MentorStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Mentor Santri'),
        ];
    }
}
