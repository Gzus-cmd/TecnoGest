<?php

namespace App\Filament\Resources\Maintenances;

use App\Filament\Resources\Maintenances\Pages\CreateMaintenance;
use App\Filament\Resources\Maintenances\Pages\EditMaintenance;
use App\Filament\Resources\Maintenances\Pages\ListMaintenances;
use App\Filament\Resources\Maintenances\Schemas\MaintenanceForm;
use App\Filament\Resources\Maintenances\Tables\MaintenancesTable;
use App\Models\Maintenance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MaintenanceResource extends Resource
{

    protected static ?string $model = Maintenance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'Mantenimiento';

    protected static ?string $modelLabel = 'Mantenimiento';

    protected static ?string $pluralModelLabel = 'Mantenimientos';

    protected static string | UnitEnum | null $navigationGroup = 'Registros';

    protected static ?int $navigationSort = 72;

    

    public static function form(Schema $schema): Schema
    {
        return MaintenanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenancesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['deviceable', 'registeredBy']);
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
            'index' => ListMaintenances::route('/'),
            'create' => CreateMaintenance::route('/create'),
            'edit' => EditMaintenance::route('/{record}/edit'),
        ];
    }
}
