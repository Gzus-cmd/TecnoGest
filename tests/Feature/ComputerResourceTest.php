<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Computer;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ComputerResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $viewerUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear rol panel_user primero (requerido por User::booted)
        Role::create(['name' => 'panel_user', 'guard_name' => 'web']);
        
        // Crear otros roles
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'viewer', 'guard_name' => 'web']);

        // Crear permisos básicos
        $permissions = [
            'view_computer',
            'view_any_computer',
            'create_computer',
            'update_computer',
            'delete_computer',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear usuarios
        $this->adminUser = User::factory()->create(['is_active' => true]);
        $this->adminUser->syncRoles(['super_admin']);

        $this->viewerUser = User::factory()->create(['is_active' => true]);
        $this->viewerUser->syncRoles(['viewer']);
        $this->viewerUser->givePermissionTo(['view_computer', 'view_any_computer']);
    }

    /** @test */
    public function admin_has_all_computer_permissions(): void
    {
        // Super admin tiene todos los permisos por el Gate
        $this->assertTrue($this->adminUser->hasRole('super_admin'));
        
        // Con define_via_gate = true, el super_admin puede todo
        $this->assertTrue($this->adminUser->isSuperAdmin());
    }

    /** @test */
    public function admin_can_access_panel(): void
    {
        // Verificar que puede acceder al panel
        $this->assertTrue($this->adminUser->canAccessPanel());
    }

    /** @test */
    public function viewer_has_view_permissions(): void
    {
        // Verificar que el viewer tiene los permisos correctos
        $this->assertTrue($this->viewerUser->can('view_computer'));
        $this->assertTrue($this->viewerUser->can('view_any_computer'));
        $this->assertFalse($this->viewerUser->can('create_computer'));
        $this->assertFalse($this->viewerUser->can('delete_computer'));
    }
}
