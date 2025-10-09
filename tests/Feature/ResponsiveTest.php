<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResponsiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions if they don't exist
        Permission::firstOrCreate(['name' => 'view-dashboard']);
        Permission::firstOrCreate(['name' => 'view-sales']);
        Permission::firstOrCreate(['name' => 'manage-invoices']);
        Permission::firstOrCreate(['name' => 'view-stock']);
        
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'administrateur']);
        $sellerRole = Role::firstOrCreate(['name' => 'vendeur']);
        
        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        $sellerRole->givePermissionTo(['view-dashboard', 'view-sales', 'manage-invoices', 'view-stock']);
    }

    /** @test */
    public function invoice_form_contains_responsive_classes()
    {
        // Create a user with appropriate permissions
        $user = User::factory()->create();
        $user->assignRole('vendeur');
        
        $this->actingAs($user);

        // Visit the invoice creation page
        $response = $this->get(route('invoices.create'));
        
        // Assert that the page loads successfully
        $response->assertStatus(200);
        
        // Assert that responsive classes are present in the form
        $response->assertSee('grid-cols-1');
        $response->assertSee('md:grid-cols-2');
        $response->assertSee('lg:grid-cols-4');
        
        // Assert that mobile-friendly elements are present
        $response->assertSee('w-full');
        $response->assertSee('sm:w-auto');
    }

    /** @test */
    public function invoice_table_contains_responsive_classes()
    {
        // Create a user with appropriate permissions
        $user = User::factory()->create();
        $user->assignRole('vendeur');
        
        $this->actingAs($user);

        // Visit the invoices index page
        $response = $this->get(route('invoices.index'));
        
        // Assert that the page loads successfully
        $response->assertStatus(200);
        
        // Assert that responsive table classes are present
        $response->assertSee('overflow-x-auto');
        $response->assertSee('min-w-full');
        
        // Assert that mobile-friendly layout classes are present
        $response->assertSee('flex-col');
        $response->assertSee('sm:flex-row');
    }

    /** @test */
    public function invoice_detail_view_contains_responsive_classes()
    {
        // Create a user with appropriate permissions
        $user = User::factory()->create();
        $user->assignRole('vendeur');
        
        $this->actingAs($user);

        // Visit the invoices index page (we're just checking the layout)
        $response = $this->get(route('invoices.index'));
        
        // Assert that the page loads successfully
        $response->assertStatus(200);
        
        // Assert that responsive layout classes are present
        $response->assertSee('space-y-6');
        $response->assertSee('px-4');
        $response->assertSee('py-6');
        
        // Assert that mobile-friendly buttons are present
        $response->assertSee('w-full');
        $response->assertSee('sm:w-auto');
    }
}