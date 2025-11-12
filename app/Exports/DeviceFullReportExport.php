<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DeviceFullReportExport implements WithMultipleSheets
{
    protected $device;
    protected $type;

    public function __construct($device, string $type)
    {
        $this->device = $device;
        $this->type = $type;
    }

    public function sheets(): array
    {
        return [
            new DeviceInfoSheet($this->device, $this->type),
            new DeviceComponentsHistorySheet($this->device, $this->type),
            new DeviceMaintenancesHistorySheet($this->device, $this->type),
            new DeviceTransfersHistorySheet($this->device, $this->type),
        ];
    }
}
