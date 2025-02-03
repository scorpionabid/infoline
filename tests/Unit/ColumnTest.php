<?php

namespace Tests\Unit;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Application\DTOs\ColumnDTO;
use App\Application\Services\ColumnService;
use App\Infrastructure\Repositories\ColumnRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColumnTest extends TestCase
{
   use RefreshDatabase;

   private ColumnService $columnService;
   private ColumnRepository $columnRepository;
   private Category $category;

   protected function setUp(): void
   {
       parent::setUp();
       $this->columnRepository = new ColumnRepository();
       $this->columnService = new ColumnService($this->columnRepository);

       // Test üçün kateqoriya yaradaq
       $this->category = Category::create([
           'name' => 'Ümumi məlumatlar'
       ]);
   }

   /** @test */
   public function it_can_create_column()
   {
       $data = [
           'name' => 'Ad',
           'data_type' => 'text',
           'input_limit' => 100,
           'category_id' => $this->category->id
       ];

       $dto = new ColumnDTO($data);
       $column = $this->columnService->create($dto);

       $this->assertInstanceOf(Column::class, $column);
       $this->assertEquals($data['name'], $column->name);
       $this->assertEquals($data['data_type'], $column->data_type);
       $this->assertEquals($data['input_limit'], $column->input_limit);
       $this->assertEquals($data['category_id'], $column->category_id);
   }

   /** @test */
   public function it_validates_column_name()
   {
       $this->expectException(\InvalidArgumentException::class);

       $data = [
           'name' => '', // boş ad
           'data_type' => 'text',
           'category_id' => $this->category->id
       ];

       $dto = new ColumnDTO($data);
       $this->columnService->create($dto);
   }

   /** @test */
   public function it_validates_data_type()
   {
       $this->expectException(\InvalidArgumentException::class);

       $data = [
           'name' => 'Ad',
           'data_type' => 'invalid_type', // yanlış tip
           'category_id' => $this->category->id
       ];

       $dto = new ColumnDTO($data);
       $this->columnService->create($dto);
   }

   /** @test */
   public function it_validates_end_date()
   {
       $this->expectException(\InvalidArgumentException::class);

       $data = [
           'name' => 'Ad',
           'data_type' => 'text',
           'end_date' => now()->subDay()->format('Y-m-d'), // keçmiş tarix
           'category_id' => $this->category->id
       ];

       $dto = new ColumnDTO($data);
       $this->columnService->create($dto);
   }

   /** @test */
   public function it_validates_input_limit()
   {
       $this->expectException(\InvalidArgumentException::class);

       $data = [
           'name' => 'Ad',
           'data_type' => 'text',
           'input_limit' => 0, // yanlış limit
           'category_id' => $this->category->id
       ];

       $dto = new ColumnDTO($data);
       $this->columnService->create($dto);
   }

   /** @test */
   public function it_belongs_to_category()
   {
       $data = [
           'name' => 'Ad',
           'data_type' => 'text',
           'category_id' => $this->category->id
       ];
       $dto = new ColumnDTO($data);
       $column = $this->columnService->create($dto);

       $this->assertInstanceOf(Category::class, $column->category);
       $this->assertEquals($this->category->id, $column->category->id);
   }

   /** @test */
   /** @test */
    public function it_can_check_if_active()
    {
    // Normal aktiv sütun
        $activeColumn = $this->columnService->create(new ColumnDTO([
            'name' => 'Ad',
            'data_type' => 'text',
            'category_id' => $this->category->id
        ]));

    // Sütunu deaktiv edirik
        $inactiveColumn = $this->columnService->deactivate(
            $activeColumn, 
            now()->subDay()->format('Y-m-d')
        );

        $this->assertTrue($activeColumn->isActive());
        $this->assertFalse($inactiveColumn->isActive());
    }

   /** @test */
   public function it_validates_data_type_correctly()
   {
       $validTypes = ['text', 'number', 'date', 'file'];
       foreach ($validTypes as $type) {
           $data = [
               'name' => 'Test Column',
               'data_type' => $type,
               'category_id' => $this->category->id
           ];
           $dto = new ColumnDTO($data);
           $column = $this->columnService->create($dto);
           $this->assertTrue($column->isValidDataType());
       }

       // Seçim tipləri üçün ayrıca test
       $selectTypes = ['select', 'multiselect'];
       foreach ($selectTypes as $type) {
           $data = [
               'name' => 'Test Column',
               'data_type' => $type,
               'category_id' => $this->category->id,
               'choices' => ['Option 1', 'Option 2']
           ];
           $dto = new ColumnDTO($data);
           $column = $this->columnService->create($dto);
           $this->assertTrue($column->isValidDataType());
       }
   }

   /** @test */
   public function it_requires_choices_for_select_types()
   {
       $selectTypes = ['select', 'multiselect'];
       
       foreach ($selectTypes as $type) {
           // Choices olmadan test
           $this->expectException(\InvalidArgumentException::class);
           
           $data = [
               'name' => 'Status',
               'data_type' => $type,
               'category_id' => $this->category->id
           ];
           $dto = new ColumnDTO($data);
           $this->columnService->create($dto);
       }
   }

   /** @test */
   public function it_cannot_change_data_type_after_creation()
   {
       $this->expectException(\InvalidArgumentException::class);

       // İlk öncə sütun yaradaq
       $data = [
           'name' => 'Ad',
           'data_type' => 'text',
           'category_id' => $this->category->id
       ];
       $dto = new ColumnDTO($data);
       $column = $this->columnService->create($dto);

       // Data tipini dəyişməyə çalışaq
       $updateData = [
           'name' => 'Ad',
           'data_type' => 'number', // data tipini dəyişməyə çalışırıq
           'category_id' => $this->category->id
       ];
       $updateDto = new ColumnDTO($updateData);
       $this->columnService->update($column->id, $updateDto);
   }
}
