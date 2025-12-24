<?php

namespace App\Filament\Resources\Printers;

use App\Filament\Resources\Printers\Pages\CreatePrinter;
use App\Filament\Resources\Printers\Pages\EditPrinter;
use App\Filament\Resources\Printers\Pages\ListPrinters;
use App\Filament\Resources\Printers\Schemas\PrinterForm;
use App\Filament\Resources\Printers\Schemas\PrinterFormSimple;
use App\Filament\Resources\Printers\Tables\PrintersTable;
use App\Models\Printer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PrinterResource extends Resource
{

    protected static ?string $model = Printer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPrinter;

    protected static ?string $navigationLabel = 'Impresoras';

    protected static ?string $recordTitleAttribute = 'serial';

    protected static int $globalSearchResultsLimit = 5;

    protected static string | UnitEnum | null $navigationGroup = 'Dispositivos';

    protected static ?int $navigationSort = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['serial'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "Impresora: {$record->serial}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Ubicación' => $record->location->name ?? 'Sin ubicación',
            'Estado' => $record->status,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        // Usar formulario completo para crear, simplificado para editar
        $page = request()->route()?->getActionMethod();
        if ($page === 'edit') {
            return PrinterFormSimple::configure($schema);
        }
        return PrinterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['location', 'modelo']);
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
            'index' => ListPrinters::route('/'),
            'create' => CreatePrinter::route('/create'),
            'edit' => EditPrinter::route('/{record}/edit'),
        ];
    }
}
