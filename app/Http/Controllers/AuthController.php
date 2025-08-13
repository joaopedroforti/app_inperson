<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Company;

class AuthController extends Controller
{
    // Exibir o formulário de login
    public function showLoginForm()
    {
        return view('auth.login');
    }
    // Processar login
    public function login(Request $request)
    {
        
        // Validar dados de login
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Verifica se o usuário existe e as credenciais estão corretas
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Buscar empresa vinculada ao usuário
            $company = Company::where('id_company', $user->id_company)->first();

            // Salvar dados na sessão
            session([
                'user' => $user,
                'rule' => $user?->job_title,
                'company_name' => $company?->company_name,
                'company_id' => $company?->id_company,
                'company_reference' => $company?->reference,
            ]);

            return redirect()->route('Dashboard');
        }

        // Se as credenciais estiverem erradas
        return back()->withErrors([
            'email' => 'As credenciais fornecidas não são válidas.',
        ]);
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        session()->forget(['user', 'company_name', 'company_reference']);
        return redirect()->route('login');
    }

    public function test()
    {
        dd(session('company_name'));

    }
}
