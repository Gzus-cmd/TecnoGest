<?php

namespace App\Filament\Resources\PowerSupplies;

use App\Filament\Resources\PowerSupplies\Pages\CreatePowerSupply;
use App\Filament\Resources\PowerSupplies\Pages\EditPowerSupply;
use App\Filament\Resources\PowerSupplies\Pages\ListPowerSupplies;
use App\Filament\Resources\PowerSupplies\Schemas\PowerSupplyForm;
use App\Filament\Resources\PowerSupplies\Tables\PowerSuppliesTable;
use App\Models\PowerSupply;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PowerSupplyResource extends Resource
{

    protected static ?string $model = PowerSupply::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = 'Fuente de Poder';

    protected static ?string $modelLabel = 'Fuente de Poder';

    protected static ?string $pluralModelLabel = 'Fuentes de Poder';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Hardware';

    protected static ?int $navigationSort = 26;

    public static function form(Schema $schema): Schema
    {
        return PowerSupplyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PowerSuppliesTable::configure($table);
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
            'index' => ListPowerSupplies::route('/'),
            'create' => CreatePowerSupply::route('/create'),
            'edit' => EditPowerSupply::route('/{record}/edit'),
        ];
    }
}
