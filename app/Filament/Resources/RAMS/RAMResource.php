<?php

namespace App\Filament\Resources\RAMS;

use App\Filament\Resources\RAMS\Pages\CreateRAM;
use App\Filament\Resources\RAMS\Pages\EditRAM;
use App\Filament\Resources\RAMS\Pages\ListRAMS;
use App\Filament\Resources\RAMS\Schemas\RAMForm;
use App\Filament\Resources\RAMS\Tables\RAMSTable;
use App\Models\RAM;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RAMResource extends Resource
{

    protected static ?string $model = RAM::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;

    protected static ?string $navigationLabel = 'Memoria RAM';

    protected static ?string $modelLabel = 'Memoria RAM';

    protected static ?string $pluralModelLabel = 'Memorias RAM';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Hardware';

    protected static ?int $navigationSort = 23;

    public static function form(Schema $schema): Schema
    {
        return RAMForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RAMSTable::configure($table);
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
            'index' => ListRAMS::route('/'),
            'create' => CreateRAM::route('/create'),
            'edit' => EditRAM::route('/{record}/edit'),
        ];
    }
}
