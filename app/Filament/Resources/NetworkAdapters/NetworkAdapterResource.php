<?php

namespace App\Filament\Resources\NetworkAdapters;

use App\Filament\Resources\NetworkAdapters\Pages\CreateNetworkAdapter;
use App\Filament\Resources\NetworkAdapters\Pages\EditNetworkAdapter;
use App\Filament\Resources\NetworkAdapters\Pages\ListNetworkAdapters;
use App\Filament\Resources\NetworkAdapters\Schemas\NetworkAdapterForm;
use App\Filament\Resources\NetworkAdapters\Tables\NetworkAdaptersTable;
use App\Models\NetworkAdapter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NetworkAdapterResource extends Resource
{

    protected static ?string $model = NetworkAdapter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWifi;

    protected static ?string $navigationLabel = 'Adaptador de Red';

    protected static ?string $modelLabel = 'Adaptador de Red';

    protected static ?string $pluralModelLabel = 'Adaptadores de Red';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Periféricos';

    protected static ?int $navigationSort = 36;

    public static function form(Schema $schema): Schema
    {
        return NetworkAdapterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NetworkAdaptersTable::configure($table);
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
            'index' => ListNetworkAdapters::route('/'),
            'create' => CreateNetworkAdapter::route('/create'),
            'edit' => EditNetworkAdapter::route('/{record}/edit'),
        ];
    }
}
