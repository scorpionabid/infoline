<?php

namespace Tests\Unit;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Application\DTOs\CategoryDTO;
use App\Application\Services\CategoryService;
use App\Infrastructure\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private CategoryService $categoryService;
    private CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = new CategoryRepository();
        $this->categoryService = new CategoryService($this->categoryRepository);
    }

    /** @test */
    public function it_can_create_category()
    {
        $data = [
            'name' => 'Ümumi məlumatlar'
        ];

        $dto = new CategoryDTO($data);
        $category = $this->categoryService->create($dto);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals($data['name'], $category->name);
    }

    /** @test */
    public function it_validates_category_name()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => '' // boş ad
        ];

        $dto = new CategoryDTO($data);
        $this->categoryService->create($dto);
    }

    /** @test */
    public function it_validates_category_name_length()
    {
        $this->expectException(\InvalidArgumentException::class);

        $data = [
            'name' => str_repeat('a', 256) // 255 simvoldan çox
        ];

        $dto = new CategoryDTO($data);
        $this->categoryService->create($dto);
    }

    /** @test */
    public function it_can_update_category()
    {
        // İlk öncə kateqoriya yaradaq
        $data = [
            'name' => 'Ümumi məlumatlar'
        ];
        $dto = new CategoryDTO($data);
        $category = $this->categoryService->create($dto);

        // İndi yeniləyək
        $updateData = [
            'name' => 'Əsas məlumatlar'
        ];
        $updateDto = new CategoryDTO($updateData);
        $updatedCategory = $this->categoryService->update($category->id, $updateDto);

        $this->assertEquals($updateData['name'], $updatedCategory->name);
    }

    /** @test */
    public function it_has_many_columns()
    {
        // Kateqoriya yaradaq
        $data = [
            'name' => 'Ümumi məlumatlar'
        ];
        $dto = new CategoryDTO($data);
        $category = $this->categoryService->create($dto);

        // İki sütun yaradaq
        Column::create([
            'name' => 'Ad',
            'data_type' => 'text',
            'category_id' => $category->id
        ]);

        Column::create([
            'name' => 'Yaş',
            'data_type' => 'number',
            'category_id' => $category->id
        ]);

        $this->assertCount(2, $category->columns);
        $this->assertInstanceOf(Column::class, $category->columns->first());
    }

    /** @test */
    public function it_can_get_active_columns()
    {
        // Kateqoriya yaradaq
        $data = [
            'name' => 'Ümumi məlumatlar'
        ];
        $dto = new CategoryDTO($data);
        $category = $this->categoryService->create($dto);

        // Aktiv sütun yaradaq
        Column::create([
            'name' => 'Ad',
            'data_type' => 'text',
            'category_id' => $category->id
        ]);

        // Deaktiv sütun yaradaq (end_date keçmiş tarixdir)
        Column::create([
            'name' => 'Yaş',
            'data_type' => 'number',
            'category_id' => $category->id,
            'end_date' => now()->subDay()
        ]);

        $this->assertCount(1, $category->activeColumns);
        $this->assertEquals('Ad', $category->activeColumns->first()->name);
    }

    /** @test */
    public function it_cannot_delete_category_with_active_columns()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Kateqoriya yaradaq
        $data = [
            'name' => 'Ümumi məlumatlar'
        ];
        $dto = new CategoryDTO($data);
        $category = $this->categoryService->create($dto);

        // Aktiv sütun yaradaq
        Column::create([
            'name' => 'Ad',
            'data_type' => 'text',
            'category_id' => $category->id
        ]);

        // Kateqoriyanı silməyə çalışaq
        $this->categoryService->delete($category->id);
    }
}
