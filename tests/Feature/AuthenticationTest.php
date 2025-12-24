<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles necesarios antes de cada test
        Role::create(['name' => 'panel_user', 'guard_name' => 'web']);
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    /** @test */
    public function login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'test@tecnogest.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // Verificar que el usuario puede autenticarse usando actingAs
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@tecnogest.com',
            'password' => bcrypt('password'),
        ]);

        // Intentar autenticar con credenciales incorrectas no autenticará
        $authenticated = auth()->attempt([
            'email' => 'test@tecnogest.com',
            'password' => 'wrong-password',
        ]);

        $this->assertFalse($authenticated);
        $this->assertGuest();
    }

    /** @test */
    public function inactive_user_is_created_correctly(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@tecnogest.com',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $this->assertFalse($user->is_active);
        $this->assertDatabaseHas('users', [
            'email' => 'inactive@tecnogest.com',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function authenticated_super_admin_has_correct_permissions(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->syncRoles(['super_admin']);

        // Verificar que el super_admin tiene el rol correcto
        $this->assertTrue($user->hasRole('super_admin'));
        $this->assertTrue($user->isSuperAdmin());
        
        // Verificar que puede acceder al panel (método del modelo)
        $this->assertTrue($user->canAccessPanel());
    }

    /** @test */
    public function guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }
}
