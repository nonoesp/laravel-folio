<?php

namespace Nonoesp\Folio\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/admin';

    protected function loggedOut(Request $request)
    {
        // Default is '/'
        return redirect(route('login')); // Nono+
    }

    // protected function authenticated(Request $request) {
    //     session(['email' => \Auth::user()->email]); // Nono+
    // }

    public function logout(Request $request)
    {
        $email = \Auth::user()->email; // Nono+
        
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        session(['email' => $email]); // Nono+

        return $this->loggedOut($request) ?: redirect('/');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        // throw \Illuminate\Validation\ValidationException::withMessages([
        //     $this->username() => [trans('auth.failed')],
        // ]);
        return redirect()->back()->with('error', 'invalid-credentials'); // Nono+
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
}
