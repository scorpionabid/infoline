<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Domain\Entities\{School, User, Region, Sector};
use App\Services\Excel\{ImportService, ExportService};
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    protected $importService;
    protected $exportService;

    public function __construct(ImportService $importService, ExportService $exportService)
    {
        $this->importService = $importService;
        $this->exportService = $exportService;
    }

    public function index()
    {
        $data = [
            'schools' => School::with(['sector.region'])->get(),
            'schoolAdmins' => User::where('user_type', 'schooladmin')->with('school')->get(),
            'regions' => Region::withCount('sectors')->get(),
            'sectors' => Sector::withCount('schools')->get()
        ];

        return view('settings.personal.index', $data);
    }

    public function downloadTemplate()
    {
        return $this->exportService->downloadTemplate('schools');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'type' => 'required|in:schools,admins'
        ]);

        $result = $this->importService->import(
            $request->file('file'),
            $request->type
        );

        return back()->with($result);
    }
}