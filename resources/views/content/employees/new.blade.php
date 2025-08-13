@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')



<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  .is-invalid {
    border-color: #FC7054 !important;
    box-shadow: 0 0 0 0.2rem rgba(252, 112, 84, 0.25);
  }
</style>


<!--Formulário de cadastro -->
<form method="POST" action="">
@csrf

<!-- Header -->
<div class="card mb-4">
  <div class="card-body">
    <div class="row align-items-start justify-content-between">

      <!-- Coluna da Imagem -->
<div class="col-auto text-center">
  <div style="background-color: #e8ddfb; border-radius: 100px; width: 100px; height: 100px;">
    <img
      id="profile_pic"
      name="profile_pic"
      src="https://uploads.promoview.com.br/2023/12/b72a1cfe.png"
      alt="Foto de Perfil"
      class="rounded-circle"
      style="width: 100px; height: 100px; object-fit: cover;"
    />
  </div>
  
  <!-- Input oculto para upload -->
  <input
    type="file"
    id="profile_pic_input"
    accept="image/*"
    onchange="uploadProfilePic(this)"
    hidden
  />
 <!-- Campo oculto para o base64 -->
 <input type="hidden" name="profile_pic_base64" id="profile_pic_base64" />

 
  <!-- Botão de ativação -->
  <small class="text-muted d-block mt-1">
    <a href="javascript:void(0)" onclick="document.getElementById('profile_pic_input').click()">Adicionar</a> |
    <a href="javascript:void(0)" onclick="resetProfilePic()">Excluir</a>
  </small>
</div>

      <!-- Coluna do Título e Abas alinhadas ao fim -->
      <div class="col-md-6 d-flex flex-column justify-content-between" style="min-height: 100px;">
        <h4 class="mb-2">Cadastro Colaborador</h4>
      </div>

      <!-- Coluna dos Botões -->
      <div class="col-md-3 d-flex justify-content-end">
        <div class="d-flex align-items-start gap-2">
          <button type="submit" class="btn btn-primary">Salvar</button>
          <a href="/employee/list" class="btn btn-outline-danger">Cancelar</a>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- /Header -->


  <!-- Section cadastro -->

  <!-- DADOS PESSOAIS -->
  <div class="card mb-4">
  <h5 class="card-header">
  <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
  Dados Pessoais
</h5>

    <div class="card-body">
      <div class="row g-3">
      <div class="col-md-3">
  <label class="form-label">Nome Completo</label>
  <input required name="full_name" type="text" class="form-control" id="full_name">
  <small id="full_name_error" class="text-danger d-none">Digite o nome completo (nome e sobrenome).</small>
</div>

        <div class="col-md-3">
          <label class="form-label">Data de Nascimento</label>
          <input required name="birth_date" type="date" class="form-control">
        </div>
       

        <div class="col-md-3">
  <label class="form-label">Gênero</label>
  <select name="id_gender" class="form-select" required>
    <option value="">Selecionar</option>
    <option value="1">Masculino</option>
    <option value="2">Feminino</option>
    <option value="3">Outros</option>
  </select>
</div>

<div class="col-md-3">
  <label class="form-label">Estado Civil</label>
  <select name="id_marital_status" class="form-select" required>
    <option value="">Selecionar</option>
    <option value="1">Solteiro(a)</option>
    <option value="2">Casado(a)</option>
    <option value="3">Divorciado(a)</option>
    <option value="4">Viúvo(a)</option>
    <option value="5">União Estável</option>
  </select>
</div>

<div class="col-md-3">
  <label class="form-label">E-mail Corporativo</label>
  <input required name="corporate_email" type="email" class="form-control">
</div>

<div class="col-md-3">
  <label class="form-label">Escolaridade</label>
  <select required name="id_education_level" class="form-select">
    <option value="">Selecionar</option>
    <option value="1">Ensino Fundamental Incompleto</option>
    <option value="2">Ensino Fundamental Completo</option>
    <option value="3">Ensino Médio Incompleto</option>
    <option value="4">Ensino Médio Completo</option>
    <option value="5">Superior Incompleto</option>
    <option value="6">Superior Completo</option>
    <option value="7">Pós-graduação</option>
    <option value="8">Mestrado</option>
    <option value="9">Doutorado</option>
  </select>
</div>




<div class="col-md-3 d-flex align-items-center">
  <div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" id="pcdSwitch">
    <label class="form-check-label" for="pcdSwitch">Pessoa com Deficiência</label>
  </div>
</div>

<div class="col-md-3" id="disabilitySelect" style="display: none;">
  <label class="form-label">Tipo de Deficiência</label>
  <select name="id_disability_type" class="form-select">
    <option value="">Selecionar</option>
    <option value="1">Física</option>
    <option value="2">Auditiva</option>
    <option value="3">Visual</option>
    <option value="4">Intelectual</option>
    <option value="5">Múltipla</option>
    <option value="6">Transtorno do Espectro Autista (TEA)</option>
    <option value="7">Outra</option>
  </select>
</div>

<script>
  document.getElementById('pcdSwitch').addEventListener('change', function () {
    const select = document.getElementById('disabilitySelect');
    select.style.display = this.checked ? 'block' : 'none';
  });
</script>


      </div>
    </div>
  </div>

  <!-- ENDEREÇO -->
<div class="card mb-4">
<h5 class="card-header">
<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
 Dados Pessoais
</h5>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">País</label>
        <select required name="country" class="form-select" id="pais">
          <option>Selecionar</option>
          <option value="Brazil">Brasil</option>
          <!-- outros -->
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">CEP</label>
        <div class="position-relative">
          <input required name="zip_code" type="text" class="form-control" id="cep" maxlength="9">
          <div id="cep-loader" class="spinner-border text-primary position-absolute top-50 end-0 me-2 d-none" style="width: 1rem; height: 1rem;"></div>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Logradouro</label>
        <input name="" disabled type="text" class="form-control" id="logradouro">
      </div>
      <div class="col-md-3">
        <label class="form-label">Número</label>
        <input name="address_number" type="text" class="form-control" id="numero">
      </div>
      <div class="col-md-3">
        <label class="form-label">Bairro</label>
        <input name="address_district" disabled type="text" class="form-control" id="bairro">
      </div>
      <div class="col-md-3">
        <label class="form-label">Complemento</label>
        <input name="address_complement" type="text" class="form-control" id="complemento">
      </div>
      <div class="col-md-3">
        <label class="form-label">Cidade</label>
        <select name="address_city" disabled class="form-select" id="cidade">
          <option>Selecionar</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Estado</label>
        <select name="address_state" disabled class="form-select" id="estado">
          <option>Selecionar</option>
        </select>
      </div>
    </div>
  </div>
</div>


  <!-- DOCUMENTOS -->
  <div class="card mb-4">
  <h5 class="card-header">
  <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-description"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 17h6" /><path d="M9 13h6" /></svg>
  Documentos
</h5>
    <div class="card-body">
      <div class="row g-3">
      <div class="col-md-3">
  <label class="form-label">CPF</label>
  <input required name="cpf" id="cpf" type="text" class="form-control">
</div>

<div class="col-md-3">
  <label class="form-label">CNPJ</label>
  <input name="cnpj" id="cnpj" type="text" class="form-control">
</div>




        <div class="col-md-3 d-flex align-items-center">
          <div class="form-check">
            <input name="foreigner" class="form-check-input" type="checkbox" id="foreigner">
            <label class="form-check-label" for="foreigner">Estrangeiro</label>
          </div>
        </div>

        <div class="col-md-3">
          <label class="form-label">RG</label>
          <input name="rg" type="text" class="form-control">
        </div>

        <div class="col-md-3">
          <label class="form-label">CNH</label>
          <input name="cnh" type="text" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">PIS</label>
          <input name="pis" type="text" class="form-control">
        </div>
        <div class="col-md-3">
  <label class="form-label" for="bank">Banco</label>
  <select name="bank" id="bank" class="form-select">
    <option value="">Selecionar</option>
    <option value="Banco do Brasil S.A.">001 - Banco do Brasil S.A.</option>
    <option value="Banco BM&FBOVESPA S.A.">006 - Banco BM&FBOVESPA S.A.</option>
    <option value="Banco Banco do Nordeste do Brasil S.A.">004 - Banco do Nordeste do Brasil S.A.</option>
    <option value="Banco Santander (Brasil) S.A.">033 - Banco Santander (Brasil) S.A.</option>
    <option value="Banco Citibank S.A.">745 - Banco Citibank S.A.</option>
    <option value="Banco Bradesco S.A.">237 - Banco Bradesco S.A.</option>
    <option value="Banco Caixa Econômica Federal">104 - Caixa Econômica Federal</option>
    <option value="Banco Itaú Unibanco S.A.">341 - Banco Itaú Unibanco S.A.</option>
    <option value="Banco Safra S.A.">422 - Banco Safra S.A.</option>
    <option value="Banco HSBC Bank Brasil S.A. - Banco Múltiplo">399 - HSBC Bank Brasil S.A. - Banco Múltiplo</option>
    <option value="Banco Votorantim S.A.">655 - Banco Votorantim S.A.</option>
    <option value="Banco Banco Original S.A.">212 - Banco Original S.A.</option>
    <option value="Banco Banco Inter S.A.">077 - Banco Inter S.A.</option>
    <option value="Banco Banco Modal S.A.">746 - Banco Modal S.A.</option>
    <option value="Banco Banco Daycoval S.A.">707 - Banco Daycoval S.A.</option>
    <option value="Banco Banco Pan S.A.">623 - Banco Pan S.A.</option>
    <option value="Banco Banco Pine S.A.">643 - Banco Pine S.A.</option>
    <option value="Banco Banco Sofisa S.A.">637 - Banco Sofisa S.A.</option>
    <option value="Banco Banco Topázio S.A.">779 - Banco Topázio S.A.</option>
    <option value="Banco Banco ABC Brasil S.A.">025 - Banco ABC Brasil S.A.</option>
    <option value="Banco Banco da Amazônia S.A.">003 - Banco da Amazônia S.A.</option>
    <option value="Banco Banco Indusval S.A.">630 - Banco Indusval S.A.</option>
    <option value="Banco Banco Rendimento S.A.">633 - Banco Rendimento S.A.</option>
    <option value="Banco Banco Mercantil do Brasil S.A.">389 - Banco Mercantil do Brasil S.A.</option>
    <option value="Banco Banco C6 S.A.">336 - Banco C6 S.A.</option>
    <option value="Banco Banco BMG S.A.">318 - Banco BMG S.A.</option>
    <option value="Banco Banco BNL do Brasil S.A.">094 - Banco BNL do Brasil S.A.</option>
    <option value="Banco Banco Rabobank International Brasil S.A.">747 - Banco Rabobank International Brasil S.A.</option>
    <option value="Banco Banco da China Brasil S.A.">067 - Banco da China Brasil S.A.</option>
    <option value="Banco Banco Credit Suisse (Brasil) S.A.">746 - Banco Credit Suisse (Brasil) S.A.</option>
    <option value="Banco Banco Cruzeiro do Sul S.A.">273 - Banco Cruzeiro do Sul S.A.</option>
    <option value="Banco Banco Fiat S.A.">218 - Banco Fiat S.A.</option>
    <option value="Banco Banco Volkswagen S.A.">172 - Banco Volkswagen S.A.</option>
    <option value="Banco Banco GMAC S.A.">249 - Banco GMAC S.A.</option>
    <option value="Banco Banco Mercedes-Benz S.A.">248 - Banco Mercedes-Benz S.A.</option>
    <option value="Banco Banco Honda S.A.">247 - Banco Honda S.A.</option>
    <option value="Banco Banco Toyota do Brasil S.A.">233 - Banco Toyota do Brasil S.A.</option>
    <option value="Banco Banco Citicard S.A.">607 - Banco Citicard S.A.</option>
    <option value="Banco Banco Paulista S.A.">643 - Banco Paulista S.A.</option>
    <option value="Banco Banco Panamericano S.A.">623 - Banco Panamericano S.A.</option>
    <option value="Banco Banco BRJ S.A.">634 - Banco BRJ S.A.</option>
    <option value="Banco Banco Crefisa S.A.">606 - Banco Crefisa S.A.</option>
    <option value="Banco Banco J.P. Morgan S.A.">747 - Banco J.P. Morgan S.A.</option>
    <option value="Banco Banco Morgan Stanley S.A.">476 - Banco Morgan Stanley S.A.</option>
    <option value="Banco Banco ABN AMRO S.A.">246 - Banco ABN AMRO S.A.</option>
    <option value="Banco Banco Safra S.A.">422 - Banco Safra S.A.</option>
    <option value="Banco Banco Cooperativo do Brasil S.A. - BANCOOB">756 - Banco Cooperativo do Brasil S.A. - BANCOOB</option>
    <option value="Banco Banco da Terra S.A.">119 - Banco da Terra S.A.</option>
    <option value="Banco Banco Pan S.A.">623 - Banco Pan S.A.</option>
    <option value="Banco Banco Modal S.A.">746 - Banco Modal S.A.</option>
    <option value="Banco Banco Original S.A.">212 - Banco Original S.A.</option>
    <option value="Banco Banco Sofisa S.A.">637 - Banco Sofisa S.A.</option>
    <option value="Banco Banco Daycoval S.A.">707 - Banco Daycoval S.A.</option>
    <option value="Banco Banco Inter S.A.">077 - Banco Inter S.A.</option>
    <option value="Banco Banco BS2 S.A.">318 - Banco BS2 S.A.</option>
    <option value="Banco Banco Digital Modal S.A.">746 - Banco Digital Modal S.A.</option>
    <option value="Banco Banco Digio S.A.">755 - Banco Digio S.A.</option>
    <option value="Banco Banco Agibank S.A.">026 - Banco Agibank S.A.</option>
    <option value="Banco Banco Original S.A.">212 - Banco Original S.A.</option>
    <option value="Banco Banco Topázio S.A.">779 - Banco Topázio S.A.</option>
    <option value="Banco Banco Votorantim S.A.">655 - Banco Votorantim S.A.</option>
  </select>
</div>

        <div class="col-md-3">
          <label class="form-label">Agência</label>
          <input name="agency" type="text" class="form-control" placeholder="000000-0">
        </div>

        <div class="col-md-3">
          <label class="form-label">Conta</label>
          <input name="account" type="text" class="form-control" placeholder="00000000-00">
        </div>
        <div class="col-md-3">
          <label class="form-label">Chave PIX</label>
          <input name="pix_key" type="text" class="form-control">
        </div>
      </div>
    </div>
  </div>

 <!-- CONTATOS -->
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
        <input type="text" class="form-control" id="celular" name="cellphone" placeholder="(99) 99999-9999">
      </div>
      <div class="col-md-3">
        <label class="form-label">Telefone</label>
        <input type="text" class="form-control" id="telefone" name="phone" placeholder="(99) 9999-9999">
      </div>
      <div class="col-md-3">
        <label class="form-label">Telefone Emergência</label>
        <input type="text" class="form-control" id="emergencia" name="emergency_phone" placeholder="(99) 9999-9999">
      </div>
      <div class="col-md-3">
        <label class="form-label">E-mail Pessoal</label>
        <input required type="email" class="form-control" id="email_pessoal" name="personal_email" placeholder="exemplo@email.com">
      </div>
    </div>
  </div>
</div>


  <!-- DADOS CONTRATUAIS -->
  <div class="card mb-4">
  <h5 class="card-header">
  <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" /></svg>
   Dados Contratuais
</h5>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Departamento</label>
          <select name="id_department" class="form-select" id="department-select">
                <option value="">Selecionar</option>
                @foreach($departments as $department)
                  <option value="{{ $department->id_department }}">{{ $department->description }}</option>
                @endforeach
              </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Cargo</label>
          <select name="id_job" class="form-select" id="job-select">
                <option value="">Selecionar</option>
              </select>
        </div>
        <div class="col-md-3">
  <label class="form-label">Tipo de Contrato</label>
  <select name="contract_type" class="form-select">
    <option value="">Selecionar</option>
    <option value="clt">CLT (Contrato de Trabalho)</option>
    <option value="pj">PJ (Pessoa Jurídica)</option>
    <option value="temporario">Temporário</option>
    <option value="estagio">Estágio</option>
    <option value="freelancer">Freelancer</option>
    <option value="aprendiz">Aprendiz</option>
    <option value="autonomo">Autônomo</option>
  </select>
</div>

        <div class="col-md-3">
          <label class="form-label">Data de Admissão</label>
          <input required name="admission_date" type="date" class="form-control">
        </div>

        <div class="col-md-3">
          <label class="form-label">Matrícula</label>
          <input name="registration_number" type="text" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Período de Experiência</label>
          <input name="experience_period" type="text" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Data Contrato</label>
          <input name="contract_date" type="date" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Vencimento Contrato</label>
          <input name="contract_expiration_date" type="date" class="form-control">
        </div>
      </div>
    </div>
  </div>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

 // Departamento e Cargo
 $('#department-select').on('change', function () {
      const departmentId = $(this).val();
      const jobSelect = $('#job-select');

      jobSelect.html('<option value="">Carregando...</option>');

      if (departmentId) {
        $.ajax({
          url: `/departments/${departmentId}/job-roles`,
          method: 'GET',
          success: function (data) {
            jobSelect.html('<option value="">Selecionar</option>');
            data.forEach(job => {
              const option = `<option value="${job.id_job}">${job.description}</option>`;
              jobSelect.append(option);
            });
          },
          error: function (xhr, status, error) {
            console.error('Erro ao buscar cargos:', xhr.status, error);
            Swal.fire({ // Substituído alert por Swal.fire
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao carregar cargos. Tente novamente.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            jobSelect.html('<option value="">Erro ao carregar cargos</option>');
          }
        });
      } else {
        jobSelect.html('<option value="">Selecionar</option>');
      }
    });












  function mascaraTelefone(valor) {
    valor = valor.replace(/\D/g, "");              // Remove tudo que não é dígito
    if (valor.length > 10) {                       // Celular com 9 dígitos
      valor = valor.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
    } else {                                      // Telefone fixo com 8 dígitos
      valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    }
    return valor;
  }

  document.getElementById('celular').addEventListener('input', function (e) {
    e.target.value = mascaraTelefone(e.target.value);
  });

  document.getElementById('telefone').addEventListener('input', function (e) {
    e.target.value = mascaraTelefone(e.target.value);
  });

  document.getElementById('emergencia').addEventListener('input', function (e) {
    e.target.value = mascaraTelefone(e.target.value);
  });
</script>

<!--Formulário de cadastro -->

  <!-- Section cadastro -->

  <script>

 // Departamento e Cargo
 $('#department-select').on('change', function () {
      const departmentId = $(this).val();
      const jobSelect = $('#job-select');

      jobSelect.html('<option value="">Carregando...</option>');

      if (departmentId) {
        $.ajax({
          url: `/departments/${departmentId}/job-roles`,
          method: 'GET',
          success: function (data) {
            jobSelect.html('<option value="">Selecionar</option>');
            data.forEach(job => {
              const option = `<option value="${job.id_job}">${job.description}</option>`;
              jobSelect.append(option);
            });
          },
          error: function (xhr, status, error) {
            console.error('Erro ao buscar cargos:', xhr.status, error);
            Swal.fire({ // Substituído alert por Swal.fire
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao carregar cargos. Tente novamente.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            jobSelect.html('<option value="">Erro ao carregar cargos</option>');
          }
        });
      } else {
        jobSelect.html('<option value="">Selecionar</option>');
      }
    });









  // Máscara CPF
  const cleaveCPF = new Cleave('#cpf', {
    delimiters: ['.', '.', '-'],
    blocks: [3, 3, 3, 2],
    numericOnly: true
  });

  // Máscara CNPJ
  const cleaveCNPJ = new Cleave('#cnpj', {
    delimiters: ['.', '.', '/', '-'],
    blocks: [2, 3, 3, 4, 2],
    numericOnly: true
  });

  function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    let soma = 0;
    for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
    let resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;
    soma = 0;
    for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    return resto === parseInt(cpf.charAt(10));
  }

  function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]+/g, '');
    if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;
    let t = cnpj.length - 2, d = cnpj.substring(t), d1 = parseInt(d.charAt(0)), d2 = parseInt(d.charAt(1));
    let calc = x => {
      let n = cnpj.substring(0, x), y = x - 7, s = 0;
      for (let i = x; i >= 1; i--) s += n.charAt(x - i) * y--;
      return ((s % 11) < 2 ? 0 : 11 - (s % 11));
    };
    return calc(t) === d1 && calc(t + 1) === d2;
  }

  // Validação ao enviar o formulário
  document.querySelector('form').addEventListener('submit', function (e) {
    const cpfInput = document.getElementById('cpf');
    const cnpjInput = document.getElementById('cnpj');
    const cpf = cpfInput.value;
    const cnpj = cnpjInput.value;

    let valido = true;

    if (!cpf || !validarCPF(cpf)) {
      cpfInput.classList.add('is-invalid');
      valido = false;
    } else {
      cpfInput.classList.remove('is-invalid');
    }

    if (!valido) {
      e.preventDefault();
    }
  });
</script>
<script>
  const inputNome = document.getElementById('full_name');
  const erroNome = document.getElementById('full_name_error');

  inputNome.addEventListener('blur', function () {
    const palavras = this.value.trim().split(/\s+/);
    if (palavras.length < 2) {
      erroNome.classList.remove('d-none');
    } else {
      erroNome.classList.add('d-none');
    }
  });
</script>

<script>
  let edited = false;

  // Detecta qualquer alteração nos campos
  document.querySelectorAll('input, select, textarea').forEach(el => {
    el.addEventListener('change', () => {
      edited = true;
    });
  });

  // Pergunta antes de sair da página se houver mudanças
  window.addEventListener('beforeunload', function (e) {
    if (edited) {
      e.preventDefault();
      e.returnValue = '';
    }
  });

  // Intercepta cliques em links ou botões que mudam de página
  document.querySelectorAll('a[href]:not([href^="javascript:void(0)"]), .btn-outline-danger').forEach(el => {
    el.addEventListener('click', function (e) {
      if (edited) {
        e.preventDefault();
        const href = this.getAttribute('href');

        Swal.fire({
          title: 'Alterações não salvas',
          html: 'Você fez alterações no formulário. Deseja salvar antes de sair?',
          showCancelButton: true,
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
            window.removeEventListener('beforeunload', () => {});
            window.location.href = href;
          }
        });
      }
    });
  });

  // Zera flag ao salvar o formulário
  document.querySelector('form').addEventListener('submit', () => {
    window.removeEventListener('beforeunload', () => {});
    edited = false;
  });
  
</script>


<script src="{{ asset('assets/functions/buscaCep.js') }}"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>



@endsection
