<?php

namespace Tests\Unit\Services\Excel;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;
use App\Domain\Entities\School;
use App\Services\Excel\DataExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class DataExportTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Column $column;
    private School $school;
    private DataValue $dataValue;

    protected function setUp(): void
    {
        parent::setUp();

        // Test datası hazırlayırıq
        $this->category = Category::factory()->create(['name' => 'Test Category']);
        
        $this->column = Column::factory()->create([
            'name' => 'Test Column',
            'data_type' => 'text',
            'category_id' => $this->category->id
        ]);

        $this->school = School::factory()->create(['name' => 'Test School']);
        
        $this->dataValue = DataValue::factory()->create([
            'column_id' => $this->column->id,
            'school_id' => $this->school->id,
            'value' => 'Test Value',
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function it_can_create_excel_file()
    {
        $exporter = new DataExport();
        $spreadsheet = $exporter->export([$this->category->id]);

        $this->assertInstanceOf(Spreadsheet::class, $spreadsheet);
    }

    /** @test */
    public function it_exports_correct_data_structure()
    {
        $exporter = new DataExport();
        $spreadsheet = $exporter->export([$this->category->id]);
        
        $worksheet = $spreadsheet->getActiveSheet();

        // Header yoxlaması
        $this->assertEquals('Məktəb', $worksheet->getCell('A1')->getValue());
        $this->assertEquals($this->column->name, $worksheet->getCell('B1')->getValue());

        // Data yoxlaması
        $this->assertEquals($this->school->name, $worksheet->getCell('A2')->getValue());
        $this->assertEquals($this->dataValue->value, $worksheet->getCell('B2')->getValue());
    }

    /** @test */
    public function it_exports_only_approved_values()
    {
        // Rejected status ilə əlavə data yaradaq
        DataValue::factory()->create([
            'column_id' => $this->column->id,
            'school_id' => $this->school->id,
            'value' => 'Rejected Value',
            'status' => 'rejected'
        ]);

        $exporter = new DataExport();
        $spreadsheet = $exporter->export([$this->category->id]);
        
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Yalnız bir data sətri olmalıdır (approved olan)
        $this->assertEquals('Test Value', $worksheet->getCell('B2')->getValue());
        $this->assertEmpty($worksheet->getCell('B3')->getValue());
    }

    /** @test */
    public function it_can_export_multiple_categories()
    {
        // İkinci kateqoriya və sütun yaradaq
        $category2 = Category::factory()->create(['name' => 'Second Category']);
        $column2 = Column::factory()->create([
            'name' => 'Second Column',
            'category_id' => $category2->id
        ]);

        DataValue::factory()->create([
            'column_id' => $column2->id,
            'school_id' => $this->school->id,
            'value' => 'Second Value',
            'status' => 'approved'
        ]);

        $exporter = new DataExport();
        $spreadsheet = $exporter->export([$this->category->id, $category2->id]);
        
        $worksheet = $spreadsheet->getActiveSheet();

        // İkinci sütunun da header və datası var
        $this->assertEquals($column2->name, $worksheet->getCell('C1')->getValue());
        $this->assertEquals('Second Value', $worksheet->getCell('C2')->getValue());
    }
}