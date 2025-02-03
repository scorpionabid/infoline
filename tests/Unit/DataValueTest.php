<?php

namespace Tests\Unit;

use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;
use App\Domain\Entities\School;
use App\Domain\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataValueTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_data_value()
    {
        $column = Column::factory()->create(['data_type' => 'text']);
        $school = School::factory()->create();
        $user = User::factory()->create();

        $data = [
            'column_id' => $column->id,
            'school_id' => $school->id,
            'value' => 'Test Value',
            'updated_by' => $user->id
        ];

        $dataValue = DataValue::create($data);

        $this->assertInstanceOf(DataValue::class, $dataValue);
        $this->assertEquals('Test Value', $dataValue->value);
        $this->assertEquals('draft', $dataValue->status);
    }

    /** @test */
    public function it_validates_value_based_on_column_type()
    {
        // Text type
        $textColumn = Column::factory()->create(['data_type' => 'text']);
        $textValue = DataValue::make([
            'column_id' => $textColumn->id,
            'value' => 'Valid text'
        ]);
        $this->assertTrue($textValue->isValidValue());

        // Number type
        $numberColumn = Column::factory()->create(['data_type' => 'number']);
        $numberValue = DataValue::make([
            'column_id' => $numberColumn->id,
            'value' => '123'
        ]);
        $this->assertTrue($numberValue->isValidValue());

        $invalidNumberValue = DataValue::make([
            'column_id' => $numberColumn->id,
            'value' => 'not a number'
        ]);
        $this->assertFalse($invalidNumberValue->isValidValue());
    }

    /** @test */
    public function it_belongs_to_school()
    {
        $dataValue = DataValue::factory()->create();
        
        $this->assertInstanceOf(School::class, $dataValue->school);
    }

    /** @test */
    public function it_belongs_to_column()
    {
        $dataValue = DataValue::factory()->create();
        
        $this->assertInstanceOf(Column::class, $dataValue->column);
    }

    /** @test */
    public function it_can_be_submitted()
    {
        $column = Column::factory()->create(['data_type' => 'text']);
        $dataValue = DataValue::factory()->create([
            'column_id' => $column->id,
            'value' => 'Valid text value',
            'status' => 'draft'
        ]);
        
        $dataValue->submit();
        
        $this->assertEquals('submitted', $dataValue->status);
    }

    /** @test */
    public function it_can_be_approved()
    {
        $column = Column::factory()->create(['data_type' => 'text']);
        $dataValue = DataValue::factory()->create([
            'column_id' => $column->id,
            'value' => 'Valid text value',
            'status' => 'submitted'
        ]);
        
        $dataValue->approve();
        
        $this->assertEquals('approved', $dataValue->status);
    }

    /** @test */
    public function it_can_be_rejected_with_comment()
    {
        $column = Column::factory()->create(['data_type' => 'text']);
        $dataValue = DataValue::factory()->create([
            'column_id' => $column->id,
            'value' => 'Valid text value',
            'status' => 'submitted'
        ]);
        
        $dataValue->reject('Səhv məlumat');
        
        $this->assertEquals('rejected', $dataValue->status);
        $this->assertEquals('Səhv məlumat', $dataValue->comment);
    }

    /** @test */
    public function it_tracks_who_updated_it()
    {
        $user = User::factory()->create();
        $dataValue = DataValue::factory()->create();
        
        $dataValue->updateValue('New Value', $user->id);
        
        $this->assertEquals('New Value', $dataValue->value);
        $this->assertEquals($user->id, $dataValue->updated_by);
    }
}