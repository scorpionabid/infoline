<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Category;
use App\Domain\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // SuperAdmin yaradırıq
        $this->admin = User::factory()->create(['user_type' => 'superadmin']);
        
        // Sanctum authentication
        Sanctum::actingAs($this->admin);
    }

    /** @test */
    public function authenticated_user_can_get_all_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'columns_count' // əlaqəli sütunların sayı
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function authenticated_user_can_create_category()
    {
        $categoryData = [
            'name' => 'Test Category'
        ];

        $response = $this->postJson('/api/v1/categories', $categoryData);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('categories', $categoryData);
    }

    /** @test */
    public function authenticated_user_can_update_category()
    {
        $category = Category::factory()->create();

        $updateData = [
            'name' => 'Updated Category Name'
        ];

        $response = $this->putJson("/api/v1/categories/{$category->id}", $updateData);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => $updateData
            ]);

        $this->assertDatabaseHas('categories', $updateData);
    }

    /** @test */
    public function category_with_active_columns_cannot_be_deleted()
    {
        $category = Category::factory()
            ->hasColumns(1)
            ->create();

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Aktiv sütunları olan kateqoriya silinə bilməz'
            ]);
    }

    /** @test */
    public function category_without_columns_can_be_deleted()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Kateqoriya uğurla silindi'
            ]);

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }
}