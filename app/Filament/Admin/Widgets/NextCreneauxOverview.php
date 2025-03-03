<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Astreinte;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class NextCreneauxOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = User::where('uuid', session('user')->uuid)->pluck('id')->first();

        $upcomingCreneaux = Astreinte::query()
            ->where('user_id', $userId)
            ->join('creneau', 'astreintes.creneau_id', '=', 'creneau.id')
            ->join('perms', 'creneau.perm_id', '=', 'perms.id')
            ->where('creneau.date', '>=', now()->toDateString())
            ->select('creneau.date', 'astreintes.astreinte_type', 'perms.nom as perm_nom')
            ->orderBy('creneau.date')
            ->get();

        $stats = [];

        foreach ($upcomingCreneaux as $creneau) {
            $date = Carbon::parse($creneau->date);
            $date->locale('fr_FR');
            $day = $date->isoFormat('dddd D MMMM');
            $creneauType = $this->translateCreneau($creneau->astreinte_type);
            $permNom = $creneau->perm_nom;

            $stats[] = Stat::make("Astreinte le $day", $permNom)
                ->description("Type de créneau : $creneauType");
        }

        return $stats;
    }

    private function translateCreneau($astreinteType): string
    {
        $mapping = [
            'Matin 1' => '9h30-10h15',
            'Matin 2' => '10h-12h',
            'Déjeuner 1' => '11h45-13h',
            'Déjeuner 2' => '12h45-14h15',
            'Soir 1' => '17h30-23h',
            'Soir 2' => '18h30-23h',
            'Soir 3' => '18h30-23h',
            'Soir 4' => '18h30-23h',
            'Divers' => 'Inconnu',
        ];

        return $mapping[$astreinteType] ?? 'Inconnu';
    }
}
