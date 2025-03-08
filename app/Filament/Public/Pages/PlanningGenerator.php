<?php

namespace App\Filament\Public\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use App\Services\PlanningGeneratorService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlanningExport;

class PlanningGenerator extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $title = 'Générateur de Planning';
    protected static string $view = 'filament.public.pages.planning';

    public ?string $jour = null;
    public ?array $participants = [];
    public ?array $modifications = [];
    public ?array $generatedSchedule = [];

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('jour')
                ->options([
                    'permLundi' => 'Lundi',
                    'permMardi' => 'Mardi',
                    'permMercredi' => 'Mercredi',
                    'permJeudi' => 'Jeudi',
                    'permVendredi' => 'Vendredi',
                ])
                ->required()
                ->reactive(),
            Forms\Components\Repeater::make('participants')
                ->label('Participants')
                ->addActionLabel('Ajouter un.e participant.e')
                ->schema([
                    Forms\Components\TextInput::make('nom')->required()->autofocus(),
                    Forms\Components\TimePicker::make('debut')->default('18:30')->required(),
                    Forms\Components\TimePicker::make('fin')->default('23:00')->required(),
                ])->columns(3),

                Forms\Components\Repeater::make('modifications')
                ->label('Modifications personnalisées')
                ->addActionLabel('Ajouter des modifications')
                ->schema([
                    Forms\Components\Select::make('permanence')
                        ->options([
                            'Bar' => 'Bar',
                            'Caisse' => 'Caisse',
                            'Sécu Pente' => 'Sécu Pente',
                            'Sécu Escalier' => 'Sécu Escalier',
                            'Autre' => 'Autre',
                        ])->required()
                        ->reactive(),
                    Forms\Components\TextInput::make('autre_permanence')
                        ->label('Nom de la perm')
                        ->visible(fn ($get) => $get('permanence') === 'Autre')
                        ->required(fn ($get) => $get('permanence') === 'Autre'),
                    Forms\Components\Select::make('horaire')
                        ->options([
                            '18:30-19:00' => '18h30-19:00',
                            '19:00-19:30' => '19:00-19:30',
                            '19:30-20:00' => '19:30-20:00',
                            '20:00-20:30' => '20:00-20:30',
                            '20:30-21:00' => '20:30-21:00',
                            '21:00-21:30' => '21:00-21:30',
                            '21:30-22:00' => '21:30-22:00',
                            '22:00-22:30' => '22:00-22:30',
                            '22:30-23:00' => '22:30-23:00',
                        ])->required(),
                    Forms\Components\TextInput::make('nombre')->numeric()->required(),
                ])->columns(3),            
        ];
    }

    public function generateSchedule()
    {
        $this->generatedSchedule = app(PlanningGeneratorService::class)->generate($this->jour, $this->participants, $this->modifications);
    }

    public function generateExcel()
    {
        return Excel::download(new PlanningExport($this->generatedSchedule), 'planning.xlsx');
    }
}
