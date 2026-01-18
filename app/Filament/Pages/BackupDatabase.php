<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class BackupDatabase extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected string $view = 'filament.pages.backup-database';

    protected static ?string $navigationLabel = 'Backup Base de Datos';

    protected static ?string $title = 'Backup y Restauración de Base de Datos';

    protected static string|UnitEnum|null $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_BackupDatabase') ?? false;
    }

    public function getDatabaseInfo(): array
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        return [
            'connection' => $connection,
            'driver' => $driver,
            'driver_name' => match ($driver) {
                'mysql' => 'MySQL',
                'pgsql' => 'PostgreSQL',
                'sqlite' => 'SQLite',
                default => ucfirst($driver),
            },
            'database' => config("database.connections.{$connection}.database"),
            'host' => config("database.connections.{$connection}.host", 'localhost'),
        ];
    }

    public function exportDatabase(): BinaryFileResponse|StreamedResponse
    {
        try {
            $dbInfo = $this->getDatabaseInfo();
            $driver = $dbInfo['driver'];
            $connection = $dbInfo['connection'];

            $timestamp = now()->format('Y-m-d_His');
            $filename = "backup_{$driver}_{$timestamp}.sql";

            $dbName = config("database.connections.{$connection}.database");
            $dbUser = config("database.connections.{$connection}.username");
            $dbPassword = config("database.connections.{$connection}.password");
            $dbHost = config("database.connections.{$connection}.host", 'localhost');
            $dbPort = config("database.connections.{$connection}.port");

            // Crear directorio de backups si no existe
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filepath = $backupPath . '/' . $filename;

            // Comando según el driver
            if ($driver === 'mysql') {
                // MySQL / MariaDB
                $command = sprintf(
                    'MYSQL_PWD=%s mysqldump -h %s -P %s -u %s --single-transaction --quick --lock-tables=false %s > %s 2>&1',
                    escapeshellarg($dbPassword),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbUser),
                    escapeshellarg($dbName),
                    escapeshellarg($filepath)
                );
            } elseif ($driver === 'pgsql') {
                // PostgreSQL
                $command = sprintf(
                    'PGPASSWORD=%s pg_dump -h %s -p %s -U %s -F p -b -v -f %s %s 2>&1',
                    escapeshellarg($dbPassword),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbUser),
                    escapeshellarg($filepath),
                    escapeshellarg($dbName)
                );
            } elseif ($driver === 'sqlite') {
                // SQLite - simplemente copiar el archivo
                $dbPath = $dbName;
                if (file_exists($dbPath)) {
                    copy($dbPath, $filepath);
                    $returnVar = 0;
                } else {
                    $returnVar = 1;
                    $output = ['Base de datos SQLite no encontrada'];
                }
            } else {
                throw new \Exception("Driver de base de datos no soportado: {$driver}");
            }

            // Ejecutar el comando (excepto para SQLite que ya se copió)
            if ($driver !== 'sqlite') {
                exec($command, $output, $returnVar);
            }

            if ($returnVar !== 0) {
                $errorMessage = implode("\n", $output ?? []);

                Notification::make()
                    ->title('Error al exportar base de datos')
                    ->danger()
                    ->body('Error: ' . ($errorMessage ?: "Verifica que las herramientas de {$dbInfo['driver_name']} estén instaladas."))
                    ->send();

                return response()->streamDownload(function () {}, '');
            }

            Notification::make()
                ->title("Base de datos exportada exitosamente ({$dbInfo['driver_name']})")
                ->success()
                ->body("El backup de {$dbInfo['driver_name']} se ha generado correctamente.")
                ->send();

            return response()->download($filepath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al exportar')
                ->danger()
                ->body($e->getMessage())
                ->send();

            return response()->streamDownload(function () {}, '');
        }
    }

    public function importDatabase()
    {
        try {
            // Validar que hay un archivo subido
            if (!request()->hasFile('sql_file')) {
                Notification::make()
                    ->title('Error')
                    ->danger()
                    ->body('No se ha seleccionado ningún archivo.')
                    ->send();
                return;
            }

            $file = request()->file('sql_file');

            // Validar extensión
            if ($file->getClientOriginalExtension() !== 'sql') {
                Notification::make()
                    ->title('Error')
                    ->danger()
                    ->body('El archivo debe tener extensión .sql')
                    ->send();
                return;
            }

            $dbInfo = $this->getDatabaseInfo();
            $driver = $dbInfo['driver'];
            $connection = $dbInfo['connection'];

            $dbName = config("database.connections.{$connection}.database");
            $dbUser = config("database.connections.{$connection}.username");
            $dbPassword = config("database.connections.{$connection}.password");
            $dbHost = config("database.connections.{$connection}.host", 'localhost');
            $dbPort = config("database.connections.{$connection}.port");

            // Guardar temporalmente el archivo
            $tempPath = storage_path('app/temp_import.sql');
            $file->move(storage_path('app'), 'temp_import.sql');

            // Comando según el driver
            if ($driver === 'mysql') {
                $command = sprintf(
                    'MYSQL_PWD=%s mysql -h %s -P %s -u %s %s < %s 2>&1',
                    escapeshellarg($dbPassword),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbUser),
                    escapeshellarg($dbName),
                    escapeshellarg($tempPath)
                );
            } elseif ($driver === 'pgsql') {
                $command = sprintf(
                    'PGPASSWORD=%s psql -h %s -p %s -U %s -d %s -f %s 2>&1',
                    escapeshellarg($dbPassword),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbUser),
                    escapeshellarg($dbName),
                    escapeshellarg($tempPath)
                );
            } elseif ($driver === 'sqlite') {
                // Para SQLite, reemplazar la base de datos
                $dbPath = $dbName;
                if (file_exists($tempPath)) {
                    copy($tempPath, $dbPath);
                    $returnVar = 0;
                } else {
                    $returnVar = 1;
                }
            } else {
                throw new \Exception("Driver de base de datos no soportado: {$driver}");
            }

            // Ejecutar el comando
            if ($driver !== 'sqlite') {
                exec($command, $output, $returnVar);
            }

            // Eliminar archivo temporal
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            if ($returnVar !== 0) {
                $errorMessage = implode("\n", $output ?? []);

                Notification::make()
                    ->title('Error al importar base de datos')
                    ->danger()
                    ->body('Error: ' . ($errorMessage ?: 'Verifica el formato del archivo SQL.'))
                    ->send();
                return;
            }

            Notification::make()
                ->title("Base de datos importada exitosamente ({$dbInfo['driver_name']})")
                ->success()
                ->body("La base de datos {$dbInfo['driver_name']} se ha restaurado correctamente.")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al importar base de datos')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function getLastBackups(): array
    {
        $backupPath = storage_path('app/backups');

        if (!is_dir($backupPath)) {
            return [];
        }

        $files = glob($backupPath . '/backup_*.sql');

        // Ordenar por fecha de modificación descendente
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Tomar solo los últimos 10
        $files = array_slice($files, 0, 10);

        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => $this->formatBytes(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'path' => $file,
            ];
        }

        return $backups;
    }

    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function downloadBackup(string $filename): BinaryFileResponse|StreamedResponse
    {
        $filepath = storage_path('app/backups/' . $filename);

        if (!file_exists($filepath)) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('El archivo no existe.')
                ->send();

            return response()->streamDownload(function () {}, '');
        }

        return response()->download($filepath);
    }

    public function deleteBackup(string $filename): void
    {
        $filepath = storage_path('app/backups/' . $filename);

        if (file_exists($filepath)) {
            unlink($filepath);

            Notification::make()
                ->title('Backup eliminado')
                ->success()
                ->send();
        }
    }
}
