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

class PowerSupplyResource extends Resource
{
    protected static ?string $model = PowerSupply::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
