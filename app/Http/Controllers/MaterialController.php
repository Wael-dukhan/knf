<?php

namespace App\Http\Controllers;
use App\Models\Material;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index()
     {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع المواد
        if ($role === 'super_admin') {
            $materials = Material::with('grade')->get();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض المواد الخاصة بالمدرسة التي يديرها
            $materials = Material::with('grade')->whereHas('grade', function ($query) use ($user) {
                $query->where('school_id', $user->school_id);
            })->get();
            
        } else if ($role === 'teacher' || $role === 'quran_teacher') {
            // في حالة المعلم أو معلم القرآن، يمكن عرض المواد التي يدرسها
            $materials = Material::with('grade')->whereHas('grade.teachers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        } else if ($role === 'student' || $role === 'parent') {
            // في حالة الطالب أو ولي الأمر، يمكن عرض المواد الخاصة بالصف الذي ينتمي إليه الطالب
            $gradeId = DB::table('student_class_section')
                ->join('class_sections', 'student_class_section.class_section_id', '=', 'class_sections.id')
                ->where('student_class_section.user_id', $user->id)
                ->whereNull('student_class_section.deleted_at')
                ->value('class_sections.grade_id');
            $materials = Material::with('grade')
                ->where('grade_id', $gradeId)
                ->get();

        }
        //  $materials = Material::all();
         return view('materials.index', compact('materials','role'));
     }
     
     public function create()
     {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع الصفوف
        if ($role === 'super_admin') {
            $grades = Grade::with(['school','academicYear'])->get();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض الصفوف الخاصة بالمدرسة التي يديرها
            $grades = Grade::where('school_id', $user->school_id)->with(['school','academicYear'])->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        dd($grades);
        // $grades = Grade::all(); // في دالة create()
        // $teachers = User::role('teacher')->get(); // يجلب كل المستخدمين الذين لديهم دور "teacher"
        // dd($grades->first()->academicYear->name);

         return view('materials.create', compact('grades'));
     }
     
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_id' => 'required|exists:grades,id',
            'main_book' => 'nullable|mimes:pdf|max:20480',
            'activity_book' => 'nullable|mimes:pdf|max:20480',
        ]);

        $data = $request->only(['name', 'description', 'grade_id']);

        // رفع كتاب المادة الرئيسي
        if ($request->hasFile('main_book')) {
            $originalName = $request->file('main_book')->getClientOriginalName();
            $path = $request->file('main_book')->storeAs('books', $originalName, 'public');
            $data['main_book_path'] = $path;
        }

        // رفع كتاب الأنشطة إن وجد
        if ($request->hasFile('activity_book')) {
            $originalName = $request->file('activity_book')->getClientOriginalName();
            $path = $request->file('activity_book')->storeAs('books', $originalName, 'public');
            $data['activity_book_path'] = $path;
        }

        Material::create($data);

        return redirect()->route('materials.index')->with('success', 'تم إنشاء المادة بنجاح');
    }

     public function show(Material $material)
     {
         return view('materials.show', compact('material'));
     }
     
    // Controller: MaterialController.php

    public function edit($id)
    {
        // الحصول على المادة الدراسية مع الصفوف والمدرسين
        $material = Material::findOrFail($id);

        // الحصول على الصفوف الدراسية
        $grades = Grade::all();

        // تمرير البيانات إلى العرض
        return view('materials.edit', compact('material', 'grades'));
    }

     
   public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_id' => 'required|exists:grades,id',
            'main_book' => 'nullable|mimes:pdf|max:20480',
            'activity_book' => 'nullable|mimes:pdf|max:20480',
        ]);

        $data = $request->only(['name', 'description', 'grade_id']);

        // رفع كتاب المادة الرئيسي
        if ($request->hasFile('main_book')) {
            if ($material->main_book_path) {
                Storage::disk('public')->delete($material->main_book_path);
            }

            $originalName = $request->file('main_book')->getClientOriginalName();
            $path = $request->file('main_book')->storeAs('books', $originalName, 'public');
            $data['main_book_path'] = $path;
        }

        // رفع كتاب الأنشطة إن وجد
        if ($request->hasFile('activity_book')) {
            if ($material->activity_book_path) {
                Storage::disk('public')->delete($material->activity_book_path);
            }

            $originalName = $request->file('activity_book')->getClientOriginalName();
            $path = $request->file('activity_book')->storeAs('books', $originalName, 'public');
            $data['activity_book_path'] = $path;
        }

        $material->update($data);

        return redirect()->route('materials.index')->with('success', 'تم تحديث المادة بنجاح');
    }


     
     public function destroy(Material $material)
     {
         $material->delete();
         return redirect()->route('materials.index')->with('success', 'تم حذف المادة');
     }
}
