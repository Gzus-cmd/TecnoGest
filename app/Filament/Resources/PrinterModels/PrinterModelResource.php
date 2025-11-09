<?php

namespace App\Filament\Resources\PrinterModels;

use App\Filament\Resources\PrinterModels\Pages\CreatePrinterModel;
use App\Filament\Resources\PrinterModels\Pages\EditPrinterModel;
use App\Filament\Resources\PrinterModels\Pages\ListPrinterModels;
use App\Filament\Resources\PrinterModels\Schemas\PrinterModelForm;
use App\Filament\Resources\PrinterModels\Tables\PrinterModelsTable;
use App\Models\PrinterModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PrinterModelResource extends Resource
{
    protected static ?string $model = PrinterModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPrinter;

    protected static ?string $navigationLabel = 'Modelos de Impresoras';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Componentes';

    public static function form(Schema $schema): Schema
    {
        return PrinterModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrinterModelsTable::configure($table);
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
            'index' => ListPrinterModels::route('/'),
            'create' => CreatePrinterModel::route('/create'),
            'edit' => EditPrinterModel::route('/{record}/edit'),
        ];
    }
}
