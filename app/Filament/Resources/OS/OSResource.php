<?php

namespace App\Filament\Resources\OS;

use App\Filament\Resources\OS\Pages\CreateOS;
use App\Filament\Resources\OS\Pages\EditOS;
use App\Filament\Resources\OS\Pages\ListOS;
use App\Filament\Resources\OS\Schemas\OSForm;
use App\Filament\Resources\OS\Tables\OSTable;
use App\Models\OS;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OSResource extends Resource
{
    protected static ?string $model = OS::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Sistemas Operativos';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Componentes';

    public static function form(Schema $schema): Schema
    {
        return OSForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OSTable::configure($table);
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
            'index' => ListOS::route('/'),
            'create' => CreateOS::route('/create'),
            'edit' => EditOS::route('/{record}/edit'),
        ];
    }
}
