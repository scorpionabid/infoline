<?php

namespace Tests\Unit\Services\Excel;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Services\Excel\Templates\TemplateGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class TemplateGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Column $textColumn;
    private Column $numberColumn;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create(['name' => 'Test Category']);

        // Müxtəlif tip sütunlar yaradaq
        $this->textColumn = Column::factory()->create([
            'name' => 'Text Column',
            'data_type' => 'text',
            'category_id' => $this->category->id
        ]);

        $this->numberColumn = Column::factory()->create([
            'name' => 'Number Column',
            'data_type' => 'number',
            'category_id' => $this->category->id
        ]);
    }

    /** @test */
    public function it_can_generate_template()
    {
        $generator = new TemplateGenerator();
        $spreadsheet = $generator->generate([$this->category->id]);

        $this->assertInstanceOf(Spreadsheet::class, $spreadsheet);
    }

    /** @test */
    public function it_creates_instruction_sheet()
    {
        $generator = new TemplateGenerator();
        $spreadsheet = $generator->generate([$this->category->id]);

        $instructionSheet = $spreadsheet->getSheetByName('Təlimatlar');
        
        $this->assertNotNull($instructionSheet);
        $this->assertStringContainsString('Doldurma qaydaları', $instructionSheet->getCell('A1')->getValue());
    }

    /** @test */
    public function it_creates_data_sheet_with_headers()
    {
        $generator = new TemplateGenerator();
        $spreadsheet = $generator->generate([$this->category->id]);

        $dataSheet = $spreadsheet->getSheetByName('Məlumatlar');
        
        // Header yoxlaması
        $this->assertEquals('Məktəb', $dataSheet->getCell('A1')->getValue());
        $this->assertEquals($this->textColumn->name, $dataSheet->getCell('B1')->getValue());
        $this->assertEquals($this->numberColumn->name, $dataSheet->getCell('C1')->getValue());
    }

    /** @test */
    public function it_adds_validation_rules()
    {
        $generator = new TemplateGenerator();
        $spreadsheet = $generator->generate([$this->category->id]);
        
        $dataSheet = $spreadsheet->getSheetByName('Məlumatlar');
        
        // Number sütununda validation olmalıdır
        $validations = $dataSheet->getDataValidation('C2');
        $this->assertEquals('decimal', $validations->getType());
    }

    /** @test */
    public function it_includes_sample_data()
    {
        $generator = new TemplateGenerator();
        $spreadsheet = $generator->generate([$this->category->id]);
        
        $dataSheet = $spreadsheet->getSheetByName('Məlumatlar');
        
        // İkinci sətirdə nümunə data olmalıdır
        $this->assertNotEmpty($dataSheet->getCell('A2')->getValue());
        $this->assertNotEmpty($dataSheet->getCell('B2')->getValue());
        $this->assertNotEmpty($dataSheet->getCell('C2')->getValue());
    }
}