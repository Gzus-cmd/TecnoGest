<?php

namespace App\Filament\Resources\Motherboards;

use App\Filament\Resources\Motherboards\Pages\CreateMotherboard;
use App\Filament\Resources\Motherboards\Pages\EditMotherboard;
use App\Filament\Resources\Motherboards\Pages\ListMotherboards;
use App\Filament\Resources\Motherboards\Schemas\MotherboardForm;
use App\Filament\Resources\Motherboards\Tables\MotherboardsTable;
use App\Models\Motherboard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MotherboardResource extends Resource
{

    protected static ?string $model = Motherboard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $navigationLabel = 'Placa Base';

    protected static ?string $modelLabel = 'Placa Base';

    protected static ?string $pluralModelLabel = 'Placas Base';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Hardware';

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return MotherboardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MotherboardsTable::configure($table);
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
            'index' => ListMotherboards::route('/'),
            'create' => CreateMotherboard::route('/create'),
            'edit' => EditMotherboard::route('/{record}/edit'),
        ];
    }
}
