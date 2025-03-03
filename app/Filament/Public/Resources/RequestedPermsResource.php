<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\RequestedPermsResource\Pages;
use App\Filament\Public\Resources\RequestedPermsResource\RelationManagers;
use App\Models\Perm;
use App\Models\RequestedPerms;
use App\Models\Semestre;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;

class RequestedPermsResource extends Resource
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $model = Perm::class;
    protected static ?string $navigationGroup = 'Permanences';
    protected static ?string $label = 'Demandes de permanences';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    public static function form(Form $form): Form
    {
        $semestreActif = Semestre::where('activated', true)->first();

        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule
                            ->where('nom', $get('nom'))
                            ->where('semestre_id', $get('semestre_id'));
                    }, ignoreRecord: true)
                    ->placeholder('Nom de la permanence')
                    ->label('Nom')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('theme')
                    ->required()
                    ->placeholder('Thème de la permanence')
                    ->label('Thème')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'unorderedList', 'undo', 'redo'])
                    ->placeholder("Donne nous des détails sur la permanence que vous voulez organiser")
                    ->label('Description (anims du jour et du soir, ambiance, musique)')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\Placeholder::make('Le Pic est là pour vous !')
                    ->content("N'oubliez pas que la team animation du pic est là pour vous aider à organiser vos anims si besoin ! ")
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\Toggle::make('teddy')
                    ->required()
                    ->label('Habillerez-vous Teddy ?')
                    ->inline(false)
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\Toggle::make('repas')
                    ->required()
                    ->label('Repas prévu ?')
                    ->inline(false)
                    ->reactive()
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
                Forms\Components\TextInput::make('idea_repas')
                    ->label('Idée du repas')
                    ->placeholder('Des idées du repas ?')
                    ->disabled(fn (callable $get) => !$get('repas'))
                    ->required(fn (callable $get) => $get('repas'))
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 4,
                        'xl' => 4,
                        '2xl' => 4,
                    ]),
                Forms\Components\TextInput::make('nom_resp')
                    ->required()
                    ->placeholder('Nom du responsable de la permanence')
                    ->label('Nom du responsable')
                    ->default(mailToName(auth()->user()?->email))
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('mail_resp')
                    ->required()
                    ->placeholder('Adresse mail du responsable de la permanence')
                    ->label('Adresse mail du responsable')
                    ->default(auth()->user()?->email)
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('nom_resp_2')
                    ->required()
                    ->placeholder('Nom du sous-responsable')
                    ->label('Nom du sous-responsable')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('mail_resp_2')
                    ->required()
                    ->placeholder('Adresse mail du sous-responsable')
                    ->label('Adresse mail du sous-responsable')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\Toggle::make('asso')
                    ->required()
                    ->label('Géré par une asso ?')
                    ->inline(false)
                    ->reactive()
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
                Forms\Components\TextInput::make('mail_asso')
                    ->required(fn (Forms\Get $get) => $get('asso'))
                    ->placeholder('Adresse mail de l\'association')
                    ->label('Adresse mail de l\'association')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 4,
                        'xl' => 4,
                        '2xl' => 4,
                    ])
                    ->disabled(fn (Forms\Get $get) => !$get('asso')),
                Forms\Components\TextInput::make('ambiance')
                    ->required()
                    ->label('Ambiance (1 chill - 5 dancefloor endiablé)')
                    ->placeholder('Entre 1 et 5')
                    ->integer()
                    ->minValue(1)
                    ->maxValue(5)
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TextInput::make('periode')
                    ->required()
                    ->placeholder('Période souhaitée et contraintes')
                    ->label('Période')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 3,
                        'lg' => 2,
                        'xl' => 2,
                        '2xl' => 3,
                    ]),
                Forms\Components\Select::make('jour')
                    ->multiple()
                    ->options([
                        'lundi' => 'Lundi',
                        'mardi' => 'Mardi',
                        'mercredi' => 'Mercredi',
                        'jeudi' => 'Jeudi',
                        'vendredi' => 'Vendredi',
                    ])
                    ->label('Jours de la semaine préférentiels')
                    ->required()
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\TagsInput::make('membres')
                    ->required()
                    ->placeholder('Appuyez sur Entrée pour ajouter un membre')
                    ->label('Membres de la permanence')
                    ->separator(' - ')
                    ->splitKeys(['Enter', ',', 'Tab'])
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ]),
                Forms\Components\Toggle::make('artiste')
                    ->required()
                    ->label('Souhaitez-vous accueillir des artistes ?')
                    ->inline(false)
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\TextInput::make('remarques')
                    ->placeholder('Donnez nous les informations qui vous semblent importantes')
                    ->label('Remarque supplémentaires')
                    ->columnSpan([
                        'sm' => 6,
                        'md' => 6,
                        'lg' => 6,
                        'xl' => 6,
                        '2xl' => 6,
                    ]),
                Forms\Components\Hidden::make('semestre_id')->default($semestreActif->id),
            ])
            ->columns(6); // Définit le nombre de colonnes global pour le formulaire
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('creneau_matin')
                    ->label('Créneau Matin')
                    ->getStateUsing(function ($record) {
                        $creneauMatin = $record->creneaux->first(function ($creneau) {
                            return $creneau->creneau === 'M' && $creneau->confirmed;
                        });
                        return $creneauMatin ? Carbon::parse($creneauMatin->date)->translatedFormat('d F Y') : 'Pas attribué';
                    }),
                Tables\Columns\TextColumn::make('creneau_dej')
                    ->label('Créneau Déjeuner')
                    ->getStateUsing(function ($record) {
                        $creneauDej = $record->creneaux->first(function ($creneau) {
                            return $creneau->creneau === 'D' && $creneau->confirmed;
                        });
                        return $creneauDej ? Carbon::parse($creneauDej->date)->translatedFormat('d F Y') : 'Pas attribué';
                    }),
                Tables\Columns\TextColumn::make('creneau_soir')
                    ->label('Créneau Soir')
                    ->getStateUsing(function ($record) {
                        $creneauSoir = $record->creneaux->first(function ($creneau) {
                            return $creneau->creneau === 'S' && $creneau->confirmed;
                        });
                        return $creneauSoir ? Carbon::parse($creneauSoir->date)->translatedFormat('d F Y') : 'Pas attribué';
                    }),
                Tables\Columns\TextColumn::make('semestre.state')
                    ->label('Semestre')
                    ->searchable(),
            ]
            )
            ->filters([
            ])
            ->actions([
            ])
            ->bulkActions(
                [
                //
                ]
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequestedPerms::route('/'),
            'create' => Pages\CreateRequestedPerms::route('/create'),
            'edit' => Pages\EditRequestedPerms::route('/{record}/edit'),
        ];
    }
}
