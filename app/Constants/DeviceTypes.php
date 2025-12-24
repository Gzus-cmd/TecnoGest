<?php

namespace App\Constants;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Projector;

/**
 * Constantes para tipos de dispositivos
 */
class DeviceTypes
{
    public const COMPUTER = Computer::class;
    public const PRINTER = Printer::class;
    public const PROJECTOR = Projector::class;

    /**
     * Todos los tipos de dispositivos
     */
    public static function all(): array
    {
        return [
            self::COMPUTER,
            self::PRINTER,
            self::PROJECTOR,
        ];
    }

    /**
     * Mapeo de tipos a nombres en español
     */
    public static function typeNames(): array
    {
        return [
            self::COMPUTER => 'Computadora',
            self::PRINTER => 'Impresora',
            self::PROJECTOR => 'Proyector',
        ];
    }
}
