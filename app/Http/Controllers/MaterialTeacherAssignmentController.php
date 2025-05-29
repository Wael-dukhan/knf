<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialTeacherAssignment;
use App\Models\ClassSection;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class MaterialTeacherAssignmentController extends Controller
{
    public function index()
    {
        $assignments = MaterialTeacherAssignment::with(['classSection', 'material', 'teacher'])->get();
        return view('material_assignments.index', compact('assignments'));
    }

    public function create($classSectionId , $materialId)
    {
        $classSection = ClassSection::findOrFail($classSectionId);
        $material = Material::findOrFail($materialId);
        $teachers = User::role('teacher')->where('school_id',$classSection->grade->school_id)->get();
        $academicYears = AcademicYear::where('school_id',$classSection->grade->school_id)->get();
        $terms = Term::where('school_id',$classSection->grade->school_id)->get();

        return view('material_assignments.create', compact('classSection', 'material', 'teachers', 'academicYears', 'terms'));
    }


    public function show($id)
    {
        $classSection = ClassSection::findOrFail($id);
        $materials = $classSection->grade->materials;
        $assignments = MaterialTeacherAssignment::where('class_section_id', $id)->get()->keyBy('material_id');
        // dd($assignments->first()?->id);
        return view('material_assignments.show', compact('classSection', 'materials', 'assignments'));
    }

  public function edit(Request $request, $assignmentId)
    {

        $assignment = MaterialTeacherAssignment::findOrFail($assignmentId);
        $schoolId = $assignment->classSection->grade->school_id;
        $teachers = User::role('teacher')->where('school_id',$schoolId)->get();
        $academicYears = AcademicYear::where('school_id',$schoolId)->get();
        $terms = Term::where('school_id',$schoolId)->get();
        // dd($assignment->classSection->grade);
        return view('material_assignments.edit', compact(
  'assignment', 'teachers', 'academicYears', 'terms'
        ));
    }


    public function update(Request $request, $assignmentId)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $assignment = MaterialTeacherAssignment::findOrFail($assignmentId);

        $assignment->update([
            'academic_year_id' => $request->academic_year_id,
            'term_id' => $request->term_id,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->route('material-assignments.show', $assignment->class_section_id)
            ->with('success', __('تم تحديث التعيين بنجاح.'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'material_id' => 'required|exists:materials,id',
            'teacher_id' => 'required|exists:users,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        // التحقق من أن المعلم ينتمي لنفس المدرسة
        $classSection = ClassSection::findOrFail($validated['class_section_id']);
        $teacher = User::findOrFail($validated['teacher_id']);

        if ($teacher->school_id !== $classSection->grade->school_id) {
            return back()->withErrors(['teacher_id' => 'المدرس لا ينتمي إلى نفس المدرسة'])->withInput();
        }

        // إنشاء التعيين
        MaterialTeacherAssignment::create([
            'class_section_id'   => $validated['class_section_id'],
            'material_id'        => $validated['material_id'],
            'teacher_id'         => $validated['teacher_id'],
            'academic_year_id'   => $validated['academic_year_id'],
            'term_id'            => $validated['term_id'],
        ]);

        return redirect()
            ->route('material-assignments.show', $validated['class_section_id'])
            ->with('success', 'تم تعيين المعلم بنجاح');
    }


    public function destroy($id)
    {
        $assignment = MaterialTeacherAssignment::findOrFail($id);
        
        $classSectionId = $assignment->class_section_id;

        $assignment->delete();

        return redirect()
            ->route('material-assignments.show', $classSectionId)
            ->with('success', 'تم حذف التعيين بنجاح');
    }

}
