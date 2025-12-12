# 📋 PLAN DE CORRECCIÓN DEL SISTEMA - TecnoGest

**Fecha:** 28 de Noviembre, 2025  
**Objetivo:** Corregir problemas de mantenimiento, traslados y asignación de periféricos

---

## 🎯 PROBLEMAS IDENTIFICADOS

### 1. **Traslado a taller no se ejecuta correctamente**
- El traslado se crea con `status = 'Finalizado'`
- El observer `updated()` requiere `wasChanged('status')` que es falso en creación
- **Resultado:** La ubicación del dispositivo NO se actualiza

### 2. **Periféricos despojados no aparecen en selects**
- Cuando una computadora va a taller, el periférico se marca como `'Activo'` y `computer_id = null`
- Los selects NO filtran periféricos disponibles (sin asignar)
- **Resultado:** No se pueden reasignar periféricos liberados

### 3. **Estados inconsistentes y sin estandarizar**
- Dispositivos usan: `'Activo'`, `'Inactivo'`, `'En Mantenimiento'`
- Transfers usan: `'Pendiente'`, `'En Proceso'`, `'Finalizado'`
- Maintenance usan: `'Pendiente'`, `'En Proceso'`, `'Finalizado'`
- Componentes usan: `'Operativo'`, `'Dañado'`, `'En Reparación'`
- **Resultado:** Lógica confusa y difícil de mantener

### 4. **No hay mensajes "Sin asignar" en formularios**
- Los selects no muestran estado actual de asignación
- **Resultado:** Usuario no sabe si un dispositivo/periférico está disponible

---

## 🔧 SOLUCIONES A IMPLEMENTAR

### **FASE 1: ESTANDARIZACIÓN DE ESTADOS** ✅

#### 1.1 Actualizar `app/Constants/Status.php`
```php
// Agregar estados de Transfer y Maintenance
public const TRANSFER_PENDING = 'Pendiente';
public const TRANSFER_IN_PROGRESS = 'En Proceso';
public const TRANSFER_COMPLETED = 'Finalizado';

public const MAINTENANCE_PENDING = 'Pendiente';
public const MAINTENANCE_IN_PROGRESS = 'En Proceso';
public const MAINTENANCE_COMPLETED = 'Finalizado';

public static function transferStatuses(): array
{
    return [
        self::TRANSFER_PENDING,
        self::TRANSFER_IN_PROGRESS,
        self::TRANSFER_COMPLETED,
    ];
}

public static function maintenanceStatuses(): array
{
    return [
        self::MAINTENANCE_PENDING,
        self::MAINTENANCE_IN_PROGRESS,
        self::MAINTENANCE_COMPLETED,
    ];
}
```

#### 1.2 Usar constantes en todos los modelos
**Archivos a modificar:**
- `app/Models/Computer.php`
- `app/Models/Printer.php`
- `app/Models/Projector.php`
- `app/Models/Peripheral.php`
- `app/Models/Transfer.php`
- `app/Models/Maintenance.php`

**Cambios:**
- Reemplazar strings hardcodeados por constantes
- Ejemplo: `'Activo'` → `Status::DEVICE_ACTIVE`

---

### **FASE 2: ARREGLAR TRASLADO A TALLER** ✅

#### 2.1 Modificar `Maintenance::createWorkshopTransfer()`
**Archivo:** `app/Models/Maintenance.php`

**Cambio:**
```php
// ANTES:
$transfer = Transfer::create([
    // ...
    'status' => 'Finalizado',
]);

// DESPUÉS:
$transfer = Transfer::create([
    // ...
    'status' => Status::TRANSFER_PENDING, // Crear como Pendiente
]);

// Inmediatamente cambiar a En Proceso y luego Finalizado
$transfer->update(['status' => Status::TRANSFER_IN_PROGRESS]);
$transfer->update(['status' => Status::TRANSFER_COMPLETED]);
```

**Resultado:** El observer detectará `wasChanged('status')` y actualizará la ubicación.

---

### **FASE 3: FILTROS DE PERIFÉRICOS DISPONIBLES** ✅

#### 3.1 Actualizar select de periféricos en Computer
**Archivo:** `app/Filament/Resources/Computers/Schemas/ComputerFormSimple.php`

**Agregar sección:**
```php
Section::make('Asignación de Periféricos')
    ->description('Asigne un conjunto de periféricos a esta computadora')
    ->schema([
        Select::make('peripheral_id')
            ->label('Conjunto de Periféricos')
            ->options(function (Get $get, $record) {
                $query = \App\Models\Peripheral::query()
                    ->where('status', Status::DEVICE_ACTIVE);
                
                // Si estamos editando, incluir el periférico actual
                if ($record && $record->peripheral_id) {
                    $query->where(function ($q) use ($record) {
                        $q->whereNull('computer_id')
                          ->orWhere('id', $record->peripheral_id);
                    });
                } else {
                    // Si estamos creando, solo disponibles
                    $query->whereNull('computer_id');
                }
                
                return $query->get()->mapWithKeys(function ($peripheral) use ($record) {
                    $location = $peripheral->location ? " - {$peripheral->location->name}" : '';
                    $assigned = ($record && $record->peripheral_id === $peripheral->id) ? ' (ACTUAL)' : '';
                    $available = $peripheral->computer_id ? ' [OCUPADO]' : ' [DISPONIBLE]';
                    
                    return [$peripheral->id => "{$peripheral->code}{$location}{$available}{$assigned}"];
                });
            })
            ->searchable()
            ->nullable()
            ->helperText('Seleccione un conjunto de periféricos disponible. Los periféricos [OCUPADO] ya están asignados a otra computadora.')
            ->placeholder('Sin asignar'),
    ])
```

#### 3.2 Actualizar select de computadoras en Peripheral
**Archivo:** `app/Filament/Resources/Peripherals/Schemas/PeripheralForm.php`

**Modificar línea 46:**
```php
// ANTES:
Select::make('computer_id')
    ->label('Asignado a CPU')
    ->options(Computer::with('location')->get()->mapWithKeys(function ($computer) {
        $location = $computer->location ? " - {$computer->location->name}" : '';
        return [$computer->id => "{$computer->serial}{$location}"];
    }))
    ->searchable()
    ->nullable()
    ->helperText('CPU al que está asignado (opcional)'),

// DESPUÉS:
Select::make('computer_id')
    ->label('Asignado a CPU')
    ->options(function ($record) {
        $query = Computer::query()
            ->where('status', Status::DEVICE_ACTIVE);
        
        // Si estamos editando, incluir la computadora actual
        if ($record && $record->computer_id) {
            $query->where(function ($q) use ($record) {
                $q->whereNull('peripheral_id')
                  ->orWhere('id', $record->computer_id);
            });
        } else {
            // Si estamos creando, solo disponibles
            $query->whereNull('peripheral_id');
        }
        
        return $query->with('location')->get()->mapWithKeys(function ($computer) use ($record) {
            $location = $computer->location ? " - {$computer->location->name}" : '';
            $assigned = ($record && $record->computer_id === $computer->id) ? ' (ACTUAL)' : '';
            $available = $computer->peripheral_id ? ' [TIENE PERIFÉRICO]' : ' [DISPONIBLE]';
            
            return [$computer->id => "{$computer->serial}{$location}{$available}{$assigned}"];
        });
    })
    ->searchable()
    ->nullable()
    ->helperText('Seleccione una computadora disponible. Las computadoras [TIENE PERIFÉRICO] ya tienen periféricos asignados.')
    ->placeholder('Sin asignar'),
```

---

### **FASE 4: MENSAJES "SIN ASIGNAR"** ✅

#### 4.1 Agregar placeholders y helperText
**Archivos a modificar:**
- Todos los selects de asignación en formularios

**Cambios:**
```php
->placeholder('Sin asignar')
->helperText('Descripción del estado de disponibilidad')
```

#### 4.2 Mostrar estado actual en tablas
**Archivos a modificar:**
- `app/Filament/Resources/Computers/Tables/ComputersTable.php`
- `app/Filament/Resources/Peripherals/Tables/PeripheralsTable.php`

**Agregar columnas:**
```php
TextColumn::make('peripheral_id')
    ->label('Periférico Asignado')
    ->formatStateUsing(function ($state, $record) {
        if (!$state) {
            return 'Sin asignar';
        }
        return $record->peripheral->code ?? 'Desconocido';
    }),

TextColumn::make('computer_id')
    ->label('Computadora Asignada')
    ->formatStateUsing(function ($state, $record) {
        if (!$state) {
            return 'Disponible';
        }
        return $record->computer->serial ?? 'Desconocido';
    }),
```

---

### **FASE 5: VALIDACIONES ADICIONALES** ✅

#### 5.1 Agregar validación en Computer
**Archivo:** `app/Models/Computer.php`

```php
protected static function booted(): void
{
    static::updating(function (Computer $computer) {
        // Si se asigna un periférico, validar que esté disponible
        if ($computer->isDirty('peripheral_id') && $computer->peripheral_id) {
            $peripheral = Peripheral::find($computer->peripheral_id);
            
            if (!$peripheral) {
                throw new \Exception('El periférico seleccionado no existe');
            }
            
            if ($peripheral->computer_id && $peripheral->computer_id !== $computer->id) {
                throw new \Exception('El periférico ya está asignado a otra computadora');
            }
            
            if ($peripheral->status !== Status::DEVICE_ACTIVE) {
                throw new \Exception('Solo se pueden asignar periféricos activos');
            }
        }
    });
    
    static::updated(function (Computer $computer) {
        // Sincronizar relación bidireccional
        if ($computer->wasChanged('peripheral_id')) {
            // Actualizar el periférico para que apunte a esta computadora
            if ($computer->peripheral_id) {
                $peripheral = Peripheral::find($computer->peripheral_id);
                if ($peripheral && $peripheral->computer_id !== $computer->id) {
                    $peripheral->updateQuietly(['computer_id' => $computer->id]);
                }
            }
            
            // Liberar el periférico anterior si existía
            $oldPeripheralId = $computer->getOriginal('peripheral_id');
            if ($oldPeripheralId && $oldPeripheralId !== $computer->peripheral_id) {
                $oldPeripheral = Peripheral::find($oldPeripheralId);
                if ($oldPeripheral) {
                    $oldPeripheral->updateQuietly(['computer_id' => null]);
                }
            }
        }
    });
}
```

#### 5.2 Agregar validación en Peripheral
**Archivo:** `app/Models/Peripheral.php`

```php
protected static function booted(): void
{
    static::updating(function (Peripheral $peripheral) {
        // Si se asigna a una computadora, validar que esté disponible
        if ($peripheral->isDirty('computer_id') && $peripheral->computer_id) {
            $computer = Computer::find($peripheral->computer_id);
            
            if (!$computer) {
                throw new \Exception('La computadora seleccionada no existe');
            }
            
            if ($computer->peripheral_id && $computer->peripheral_id !== $peripheral->id) {
                throw new \Exception('La computadora ya tiene un periférico asignado');
            }
            
            if ($computer->status !== Status::DEVICE_ACTIVE) {
                throw new \Exception('Solo se pueden asignar a computadoras activas');
            }
        }
    });
    
    static::updated(function (Peripheral $peripheral) {
        // Sincronizar relación bidireccional
        if ($peripheral->wasChanged('computer_id')) {
            // Actualizar la computadora para que apunte a este periférico
            if ($peripheral->computer_id) {
                $computer = Computer::find($peripheral->computer_id);
                if ($computer && $computer->peripheral_id !== $peripheral->id) {
                    $computer->updateQuietly(['peripheral_id' => $peripheral->id]);
                }
            }
            
            // Liberar la computadora anterior si existía
            $oldComputerId = $peripheral->getOriginal('computer_id');
            if ($oldComputerId && $oldComputerId !== $peripheral->computer_id) {
                $oldComputer = Computer::find($oldComputerId);
                if ($oldComputer) {
                    $oldComputer->updateQuietly(['peripheral_id' => null]);
                }
            }
        }
    });
}
```

---

## 📊 RESUMEN DE ARCHIVOS A MODIFICAR

### Constantes
- ✅ `app/Constants/Status.php` - Agregar estados de Transfer y Maintenance

### Modelos
- ✅ `app/Models/Computer.php` - Usar constantes + validaciones + sync bidireccional
- ✅ `app/Models/Peripheral.php` - Usar constantes + validaciones + sync bidireccional
- ✅ `app/Models/Printer.php` - Usar constantes
- ✅ `app/Models/Projector.php` - Usar constantes
- ✅ `app/Models/Transfer.php` - Usar constantes
- ✅ `app/Models/Maintenance.php` - Usar constantes + arreglar traslado

### Formularios
- ✅ `app/Filament/Resources/Computers/Schemas/ComputerForm.php` - Agregar select de periféricos
- ✅ `app/Filament/Resources/Computers/Schemas/ComputerFormSimple.php` - Agregar select de periféricos
- ✅ `app/Filament/Resources/Peripherals/Schemas/PeripheralForm.php` - Mejorar select de computadoras

### Tablas
- ✅ `app/Filament/Resources/Computers/Tables/ComputersTable.php` - Agregar columna periférico
- ✅ `app/Filament/Resources/Peripherals/Tables/PeripheralsTable.php` - Agregar columna computadora

---

## ✅ CRITERIOS DE ÉXITO

1. **Traslado funciona:**
   - Al crear mantenimiento con `requires_workshop = true`
   - El dispositivo cambia de ubicación al taller
   - Se crea un registro de Transfer

2. **Periféricos disponibles:**
   - Los periféricos sin asignar aparecen en el select
   - Los periféricos despojados por mantenimiento aparecen disponibles
   - Se muestra claramente el estado [DISPONIBLE] o [OCUPADO]

3. **Estados consistentes:**
   - Todos los modelos usan constantes de `Status`
   - No hay strings hardcodeados
   - Código más mantenible

4. **UX mejorada:**
   - Mensajes "Sin asignar" y "Disponible"
   - Información clara sobre disponibilidad
   - Prevención de asignaciones duplicadas

---

## 🔄 ORDEN DE IMPLEMENTACIÓN

1. **FASE 1:** Estandarización de estados (sin romper nada)
2. **FASE 2:** Arreglar traslado a taller (crítico)
3. **FASE 3:** Filtros de periféricos (mejora UX)
4. **FASE 4:** Mensajes "Sin asignar" (mejora UX)
5. **FASE 5:** Validaciones (prevención de errores)
6. **PRUEBAS:** Validar flujo completo

---

**Tiempo estimado:** 2-3 horas  
**Impacto:** Alto - Resuelve problemas críticos del sistema
