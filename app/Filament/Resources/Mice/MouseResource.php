<?php

namespace App\Filament\Resources\Mice;

use App\Filament\Resources\Mice\Pages\CreateMouse;
use App\Filament\Resources\Mice\Pages\EditMouse;
use App\Filament\Resources\Mice\Pages\ListMice;
use App\Filament\Resources\Mice\Schemas\MouseForm;
use App\Filament\Resources\Mice\Tables\MiceTable;
use App\Models\Mouse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MouseResource extends Resource
{
    protected static ?string $model = Mouse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MiceTable::configure($table);
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
            'index' => ListMice::route('/'),
            'create' => CreateMouse::route('/create'),
            'edit' => EditMouse::route('/{record}/edit'),
        ];
    }
}
