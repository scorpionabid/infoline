<?php

namespace Tests\Feature\API\V1;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\School;
use App\Domain\Entities\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $school;
    private $column;
    private $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tempDir = sys_get_temp_dir();
        
        // Test istifadəçisi yaradırıq
        $this->user = User::factory()->create([
            'user_type' => 'superadmin'
        ]);
        
        // Test məktəbi və sütunu yaradırıq
        $this->school = School::factory()->create();
        
        $category = Category::factory()->create();
        $this->column = Column::factory()->create([
            'name' => 'Test Column',
            'category_id' => $category->id,
            'data_type' => 'text'
        ]);
    }

    /** @test */
    public function authenticated_user_can_import_data()
    {
        Sanctum::actingAs($this->user);
        
        $file = $this->createValidExcelFile();

        $response = $this->postJson('/api/v1/excel/import', [
            'file' => $file,
            'school_id' => $this->school->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'success',
                'imported_count',
                'errors',
                'row_errors'
            ],
            'message'
        ]);
    }

    /** @test */
    public function it_validates_file_upload()
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->postJson('/api/v1/excel/import', [
            'school_id' => $this->school->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /** @test */
    public function it_validates_school_id()
    {
        Sanctum::actingAs($this->user);
        
        $file = $this->createValidExcelFile();

        $response = $this->postJson('/api/v1/excel/import', [
            'file' => $file,
            'school_id' => 999999 // non-existent ID
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['school_id']);
    }

    /** @test */
    public function unauthenticated_user_cannot_import()
    {
        // Auth olmadan sorğu göndəririk
        $file = $this->createValidExcelFile();

        $response = $this->postJson('/api/v1/excel/import', [
            'file' => $file,
            'school_id' => $this->school->id
        ]);

        $response->assertStatus(401);
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

    protected function tearDown(): void
    {
        @unlink($this->tempDir . '/test.xlsx');
        parent::tearDown();
    }
}