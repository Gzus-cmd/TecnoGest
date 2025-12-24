<?php

namespace App\Filament\Resources\Projectors;

use App\Filament\Resources\Projectors\Pages\CreateProjector;
use App\Filament\Resources\Projectors\Pages\EditProjector;
use App\Filament\Resources\Projectors\Pages\ListProjectors;
use App\Filament\Resources\Projectors\Schemas\ProjectorForm;
use App\Filament\Resources\Projectors\Schemas\ProjectorFormSimple;
use App\Filament\Resources\Projectors\Tables\ProjectorsTable;
use App\Models\Projector;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ProjectorResource extends Resource
{

    protected static ?string $model = Projector::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static ?string $navigationLabel = 'Proyector';

    protected static ?string $modelLabel = 'Proyector';

    protected static ?string $pluralModelLabel = 'Proyectores';

    protected static ?string $recordTitleAttribute = 'serial';

    protected static int $globalSearchResultsLimit = 5;

    protected static string | UnitEnum | null $navigationGroup = 'Dispositivos';

    protected static ?int $navigationSort = 3;

    public static function getGloballySearchableAttributes(): array
    {
        return ['serial'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "Proyector: {$record->serial}";
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
            return ProjectorFormSimple::configure($schema);
        }
        return ProjectorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectorsTable::configure($table);
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
            'index' => ListProjectors::route('/'),
            'create' => CreateProjector::route('/create'),
            'edit' => EditProjector::route('/{record}/edit'),
        ];
    }
}
