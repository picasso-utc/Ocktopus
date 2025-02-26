<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Astreinte;
use App\Models\Semestre;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NoteAstreinteOverview extends BaseWidget
{

    protected static function getStartSemester(): string
    {
        $semestre = Semestre::where('activated', true)->first();

        return $semestre ? $semestre->startOfSemestre : now();
    }

    /**
     * Get the end date of the active semester.
     *
     * @return mixed
     */
    protected static function getEndSemester(): mixed //string ou carbon
    {
        $semestre = Semestre::where('activated', true)->first();

        return $semestre ? $semestre->endOfSemestre : now()->addMonth();
    }

    protected function getStats(): array
    {
        $userId = User::where('uuid', session('user')->uuid)->pluck('id')->first();

        // Nombre d'astreintes par type pour l'utilisateur spécifié
        $astreintesByType = Astreinte::query()
            ->where('user_id', $userId)
            ->join('creneau', 'astreintes.creneau_id', '=', 'creneau.id')
            ->whereBetween('date', [self::getStartSemester(), self::getEndSemester()])
            ->selectRaw('astreinte_type, COUNT(*) as count')
            ->groupBy('astreinte_type')
            ->get()
            ->pluck('count', 'astreinte_type');

        // Nombre moyen d'astreintes par utilisateur
        $totalAstreintes = Astreinte::query()
            ->join('creneau', 'astreintes.creneau_id', '=', 'creneau.id')
            ->whereBetween('date', [self::getStartSemester(), self::getEndSemester()])
            ->count();
        $totalUsers = User::query()->where("role", "!=", "none")->count();
        $averageAstreintesPerUser = $totalUsers > 0 ? $totalAstreintes / $totalUsers : 0;

        // Somme des astreintes pour les types "Matin 1" et "Matin 2"
        $sumMatin1Matin2 = $astreintesByType->get('Matin 1', 0) + $astreintesByType->get('Matin 2', 0);
        // Somme des astreintes pour les types "Midi 1" et "Midi 2"
        $sumMidi1Midi2 = $astreintesByType->get('Déjeuner 1', 0) + $astreintesByType->get('Déjeuner 2', 0);
        // Somme des astreintes pour les types "Soir 1" et "Soir 2"
        $sumSoir1Soir2 = $astreintesByType->get('Soir 1', 0) + $astreintesByType->get('Soir 2', 0);


        $totalPointsUtilisateur = User::find($userId)->nombre_points;

        $nombreAstreintesNotees = Astreinte::query()
            ->join('creneau', 'astreintes.creneau_id', '=', 'creneau.id')
            ->whereBetween('date', [self::getStartSemester(), self::getEndSemester()])
            ->whereNotNull('note_orga')
            ->count();
        $pourcentageAstreintesNotees = $totalAstreintes > 0
            ? ($nombreAstreintesNotees / $totalAstreintes) * 100
            : 0;
        $couleurPourcentage = 'success' ;
        if ($pourcentageAstreintesNotees < 75) {
            $couleurPourcentage =  'danger';
        } elseif ($couleurPourcentage > 90) {
            $couleurPourcentage = 'success';
        }
        return [
            Stat::make('Astreintes', Astreinte::query()
                ->where('user_id', $userId) 
                ->join('creneau', 'astreintes.creneau_id', '=', 'creneau.id')
                ->whereBetween('date', [self::getStartSemester(), self::getEndSemester()])
                ->count()) 
                ->description('Votre nombre d\'astreintes'),
            Stat::make('Astreintes matin', $sumMatin1Matin2)
                ->description('Votre nombre d\'astreintes du matin'),
            Stat::make('Astreintes midi', $sumMidi1Midi2)
                ->description('Votre nombre d\'astreintes du midi'),
            Stat::make('Astreintes soir', $sumSoir1Soir2)
                ->description('Votre nombre d\'astreintes du soir'),
            Stat::make('Astreintes moyenne', round($averageAstreintesPerUser, 2))
                ->description('Nombre moyen d\'astreintes par utilisateur'),
            Stat::make('Nombre total de points', $totalPointsUtilisateur)
                ->description('Total des points basé sur les astreintes'),
            Stat::make('Astreintes notées', round($pourcentageAstreintesNotees, 2) . '%')
                ->description('Pourcentage d\'astreintes notée')
                ->color($couleurPourcentage),

        ];
    }
}

