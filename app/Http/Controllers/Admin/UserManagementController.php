<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function create()
    {
        // ddd('admin');
        $roles = Role::pluck('name', 'id'); // جلب الأدوار

        return view('admin.users.create',['roles'=> $roles]);
    }

    public function index()
    {
        $users = User::all();  // الحصول على جميع المستخدمين من قاعدة البيانات
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role_id'=> $request->role_id,
        ]);

        $role = Role::find($request->role_id);
        $user->assignRole($role);

        return redirect()->route('admin.users.create')->with('success', 'تم إنشاء المستخدم وتعيين الدور بنجاح.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::pluck('name', 'id'); // جلب الأدوار
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        // تحديث الدور
        $role = Role::find($request->role_id);
        $user->syncRoles([$role]);

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح.');
    }
    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $role = Role::findOrFail($request->role_id);

        $user->assignRole($role);

        return redirect()->route('admin.users.index')->with('success', 'تم تعيين الدور للمستخدم بنجاح.');
    }
    public function removeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $role = Role::findOrFail($request->role_id);

        $user->removeRole($role);

        return redirect()->route('admin.users.index')->with('success', 'تم إزالة الدور من المستخدم بنجاح.');
    }
    public function getUserRoles($id)
    {
        $user = User::findOrFail($id);
        $roles = $user->getRoleNames();

        return response()->json($roles);
    }
    public function getUserPermissions($id)
    {
        $user = User::findOrFail($id);
        $permissions = $user->getAllPermissions();

        return response()->json($permissions);
    }
    public function getUserByRole($role)
    {
        $users = User::role($role)->get();

        return response()->json($users);
    }
    public function getUserBySchool($schoolId)
    {
        $users = User::where('school_id', $schoolId)->get();

        return response()->json($users);
    }
    public function getUserByClass($classId)
    {
        $users = User::where('class_id', $classId)->get();

        return response()->json($users);
    }
    public function getUserByGrade($gradeId)
    {
        $users = User::where('grade_id', $gradeId)->get();

        return response()->json($users);
    }
    public function getUserBySubject($subjectId)
    {
        $users = User::where('subject_id', $subjectId)->get();

        return response()->json($users);
    }
    public function getUserByStatus($status)
    {
        $users = User::where('status', $status)->get();

        return response()->json($users);
    }

}
