<?php

namespace App\Filament\Fields;

require_once __DIR__ . '../../../helpers.php';

use App\Enums\MemberRole;
use Filament\Forms\Components\Select;

class UserRoleSelect extends Select
{
    public function setUp(): void
    {
        $this->options(enum_pluck(MemberRole::class));
        $this->label('Rôle');
        $this->required();
        $this->default('none');
    }
}
