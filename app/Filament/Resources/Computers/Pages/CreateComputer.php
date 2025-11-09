<?php

namespace App\Filament\Resources\Computers\Pages;

use App\Filament\Resources\Computers\ComputerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComputer extends CreateRecord
{

    protected static ?string $title = 'Formulario de Computadora';

    protected static string $resource = ComputerResource::class;
}
