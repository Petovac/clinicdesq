<?php

namespace App\Http\Controllers\PetParent;

use App\Http\Controllers\Controller;
use App\Models\PetParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentAuthController extends Controller
{
    public function showLoginForm()
    {
        $a = rand(1, 20);
        $b = rand(1, 20);
        session(['captcha_answer' => $a + $b]);

        return view('parent.auth.login', [
            'captcha_question' => "What is {$a} + {$b}?",
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'          => 'required|string',
            'captcha_answer' => 'required|integer',
        ]);

        if ((int) $request->captcha_answer !== (int) session('captcha_answer')) {
            return back()->withInput()->withErrors(['captcha_answer' => 'Incorrect answer. Please try again.']);
        }

        $parent = PetParent::where('phone', $request->phone)->first();

        if (!$parent) {
            return back()->withInput()->withErrors(['phone' => 'No account found with this phone number.']);
        }

        Auth::guard('pet_parent')->loginUsingId($parent->id);
        session()->regenerate();

        return redirect()->route('parent.dashboard');
    }

    public function logout()
    {
        Auth::guard('pet_parent')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('parent.login');
    }
}
