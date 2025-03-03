<?php

namespace App\Filament\Admin\Resources\SemestreResource\Pages;

use App\Filament\Admin\Resources\SemestreResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateSemestre extends CreateRecord
{
    protected static bool $canCreateAnother = false;
    protected static string $resource = SemestreResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

public function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                TextInput::make('state')
                    ->label('Nom') 
                    ->maxLength(3)
                    ->required()
                    ->rules(['regex:/^[AP]\d{2}$/']),
                DatePicker::make('startOfSemestre')
                    ->label('Start of Semestre')
                    ->required(),
                DatePicker::make('endOfSemestre')
                    ->label('End of Semestre')
                    ->required()
                    ->after('startOfSemestre'),
                ]
            );
    }
}
