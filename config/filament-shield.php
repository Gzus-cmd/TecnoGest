<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Shield Resource
    |--------------------------------------------------------------------------
    |
    | Here you may configure the built-in role management resource. You can
    | customize the URL, choose whether to show model paths, group it under
    | a cluster, and decide which permission tabs to display.
    |
    */

    'shield_resource' => [
        'slug' => 'shield/roles',
        'show_model_path' => true,
        'cluster' => null,
        'tabs' => [
            'pages' => true,
            'widgets' => true,
            'resources' => true,
            'custom_permissions' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy
    |--------------------------------------------------------------------------
    |
    | When your application supports teams, Shield will automatically detect
    | and configure the tenant model during setup. This enables tenant-scoped
    | roles and permissions throughout your application.
    |
    */

    'tenant_model' => null,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This value contains the class name of your user model. This model will
    | be used for role assignments and must implement the HasRoles trait
    | provided by the Spatie\Permission package.
    |
    */

    'auth_provider_model' => 'App\\Models\\User',

    /*
    |--------------------------------------------------------------------------
    | Super Admin
    |--------------------------------------------------------------------------
    |
    | Here you may define a super admin that has unrestricted access to your
    | application. You can choose to implement this via Laravel's gate system
    | or as a traditional role with all permissions explicitly assigned.
    |
    */

    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
        'define_via_gate' => true,
        'intercept_gate' => 'before',
    ],

    /*
    |--------------------------------------------------------------------------
    | Panel User
    |--------------------------------------------------------------------------
    |
    | When enabled, Shield will create a basic panel user role that can be
    | assigned to users who should have access to your Filament panels but
    | don't need any specific permissions beyond basic authentication.
    |
    */

    'panel_user' => [
        'enabled' => true,
        'name' => 'panel_user',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Builder
    |--------------------------------------------------------------------------
    |
    | You can customize how permission keys are generated to match your
    | preferred naming convention and organizational standards. Shield uses
    | these settings when creating permission names from your resources.
    |
    | Supported formats: snake, kebab, pascal, camel, upper_snake, lower_snake
    |
    */

    'permissions' => [
        'separator' => ':',
        'case' => 'pascal',
        'generate' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Policies
    |--------------------------------------------------------------------------
    |
    | Shield can automatically generate Laravel policies for your resources.
    | When merge is enabled, the methods below will be combined with any
    | resource-specific methods you define in the resources section.
    |
    */

    'policies' => [
        'path' => app_path('Policies'),
        'merge' => true,
        'generate' => true,
        'methods' => [
            'viewAny', 'view', 'create', 'update', 'delete', 'restore',
            'forceDelete', 'forceDeleteAny', 'restoreAny', 'replicate', 'reorder',
        ],
        'single_parameter_methods' => [
            'viewAny',
            'create',
            'deleteAny',
            'forceDeleteAny',
            'restoreAny',
            'reorder',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Shield supports multiple languages out of the box. When enabled, you
    | can provide translated labels for permissions to create a more
    | localized experience for your international users.
    |
    */

    'localization' => [
        'enabled' => false,
        'key' => 'filament-shield::filament-shield.resource_permission_prefixes_labels',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | Here you can fine-tune permissions for specific Filament resources.
    | Use the 'manage' array to override the default policy methods for
    | individual resources, giving you granular control over permissions.
    |
    */

    'resources' => [
        'subject' => 'model',
        'manage' => [
            \BezhanSalleh\FilamentShield\Resources\Roles\RoleResource::class => [
                'viewAny',
                'view',
                'create',
                'update',
                'delete',
            ],
        ],
        'exclude' => [
            //
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    |
    | Most Filament pages only require view permissions. Pages listed in the
    | exclude array will be skipped during permission generation and won't
    | appear in your role management interface.
    |
    */

    'pages' => [
        'subject' => 'class',
        'prefix' => 'view',
        'exclude' => [
            \Filament\Pages\Dashboard::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    |
    | Like pages, widgets typically only need view permissions. Add widgets
    | to the exclude array if you don't want them to appear in your role
    | management interface.
    |
    */

    'widgets' => [
        'subject' => 'class',
        'prefix' => 'view',
        'exclude' => [
            \Filament\Widgets\AccountWidget::class,
            \Filament\Widgets\FilamentInfoWidget::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Permissions
    |--------------------------------------------------------------------------
    |
    | Sometimes you need permissions that don't map to resources, pages, or
    | widgets. Define any custom permissions here and they'll be available
    | when editing roles in your application.
    |
    */

    'custom_permissions' => [
        // ═══════════════════════════════════════════════════════════════════
        // 💻 Computadoras
        // ═══════════════════════════════════════════════════════════════════
        'ComputerAssignPeripheral' => '💻 Computadoras » Asignar Periférico',
        'ComputerDismantle' => '💻 Computadoras » Desmantelar',
        'ComputerUpdateSystem' => '💻 Computadoras » Actualizar Sistema',
        'ComputerViewComponents' => '💻 Computadoras » Ver Componentes',
        'ComputerViewHistory' => '💻 Computadoras » Ver Historial',
        'ComputerGenerateReport' => '💻 Computadoras » Generar Reporte',
        'ComputerPreventiveMaintenance' => '💻 Computadoras » Mantenimiento Preventivo',
        
        // ═══════════════════════════════════════════════════════════════════
        // 🖱️ Periféricos
        // ═══════════════════════════════════════════════════════════════════
        'PeripheralViewComponents' => '🖱️ Periféricos » Ver Componentes',
        'PeripheralUpdateComponents' => '🖱️ Periféricos » Actualizar Componentes',
        'PeripheralDismantle' => '🖱️ Periféricos » Desmantelar',
        
        // ═══════════════════════════════════════════════════════════════════
        // 🔧 Mantenimientos
        // ═══════════════════════════════════════════════════════════════════
        'MaintenanceExecute' => '🔧 Mantenimientos » Ejecutar',
        'MaintenanceFinish' => '🔧 Mantenimientos » Finalizar',
        
        // ═══════════════════════════════════════════════════════════════════
        // 🚚 Traslados
        // ═══════════════════════════════════════════════════════════════════
        'TransferExecute' => '🚚 Traslados » Ejecutar',
        'TransferFinish' => '🚚 Traslados » Finalizar',
        
        // ═══════════════════════════════════════════════════════════════════
        // 🖨️ Impresoras
        // ═══════════════════════════════════════════════════════════════════
        'PrinterViewDetails' => '🖨️ Impresoras » Ver Detalles',
        'PrinterUpdateSpares' => '🖨️ Impresoras » Actualizar Repuestos',
        'PrinterViewHistory' => '🖨️ Impresoras » Ver Historial',
        'PrinterGenerateReport' => '🖨️ Impresoras » Generar Reporte',
        'PrinterDismantle' => '🖨️ Impresoras » Desmantelar',
        'PrinterPreventiveMaintenance' => '🖨️ Impresoras » Mantenimiento Preventivo',
        
        // ═══════════════════════════════════════════════════════════════════
        // 📽️ Proyectores
        // ═══════════════════════════════════════════════════════════════════
        'ProjectorViewDetails' => '📽️ Proyectores » Ver Detalles',
        'ProjectorUpdateSpares' => '📽️ Proyectores » Actualizar Repuestos',
        'ProjectorViewHistory' => '📽️ Proyectores » Ver Historial',
        'ProjectorGenerateReport' => '📽️ Proyectores » Generar Reporte',
        'ProjectorDismantle' => '📽️ Proyectores » Desmantelar',
        'ProjectorPreventiveMaintenance' => '📽️ Proyectores » Mantenimiento Preventivo',
        
        // ═══════════════════════════════════════════════════════════════════
        // 📦 Componentes
        // ═══════════════════════════════════════════════════════════════════
        'ComponentExport' => '📦 Componentes » Exportar Excel/CSV',
        'ComponentBulkDelete' => '📦 Componentes » Eliminar por Lote',
        'ComponentHistoryExport' => '📦 Componentes » Exportar Historial',
        
        // ═══════════════════════════════════════════════════════════════════
        // 📊 Sistema y Reportes
        // ═══════════════════════════════════════════════════════════════════
        'ViewAdvancedReports' => '📊 Sistema » Ver Reportes Avanzados',
        'ExportData' => '📊 Sistema » Exportar Datos',
        'ManageSystemSettings' => '📊 Sistema » Gestionar Configuraciones',
    ],

    /*
    |--------------------------------------------------------------------------
    | Entity Discovery
    |--------------------------------------------------------------------------
    |
    | By default, Shield only looks for entities in your default Filament
    | panel. Enable these options if you're using multiple panels and want
    | Shield to discover entities across all of them.
    |
    */

    'discovery' => [
        'discover_all_resources' => false,
        'discover_all_widgets' => false,
        'discover_all_pages' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Policy
    |--------------------------------------------------------------------------
    |
    | Shield can automatically register a policy for role management itself.
    | This lets you control who can manage roles using Laravel's built-in
    | authorization system. Requires a RolePolicy class in your app.
    |
    */

    'register_role_policy' => true,

];
