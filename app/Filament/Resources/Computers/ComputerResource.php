<?php

namespace App\Filament\Resources\Computers;

use App\Filament\Resources\Computers\Pages\CreateComputer;
use App\Filament\Resources\Computers\Pages\EditComputer;
use App\Filament\Resources\Computers\Pages\ListComputers;
use App\Filament\Resources\Computers\Schemas\ComputerForm;
use App\Filament\Resources\Computers\Schemas\ComputerFormSimple;
use App\Filament\Resources\Computers\Tables\ComputersTable;
use App\Models\Computer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ComputerResource extends Resource
{

    protected static ?string $model = Computer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $navigationLabel = 'Computadora';

    protected static ?string $modelLabel = 'Computadora';

    protected static ?string $pluralModelLabel = 'Computadoras';

    protected static ?string $recordTitleAttribute = 'serial';

    protected static int $globalSearchResultsLimit = 5;

    protected static string | UnitEnum | null $navigationGroup = 'Dispositivos';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['serial'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "Computadora: {$record->serial}";
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
        $livewire = $schema->getLivewire();
        
        if ($livewire instanceof EditComputer) {
            return ComputerFormSimple::configure($schema);
        }
        
        return ComputerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComputersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['location', 'os', 'peripheral']);
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
            'index' => ListComputers::route('/'),
            'create' => CreateComputer::route('/create'),
            'edit' => EditComputer::route('/{record}/edit'),
        ];
    }
}
