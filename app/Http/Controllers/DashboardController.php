<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with user roles and permissions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user(); // الحصول على المستخدم الحالي

        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط

        // الحصول على صلاحيات المستخدم
        $permissions = $user->getAllPermissions();
        return view("index", [
            'role' => $role,
            'permissions' => $permissions
        ]);
        // إرجاع الـ View مع البيانات
        // return view('home', [
        //     'role' => $role,
        //     'permissions' => $permissions
        // ]);
    }
}
