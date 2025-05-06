<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {

         // Validação dos dados
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Verifica as credenciais do usuário
        $user = User::where('email', $request->email)->first();

        // Verifica credenciais
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
                ]);
        }

        // Verifica status do usuário
        if ($user->status == "inativo") {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'status' => 'Sua conta está inativa. Entre em contato com o administrador.',
                ]);
        }

        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json(['message' => 'Credenciais inválidas'], 401);
        // }

        // if($user->status == "inativo"){
        //     //return redirect()->back()->with('erro','Usuário sem acesso.');
        //     return redirect()>back()->json(['message' => 'Usuário sem acesso'], 401);
        // }
        
        $request->authenticate();

        // Gerar o token com Sanctum
        $token = $user->createToken('API token')->plainTextToken;

        // Autenticar o usuário usando o token
        Auth::login($user);

        // Regenerar a sessão
        $request->session()->regenerate();

        if (Auth::user()->hasAnyPermission(['knowledge.add', 'knowledge.edit', 'knowledge.delete'])) {
            //return redirect()->intended(route('knowledge.list', absolute: false));
            return redirect()->route('knowledge.list');
        } elseif (Auth::user()->hasAnyPermission(['projects.all', 'projects.my', 'projects.all.manage',  'projects.all.add', 'projects.all.edit', 'projects.all.delete', 'projects.my.manage', 'projects.my.add', 'projects.my.edit', 'projects.my.delete'])) {
            //return redirect()->intended(route('project.list', absolute: false));
            return redirect()->route('project.list');
        }else{
            return redirect()->intended(route('knowledge.list', absolute: false));
        }

       
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
