<x-filament-panels::page>
    @php
        $dbInfo = $this->getDatabaseInfo();
    @endphp

    {{-- Información de la Base de Datos Actual --}}
    <x-filament::section>
        <x-slot name="heading">
            Conexión Activa: {{ $dbInfo['driver_name'] }}
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::badge color="success" icon="heroicon-o-check-circle">
                Conectado
            </x-filament::badge>
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <x-filament::fieldset>
                <x-slot name="label">Host</x-slot>
                {{ $dbInfo['host'] ?? 'N/A' }}
            </x-filament::fieldset>

            <x-filament::fieldset>
                <x-slot name="label">Base de Datos</x-slot>
                {{ $dbInfo['database'] }}
            </x-filament::fieldset>

            <x-filament::fieldset>
                <x-slot name="label">Conexión</x-slot>
                {{ $dbInfo['connection'] }}
            </x-filament::fieldset>
        </div>
    </x-filament::section>

    <style>
        .backup-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        @media (min-width: 1024px) {
            .backup-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .backup-section-content>*+* {
            margin-top: 1rem;
        }
    </style>

    <div class="backup-grid">
        {{-- Sección de Exportar --}}
        <x-filament::section icon="heroicon-o-arrow-down-tray" icon-color="success">
            <x-slot name="heading">
                Exportar Base de Datos
            </x-slot>

            <x-slot name="description">
                Generar backup de {{ $dbInfo['driver_name'] }}
            </x-slot>

            <div class="backup-section-content">
                <p>Genera un archivo SQL con toda la información de la base de datos.</p>

                <x-filament::fieldset>
                    <x-slot name="label">Archivo generado</x-slot>
                    <code
                        style="background: rgba(0,0,0,0.3); padding: 0.25rem 0.5rem; border-radius: 0.375rem;">backup_{{ strtolower($dbInfo['driver']) }}_YYYY-MM-DD_HHMMSS.sql</code>
                </x-filament::fieldset>

                <div style="margin-top: 1.5rem;">
                    <x-filament::button wire:click="exportDatabase" color="success" icon="heroicon-o-arrow-down-tray"
                        outlined>
                        Exportar {{ $dbInfo['driver_name'] }}
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Sección de Importar --}}
        <x-filament::section icon="heroicon-o-arrow-up-tray" icon-color="danger">
            <x-slot name="heading">
                Importar Base de Datos
            </x-slot>

            <x-slot name="description">
                Restaurar desde archivo SQL
            </x-slot>

            <div class="backup-section-content">
                <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="danger">
                    <x-slot name="heading">⚠️ ADVERTENCIA</x-slot>
                    <p>Esta acción reemplazará TODA la información actual. Esta operación NO se puede deshacer.</p>
                </x-filament::section>

                <form wire:submit.prevent="importDatabase" enctype="multipart/form-data">
                    <x-filament::fieldset>
                        <x-slot name="label">Seleccionar archivo SQL</x-slot>
                        <div style="position: relative;">
                            <input type="file" name="sql_file" id="sql_file" accept=".sql" class="fi-input"
                                style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer;"
                                onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'Ningún archivo seleccionado';"
                                required />
                            <x-filament::button type="button" color="gray" icon="heroicon-o-folder-open" outlined
                                style="pointer-events: none;">
                                Examinar archivos
                            </x-filament::button>
                        </div>
                        <p id="file-name" style="margin-top: 0.5rem; font-size: 0.875rem; color: #9ca3af;">Ningún
                            archivo seleccionado</p>
                    </x-filament::fieldset>

                    <div style="margin-top: 1.5rem;">
                        <x-filament::button type="submit" color="danger" icon="heroicon-o-arrow-up-tray" outlined>
                            Importar a {{ $dbInfo['driver_name'] }}
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </x-filament::section>
    </div>

    {{-- Historial de Backups --}}
    <x-filament::section icon="heroicon-o-document-text" icon-color="primary">
        <x-slot name="heading">
            Historial de Backups
        </x-slot>

        <x-slot name="description">
            Últimos 10 backups generados
        </x-slot>

        @php
            $lastBackups = $this->getLastBackups();
        @endphp

        @if (empty($lastBackups))
            <x-filament::section>
                <x-filament::icon icon="heroicon-o-archive-box-x-mark" />
                <p>No hay backups disponibles</p>
            </x-filament::section>
        @else
            <div class="fi-ta-ctn">
                <table class="fi-ta-table">
                    <thead>
                        <tr>
                            <th class="fi-ta-header-cell">
                                Archivo
                            </th>
                            <th class="fi-ta-header-cell">
                                Tamaño
                            </th>
                            <th class="fi-ta-header-cell">
                                Fecha
                            </th>
                            <th class="fi-ta-header-cell">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lastBackups as $backup)
                            <tr>
                                <td class="fi-ta-cell">
                                    <x-filament::icon icon="heroicon-o-document" />
                                    {{ $backup['name'] }}
                                </td>
                                <td class="fi-ta-cell">
                                    <x-filament::badge>
                                        {{ $backup['size'] }}
                                    </x-filament::badge>
                                </td>
                                <td class="fi-ta-cell">
                                    {{ $backup['date'] }}
                                </td>
                                <td class="fi-ta-cell">
                                    <x-filament::button wire:click="downloadBackup('{{ $backup['name'] }}')"
                                        color="primary" size="sm" icon="heroicon-o-arrow-down-tray">
                                        Descargar
                                    </x-filament::button>

                                    <x-filament::button wire:click="deleteBackup('{{ $backup['name'] }}')"
                                        wire:confirm="¿Estás seguro de eliminar este backup?" color="danger"
                                        size="sm" icon="heroicon-o-trash">
                                        Eliminar
                                    </x-filament::button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>

    {{-- Información adicional --}}
    <x-filament::section icon="heroicon-o-information-circle" icon-color="info">
        <x-slot name="heading">
            Información Importante
        </x-slot>

        <ul style="list-style: none; padding: 0; space-y: 0.75rem;">
            <li style="margin-bottom: 0.75rem;"><x-filament::icon icon="heroicon-o-check-circle" color="success" /> Los
                backups se almacenan en:
                <code>storage/app/backups/</code>
            </li>
            <li><x-filament::icon icon="heroicon-o-check-circle" color="success" /> Se recomienda realizar backups
                periódicos antes de actualizaciones importantes</li>
            <li><x-filament::icon icon="heroicon-o-check-circle" color="success" /> Soporte para <strong>MySQL</strong>,
                <strong>PostgreSQL</strong> y <strong>SQLite</strong>
            </li>
            <li><x-filament::icon icon="heroicon-o-check-circle" color="success" /> Guarda copias de seguridad en
                ubicaciones externas al servidor</li>
            <li><x-filament::icon icon="heroicon-o-check-circle" color="success" /> Los archivos de backup pueden ser
                grandes, asegúrate de tener suficiente espacio en disco</li>
        </ul>
    </x-filament::section>
</x-filament-panels::page>
