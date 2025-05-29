<?php

// app/Http/Controllers/ActivityController.php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ClassSection;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('classSection')->latest()->get();
        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $classSections = ClassSection::all();
        return view('activities.create', compact('classSections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:educational,quran_memorization,entertainment',
            'description' => 'nullable|string',
        ]);

        Activity::create($request->all());
        return redirect()->route('admin.activities.index')->with('success', 'تمت إضافة النشاط بنجاح');
    }

    public function edit(Activity $activity)
    {
        $classSections = ClassSection::all();
        return view('activities.edit', compact('activity', 'classSections'));
    }

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:educational,quran_memorization,entertainment',
            'description' => 'nullable|string',
        ]);

        $activity->update($request->all());
        return redirect()->route('admin.activities.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('admin.activities.index')->with('success', 'تم الحذف بنجاح');
    }
}
