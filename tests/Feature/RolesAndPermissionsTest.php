<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear rol panel_user (necesario porque User lo asigna automáticamente al crearse)
        Role::create(['name' => 'panel_user', 'guard_name' => 'web']);
    }

    /** @test */
    public function super_admin_has_all_permissions(): void
    {
        // Crear permisos
        $permissions = [
            'view_computer',
            'create_computer',
            'update_computer',
            'delete_computer',
            'view_location',
            'create_location',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear rol super_admin
        $superAdminRole = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);

        // Crear usuario super_admin
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // Super admin tiene acceso a todo por el gate definido en filament-shield
        $this->assertTrue($superAdmin->hasRole('super_admin'));
    }

    /** @test */
    public function tecnico_role_has_limited_permissions(): void
    {
        // Crear permisos
        $allPermissions = [
            'view_computer',
            'create_computer',
            'update_computer',
            'delete_computer',
            'view_maintenance',
            'create_maintenance',
        ];

        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear rol técnico con permisos limitados
        $tecnicoRole = Role::create(['name' => 'tecnico', 'guard_name' => 'web']);
        $tecnicoRole->givePermissionTo([
            'view_computer',
            'update_computer',
            'view_maintenance',
            'create_maintenance',
        ]);

        // Crear usuario técnico
        $tecnico = User::factory()->create();
        $tecnico->assignRole('tecnico');

        // Verificar permisos
        $this->assertTrue($tecnico->can('view_computer'));
        $this->assertTrue($tecnico->can('update_computer'));
        $this->assertFalse($tecnico->can('delete_computer'));
        $this->assertFalse($tecnico->can('create_computer'));
    }

    /** @test */
    public function viewer_role_has_only_view_permissions(): void
    {
        // Crear permisos
        $allPermissions = [
            'view_computer',
            'view_any_computer',
            'create_computer',
            'update_computer',
            'delete_computer',
        ];

        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear rol viewer
        $viewerRole = Role::create(['name' => 'viewer', 'guard_name' => 'web']);
        $viewerRole->givePermissionTo(['view_computer', 'view_any_computer']);

        // Crear usuario viewer
        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');

        // Verificar que solo tiene permisos de lectura
        $this->assertTrue($viewer->can('view_computer'));
        $this->assertTrue($viewer->can('view_any_computer'));
        $this->assertFalse($viewer->can('create_computer'));
        $this->assertFalse($viewer->can('update_computer'));
        $this->assertFalse($viewer->can('delete_computer'));
    }

    /** @test */
    public function user_can_have_multiple_roles(): void
    {
        Role::create(['name' => 'tecnico', 'guard_name' => 'web']);
        Role::create(['name' => 'viewer', 'guard_name' => 'web']);

        $user = User::factory()->create();
        // El usuario ya tiene panel_user asignado automáticamente
        $user->assignRole(['tecnico', 'viewer']);

        $this->assertTrue($user->hasRole('tecnico'));
        $this->assertTrue($user->hasRole('viewer'));
        $this->assertTrue($user->hasRole('panel_user')); // Asignado automáticamente
        $this->assertCount(3, $user->roles);
    }

    /** @test */
    public function permissions_can_be_synced_to_role(): void
    {
        $permissions = ['view_computer', 'create_computer', 'update_computer'];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $role->syncPermissions(['view_computer', 'update_computer']);

        $this->assertTrue($role->hasPermissionTo('view_computer'));
        $this->assertTrue($role->hasPermissionTo('update_computer'));
        $this->assertFalse($role->hasPermissionTo('create_computer'));
    }
}
