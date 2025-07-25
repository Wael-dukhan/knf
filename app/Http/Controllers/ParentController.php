<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class ParentController extends Controller
{
    public function childrenIndex()
    {
        $parent = auth()->user();

        $children = $parent->children()->with(['currentStudentClassSection.classSection.grade'])->get();

        return view('parent.children.index', compact('children'));
    }
}
