<?php

namespace App\Constants;

use App\Models\AudioDevice;
use App\Models\CPU;
use App\Models\GPU;
use App\Models\Keyboard;
use App\Models\Monitor;
use App\Models\Motherboard;
use App\Models\Mouse;
use App\Models\NetworkAdapter;
use App\Models\PowerSupply;
use App\Models\RAM;
use App\Models\ROM;
use App\Models\SparePart;
use App\Models\Splitter;
use App\Models\Stabilizer;
use App\Models\TowerCase;

/**
 * Constantes para tipos de componentes
 * Evita repetir strings de nombres de clases en todo el código
 */
class ComponentTypes
{
    // Hardware Principal
    public const MOTHERBOARD = Motherboard::class;
    public const CPU = CPU::class;
    public const GPU = GPU::class;
    public const RAM = RAM::class;
    public const ROM = ROM::class;
    public const POWER_SUPPLY = PowerSupply::class;
    public const TOWER_CASE = TowerCase::class;

    // Periféricos
    public const MONITOR = Monitor::class;
    public const KEYBOARD = Keyboard::class;
    public const MOUSE = Mouse::class;
    public const NETWORK_ADAPTER = NetworkAdapter::class;
    public const AUDIO_DEVICE = AudioDevice::class;
    public const SPLITTER = Splitter::class;
    public const STABILIZER = Stabilizer::class;

    // Repuestos
    public const SPARE_PART = SparePart::class;

    /**
     * Componentes de hardware principal
     */
    public static function hardwareComponents(): array
    {
        return [
            self::CPU,
            self::MOTHERBOARD,
            self::GPU,
            self::RAM,
            self::ROM,
            self::POWER_SUPPLY,
            self::TOWER_CASE,
        ];
    }

    /**
     * Componentes periféricos
     */
    public static function peripheralComponents(): array
    {
        return [
            self::MONITOR,
            self::KEYBOARD,
            self::MOUSE,
            self::SPLITTER,
            self::AUDIO_DEVICE,
            self::NETWORK_ADAPTER,
        ];
    }

    /**
     * Todos los tipos de componentes excepto repuestos
     */
    public static function allExceptSpareParts(): array
    {
        return array_merge(
            self::hardwareComponents(),
            self::peripheralComponents()
        );
    }

    /**
     * Mapeo de tipos a nombres en español
     */
    public static function typeNames(): array
    {
        return [
            self::CPU => 'Procesador',
            self::GPU => 'Tarjeta Gráfica',
            self::RAM => 'Memoria RAM',
            self::ROM => 'Almacenamiento',
            self::POWER_SUPPLY => 'Fuente de Poder',
            self::NETWORK_ADAPTER => 'Adaptador de Red',
            self::MOTHERBOARD => 'Placa Base',
            self::MONITOR => 'Monitor',
            self::KEYBOARD => 'Teclado',
            self::MOUSE => 'Ratón',
            self::STABILIZER => 'Estabilizador',
            self::TOWER_CASE => 'Gabinete',
            self::SPLITTER => 'Splitter',
            self::AUDIO_DEVICE => 'Dispositivo de Audio',
            self::SPARE_PART => 'Repuesto',
        ];
    }
}
