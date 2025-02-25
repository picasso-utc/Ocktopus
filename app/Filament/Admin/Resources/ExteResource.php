<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExteResource\Pages;
use App\Filament\Admin\Resources\ExteResource\RelationManagers;
use App\Mail\ExteConfirmation;
use App\Mail\ExteRefus;
use App\Models\Exte;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class ExteResource extends Resource
{
    protected static ?string $model = Exte::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $label = 'Liste des extés';
    protected static ?string $navigationGroup = 'Général';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date de la demande')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('etu_nom_prenom')->label('Nom Prénom Étudiant'),
                Tables\Columns\TextColumn::make('etu_mail')->label('Mail Étudiant'),
                Tables\Columns\TextColumn::make('exte_nom_prenom')->label('Nom Prénom Exté'),
                Tables\Columns\TextColumn::make('exte_date_debut')->label('Date début Exté'),
                Tables\Columns\TextColumn::make('exte_date_fin')->label('Date Fin Exté'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('voir_commentaire')
                    ->label('Voir Commentaire')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (Exte $record) => !is_null($record->commentaire)) // Ne montre que si le commentaire n'est pas nul
                    ->action(function (Exte $record, $data) {
                        Notification::make()
                            ->title('Commentaire')
                            ->body($record->commentaire)
                            ->info()
                            ->send();
                    }),
                Tables\Actions\Action::make('Envoyer Mail')
                    ->color('success')
                    ->label('Envoyer Mail')
                    ->visible(fn ($record) => !$record->mailed)
                    ->icon('heroicon-o-envelope-open')
                    ->action(function ($record) {
                        Mail::to($record->etu_mail)->send(new ExteConfirmation($record));
                        $record->mailed = 1;
                        $record->save();
                    }),
                Tables\Actions\Action::make('Refuser Demande')
                    ->color('warning')
                    ->label('Refuser Demande')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn ($record) => !$record->mailed)
                    ->action(function ($record) {
                        Mail::to($record->etu_mail)->send(new ExteRefus($record));
                        $record->delete();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('envoyer_email')
                    ->label('Envoyer Emails')
                    ->color('success')
                    ->icon('heroicon-o-envelope-open')
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            Mail::to($record->etu_mail)->send(new ExteConfirmation($record));
                            $record->mailed = 1;
                            $record->save();
                        }
                    })
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('refuser_demande')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->label('Refuser Demandes')
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            Mail::to($record->etu_mail)->send(new ExteRefus($record));
                            $record->delete();
                        }
                    })
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\DeleteBulkAction::make()
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => !$record->mailed
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
            'index' => Pages\ListExtes::route('/'),
        ];
    }
}
