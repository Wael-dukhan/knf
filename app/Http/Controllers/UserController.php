<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
        // في حالة المشرف العام، يمكن عرض جميع المستخدمين
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع المستخدمين
            $users = User::with('school')->get();
            $schools = School::all(); // للحصول على كل المدارس
            $roles = Role::all();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض المستخدمين الخاصين بالمدرسة التي يديرها
            $users = User::with('school')
                ->where('school_id', $user->school_id)
                ->get();
            $schools = School::where('id', $user->school_id)->get();
            $roles = Role::all()->skip(1); // تخطي دور "admin"
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        // $users = User::with('school')->get();
        // $schools = School::all(); // للحصول على كل المدارس
        // $roles = Role::all();
    
        return view('users.index', compact('users', 'schools','roles'));
    }
    

    public function create()
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع الأدوار والمدارس
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع الأدوار والمدارس
            $roles = Role::all()->skip(1); // تخطي دور "admin"
            $schools = School::all();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض الأدوار الخاصة بالمدرسة التي يديرها
            $roles = Role::whereNotIn('name', ['super_admin', 'school_manager'])->get();
            $schools = School::where('id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        // $roles = Role::all()->skip(1); // تخطي دور "admin"
        // $schools = School::all(); // جلب جميع المدارس
        $parents = User::role('parent')->get(); // استرجاع الطلاب
        return view('users.create', compact('roles', 'schools', 'parents'));
    }

    
    public function store(Request $request)
    {
        // dd($request->all());
        // التحقق من أن هناك مديرًا في نفس المدرسة
        if ($request->role_id == 2 && User::where('school_id', $request->school_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'manager');
            })->exists()) {
            return redirect()->back()->withErrors(['school_id' => 'هناك مدير موجود بالفعل لهذه المدرسة.']);
        }

        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'school_id' => 'required|exists:schools,id', // تأكيد أن المدرسة موجودة
            'gender' => 'required|in:male,female'
        ]);

        // إنشاء المستخدم الجديد
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => $request->school_id, // إضافة المدرسة
            'gender' => $request->gender
        ]);

        // تعيين الدور للمستخدم باستخدام laravel-permission
        $role = Role::findById($request->role_id);
        $user->assignRole($role);

        
        if ($request->role_id == 4) {
            $request->validate([
                'parent_id' => 'required|exists:users,id',
            ]);

            $parentId = $request->parent_id;

            // Many-to-Many relationship
            $user->parents()->syncWithoutDetaching([$parentId]);

        }

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع الأدوار والمدارس
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع الأدوار والمدارس
            $roles = Role::all()->skip(1); // تخطي دور "admin"
            $schools = School::all();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض الأدوار الخاصة بالمدرسة التي يديرها
            $roles = Role::whereNotIn('name', ['super_admin', 'school_manager'])->get();
            $schools = School::where('id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        // $roles = Role::all()->skip(1); // تخطي دور "admin"
        // $schools = School::all(); // جلب جميع المدارس
        $parents = User::role('parent')->get(); // استرجاع الطلاب
        // dd($user);
        // // إذا كان المستخدم هو مدير، تأكد من أن هناك مديرًا في نفس المدرسة
        // if ($userRole == 'manager' && User::where('school_id', $user->school_id)
        //     ->whereHas('roles', function ($query) {
        //         $query->where('name', 'manager');
        //     })->exists()) {
        //     return redirect()->back()->withErrors(['school_id' => 'هناك مدير موجود بالفعل لهذه المدرسة.']);
        // }

        return view('users.edit', compact('user','roles', 'schools', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'school_id' => 'required|exists:schools,id',
            'gender' => 'required|in:male,female'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->school_id = $request->school_id;
        $user->gender = $request->gender;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        // dd($user);
        $user->save();

        // Sync role
        $user->syncRoles([Role::findById($request->role_id)->name]);

        return redirect()->route('users.index')->with('success', __('messages.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}
