<?php

namespace App\Filament\Resources\Keyboards;

use App\Filament\Resources\Keyboards\Pages\CreateKeyboard;
use App\Filament\Resources\Keyboards\Pages\EditKeyboard;
use App\Filament\Resources\Keyboards\Pages\ListKeyboards;
use App\Filament\Resources\Keyboards\Schemas\KeyboardForm;
use App\Filament\Resources\Keyboards\Tables\KeyboardsTable;
use App\Models\Keyboard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KeyboardResource extends Resource
{
    protected static ?string $model = Keyboard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return KeyboardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KeyboardsTable::configure($table);
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
            'index' => ListKeyboards::route('/'),
            'create' => CreateKeyboard::route('/create'),
            'edit' => EditKeyboard::route('/{record}/edit'),
        ];
    }
}
