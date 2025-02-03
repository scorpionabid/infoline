<?php

namespace App\Services\Dashboard;

use App\Domain\Entities\DataValue;
use App\Domain\Entities\Region;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStatistics(): array
    {
        return [
            'total_regions' => Region::count(),
            'total_sectors' => Sector::count(),
            'total_schools' => School::count(),
            'data_submissions' => [
                'total' => DataValue::count(),
                'pending' => DataValue::where('status', 'draft')->count(),
                'submitted' => DataValue::where('status', 'submitted')->count(),
                'approved' => DataValue::where('status', 'approved')->count()
            ]
        ];
    }

    public function getRegionStatistics(): array
    {
        return Region::withCount(['sectors', 'schools'])
            ->get()
            ->map(function ($region) {
                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'sectors_count' => $region->sectors_count,
                    'schools_count' => $region->schools_count,
                    'data_submissions' => $this->getRegionDataSubmissions($region->id)
                ];
            })
            ->toArray();
    }

    public function getSchoolStatistics(?int $regionId = null, ?int $sectorId = null): array
    {
        $query = School::query()
            ->withCount(['dataValues as total_submissions' => function ($query) {
                $query->whereNotNull('value');
            }])
            ->withCount(['dataValues as approved_submissions' => function ($query) {
                $query->where('status', 'approved');
            }]);

        if ($regionId) {
            $query->whereHas('sector', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        if ($sectorId) {
            $query->where('sector_id', $sectorId);
        }

        return $query->get()
            ->map(function ($school) {
                return [
                    'id' => $school->id,
                    'name' => $school->name,
                    'sector' => $school->sector->name,
                    'region' => $school->sector->region->name,
                    'total_submissions' => $school->total_submissions,
                    'approved_submissions' => $school->approved_submissions
                ];
            })
            ->toArray();
    }

    public function getDataSubmissionStats(): array
    {
        return [
            'submissions_by_status' => $this->getSubmissionsByStatus(),
            'submissions_by_month' => $this->getSubmissionsByMonth(),
            'latest_submissions' => $this->getLatestSubmissions()
        ];
    }

    private function getRegionDataSubmissions(int $regionId): array
    {
        return [
            'total' => DataValue::whereHas('school.sector', function ($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })->count(),
            'approved' => DataValue::whereHas('school.sector', function ($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })->where('status', 'approved')->count()
        ];
    }

    private function getSubmissionsByStatus(): array
    {
        return DataValue::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    private function getSubmissionsByMonth(): array
    {
        return DataValue::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
    }

    private function getLatestSubmissions(int $limit = 10): array
    {
        return DataValue::with(['school', 'column'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'school' => $submission->school->name,
                    'column' => $submission->column->name,
                    'value' => $submission->value,
                    'status' => $submission->status,
                    'created_at' => $submission->created_at->format('Y-m-d H:i:s')
                ];
            })
            ->toArray();
    }
}