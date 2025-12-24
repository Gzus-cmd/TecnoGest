<?php

namespace App\Filament\Resources\Splitters;

use App\Filament\Resources\Splitters\Pages\CreateSplitter;
use App\Filament\Resources\Splitters\Pages\EditSplitter;
use App\Filament\Resources\Splitters\Pages\ListSplitters;
use App\Filament\Resources\Splitters\Schemas\SplitterForm;
use App\Filament\Resources\Splitters\Tables\SplittersTable;
use App\Models\Splitter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SplitterResource extends Resource
{

    protected static ?string $model = Splitter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewfinderCircle;

    protected static ?string $navigationLabel = 'Splitter';

    protected static ?string $modelLabel = 'Splitter';

    protected static ?string $pluralModelLabel = 'Splitters';

    protected static string | UnitEnum | null $navigationGroup = 'Catálogo de Periféricos';

    protected static ?int $navigationSort = 42;

    public static function form(Schema $schema): Schema
    {
        return SplitterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SplittersTable::configure($table);
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
            'index' => ListSplitters::route('/'),
            'create' => CreateSplitter::route('/create'),
            'edit' => EditSplitter::route('/{record}/edit'),
        ];
    }
}
