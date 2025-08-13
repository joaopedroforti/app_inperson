@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')

<form method="POST" action="/candidate/new" id="employeeForm">
@csrf

<div class="card mb-4">
  <div class="card-body">
    <div class="row align-items-start justify-content-between">

      <div class="col-auto text-center">
  <div style="background-color: #e8ddfb; border-radius: 100px; width: 100px; height: 100px;">
    <img
      id="profile_pic"
      name="profile_pic"
   src="{{ $person->profile_pic_base64 ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8lRbS7eKYzDq-Ftxc1p8G_TTw2unWBMEYUw&s' }}"
      alt="Foto de Perfil"
      class="rounded-circle"
      style="width: 100px; height: 100px; object-fit: cover;"
    />
  </div>
  
  <input
    type="file"
    id="profile_pic_input"
    accept="image/*"
    onchange="uploadProfilePic(this)"
    hidden
  />
  <input type="hidden" name="profile_pic_base64" id="profile_pic_base64" value="{{ $person->profile_pic_base64 ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8lRbS7eKYzDq-Ftxc1p8G_TTw2unWBMEYUw&s' }}">

 
  <small class="text-muted d-block mt-1">
    <a href="javascript:void(0)" onclick="document.getElementById('profile_pic_input').click()">Adicionar</a> |
    <a href="javascript:void(0)" onclick="resetProfilePic()">Excluir</a>
  </small>
</div>
<input hidden name="id_person" id="reference" value="{{ $person->id_person}}">




      <div class="col-md-6 d-flex flex-column justify-content-between" style="min-height: 100px;">
        <h4 class="mb-2">{{ $person->full_name ?? 'Cadastro Colaborador' }}</h4>

        <div class="d-flex align-items-center gap-3 mt-auto flex-wrap">
          <button class="btn btn-primary d-flex align-items-center gap-1 px-3 py-2">
            <i class="mdi mdi-file-document-outline"></i>
            <span><strong>Cadastro</strong></span>
          </button>
         
          <div class="text-muted d-flex align-items-center gap-1 cursor-pointer">
            <i class="mdi mdi-account-outline"></i>
            <a href="{{ route('Perfil Comportamental Candidato', Crypt::encryptString($person['id_person'])) }}"><span><strong>Perfil Comportamental</strong></span></a>
          </div>
         
          <i class="mdi mdi-file-document-outline"></i>
          <a href="{{ route('Historico do Candidato', Crypt::encryptString($person['id_person'])) }}"><span><strong>Recrutamentos</strong></span></a>
         

        </div>
      </div>

      <div class="col-md-3 d-flex justify-content-end">
        <div class="d-flex align-items-start gap-2">
          <button class="btn btn-primary" type="submit">Salvar</button>
          <a href="/bancotalentos" class="btn btn-outline-danger">Cancelar</a>

        </div>
      </div>

    </div>
  </div>
</div>
<div class="card mb-4">
  <h5 class="card-header">
  <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
  Dados Pessoais
</h5>

    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label" for="full_name">Nome Completo</label>
          <input value="{{ $person->full_name ?? '' }}" name="full_name" type="text" id="full_name" class="form-control" placeholder="Nome Completo">
        </div>
        <div class="col-md-3">
          <label class="form-label" for="birth_date">Data de Nascimento</label>
          <input value="{{ $person->birth_date ?? '' }}" name="birth_date" type="date" id="birth_date" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label" for="id_gender">Gênero</label>
          <select name="id_gender" id="id_gender" class="form-select">
    <option value="">Selecionar</option>
    <option value="1" {{ isset($person->id_gender) && $person->id_gender == 1 ? 'selected' : '' }}>Masculino</option>
    <option value="2" {{ isset($person->id_gender) && $person->id_gender == 2 ? 'selected' : '' }}>Feminino</option>
    <option value="3" {{ isset($person->id_gender) && $person->id_gender == 3 ? 'selected' : '' }}>Outros</option>
  </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Estado Civil</label>
          <select name="id_marital_status" class="form-select">
    <option value="">Selecionar</option>
    <option value="1" {{ isset($person->id_marital_status) && $person->id_marital_status == 1 ? 'selected' : '' }}>Solteiro(a)</option>
    <option value="2" {{ isset($person->id_marital_status) && $person->id_marital_status == 2 ? 'selected' : '' }}>Casado(a)</option>
    <option value="3" {{ isset($person->id_marital_status) && $person->id_marital_status == 3 ? 'selected' : '' }}>Divorciado(a)</option>
    <option value="4" {{ isset($person->id_marital_status) && $person->id_marital_status == 4 ? 'selected' : '' }}>Viúvo(a)</option>
    <option value="5" {{ isset($person->id_marital_status) && $person->id_marital_status == 5 ? 'selected' : '' }}>União Estável</option>
  </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">E-mail Corporativo</label>
          <input value="{{ $person->corporate_email ?? '' }}" name="corporate_email" type="email" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Escolaridade</label>
          <select name="id_education_level" class="form-select">
    <option value="">Selecionar</option>
    <option value="1" {{ isset($person->id_education_level) && $person->id_education_level == 1 ? 'selected' : '' }}>Ensino Fundamental Incompleto</option>
    <option value="2" {{ isset($person->id_education_level) && $person->id_education_level == 2 ? 'selected' : '' }}>Ensino Fundamental Completo</option>
    <option value="3" {{ isset($person->id_education_level) && $person->id_education_level == 3 ? 'selected' : '' }}>Ensino Médio Incompleto</option>
    <option value="4" {{ isset($person->id_education_level) && $person->id_education_level == 4 ? 'selected' : '' }}>Ensino Médio Completo</option>
    <option value="5" {{ isset($person->id_education_level) && $person->id_education_level == 5 ? 'selected' : '' }}>Superior Incompleto</option>
    <option value="6" {{ isset($person->id_education_level) && $person->id_education_level == 6 ? 'selected' : '' }}>Superior Completo</option>
    <option value="7" {{ isset($person->id_education_level) && $person->id_education_level == 7 ? 'selected' : '' }}>Pós-graduação</option>
    <option value="8" {{ isset($person->id_education_level) && $person->id_education_level == 8 ? 'selected' : '' }}>Mestrado</option>
    <option value="9" {{ isset($person->id_education_level) && $person->id_education_level == 9 ? 'selected' : '' }}>Doutorado</option>
  </select>
        </div>
        <div class="col-md-3 d-flex align-items-center">
        <div class="form-check form-switch">
  <input 
    class="form-check-input" 
    type="checkbox" 
    id="pcdSwitch" 
    name="is_pcd" 
    {{ isset($person->id_disability_type) && $person->id_disability_type != 0 ? 'checked' : '' }}>
  <label class="form-check-label" for="pcdSwitch">Pessoa com Deficiência</label>      
</div>

        </div>
        <div class="col-md-3" id="disabilitySelect" style="display: {{ (isset($person->id_disability_type) && $person->id_disability_type != '' && $person->id_disability_type != 0) ? 'block' : 'none' }};">
  <label class="form-label">Tipo de Deficiência</label>
  <select name="id_disability_type" class="form-select">
    <option selected value="">Selecionar</option>
    <option value="1" {{ isset($person->id_disability_type) && $person->id_disability_type == 1 ? 'selected' : '' }}>Física</option>
    <option value="2" {{ isset($person->id_disability_type) && $person->id_disability_type == 2 ? 'selected' : '' }}>Auditiva</option>
    <option value="3" {{ isset($person->id_disability_type) && $person->id_disability_type == 3 ? 'selected' : '' }}>Visual</option>
    <option value="4" {{ isset($person->id_disability_type) && $person->id_disability_type == 4 ? 'selected' : '' }}>Intelectual</option>
    <option value="5" {{ isset($person->id_disability_type) && $person->id_disability_type == 5 ? 'selected' : '' }}>Múltipla</option>
    <option value="6" {{ isset($person->id_disability_type) && $person->id_disability_type == 6 ? 'selected' : '' }}>Transtorno do Espectro Autista (TEA)</option>
    <option value="7" {{ isset($person->id_disability_type) && $person->id_disability_type == 7 ? 'selected' : '' }}>Outra</option>
  </select>
</div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
<h5 class="card-header">
<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
Endereço
</h5>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">País</label>
        <select name="country" class="form-select" id="country">
          <option>Selecionar</option>
          <option selected value="Brasil">Brasil</option>
          </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">CEP</label>
        <div class="position-relative">
          <input value="{{ $person->zip_code ?? '' }}" name="zip_code" type="text" class="form-control" id="cep" maxlength="9">
          <div id="cep-loader" class="spinner-border text-primary position-absolute top-50 end-0 me-2 d-none" style="width: 1rem; height: 1rem;"></div>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Logradouro</label>
        <input readonly name="address_street" value="{{ $person->address_street ?? '' }}" type="text" class="form-control" id="logradouro">
      </div>
      <div class="col-md-3">
        <label class="form-label">Número</label>
        <input value="{{ $person->address_number ?? '' }}" name="address_number" type="text" class="form-control" id="numero">
      </div>
      <div class="col-md-3">
        <label class="form-label">Bairro</label>
        <input value="{{ $person->address_district ?? '' }}" name="address_district" type="text" class="form-control" id="bairro">
      </div>
      <div class="col-md-3">
        <label class="form-label">Complemento</label>
        <input value="{{ $person->address_complement ?? '' }}" name="address_complement" type="text" class="form-control" id="complemento">
      </div>
      <div class="col-md-3">
        <label class="form-label">Cidade</label>
        <select name="address_city" class="form-select" id="cidade">
          <option selected value="{{ $person->address_city ?? '' }}">{{ $person->address_city ?? '' }}</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Estado</label>
        <select name="address_state" class="form-select" id="estado">
        <option selected value="{{ $person->address_state ?? '' }}">{{ $person->address_state ?? '' }}</option>
        </select>
      </div>
    </div>
  </div>
</div>


  <div class="card mb-4">
  <h5 class="card-header">
  <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-description"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 17h6" /><path d="M9 13h6" /></svg>
  Documentos
</h5>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">CPF</label>
          <input value="{{ $person->cpf ?? '' }}" name="cpf" type="text" class="form-control" id="cpf">
        </div>
        <div class="col-md-3">
          <label class="form-label">CNPJ</label>
          <input value="{{ $person->cnpj ?? '' }}" name="cnpj" type="text" class="form-control" id="cnpj">
        </div>
        <div class="col-md-3 d-flex align-items-center">
  <div class="form-check mt-3">
  <input 
    name="foreigner" 
    class="form-check-input" 
    type="checkbox" 
    id="estrangeiro"
    {{ !empty($person->foreigner) ? 'checked' : '' }}>
<label class="form-check-label" for="estrangeiro">Estrangeiro</label>
  </div>
</div>

       

        
    
      </div>
    </div>
  </div>

 <div class="card mb-4">
  <h5 class="card-header">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
      viewBox="0 0 24 24" fill="none" stroke="currentColor"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
      <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>
    </svg>
    Contatos
  </h5>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Celular / WhatsApp</label>
        <input value="{{ $person->cellphone ?? '' }}" type="text" class="form-control" id="celular" name="cellphone" placeholder="(99) 99999-9999">
      </div>
      <div class="col-md-3">
        <label class="form-label">Telefone</label>
        <input value="{{ $person->phone ?? '' }}" type="text" class="form-control" id="telefone" name="phone" placeholder="(99) 9999-9999">
      </div>
      <div class="col-md-3">
        <label class="form-label">Telefone Emergência</label>
        <input value="{{ $person->emergency_phone ?? '' }}" type="text" class="form-control" id="emergencia" name="emergency_phone" placeholder="(99) 9999-9999">
      </div>
      <div class="col-md-3">
        <label class="form-label">E-mail Pessoal</label>
        <input value="{{ $person->personal_email ?? '' }}" type="email" class="form-control" id="email_pessoal" name="personal_email" placeholder="exemplo@email.com">
      </div>
    </div>
  </div>
</div>



</form>

@endsection

@section('page-script')



<script>
  $(document).ready(function () {










    
    const departmentId = '{{ $person->department ?? '' }}';
    const selectedJobId = '{{ $person->role ?? '' }}';

    // Define o departamento já selecionado e carrega os cargos correspondentes
    if (departmentId) {
      $('#department-select').val(departmentId);
      console.log('Departamento pré-selecionado:', departmentId);
      carregarCargos(departmentId, selectedJobId);
    }

    // Ao trocar o departamento manualmente
    $('#department-select').on('change', function () {
      const depId = $(this).val();
      carregarCargos(depId, null); // Zera o cargo selecionado
    });

    function carregarCargos(departmentId, selectedJobId = null) {
      const jobSelect = $('#job-select');
      jobSelect.html('<option value="">Carregando...</option>');

      if (departmentId) {
        $.ajax({
          url: `/departments/${departmentId}/job-roles`,
          method: 'GET',
          success: function (data) {
            console.log('Cargos carregados:', data);

            jobSelect.html('<option value="">Selecionar</option>');

            data.forEach(job => {
              const isSelected = selectedJobId && String(selectedJobId) === String(job.id_job);
              const option = `<option value="${job.id_job}" ${isSelected ? 'selected' : ''}>${job.description}</option>`;
              jobSelect.append(option);
            });
          },
          error: function (xhr, status, error) {
            console.error('Erro ao buscar cargos:', xhr.status, error);
            Swal.fire({
              icon: 'error',
              title: 'Erro!',
              text: 'Erro ao carregar cargos. Tente novamente.',
              customClass: { confirmButton: 'btn btn-primary' },
              buttonsStyling: false
            });
            jobSelect.html('<option value="">Erro ao carregar cargos</option>');
          }
        });
      } else {
        jobSelect.html('<option value="">Selecionar</option>');
      }
    }
  });
</script>














<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>

<script>
$(document).ready(function () {
  // Máscaras
  $('#cep').mask('00000-000');
  $('#cpf').mask('000.000.000-00');
  $('#cnpj').mask('00.000.000/0000-00');
  $('#celular').mask('(00) 00000-0000');
  $('#telefone').mask('(00) 00000-0000');
  $('#emergencia').mask('(00) 00000-0000');
  
  // Script para mostrar/ocultar o campo de deficiência
  $('#pcdSwitch').on('change', function () {
    $('#disabilitySelect').toggle(this.checked);
    if (!this.checked) {
      $('select[name="id_disability_type"]').val('');
    }
  });




  function buscarCEP(cep) {
  const pais = $('#country').val();
  cep = cep.replace(/\D/g, '');
  
  if (pais === 'Brasil' && cep.length === 8) {
    $('#cep-loader').removeClass('d-none');

    $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function (data) {
      if (!data.erro) {
        $('[name="address_street"]').val(data.logradouro);
        $('[name="address_district"]').val(data.bairro);
        $('#cidade').html(`<option selected>${data.localidade}</option>`);
        $('#estado').html(`<option selected>${data.uf}</option>`);
      } else {
        $('[name="address_street"]').val('');
        $('[name="address_district"]').val('');
        $('#cidade').html(`<option selected value="">Selecionar</option>`);
        $('#estado').html(`<option selected value="">Selecionar</option>`);
        Swal.fire('Erro!', 'CEP não encontrado.', 'error');
      }
    }).fail(() => {
      Swal.fire('Erro!', 'Não foi possível buscar o CEP. Tente novamente.', 'error');
    }).always(() => {
      $('#cep-loader').addClass('d-none');
    });
  }
}

// Executa ao perder o foco do input
$('#cep').on('blur', function () {
  buscarCEP($(this).val());
});

// Executa automaticamente ao carregar a página se o campo já estiver preenchido
$(document).ready(function () {
  const cepInicial = $('#cep').val();
  if (cepInicial && cepInicial.replace(/\D/g, '').length === 8) {
    buscarCEP(cepInicial);
  }
});


  // Detecção de alteração no formulário
  let edited = false;
  $('form :input').on('change input', function () {
    edited = true;
  });

  // Alerta de saída nativo do navegador
  $(window).on('beforeunload', function (e) {
    if (edited) {
      e.preventDefault();
      return 'Você fez alterações no cadastro. Deseja salvar antes de sair?';
    }
  });
  
  $('a[href]:not([href^="javascript:void(0)"]), a[href="/candidate/profile/{{ $person->cpf ?? '' }}"], .btn-outline-danger').on('click', function (e) {
  if (edited) {
    e.preventDefault();
    const href = $(this).attr('href');

    Swal.fire({
      title: 'Alterações não salvas',
      html: '<p>Você fez alterações no formulário. Deseja salvar antes de sair?</p>',
      showCancelButton: true,
      showConfirmButton: true,
      confirmButtonText: 'Sair sem salvar',
      cancelButtonText: 'Ficar na página',
      customClass: {
        popup: 'shadow border-0 rounded-3',
        title: 'fw-bold fs-5',
        htmlContainer: 'text-muted',
        confirmButton: 'btn btn-primary px-4 py-2',
        cancelButton: 'btn btn-secondary px-4 py-2 me-2',
        actions: 'd-flex justify-content-center gap-2 mt-3'
      },
      buttonsStyling: false,
      reverseButtons: true,
      width: 500
    }).then((result) => {
      if (result.isConfirmed) {
        $(window).off('beforeunload');
        window.location.href = href;
      }
    });
  }
});

  
  // Botão "Salvar" do formulário
  $('form').on('submit', function(e) {
      // Desativa o alerta do navegador ao submeter o formulário
      $(window).off('beforeunload');
  });
});
</script>
@endsection