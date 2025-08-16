<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class ParentController extends Controller
{
    public function childrenIndex()
    {
        $parent = auth()->user();

        $children = $parent->children()->with(['currentStudentClassSection.classSection.grade'])->get();

        return view('parent.children.index', compact('children'));
    }

    public function getParents($schoolId)
    {
        $parents = User::role('parent')   // من spatie
                    ->where('school_id', $schoolId)
                    ->select('id', 'name') // الأفضل تحديد الأعمدة المطلوبة فقط
                    ->get();

        return response()->json($parents);
    }

}
