<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\StudentParentController;
use App\Http\Controllers\StudentClassSectionController;
use App\Http\Controllers\MaterialTeacherAssignmentController;
use App\Http\Controllers\StudentAttendanceRecordController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ClassSectionController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuranLevelController;
use App\Http\Controllers\QuranClassesController;
use App\Http\Controllers\QuranTeacherAttendanceController;
use App\Http\Controllers\QuranStudentAttendanceController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\MarkEntryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['web']], function () {
    Route::get('/set-locale/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'ar'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    })->name('setLocale');
});


Route::post('/logout', function () {
    Auth::logout();  // تسجيل الخروج
    return redirect('/login');  // إعادة توجيه إلى الصفحة الرئيسية أو الصفحة التي تريدها
})->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('/register', [RegisteredUserController::class, 'create'])
    ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    // dd('guest');
});


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::resource('materials', MaterialController::class)->middleware(['auth','role:super_admin|school_manager|teacher|student']);

Route::get('/material-assignments', [MaterialTeacherAssignmentController::class, 'index'])->name('material-assignments.index');
Route::get('/class-sections/{id}/material-assignments', [MaterialTeacherAssignmentController::class, 'show'])->name('material-assignments.show');
Route::get('/class-sections/{classSectionId}/material-assignment/{materialId}/create', [MaterialTeacherAssignmentController::class, 'create'])->name('material-assignment.create');
Route::post('/material-assignments', [MaterialTeacherAssignmentController::class, 'store'])->name('material-assignment.store');
Route::get('/class-sections/assign-material/{assignmentId}', [MaterialTeacherAssignmentController::class, 'edit'])->name('material-assignment.edit');
Route::put('/material-assignments/{assignmentId}', [MaterialTeacherAssignmentController::class, 'update'])->name('material-assignment.update');
Route::delete('/material-assignments/{assignment}', [MaterialTeacherAssignmentController::class, 'destroy'])
    ->name('material-assignments.destroy');

Route::prefix('parent')->middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('parent.users.show');
    Route::get('/children', [ParentController::class, 'childrenIndex'])->name('parent.children.index');
});

// عرض النموذج لتعيين طالب لشعبة
Route::get('/grades/{gradeId}/assign-student', [StudentClassSectionController::class, 'create'])->name('student.assign.create');

// معالجة طلب تعيين الطالب إلى الشعبة
Route::post('/grades/{gradeId}/assign-student', [StudentClassSectionController::class, 'assign'])->name('student.assign');

Route::delete('/admin/class_sections/{classSectionId}/{studentId}/{academicYearId}', [StudentClassSectionController::class, 'destroy'])->name('student.class_sections.delete');

// مسار عرض الطلاب وأولياء الأمور
Route::prefix('students')->name('students.')->group(function () {
    // عرض الطلاب وأولياء الأمور
    Route::get('/parents', [StudentParentController::class, 'index'])->name('parents.index');

    // عرض نموذج إضافة أولياء الأمور
    Route::get('/parents/create', [StudentParentController::class, 'create'])->name('parents.create');

    // تخزين أولياء الأمور للطلاب
    Route::post('/parents', [StudentParentController::class, 'store'])->name('parents.store');

    // عرض نموذج تعديل أولياء الأمور
    Route::get('{student}/parents/{parent}/edit', [StudentParentController::class, 'edit'])->name('parents.edit');

    // تحديث أولياء الأمور للطلاب
    Route::put('{student}/parents/{parent}', [StudentParentController::class, 'update'])->name('parents.update');

    // حذف أولياء الأمور
    Route::delete('{student}/parents/{parent}', [StudentParentController::class, 'destroy'])->name('parents.destroy');
})->middleware(['auth','role:super_admin|school_manager']);

Route::resource('users', UserController::class)->middleware(['auth', 'role:super_admin|school_manager']);

Route::middleware(['auth', 'role:super_admin|school_manager|quran_supervisor|quran_teacher|teacher'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update'); // للتحديث
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy'); // للحذف

    Route::get('/schools/{schoolId}/quran-teachers', [QuranClassesController::class, 'getQuranTeachersBySchool'])->name('schools.quran-teachers');

    Route::resource('schools', SchoolController::class);
    
    Route::resource('academic_years', AcademicYearController::class);
    
    Route::resource('terms', TermController::class);
    
    Route::resource('grades', GradeController::class);

    Route::get('class_sections/grades-by-school/{school_id}', [GradeController::class, 'getGradesBySchool'])->name('grades.by_school');

    Route::resource('class_sections', ClassSectionController::class);


});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/grades', [GradeController::class, 'showTeacherGrades'])->name('grades.show');
    Route::get('class_sections/{class_section}', [ClassSectionController::class, 'show'])->name('class_sections.show');

});

Route::get('schools/{school}/grade_levels', [App\Http\Controllers\SchoolController::class, 'gradeLevels'])
    ->name('grade_levels.index');
Route::get('my-school/grade_levels', [App\Http\Controllers\SchoolController::class, 'my_school_gradeLevels'])
    ->name('my-school-gradeLevels');
Route::get('schools/{school}/grade_levels/{grade_level}', [App\Http\Controllers\GradeLevelController::class, 'show'])
    ->name('grade_levels.show');
Route::get('student/class_sections/{classSection}/edit', [StudentClassSectionController::class, 'edit'])
    ->name('student.class_sections.edit');
Route::put('student/class_sections/{classSection}/update', [StudentClassSectionController::class, 'update'])
    ->name('student.class_sections.update');


Route::middleware(['auth'])->group(function () {
    Route::get('/my-profile', [UserController::class, 'showCurrentUser'])->name('profile.show'); // عرض بيانات المستخدم الحالي
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // تعديل بيانات الملف الشخصي
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});


// Route لتحديث كلمة المرور (بعد نسيانها)
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');


Route::post('/students/{student}/update-status', [StudentController::class, 'updateStatus'])
     ->name('students.updateStatus');



Route::get('/attendance/{classSection}/index', [StudentAttendanceRecordController::class, 'index'])->name('attendance.index');
Route::post('/attendance/ajax-update', [StudentAttendanceRecordController::class, 'ajaxUpdate'])->name('attendance.ajaxUpdate');


Route::get('/teacher-attendance/{schoolId}/index', [TeacherAttendanceController::class, 'index'])->name('teacher-attendance.index');
Route::post('/teacher-attendance/ajax-update', [TeacherAttendanceController::class, 'ajaxUpdate'])->name('teacher-attendance.ajaxUpdate');


// Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::resource('quran-levels', QuranLevelController::class);
    
    // مسار مخصص لتقارير أو إجراءات أخرى
    Route::get('quran-levels/report', [QuranLevelController::class, 'report'])->name('quran-levels.report');
// });

Route::resource('quran-classes', QuranClassesController::class);

Route::get('/schools/{schoolId}/quran-levels', [App\Http\Controllers\QuranLevelController::class, 'getQuranLevelsBySchool'])
    ->name('schools.quran-levels');

Route::get('/schools/{schoolId}/quran-levels/json', [App\Http\Controllers\QuranLevelController::class, 'getJsonQuranLevelsBySchool'])
    ->name('schools.quran-levels.json');

Route::middleware(['auth', 'role:super_admin|quran_supervisor'])->prefix('quran-classes')->group(function () {

    // عرض نموذج اختيار الطلاب لتعيينهم لحلقة قرآنية (GET)
    Route::get('{quranClass}/assign-students', [QuranClassesController::class, 'assignStudentsForm'])
        ->name('quranClass.assign_students.form');

    // استقبال الطلب وتعيين الطلاب للحلقة (POST)
    Route::post('{quranClass}/assign-students', [QuranClassesController::class, 'assignStudents'])
        ->name('quranClass.assign_students.store');

});



Route::prefix('quran-teacher-attendance')->middleware(['auth'])->group(function () {
    
    // صفحة عرض حضور معلمي حلقات القرآن حسب المدرسة (مع تمرير schoolId)
    Route::get('/{schoolId}', [QuranTeacherAttendanceController::class, 'index'])
        ->name('quran_teacher_attendance.index');

    // تحديث الحضور عبر AJAX
    Route::post('/ajax-update', [QuranTeacherAttendanceController::class, 'ajaxUpdate'])
        ->name('quran_teacher_attendance.ajaxUpdate');
});

Route::get('quran-classes/{quranClass}/attendance', [QuranStudentAttendanceController::class, 'index'])->name('quran_student_attendance.index');
Route::post('quran-classes/attendance/ajax-update', [QuranStudentAttendanceController::class, 'ajaxUpdate'])->name('quran_student_attendance.ajaxUpdate');

Route::prefix('quran-teacher')->middleware(['auth', 'role:quran_teacher'])->name('quran-teacher.')->group(function () {

    Route::get('myLevelsWithClasses', [QuranLevelController::class, 'myLevelsWithClasses'])->name('myLevelsWithClasses');

    // عرض الحلقات التي يُدرسها معلم القرآن الحالي
    Route::get('/classes', [QuranClassesController::class, 'index'])
        ->name('classes.index');

    // عرض صفحة الحضور الخاصة بمعلم الحلقة
    Route::get('/attendance', [QuranTeacherAttendanceController::class, 'index'])
        ->name('attendance.index');

});

// تمرير المادة والشعبة (مثلاً المادة ID = 5، الشعبة ID = 2)
Route::get('/marks/create/{material}/{section}', [MarkEntryController::class, 'create'])->name('marks.create');
Route::post('/marks/store', [MarkEntryController::class, 'store'])->name('marks.store');