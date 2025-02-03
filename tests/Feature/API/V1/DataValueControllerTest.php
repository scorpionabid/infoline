<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Column;
use App\Domain\Entities\School;
use App\Domain\Entities\User;
use App\Domain\Entities\DataValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DataValueControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private School $school;
    private Column $column;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Test məlumatlarını hazırlayırıq
        $this->admin = User::factory()->create(['user_type' => 'superadmin']);
        $this->school = School::factory()->create();
        $this->column = Column::factory()->create(['data_type' => 'text']);
        
        Sanctum::actingAs($this->admin);
    }

    /** @test */
    public function user_can_list_data_values()
    {
        DataValue::factory()->count(3)->create([
            'school_id' => $this->school->id,
            'column_id' => $this->column->id
        ]);

        $response = $this->getJson('/api/v1/data-values');

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'value',
                            'status',
                            'school' => ['id', 'name'],
                            'column' => ['id', 'name', 'data_type']
                        ]
                    ],
                    'message'
                ]);
    }

    /** @test */
    public function user_can_create_data_value()
    {
        $data = [
            'school_id' => $this->school->id,
            'column_id' => $this->column->id,
            'value' => 'Test Value'
        ];

        $response = $this->postJson('/api/v1/data-values', $data);

        $response->assertCreated()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'value',
                        'status'
                    ],
                    'message'
                ]);

        $this->assertDatabaseHas('data_values', [
            'value' => 'Test Value',
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function it_validates_value_according_to_column_type()
    {
        $numberColumn = Column::factory()->create(['data_type' => 'number']);
        
        $data = [
            'school_id' => $this->school->id,
            'column_id' => $numberColumn->id,
            'value' => 'not a number'
        ];

        $response = $this->postJson('/api/v1/data-values', $data);

        $response->assertUnprocessable()
                ->assertJsonValidationErrors(['value']);
    }

    /** @test */
    public function user_can_submit_data_value()
    {
        $dataValue = DataValue::factory()->create([
            'school_id' => $this->school->id,
            'column_id' => $this->column->id,
            'status' => 'draft'
        ]);

        $response = $this->postJson("/api/v1/data-values/{$dataValue->id}/submit");

        $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'status' => 'submitted'
                    ]
                ]);
    }

    /** @test */
    public function admin_can_approve_data_value()
    {
        $dataValue = DataValue::factory()->create([
            'status' => 'submitted'
        ]);

        $response = $this->postJson("/api/v1/data-values/{$dataValue->id}/approve");

        $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'status' => 'approved'
                    ]
                ]);
    }

    /** @test */
    public function admin_can_reject_data_value_with_comment()
    {
        $dataValue = DataValue::factory()->create([
            'status' => 'submitted'
        ]);

        $response = $this->postJson("/api/v1/data-values/{$dataValue->id}/reject", [
            'comment' => 'Səhv məlumat'
        ]);

        $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'status' => 'rejected',
                        'comment' => 'Səhv məlumat'
                    ]
                ]);
    }
}
