<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';
    protected $redirectTo = self::redirectTo();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public static function redirectTo()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return '/home';
        } elseif ($user->hasRole('school_manager')) {
            return 'my-school/grade_levels';
        } elseif ($user->hasRole('teacher')) {
            return '/teacher/grades';
        } elseif ($user->hasRole('quran_teacher')) {
            return '/quran-teacher/myLevelsWithClasses';
        } elseif ($user->hasRole('parent')) {
            return '/teacher/dashboard';
        } elseif ($user->hasRole('student')) {
            return '/student/dashboard';
        } 
        else {
            return '/home'; // القيمة الافتراضية
        }
    }

}
