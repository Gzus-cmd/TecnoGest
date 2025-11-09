<?php

namespace App\Filament\Resources\AudioDevices;

use App\Filament\Resources\AudioDevices\Pages\CreateAudioDevice;
use App\Filament\Resources\AudioDevices\Pages\EditAudioDevice;
use App\Filament\Resources\AudioDevices\Pages\ListAudioDevices;
use App\Filament\Resources\AudioDevices\Schemas\AudioDeviceForm;
use App\Filament\Resources\AudioDevices\Tables\AudioDevicesTable;
use App\Models\AudioDevice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AudioDeviceResource extends Resource
{
    protected static ?string $model = AudioDevice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AudioDeviceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AudioDevicesTable::configure($table);
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
            'index' => ListAudioDevices::route('/'),
            'create' => CreateAudioDevice::route('/create'),
            'edit' => EditAudioDevice::route('/{record}/edit'),
        ];
    }
}
