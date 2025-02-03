<?php

namespace Tests\Unit\Services\Excel;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\School;
use App\Services\Excel\DataImport;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataImportTest extends TestCase
{
    use RefreshDatabase;

    private $importer;
    private $school;
    private $column;
    private $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Temporary direktoriyanı yaradırıq
        $this->tempDir = sys_get_temp_dir();
        
        $this->importer = new DataImport();
        $this->school = School::factory()->create();
        
        $category = Category::factory()->create();
        $this->column = Column::factory()->create([
            'name' => 'Test Column',
            'category_id' => $category->id,
            'data_type' => 'text'
        ]);
    }

    /** @test */
    public function it_can_import_valid_file()
    {
        $file = $this->createValidExcelFile();
        
        $result = $this->importer->import($file, $this->school);
        
        $this->assertTrue($result->toArray()['success']);
        $this->assertEquals(1, $result->toArray()['imported_count']);
        $this->assertEmpty($result->toArray()['errors']);
    }

    /** @test */
    public function it_validates_file_structure()
    {
        $file = $this->createInvalidExcelFile();
        
        $result = $this->importer->import($file, $this->school);
        
        $this->assertFalse($result->toArray()['success']);
        $this->assertNotEmpty($result->toArray()['errors']);
    }

    private function createValidExcelFile(): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Məktəb');
        $sheet->setCellValue('B1', 'Test Column');
        $sheet->setCellValue('A2', $this->school->name);
        $sheet->setCellValue('B2', 'Test Value');
        
        $filePath = $this->tempDir . '/test.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        
        return new UploadedFile(
            $filePath,
            'test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    private function createInvalidExcelFile(): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Invalid Header');
        
        $filePath = $this->tempDir . '/invalid.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        
        return new UploadedFile(
            $filePath,
            'invalid.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    protected function tearDown(): void
    {
        // Test fayllarını təmizləyirik
        @unlink($this->tempDir . '/test.xlsx');
        @unlink($this->tempDir . '/invalid.xlsx');
        
        parent::tearDown();
    }
}