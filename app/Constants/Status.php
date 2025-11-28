<?php

namespace App\Constants;

/**
 * Estados de dispositivos y componentes
 */
class Status
{
    // Estados de Dispositivos
    public const DEVICE_ACTIVE = 'Activo';
    public const DEVICE_INACTIVE = 'Inactivo';
    public const DEVICE_MAINTENANCE = 'En Mantenimiento';
    public const DEVICE_DISMANTLED = 'Desmantelado';

    // Estados de Componentes
    public const COMPONENT_OPERATIONAL = 'Operativo';
    public const COMPONENT_DAMAGED = 'Dañado';
    public const COMPONENT_IN_REPAIR = 'En Reparación';

    // Estados de Asignación (en tabla pivot componentables)
    public const ASSIGNMENT_CURRENT = 'Vigente';
    public const ASSIGNMENT_REMOVED = 'Removido';
    public const ASSIGNMENT_DISMANTLED = 'Desmantelado';

    // Estados de Transfer
    public const TRANSFER_PENDING = 'Pendiente';
    public const TRANSFER_IN_PROGRESS = 'En Proceso';
    public const TRANSFER_COMPLETED = 'Finalizado';

    // Estados de Maintenance
    public const MAINTENANCE_PENDING = 'Pendiente';
    public const MAINTENANCE_IN_PROGRESS = 'En Proceso';
    public const MAINTENANCE_COMPLETED = 'Finalizado';

    /**
     * Todos los estados de dispositivos
     */
    public static function deviceStatuses(): array
    {
        return [
            self::DEVICE_ACTIVE,
            self::DEVICE_INACTIVE,
            self::DEVICE_MAINTENANCE,
            self::DEVICE_DISMANTLED,
        ];
    }

    /**
     * Todos los estados de componentes
     */
    public static function componentStatuses(): array
    {
        return [
            self::COMPONENT_OPERATIONAL,
            self::COMPONENT_DAMAGED,
            self::COMPONENT_IN_REPAIR,
        ];
    }

    /**
     * Todos los estados de asignación
     */
    public static function assignmentStatuses(): array
    {
        return [
            self::ASSIGNMENT_CURRENT,
            self::ASSIGNMENT_REMOVED,
            self::ASSIGNMENT_DISMANTLED,
        ];
    }

    /**
     * Todos los estados de transfer
     */
    public static function transferStatuses(): array
    {
        return [
            self::TRANSFER_PENDING,
            self::TRANSFER_IN_PROGRESS,
            self::TRANSFER_COMPLETED,
        ];
    }

    /**
     * Todos los estados de maintenance
     */
    public static function maintenanceStatuses(): array
    {
        return [
            self::MAINTENANCE_PENDING,
            self::MAINTENANCE_IN_PROGRESS,
            self::MAINTENANCE_COMPLETED,
        ];
    }

    /**
     * Estados de dispositivos que no deben aparecer en transferencias
     */
    public static function excludeFromTransfers(): array
    {
        return [
            self::DEVICE_DISMANTLED,
            self::DEVICE_MAINTENANCE,
        ];
    }
}
