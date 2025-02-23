<?php

namespace App\Http\Controllers;

use App\Domain\Entities\{Region, Sector, School, User, Category, SchoolData};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class SuperAdminDashboardController extends Controller
{
    // app/Http/Controllers/SuperAdminDashboardController.php

    public function index(Request $request)
    {
        try {
            // Base statistika
            $stats = [
                'regionCount' => Region::count(),
                'sectorCount' => Sector::count(),
                'schoolCount' => School::count(),
                'userCount' => User::count(),
            ];

            // Filter məlumatları
            $sectors = Sector::orderBy('name')->get();
            $categories = Category::all();

            // SchoolData cədvəli mövcuddursa
            if (Schema::hasTable('school_data')) {
                // Məktəb məlumatlarını al
                $query = SchoolData::with(['school.sector', 'category']);

                // Filtrləri tətbiq et
                if ($request->filled('sector_id')) {
                    $query->whereHas('school', function($q) use ($request) {
                        $q->where('sector_id', $request->sector_id);
                    });
                }

                if ($request->filled('category_id')) {
                    $query->where('category_id', $request->category_id);
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                // Məlumatları paginate et
                $schoolData = $query->latest()->paginate(15)->withQueryString();
            } else {
                // Boş collection üçün paginator yaradaq
                $schoolData = new LengthAwarePaginator(
                    collect([]),
                    0,
                    15,
                    1,
                    [
                        'path' => $request->url(),
                        'query' => $request->query(),
                    ]
                );
                \Log::warning('school_data table does not exist');
            }

            return view('pages.dashboard.super-admin', compact(
                'stats',
                'sectors', 
                'categories',
                'schoolData'
            ));

        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Xəta halında boş paginator
            $schoolData = new LengthAwarePaginator(
                collect([]),
                0,
                15,
                1,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('pages.dashboard.super-admin', [
                'stats' => [
                    'regionCount' => 0,
                    'sectorCount' => 0,
                    'schoolCount' => 0,
                    'userCount' => 0,
                ],
                'sectors' => collect([]),
                'categories' => collect([]),
                'schoolData' => $schoolData,
                'error' => 'Məlumatlar yüklənərkən xəta baş verdi.'
            ]);
        }
    }
}