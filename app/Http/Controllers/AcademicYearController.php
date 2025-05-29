<?php 
namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('school')->orderByDesc('id')->get();
        // dd($academicYears);
        return view('academic_years.index', compact('academicYears'));
    }

    public function create()
    {
        $schools = \App\Models\School::all();
        return view('academic_years.create', compact('schools'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            // 'name' => 'required|unique:academic_years,name|max:20',
            'name' => [
                'required',
                'max:20',
                Rule::unique('academic_years')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                }),
            ],
            'school_id' => 'required|exists:schools,id',
        ]);
    
        AcademicYear::create([
            'name' => $request->name,
            'school_id' => $request->school_id,
        ]);
    
        return redirect()->route('admin.academic_years.index')->with('success', 'تمت إضافة السنة الدراسية بنجاح');
    }
    
    public function edit(AcademicYear $academic_year)
    {
        $schools = \App\Models\School::all(); // جلب جميع المدارس
        return view('academic_years.edit', compact('academic_year', 'schools'));
    }
    

    public function update(Request $request, AcademicYear $academic_year)
    {
        $request->validate([
            'name' => 'required|max:20|unique:academic_years,name,' . $academic_year->id,
            'school_id' => 'required|exists:schools,id',
        ]);
    
        $academic_year->update($request->only(['name', 'school_id']));
    
        return redirect()->route('admin.academic_years.index')->with('success', 'تم التحديث بنجاح');
    }
    
    public function destroy(AcademicYear $academic_year)
    {
        $academic_year->delete();

        return redirect()->route('admin.academic_years.index')->with('success', 'تم الحذف بنجاح');
    }


}
