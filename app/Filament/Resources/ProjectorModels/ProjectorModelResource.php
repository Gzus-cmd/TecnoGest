<?php

namespace App\Filament\Resources\ProjectorModels;

use App\Filament\Resources\ProjectorModels\Pages\CreateProjectorModel;
use App\Filament\Resources\ProjectorModels\Pages\EditProjectorModel;
use App\Filament\Resources\ProjectorModels\Pages\ListProjectorModels;
use App\Filament\Resources\ProjectorModels\Schemas\ProjectorModelForm;
use App\Filament\Resources\ProjectorModels\Tables\ProjectorModelsTable;
use App\Models\ProjectorModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProjectorModelResource extends Resource
{

    protected static ?string $model = ProjectorModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static ?string $navigationLabel = 'Modelo de Proyector';

    protected static ?string $modelLabel = 'Modelo de Proyector';

    protected static ?string $pluralModelLabel = 'Modelos de Proyectores';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Periféricos';

    protected static ?int $navigationSort = 52;

    public static function form(Schema $schema): Schema
    {
        return ProjectorModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectorModelsTable::configure($table);
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
            'index' => ListProjectorModels::route('/'),
            'create' => CreateProjectorModel::route('/create'),
            'edit' => EditProjectorModel::route('/{record}/edit'),
        ];
    }
}
