@extends('layouts/layoutMaster')

@section('title', 'Configurações da Empresa')

@section('content')
<form method="POST" action="{{ route('settings.update') }}">
    @csrf
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-start justify-content-between">
                <div class="col-md-6 d-flex flex-column justify-content-between">
                    <h4 class="mb-2">Configurações</h4>
                </div>
                <div class="col-md-3 d-flex justify-content-end">
                    <div class="d-flex align-items-start gap-2">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="/dashboard" class="btn btn-outline-danger">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Configurações da Empresa</h5>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Linha 1: Razão Social e CNPJ --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="company_name" class="form-label">Razão Social</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $company->company_name }}" required>
                </div>
                <div class="col-md-6">
                    <label for="cnpj" class="form-label">CNPJ</label>
                    <input readonly type="text" class="form-control" id="cnpj" name="cnpj" value="{{ $company->cnpj }}" required>
                </div>
            </div>

            {{-- Linha 2: Usuários (Administrador e RH) --}}
            <div class="row mb-4">
                <h5 class="mt-4 mb-3">Usuários</h5>
                <div class="col-md-6">
                    <label for="master_user" class="form-label">Administrador (master)</label>
                    <select class="form-select" id="master_user" name="master_user">
                        <option value="">Selecione um usuário...</option>
                        {{-- Loop para usuários aqui --}}
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="rh_user" class="form-label">Recursos Humanos (RH)</label>
                    <select class="form-select" id="rh_user" name="rh_user">
                        <option value="">Selecione um usuário...</option>
                        {{-- Loop para usuários aqui --}}
                    </select>
                </div>
            </div>

            {{-- Linha 3: Endereço --}}
            <h5 class="mt-4 mb-3">Endereço</h5>
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <label for="zip_code" class="form-label">CEP*</label>
                    <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ $company->zip_code }}" required>
                </div>
                <div class="col-md-3">
                    <label for="address_state" class="form-label">Estado *</label>
                    <input type="text" class="form-control" id="address_state" name="address_state" value="{{ $company->address_state }}" required>
                </div>
                <div class="col-md-3">
                    <label for="address_city" class="form-label">Cidade*</label>
                    <input type="text" class="form-control" id="address_city" name="address_city" value="{{ $company->address_city }}" required>
                </div>
                <div class="col-md-3">
                    <label for="address_district" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="address_district" name="address_district" value="{{ $company->address_district }}" required>
                </div>
            </div>
            <div class="row mb-4 g-3">
                <div class="col-md-9">
                    <label for="address_street" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="address_street" name="address_street" value="{{ $company->address_street }}" required>
                </div>
                <div class="col-md-3">
                    <label for="address_number" class="form-label">Número</label>
                    <input type="text" class="form-control" id="address_number" name="address_number" value="{{ $company->address_number }}" required>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <label for="address_complement" class="form-label">Complemento</label>
                    <input type="text" class="form-control" id="address_complement" name="address_complement" value="{{ $company->address_complement }}">
                </div>
            </div>

            {{-- Outros campos que não estão na imagem mas estão no modelo --}}
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <label for="webhook_link" class="form-label">Webhook Link</label>
                    <input type="url" class="form-control" id="webhook_link" name="webhook_link" value="{{ $company->webhook_link }}">
                </div>
                <div class="col-md-6">
                    <label for="api_key" class="form-label">API Key</label>
                    <input disabled type="text" class="form-control" id="api_key" name="api_key" value="{{ $company->api_key }}">
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('zip_code').addEventListener('change', function() {
            const cep = this.value.replace(/\D/g, ''); // Remove non-digit characters
            if (cep.length === 8) { // Valid Brazilian CEP length
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('address_street').value = data.logradouro || '';
                            document.getElementById('address_district').value = data.bairro || '';
                            document.getElementById('address_city').value = data.localidade || '';
                            document.getElementById('address_state').value = data.uf || '';
                            document.getElementById('address_complement').value = data.complemento || '';
                            // address_number is not updated
                        } else {
                            alert('CEP não encontrado.');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao consultar o ViaCEP:', error);
                        alert('Erro ao consultar o CEP. Tente novamente.');
                    });
            } else {
                alert('Por favor, insira um CEP válido com 8 dígitos.');
            }
        });
    </script>
</form>
@endsection