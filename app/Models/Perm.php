<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perm extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'theme', 'description', 'periode', 'ambiance', 'membres',
        'asso', 'nom_resp', 'mail_resp', 'nom_resp_2', 'mail_resp_2', 'mail_asso', 'validated','semestre'
    ];

    public function creneaux()
    {
        return $this->hasMany(Creneau::class);
    }
    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    // Validation pour limiter à trois créneaux
    public function validateCreneauCount()
    {
        $creneauCount = $this->creneaux()->count();

        return $creneauCount < 3;
    }
}
