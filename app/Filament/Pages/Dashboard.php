<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasPageShield;

    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard';
}
