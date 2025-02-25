<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Domain\Entities\Deadline;
use App\Domain\Entities\Category;
use App\Domain\Entities\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeadlineController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:super']);
    }

    /**
     * Deadline siyahısını göstərir
     */
    public function index()
    {
        $deadlines = Deadline::with(['category', 'creator', 'assignee'])
            ->latest()
            ->paginate(10);

        $categories = Category::all();
        $users = User::all();

        return view('pages.settings.personal.deadlines.index', compact('deadlines', 'categories', 'users'));
    }

    /**
     * Yeni deadline əlavə edir
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:today',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'required|exists:users,id'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = false;

        $deadline = Deadline::create($validated);

        return response()->json([
            'message' => 'Deadline uğurla əlavə edildi',
            'deadline' => $deadline
        ]);
    }

    /**
     * Deadline məlumatlarını yeniləyir
     */
    public function update(Request $request, Deadline $deadline)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'boolean'
        ]);

        $deadline->update($validated);

        return response()->json([
            'message' => 'Deadline uğurla yeniləndi',
            'deadline' => $deadline
        ]);
    }

    /**
     * Deadline-ı silir
     */
    public function destroy(Deadline $deadline)
    {
        $deadline->delete();

        return response()->json([
            'message' => 'Deadline uğurla silindi'
        ]);
    }
}