<?php

namespace App\Http\Controllers\Settings\Table;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deadline;
use Illuminate\Http\Request;

class DeadlineController extends Controller
{
    public function index()
    {
        $deadlines = Deadline::with('category')->latest()->get();
        return view('settings.table.deadlines.index', compact('deadlines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'deadline' => 'required|date',
            'warning_days' => 'required|integer|min:1|max:30',
        ]);

        Deadline::create($validated);

        return redirect()->back()->with('success', 'Son tarix uğurla əlavə edildi.');
    }

    public function update(Request $request, Deadline $deadline)
    {
        $validated = $request->validate([
            'deadline' => 'required|date',
            'warning_days' => 'required|integer|min:1|max:30',
        ]);

        $deadline->update($validated);

        return redirect()->back()->with('success', 'Son tarix uğurla yeniləndi.');
    }

    public function destroy(Deadline $deadline)
    {
        $deadline->delete();

        return redirect()->back()->with('success', 'Son tarix uğurla silindi.');
    }
}