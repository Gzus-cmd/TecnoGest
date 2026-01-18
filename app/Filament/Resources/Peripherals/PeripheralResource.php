<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Peripherals\Pages\CreatePeripheral;
use App\Filament\Resources\Peripherals\Pages\EditPeripheral;
use App\Filament\Resources\Peripherals\Pages\ListPeripherals;
use App\Filament\Resources\Peripherals\Schemas\PeripheralForm;
use App\Filament\Resources\Peripherals\Tables\PeripheralsTable;
use App\Models\Peripheral;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PeripheralResource extends Resource
{

    protected static ?string $model = Peripheral::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $navigationLabel = 'Periféricos';

    protected static ?string $modelLabel = 'Periférico';

    protected static ?string $pluralModelLabel = 'Periféricos';

    protected static string | UnitEnum | null $navigationGroup = 'Dispositivos';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PeripheralForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeripheralsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['location', 'computer']);
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
            'index' => ListPeripherals::route('/'),
            'create' => CreatePeripheral::route('/create'),
            'edit' => EditPeripheral::route('/{record}/edit'),
        ];
    }
}
