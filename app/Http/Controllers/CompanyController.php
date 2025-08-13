<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
class CompanyController extends Controller
{


    public function endereco($id)
    {
        $empresa = Company::find($id);
    
        if (!$empresa) {
            return response()->json(['erro' => 'Empresa não encontrada'], 404);
        }
    
        return response()->json([
            'pais'         => $empresa->country,
            'cep'          => $empresa->zip_code,
            'logradouro'   => $empresa->address_street,
            'numero'       => $empresa->address_number,
            'bairro'       => $empresa->address_district,
            'complemento'  => $empresa->address_complement,
            'cidade'       => $empresa->address_city,
            'estado'       => $empresa->address_state,
        ]);
    }




    public function index()
    {
        // 1. Obtém o company_id da sessão
        $companyId = Session::get('company_id');

        // 2. Busca os dados da empresa no banco
        $company = Company::find($companyId);

        // 3. Verifica se a empresa existe e a passa para a view
        if (!$company) {
            // Pode redirecionar ou mostrar um erro caso a empresa não seja encontrada
            return redirect()->back()->with('error', 'Empresa não encontrada.');
        }

        // Passa a variável $company para a view
        return view('settings', compact('company'));
    }


    public function update(Request $request)
    {
        // Validação dos dados do formulário
        $request->validate([
            'company_name' => 'required|string|max:255',
            'zip_code' => 'required|string|max:9',
            'address_street' => 'required|string|max:255',
            'address_number' => 'required|string|max:50',
            'address_district' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_state' => 'required|string|max:255',
            'address_complement' => 'nullable|string|max:255',
            'webhook_link' => 'nullable|url|max:255',
        ]);

        $companyId = Session::get('company_id');
        $company = Company::find($companyId);

        if (!$company) {
            return redirect()->back()->with('error', 'Empresa não encontrada.');
        }

        // Preenche o modelo com os dados da requisição
        $company->update([
            'company_name' => $request->input('company_name'),
            'zip_code' => $request->input('zip_code'),
            'address_street' => $request->input('address_street'),
            'address_number' => $request->input('address_number'),
            'address_district' => $request->input('address_district'),
            'address_complement' => $request->input('address_complement'),
            'address_city' => $request->input('address_city'),
            'address_state' => $request->input('address_state'),
            'webhook_link' => $request->input('webhook_link'),
        ]);

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
    }




    public function settings()
    {
        $companyId = session('company_id');

        // 2. Buscar a empresa no banco de dados
        $company = Company::find($companyId);
    dd($company);
        // 3. Passar a variável para a view
        return view('dashboard', ['company' => $company]);
    }
    

    
}