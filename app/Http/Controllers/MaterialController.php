<?php

namespace App\Http\Controllers;
use App\Models\Material;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index()
     {
         $materials = Material::all();
         return view('materials.index', compact('materials'));
     }
     
     public function create()
     {
        $grades = Grade::all(); // في دالة create()
        // $teachers = User::role('teacher')->get(); // يجلب كل المستخدمين الذين لديهم دور "teacher"

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
