<?php

namespace App\Filament\Resources\Computers;

use App\Filament\Resources\Computers\Pages\CreateComputer;
use App\Filament\Resources\Computers\Pages\EditComputer;
use App\Filament\Resources\Computers\Pages\ListComputers;
use App\Filament\Resources\Computers\Schemas\ComputerForm;
use App\Filament\Resources\Computers\Tables\ComputersTable;
use App\Models\Computer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ComputerResource extends Resource
{
    protected static ?string $model = Computer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $navigationLabel = 'Computadora';

    protected static ?string $modelLabel = 'Computadora';

    protected static ?string $pluralModelLabel = 'Computadoras';

    protected static string | UnitEnum | null $navigationGroup = 'Dispositivos';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ComputerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComputersTable::configure($table);
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
            'index' => ListComputers::route('/'),
            'create' => CreateComputer::route('/create'),
            'edit' => EditComputer::route('/{record}/edit'),
        ];
    }
}
