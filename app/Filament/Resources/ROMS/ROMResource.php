<?php

namespace App\Filament\Resources\ROMS;

use App\Filament\Resources\ROMS\Pages\CreateROM;
use App\Filament\Resources\ROMS\Pages\EditROM;
use App\Filament\Resources\ROMS\Pages\ListROMS;
use App\Filament\Resources\ROMS\Schemas\ROMForm;
use App\Filament\Resources\ROMS\Tables\ROMSTable;
use App\Models\ROM;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ROMResource extends Resource
{

    protected static ?string $model = ROM::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServer;

    protected static ?string $navigationLabel = 'Almacenamiento';

    protected static ?string $modelLabel = 'Almacenamiento';

    protected static ?string $pluralModelLabel = 'Almacenamientos';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Hardware';

    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return ROMForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ROMSTable::configure($table);
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
            'index' => ListROMS::route('/'),
            'create' => CreateROM::route('/create'),
            'edit' => EditROM::route('/{record}/edit'),
        ];
    }
}
