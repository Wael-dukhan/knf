<?php

namespace App\Http\Controllers;

use App\Models\QuranClass;
use App\Models\QuranLevel;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\User;

class QuranClassesController extends Controller
{
    // عرض جميع الحلقات
    public function index()
    {
        $quranClasses = QuranClass::with(['quranLevel.school', 'quranTeacher', 'students'])->get();
        $schools = School::all();
        $teachers = User::role('quran_teacher')->get();

        return view('quran-classes.index', compact('quranClasses', 'schools', 'teachers'));
    }


    // صفحة إنشاء حلقة جديدة
    public function create()
    {
        $quranLevels = QuranLevel::all();
        $teachers = User::role('quran_teacher')->get();
        return view('quran-classes.create', compact('quranLevels', 'teachers'));
    }

    // حفظ حلقة جديدة
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'quran_level_id' => 'required|exists:quran_levels,id',
            'teacher_id' => 'required|exists:users,id', // Assuming teachers are users
        ]);

        // إذا التحقق تم بنجاح، ننشئ الحلقة القرآنية
        $quranClass = QuranClass::create($validatedData);

        return redirect()->route('quran-classes.index')
                        ->with('success', __('Quran class created successfully.'));
    }


    // عرض تفاصيل حلقة معينة
    public function show(QuranClass $quranClass)
    {   
        $quranClass->load('quranTeacher', 'students', 'quranLevel');
        // dd($quranClass);
        return view('quran-classes.show', compact('quranClass'));
    }

    // صفحة تعديل حلقة
    public function edit(QuranClass $quranClass)
    {
        $quranLevel = QuranLevel::where('school_id', $quranClass->quranLevel->school_id)
                                ->get();
        $teachers = User::role('quran_teacher')->where('school_id' , $quranClass->quranLevel->school_id)->get();
        return view('quran-classes.edit', compact('quranClass', 'quranLevel', 'teachers'));
    }

    // تحديث بيانات حلقة
    public function update(Request $request, QuranClass $quranClass)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quran_level_id' => 'required|exists:quran_levels,id',
            'teacher_id' => 'nullable|exists:users,id',
        ]);
        // dd($request->all());
        $quranClass->update($request->all());

        return redirect()->route('quran-levels.show' , $quranClass->quranLevel->id)->with('success', 'Quran class updated successfully.');
    }

    // حذف حلقة
    public function destroy(QuranClass $quranClass)
    {
        $quranClass->delete();
        return redirect()->route('quran-classes.index')->with('success', 'Quran class deleted successfully.');
    }

    // إضافة طالب إلى حلقة
    public function addStudent(Request $request, QuranClass $quranClass)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        // تحقق مما إذا كان الطالب مسجلاً بالفعل في هذه الحلقة
        if ($quranClass->isStudentEnrolled($request->student_id)) {
            return redirect()->back()->with('error', 'Student is already enrolled in this class.');
        }

        // إضافة الطالب إلى الحلقة
        $quranClass->students()->attach($request->student_id, ['status' => 'active']);

        return redirect()->route('quran-classes.show', $quranClass->id)
                        ->with('success', 'Student added to the Quran class successfully.');
    }
    // إزالة طالب من حلقة
    public function removeStudent(Request $request, QuranClass $quranClass)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        // تحقق مما إذا كان الطالب مسجلاً في هذه الحلقة
        if (!$quranClass->isStudentEnrolled($request->student_id)) {
            return redirect()->back()->with('error', 'Student is not enrolled in this class.');
        }

        // إزالة الطالب من الحلقة
        $quranClass->students()->detach($request->student_id);

        return redirect()->route('quran-classes.show', $quranClass->id)
                        ->with('success', 'Student removed from the Quran class successfully.');
    }

    public function assignStudentsForm(QuranClass $quranClass)
    {
        // جلب الطلاب الذين ينتمون لنفس المدرسة والمستوى القرآني المرتبط بالحلفة
        $schoolId = $quranClass->quranLevel->school_id;  // افتراضياً الحلقة مرتبطة بمستوى يحتوي على مدرسة

        // الطلاب الذين ينتمون للمدرسة، لديهم دور student، ولم يتم تعيينهم لهذه الحلقة (باستثناء soft deleted)
        $students = User::role('student')
                        ->where('school_id', $schoolId)
                        ->whereDoesntHave('quranClasses', function($query) use ($quranClass) {
                            $query->where('quran_class_id', $quranClass->id);
                        })
                        ->get();

        return view('quran-classes.assign-students', compact('quranClass', 'students'));
    }

    // تعيين طلاب لحلقة قرآنية
    public function assignStudents(Request $request, QuranClass $quranClass)
    {
        // dd($request->all() , $quranClass);
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id', // التأكد من أن كل id طالب موجود في قاعدة البيانات
        ]);

        // إضافة الطلاب إلى الحلقة
        foreach ($request->student_ids as $studentId) {
            if (!$quranClass->isStudentEnrolled($studentId)) {
                $quranClass->students()->attach($studentId, [
                    'status' => 'active',
                    'joined_at' => now(),
                    'quran_level_id' => $quranClass->quran_level_id, // افتراضياً نربط المستوى القرآني
                ]);
            }
        }

        return redirect()->route('quran-classes.show', $quranClass->id)
                        ->with('success', 'Students assigned to the Quran class successfully.');
    }
}
