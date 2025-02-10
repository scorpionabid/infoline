<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Column;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // Ana ayarlar səhifəsi
    public function index()
    {
        // Əsas ayarlar dashboard-u
        return view('settings.index');
    }

    // Kateqoriyalar üçün metodlar
    public function categories()
    {
        // Bütün kateqoriyaları gətir
        $categories = Category::all();
        return view('settings.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        // Kateqoriya yaratma forması
        return view('settings.categories.create');
    }

    public function storeCategory(Request $request)
    {
        // Kateqoriya saxlama validasiyası
        $validated = $request->validate([
            'name' => 'required|unique:categories|max:255',
            'description' => 'nullable|max:500'
        ]);

        // Yeni kateqoriya yaradılması
        $category = Category::create($validated);

        return redirect()
            ->route('settings.categories')
            ->with('success', 'Kateqoriya uğurla əlavə edildi');
    }

    // Cədvəl ayarları üçün metodlar
    public function table(Request $request)
    {
        // Kateqoriyaları gətir
        $categories = Category::all();
        
        // Seçilmiş kateqoriya (əgər varsa)
        $selectedCategory = $request->has('category') 
            ? Category::findOrFail($request->category) 
            : null;

        return view('settings.table', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory
        ]);
    }

    public function columns()
    {
        // Bütün sütunları gətir
        $columns = Column::with('category')->get();
        return view('settings.columns.index', compact('columns'));
    }

    public function createColumn()
    {
        // Sütun yaratma forması üçün kateqoriyaları gətir
        $categories = Category::all();
        return view('settings.columns.create', compact('categories'));
    }

    public function storeColumn(Request $request)
    {
        // Sütun saxlama validasiyası
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
            'data_type' => 'required|in:text,number,date,select'
        ]);

        // Yeni sütun yaradılması
        $column = Column::create($validated);

        return redirect()
            ->route('settings.columns')
            ->with('success', 'Sütun uğurla əlavə edildi');
    }

    // Personal ayarları
    public function personal()
    {
        // Personal ayarları səhifəsi
        return view('settings.personal');
    }

    // Məktəblər üçün metodlar
    public function schools()
    {
        // Bütün məktəbləri gətir
        $schools = School::all();
        return view('settings.schools.index', compact('schools'));
    }

    public function createSchool()
    {
        // Məktəb yaratma forması
        $sectors = Sector::all();
        return view('settings.schools.create', compact('sectors'));
    }

    public function storeSchool(Request $request)
    {
        // Məktəb saxlama validasiyası
        $validated = $request->validate([
            'name' => 'required|unique:schools|max:255',
            'sector_id' => 'required|exists:sectors,id',
            'utis_code' => 'required|unique:schools',
            'phone' => 'nullable|string',
            'email' => 'nullable|email|unique:schools'
        ]);

        // Yeni məktəb yaradılması
        $school = School::create($validated);

        return redirect()
            ->route('settings.schools')
            ->with('success', 'Məktəb uğurla əlavə edildi');
    }

    // Sektorlar üçün metodlar
    public function sectors()
    {
        // Bütün sektorları gətir
        $sectors = Sector::with('region')->get();
        return view('settings.sectors.index', compact('sectors'));
    }

    public function createSector()
    {
        // Sektor yaratma forması
        $regions = Region::all();
        return view('settings.sectors.create', compact('regions'));
    }

    public function storeSector(Request $request)
    {
        // Sektor saxlama validasiyası
        $validated = $request->validate([
            'name' => 'required|unique:sectors|max:255',
            'region_id' => 'required|exists:regions,id',
            'phone' => 'nullable|string'
        ]);

        // Yeni sektor yaradılması
        $sector = Sector::create($validated);

        return redirect()
            ->route('settings.sectors')
            ->with('success', 'Sektor uğurla əlavə edildi');
    }

    // İmport/Export üçün metodlar
    public function import()
    {
        return view('settings.import');
    }

    public function export()
    {
        return view('settings.export');
    }

    public function templates()
    {
        return view('settings.templates');
    }

    public function downloadTemplate()
    {
        $templatePath = public_path('templates/school_template.xlsx');
        return response()->download($templatePath);
    }
}