<?php

namespace App\Filament\Resources\Components;

use App\Filament\Resources\Components\Pages\CreateComponent;
use App\Filament\Resources\Components\Pages\EditComponent;
use App\Filament\Resources\Components\Pages\ListComponents;
use App\Filament\Resources\Components\Schemas\ComponentForm;
use App\Filament\Resources\Components\Tables\ComponentsTable;
use App\Models\Component;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ComponentResource extends Resource
{

    protected static ?string $model = Component::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PuzzlePiece;

    protected static ?string $navigationLabel = 'Componentes y Repuestos';

    protected static ?string $modelLabel = 'Componente';

    protected static ?string $pluralModelLabel = 'Componentes';

    protected static ?string $recordTitleAttribute = 'serial';

    protected static string | UnitEnum | null $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return ComponentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComponentsTable::configure($table)
            ->recordAction(null)
            ->recordUrl(null);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['provider', 'componentable'])
            ->withCount(['computers', 'printers', 'projectors']);
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
            'index' => ListComponents::route('/'),
            'create' => CreateComponent::route('/create'),
            'edit' => EditComponent::route('/{record}/edit'),
        ];
    }
}
