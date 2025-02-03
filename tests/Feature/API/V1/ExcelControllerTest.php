<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;
use App\Domain\Entities\School;
use App\Domain\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExcelControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['user_type' => 'superadmin']);
        Sanctum::actingAs($this->admin);

        // Test datası hazırlayırıq
        $this->category = Category::factory()->create(['name' => 'Test Category']);
        $column = Column::factory()->create([
            'category_id' => $this->category->id
        ]);
        $school = School::factory()->create();
        DataValue::factory()->create([
            'column_id' => $column->id,
            'school_id' => $school->id,
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function user_can_export_data_to_excel()
    {
        $response = $this->getJson("/api/v1/excel/export?categories[]={$this->category->id}");

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->assertHeader('Content-Disposition', 'attachment; filename=export.xlsx');
    }

    /** @test */
    public function it_validates_category_parameter()
    {
        $response = $this->getJson('/api/v1/excel/export');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['categories']);
    }

    /** @test */
    public function only_authenticated_users_can_export()
    {
        $this->app->get('auth')->forgetGuards();

        $response = $this->getJson("/api/v1/excel/export?categories[]={$this->category->id}");

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_returns_error_for_invalid_category()
    {
        $response = $this->getJson('/api/v1/excel/export?categories[]=999');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['categories.0']);
    }
}