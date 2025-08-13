@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Cadastro Nova Vaga')

@section('content')

{{-- SweetAlert2 CSS (para os alertas bonitos) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.css">

{{-- CSS para o Menu Multi-Etapas (corrigido com cor roxa) --}}
<style>
  .multi-step-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start; /* Alinha os itens ao topo */
    margin-bottom: 2rem;
    position: relative; /* Para posicionar a linha de progresso e os círculos */
    padding-top: 15px; /* Espaço para a linha e círculos */
    padding-bottom: 20px; /* Espaço para o texto da label */
  }

  .multi-step-header .step-item {
    flex: 1; /* Distribui o espaço igualmente entre os itens */
    text-align: center;
    position: relative;
    cursor: pointer;
    padding: 0 5px; /* Pequeno padding horizontal para evitar quebra de texto */
    z-index: 2; /* Garante que os itens fiquem acima das linhas */
  }

  .multi-step-header .step-item .step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #ccc; /* Cor do círculo inativo */
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin: 0 auto; /* Centraliza o círculo */
    position: relative; /* Para sobrepor a linha */
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }

  .multi-step-header .step-item.active .step-circle {
    background-color: #696cff; /* Cor roxa para o círculo ativo */
    box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.2); /* Sombra para o ativo */
  }

  .multi-step-header .step-item.completed .step-circle {
    background-color: #696cff; /* Cor roxa para o círculo completo */
  }

  .multi-step-header .step-item .step-label {
    font-size: 0.9rem;
    color: #6c757d;
    white-space: nowrap; /* Evita quebra de linha */
    margin-top: 8px; /* Espaço entre círculo e label */
    display: block; /* Garante que o span seja um bloco para margin-top */
    transition: color 0.3s ease;
  }

  .multi-step-header .step-item.active .step-label {
    color: #696cff;
    font-weight: bold;
  }

  .multi-step-header .step-item.completed .step-label {
    color: #696cff; /* Cor roxa para o label completo */
  }

  /* Linha de Conexão Horizontal (Fundo) */
  .multi-step-header::before {
    content: '';
    position: absolute;
    top: 33px; /* Centraliza verticalmente com o meio do círculo (15px + 18px do raio) */
    left: 0;
    width: 100%;
    height: 1px; /* Espessura da linha */
    background-color: #e0e0e0; /* Cor da linha inativa */
    z-index: 1; /* Abaixo dos círculos */
  }

  /* Linha de Progresso Ativa (Animada) */
  .multi-step-header .progress-line {
    position: absolute;
    top: 33px; /* Mesma posição vertical da linha de fundo */
    left: 0;
    height: 1px;
    background-color: #696cff; /* Cor da linha ativa */
    width: 0%; /* Começa com 0% */
    transition: width 0.3s ease-in-out; /* Animação suave */
    z-index: 1; /* Abaixo dos círculos */
  }

  /* Esconde todos os conteúdos por padrão */
  .step-content {
    display: none;
  }

  /* Mostra apenas o conteúdo ativo */
  .step-content.active {
    display: block;
  }

  /* SweetAlert2 z-index para sobrepor tudo */
  .swal2-container {
      z-index: 99999 !important;
  }
</style>

<form method="POST" action="" id="multiStepForm">
  @csrf

  <div class="card mb-4">
    <div class="card-body">
      <div class="row align-items-center justify-content-between">
        <div class="col-md-6 d-flex flex-column justify-content-between">
          <h4 class="mb-2">Cadastro Nova Vaga</h4>
        </div>
        <div class="col-md-3 d-flex justify-content-end">
          <div class="d-flex align-items-start gap-2">
          <button type="button" class="btn btn-outline-danger me-2 btn-cancel">Cancelar</button>
<button type="button" class="btn btn-outline-secondary me-2 btn-prev">Anterior</button>
<button type="button" class="btn btn-primary btn-next">Próximo</button>
 </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <div class="multi-step-header">
        <div class="progress-line"></div> {{-- Linha de progresso animada --}}
        <div class="step-item active" data-step="1">
          <span class="step-circle">1</span>
          <span class="step-label">Detalhe da Vaga</span>
        </div>
        <div class="step-item" data-step="2">
          <span class="step-circle">2</span>
          <span class="step-label">Forma de Contrato</span>
        </div>
        <div class="step-item" data-step="3">
          <span class="step-circle">3</span>
          <span class="step-label">Perguntas Customizadas</span>
        </div>
        <div class="step-item" data-step="4">
          <span class="step-circle">4</span>
          <span class="step-label">Publicação</span>
        </div>
      </div>
    </div>
  </div>

  <div class="step-content-wrapper">

    {{-- ETAPA 1: DETALHE DA VAGA (CONFORME IMAGEM "Publicacao.png") --}}
    <div id="step-4-content" class="step-content active">
      <div class="card mb-4">
        <h5 class="card-header">Publicação</h5>
        <div class="card-body">
          <div class="row g-3">
            {{-- Data Abertura, Data Encerramento, Divulgar no Porta de Vagas? --}}
            <div class="col-md-4">
              <label for="dataAbertura" class="form-label">Data Abertura da Vaga</label>
              <input type="date" class="form-control" id="dataAbertura" name="data_abertura_vaga">
            </div>
            <div class="col-md-4">
              <label for="dataEncerramento" class="form-label">Data Encerramento da Vaga</label>
              <input type="date" class="form-control" id="dataEncerramento" name="data_encerramento_vaga">
            </div>
            <div class="col-md-4">
              <label class="form-label">Divulgar no Porta de Vagas?*</label><br>
              <div class="form-check form-check-inline mt-2">
                <input class="form-check-input" type="radio" name="confidential" value="0" checked>
                <label class="form-check-label">Sim</label>
              </div>
              <div class="form-check form-check-inline mt-2">
                <input class="form-check-input" type="radio" name="confidential" value="1">
                <label class="form-check-label">Não</label>
              </div>
            </div>
            {{-- Observações --}}
            <div class="col-12">
              <label for="observacoes" class="form-label">Observações</label>
              <textarea id="observacoes" name="observacoes"></textarea>
              <small class="text-muted">Descreva se necessário informações específicas tratadas com o gestor ou responsável pela vaga. Exemplo: Vaga para novo projeto de vendas.</small>
            </div>
          </div>
        </div>
      </div>











    </div>

    {{-- ETAPA 2: FORMA DE CONTRATO (CONFORME IMAGEM "Forma de contrato.png") --}}
    <div id="step-2-content" class="step-content">
      <div class="card mb-4">
        <div class="card-body">
          <div class="row g-3">
            {{-- Modalidade de Contrato, Jornada de Trabalho* --}}
            <div class="col-md-6">
              <label class="form-label">Modalidade de Contrato</label>
              <select name="contract_type" class="form-select">
                <option value="">Selecionar</option>
                <option value="CLT">CLT</option>
                <option value="PJ">PJ</option>
                <option value="Estágio">Estágio</option>
                <option value="Freelancer">Freelancer</option>
              </select>
            </div>
            
<div class="col-md-6">
  <label for="horario" class="form-label d-flex align-items-center">
    Jornada de Trabalho
    <span class="text-danger ms-1">*</span>

    <span
      tabindex="0"
      role="button"
      data-bs-toggle="popover"
      data-bs-trigger="hover focus"
      data-bs-placement="top"
      data-bs-content="Exemplo: Segunda a Sexta das 08:00 às 17:00"
      class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white ms-2"
      style="width: 20px; height: 20px; font-size: 14px; cursor: pointer;"
    >
      ?
    </span>
  </label>

  <input name="working_hours" type="text" class="form-control">
</div>

            {{-- Oferta Salarial*, Benefícios --}}
            <div class="col-md-6">
              <label for="salario_tipo" class="form-label">Oferta Salarial*</label>
              <div class="input-group mb-3">
                <select class="form-select" id="salario_tipo" name="salario_tipo" style="max-width: 150px;">
                  <option value="Valor">Valor</option>
                  <option value="Faixa">Faixa</option>
                  <option value="A combinar">A combinar</option>
                </select>

                <div id="valor_unico_container" style="display: block;">
                  <input type="text" class="form-control" id="salario_valor" placeholder="Valor">
                </div>
                <div id="faixa_container" style="display: none;">
                  <input type="text" class="form-control" id="salario_de" placeholder="De">
                  <input type="text" class="form-control" id="salario_ate" placeholder="Até">
                </div>
              </div>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="ocultar_salario" name="ocultar_salario">
                <label class="form-check-label" for="ocultar_salario">
                  Ocultar valor para candidatos
                </label>
              </div>
              <input type="hidden" id="salary" name="salary" value="">
            </div>
            <div class="col-md-6">
              <label class="form-label">Benefícios</label>
              <input name="benefits" type="text" class="form-control">
            </div>

            {{-- Local de Trabalho, Sincronizar, Endereço --}}
            <div class="col-12 mt-4">
              <h5 class="d-flex align-items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-map-pin">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M9 11a3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                  <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                </svg>
                Local de Trabalho
              </h5>
              <div class="mb-3">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="workplace_type" id="presencial" value="Presencial" checked>
                  <label class="form-check-label" for="presencial">Presencial</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="workplace_type" id="remoto" value="Remoto">
                  <label class="form-check-label" for="remoto">Remoto</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="workplace_type" id="hibrido" value="Hibrido">
                  <label class="form-check-label" for="hibrido">Híbrido</label>
                </div>
              </div>

              <div id="alerta-endereco" class="alert alert-warning d-none mt-3">
                Sincronizar com endereço de cadastro da empresa?
                <button type="button" class="btn btn-sm btn-primary ms-2" id="btn-sincronizar">Sincronizar</button>
              </div>

              <div class="row g-3 mt-2 d-none" id="campos-endereco">
                <div class="col-md-6">
                  <label for="cep" class="form-label">CEP*</label>
                  <input type="text" class="form-control" id="cep" name="cep">
                </div>
                <div class="col-md-6">
                  <label for="estado" class="form-label">Estado*</label>
                  <input type="text" class="form-control" id="estado" name="estado">
                </div>
                <div class="col-md-6">
                  <label for="cidade" class="form-label">Cidade*</label>
                  <input type="text" class="form-control" id="cidade" name="cidade">
                </div>
                <div class="col-md-6">
                  <label for="bairro" class="form-label">Bairro</label>
                  <input type="text" class="form-control" id="bairro" name="bairro">
                </div>
                 <div class="col-md-9">
                  <label for="logradouro" class="form-label">Endereço</label>
                  <input type="text" class="form-control" id="logradouro" name="logradouro">
                </div>
                <div class="col-md-3">
                  <label for="numero" class="form-label">Número</label>
                  <input type="text" class="form-control" id="numero" name="numero">
                </div>
                <div class="col-12">
                  <label for="complemento" class="form-label">Complemento</label>
                  <input type="text" class="form-control" id="complemento" name="complemento">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- ETAPA 3: PERGUNTAS CUSTOMIZADAS (CONFORME IMAGEM "Perguntas customizadas.png") --}}
    <div id="step-3-content" class="step-content">
      <div class="card mb-4">
        <h5 class="card-header">Perguntas Customizadas</h5>
        <div class="card-body">
          <p>O que você gostaria de perguntar para os candidatos desta vaga?</p>
          <div class="mb-4">
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalPergunta">
              <i class="mdi mdi-plus me-1"></i> Adicionar Pergunta
            </button>
          </div>

          <input type="hidden" name="q1" id="q1" value="">
<input type="hidden" name="q2" id="q2" value="">
<input type="hidden" name="q3" id="q3" value="">
<input type="hidden" name="q4" id="q4" value="">
<input type="hidden" name="q5" id="q5" value="">


          <div class="card mb-4" id="cardListaPerguntas" style="display:none;">
            <div class="card-body p-0">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>Tipo de Pergunta</th>
                    <th>Pergunta</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody id="tbodyPerguntas"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- ETAPA 4: PUBLICAÇÃO (CONFORME IMAGEM "Detalhes da vaga.png") --}}
    <div id="step-1-content" class="step-content">
      <div class="card mb-4">
        <div class="card-body">
          <div class="row g-3">
            {{-- Título da Vaga* --}}
            <div class="col-12">
              <label class="form-label">Título da Vaga*</label>
              <input name="description" type="text" class="form-control" required>
            </div>
            {{-- Departamento, Cargo, Posições? --}}
            <div class="col-md-4">
              <label class="form-label">Departamento</label>
              <select name="id_department" class="form-select" id="department-select" required>
                <option value="">Selecionar</option>
                @foreach($departments as $department)
                  <option value="{{ $department->id_department }}">{{ $department->description }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Cargo</label>
              <select name="id_job" class="form-select" id="job-select" required>
                <option value="">Selecionar</option>
              </select>
            </div>
            <div class="col-md-4">
  <label class="form-label d-flex align-items-center">
    Posições
    <span class="ms-1 text-danger">*</span>

    <span
      tabindex="0"
      role="button"
      data-bs-toggle="popover"
      data-bs-trigger="hover focus"
      data-bs-placement="top"
      data-bs-content="Número de vagas para esta posição"
      class="d-inline-flex align-items-center justify-content-center rounded-circle bg-dark text-white ms-2"
      style="width: 20px; height: 20px; font-size: 14px; cursor: pointer;"
    >
      ?
    </span>
  </label>

  <input type="number" name="positions" class="form-control">
</div>

            {{-- Botão Sincronizar com a Engenharia de Cargos --}}
            <div class="col-12 mb-3">
              <button type="button" class="btn btn-outline-primary" id="btnSincronizarEngenharia">Sincronizar com a Engenharia de Cargos</button>
            </div>
            {{-- Descrição da Vaga --}}
            <div class="col-12">
              <label for="descricaoVaga" class="form-label">Descrição da Vaga</label>
              <textarea class="form-control" id="descricaoVaga" name="descricao_vaga" rows="3"></textarea>
            </div>
            {{-- Principais Atividades *, Requisitos* --}}
            <div class="col-12">
              <label for="atividades" class="form-label">Principais Atividades *</label>
              <textarea id="atividades" name="atividades"></textarea>
            </div>
            <div class="col-12">
              <label for="requisitos" class="form-label">Requisitos*</label>
              <textarea id="requisitos" name="requisitos"></textarea>
            </div>
          </div>
        </div>
      </div>












    </div>

  </div>
</form>

{{-- MODAL DE CADASTRO DE PERGUNTA (sem alterações no HTML) --}}
<div class="modal fade" id="modalPergunta" tabindex="-1" aria-labelledby="modalPerguntaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="formPergunta">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPerguntaLabel">Cadastrar Pergunta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="tipoPergunta" class="form-label">Tipo de Pergunta</label>
            <select id="tipoPergunta" class="form-select" required>
              <option value="" disabled selected>Selecione o tipo</option>
              <option value="alternativa">Alternativa (múltiplas opções)</option>
              <option value="objetiva">Objetiva (resposta aberta)</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="textoPergunta" class="form-label">Texto da Pergunta</label>
            <textarea id="textoPergunta" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3 d-none" id="containerAlternativas">
            <label class="form-label">Alternativas</label>
            <div id="listaAlternativas">
              <div class="input-group mb-2 alternativa-item">
                <input type="text" class="form-control alternativa-texto" placeholder="Alternativa 1" required>
                <button type="button" class="btn btn-danger btn-remover-alternativa" title="Remover alternativa">X</button>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddAlternativa">Adicionar Alternativa</button>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Adicionar Pergunta</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@section('page-script')
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.tiny.cloud/1/n7b8zpu0tl0lg9ka80vagfoo3vtu97zk3rwall7rpfhg95q7/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>















  $(document).ready(function() {
    // --- Lógica do Multi-Etapas (APENAS jQuery) ---
    let currentStep = 1;
    const totalSteps = 4;
    const $progressLine = $('.multi-step-header .progress-line');

    function showStep(stepNum) {
      // Validação básica para garantir que não se pule etapas para frente sem preencher o atual
      if (stepNum > currentStep && !validateCurrentStep(currentStep)) {
        return;
      }

      // Oculta todos os conteúdos das etapas
      $('.step-content').removeClass('active');
      // Mostra o conteúdo da etapa atual
      $(`#step-${stepNum}-content`).addClass('active');

      // Atualiza o header das etapas
      $('.multi-step-header .step-item').removeClass('active completed');
      for (let i = 1; i <= totalSteps; i++) {
        const stepItem = $(`.multi-step-header .step-item[data-step="${i}"]`);
        if (i < stepNum) {
          stepItem.addClass('completed');
        } else if (i === stepNum) {
          stepItem.addClass('active');
        }
      }

      currentStep = stepNum;
      updateHeaderButtonsVisibility();
      updateProgressBar();
    }

    function updateHeaderButtonsVisibility() {
  const $btnPrev = $('.btn-prev');
  const $btnNext = $('.btn-next');
  const $btnPublicar = $('#btnPublicarFinal');

  // Mostra ou esconde botão "Anterior"
  if (currentStep > 1) {
    $btnPrev.show();
  } else {
    $btnPrev.hide();
  }

  // Troca "Próximo" por "Publicar"
  if (currentStep === totalSteps) {
    $btnNext.hide();
    if ($btnPublicar.length === 0) {
      $('.btn-next').after(`
        <button type="button" id="btnPublicarFinal" class="btn btn-success">Publicar</button>
      `);
    } else {
      $btnPublicar.show();
    }
  } else {
    $btnNext.show();
    $btnPublicar.hide();
  }
}



    function updateProgressBar() {
        const percentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $progressLine.css('width', `${percentage}%`);
    }

    function validateCurrentStep(stepNum) {
      let isValid = true;
      const currentStepContent = $(`#step-${stepNum}-content`);

      // Remover quaisquer mensagens de erro ou classes `is-invalid` de validações anteriores
      currentStepContent.find('.is-invalid').removeClass('is-invalid');
      currentStepContent.find('.invalid-feedback').remove();
      currentStepContent.find('.radio-error-message').remove();

      // Valida campos required dentro da etapa atual
      currentStepContent.find('[required]').each(function() {
        if ($(this).is('input[type="radio"]')) {
            const name = $(this).attr('name');
            if ($(`input[name="${name}"]:checked`, currentStepContent).length === 0) {
                isValid = false;
                const parentDiv = $(this).closest('.col-md-4, .col-12');
                if (parentDiv.find('.radio-error-message').length === 0) {
                    parentDiv.append('<div class="invalid-feedback radio-error-message" style="display:block;">Selecione uma opção.</div>');
                }
            }
        } else if ($(this).val().trim() === '') {
          $(this).addClass('is-invalid');
          isValid = false;
          if ($(this).next('.invalid-feedback').length === 0) {
              $(this).after('<div class="invalid-feedback">Este campo é obrigatório.</div>');
          }
        } else {
          $(this).removeClass('is-invalid');
          $(this).next('.invalid-feedback').remove();
        }
      });

      // Validação específica para TinyMCE (Rich Text Editors)
      tinymce.triggerSave();

      const tinymceSelectors = ['#observacoes', '#atividades', '#requisitos', '#descricaoVaga'];
      tinymceSelectors.forEach(selector => {
          const editorElement = currentStepContent.find(selector);
          if (editorElement.length && tinymce.get(selector)) {
              // Exemplo de como tornar TinyMCE obrigatório (descomente se desejar)
              // if (tinymce.get(selector).getContent().trim() === '') {
              //     isValid = false;
              //     if (editorElement.next('.invalid-feedback').length === 0) {
              //         editorElement.after('<div class="invalid-feedback" style="display:block;">Este campo é obrigatório.</div>');
              //     }
              // } else {
              //     editorElement.next('.invalid-feedback').remove();
              // }
          }
      });

      return isValid;
    }

   // Eventos para botões "Próximo"
$('.btn-next').on('click', function () {
  if (currentStep < totalSteps) {
    showStep(currentStep + 1);
  }
});

// Eventos para botões "Anterior"
$('.btn-prev').on('click', function () {
  if (currentStep > 1) {
    showStep(currentStep - 1);
  }
});

// Atualiza visibilidade dos botões do topo
function updateHeaderButtonsVisibility() {
  const $btnPrev = $('.btn-prev');
  const $btnNext = $('.btn-next');

  // Oculta o botão "Anterior" na primeira etapa
  if (currentStep > 1) {
    $btnPrev.show();
  } else {
    $btnPrev.hide();
  }

  // Na última etapa (step 4), o botão "Próximo" vira "Publicar"
  if (currentStep === totalSteps) {
    $btnNext.removeClass('btn-primary').addClass('btn-success').text('Publicar');
    $btnNext.off('click').on('click', function(e) {
      e.preventDefault();
      $('#multiStepForm').submit();
    });
  } else {
    // Nas etapas anteriores, o botão volta ao normal
    $btnNext.removeClass('btn-success').addClass('btn-primary').text('Próximo');
    $btnNext.off('click').on('click', function(e) {
      showStep(currentStep + 1);
    });
  }
}


    // Eventos para os itens do header (clicar para ir para a etapa)
    $('.multi-step-header .step-item').on('click', function() {
      const clickedStep = parseInt($(this).data('step'));
      showStep(clickedStep);
    });

    // --- Integração com SweetAlert2 ---

    // Botão Cancelar (topo da página e na navegação inferior)
    $(document).on('click', '#btnCancelarTopo, .btn-cancel', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Deseja realmente CANCELAR?',
        text: 'Todos os dados preenchidos serão perdidos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger me-2'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '/vacancy/list'; // Exemplo: redireciona para a home
        }
      });
    });

   // Botão Publicar (última etapa)
$('#btnPublicarFinal').on('click', function(e) {
  e.preventDefault(); // Impede a submissão padrão inicial do formulário

  if (validateCurrentStep(totalSteps)) {
      // Garante que o conteúdo dos TinyMCEs seja salvo nos textareas ocultos
      tinymce.triggerSave();

      // Coleta todos os dados do formulário
      // `serialize()` é bom para inputs simples, mas pode não incluir TinyMCE
      // e o array JSON de perguntas corretamente se não estiverem no HTML "normal".
      // Para garantir que tudo seja enviado, usaremos FormData.
      const formData = new FormData($('#multiStepForm')[0]);

      // Adiciona explicitamente o JSON das perguntas ao FormData
      // Garante que o campo 'perguntas_json' tem o valor mais recente
      formData.set('perguntas_json', $('#perguntas_json').val());

      // Se você tiver outros dados que não são inputs normais, adicione aqui
      // Ex: formData.append('meu_dado_extra', meuValor);


      // Desabilita o botão para evitar múltiplos cliques
      $(this).prop('disabled', true).text('Publicando...');

      $.ajax({
          url: $('#multiStepForm').attr('action'), // Pega a URL de action do seu formulário
          method: 'POST', // Ou 'PUT'/'PATCH' dependendo da sua rota Laravel
          data: formData,
          processData: false, // Necessário para enviar FormData corretamente
          contentType: false, // Necessário para enviar FormData corretamente
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Garante que o token CSRF seja enviado
          },
          success: function(response) {
              Swal.fire({
                icon: 'success',
                title: 'Vaga Publicada com Sucesso',
                showConfirmButton: true,
                confirmButtonText: 'Finalizar',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
              }).then(() => {
                // Redireciona para a listagem de vagas ou outra página de sucesso
                window.location.href = '/vacancy/list';
              });
          },
          error: function(xhr) {
              console.error("Erro ao publicar vaga:", xhr.responseText);
              let errorMessage = 'Ocorreu um erro ao publicar a vaga.';
              try {
                  const errorResponse = JSON.parse(xhr.responseText);
                  if (errorResponse.message) {
                      errorMessage = errorResponse.message;
                  } else if (errorResponse.errors) {
                      // Se houver erros de validação do Laravel
                      errorMessage = 'Verifique os campos do formulário:';
                      for (const field in errorResponse.errors) {
                          errorMessage += `\n- ${errorResponse.errors[field][0]}`;
                      }
                  }
              } catch (e) {
                  // Se a resposta não for JSON
              }

              Swal.fire({
                icon: 'error',
                title: 'Erro na Publicação',
                text: errorMessage,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
              });
          },
          complete: function() {
              // Reabilita o botão, independentemente do sucesso ou falha
              $('#btnPublicarFinal').prop('disabled', false).text('Publicar');
          }
      });
  } else {
      Swal.fire({ // Substituído alert por Swal.fire
          icon: 'warning',
          title: 'Campos Obrigatórios',
          text: 'Por favor, preencha todos os campos obrigatórios na última etapa antes de publicar.',
          customClass: {
              confirmButton: 'btn btn-primary'
          },
          buttonsStyling: false
      });
  }
});

    // Inicializa a primeira etapa e a barra de progresso
    showStep(1);
    updateProgressBar();

    // --- Seus scripts originais e outros (com ajustes e correções) ---

    // Popover
    $(document).on('click', '[data-bs-toggle="popover"]', function () {
        $('[data-bs-toggle="popover"]').each(function () {
            $(this).popover('dispose');
        });
        const popover = new bootstrap.Popover(this);
        popover.show();
    });

    // TinyMCE
    tinymce.init({
      selector: '#atividades, #requisitos, #observacoes, #descricaoVaga',
      plugins: 'lists link image table code',
      toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
      height: 200,
      content_langs: [
        { title: 'Portuguese', code: 'pt' }
      ],
      setup: function (editor) {
        editor.on('change', function () {
          tinymce.triggerSave();
        });
      }
    });

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

    // Salário
    const salarioTipo = $('#salario_tipo');
    const valorUnicoContainer = $('#valor_unico_container');
    const faixaContainer = $('#faixa_container');
    const salarioValor = $('#salario_valor');
    const salarioDe = $('#salario_de');
    const salarioAte = $('#salario_ate');
    const salaryHidden = $('#salary');

    function formatarReal(valor) {
        if (!valor) return 'R$ 0,00';
        valor = valor.replace(/\D/g, '');
        valor = (parseInt(valor) / 100).toFixed(2);
        valor = valor.replace('.', ',');
        valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        return 'R$ ' + valor;
    }

    function atualizarCampoOculto() {
        const tipo = salarioTipo.val();

        if (tipo === 'A combinar') {
            salaryHidden.val('A combinar');
        } else if (tipo === 'Valor') {
            salaryHidden.val(formatarReal(salarioValor.val()));
        } else if (tipo === 'Faixa') {
            const de = formatarReal(salarioDe.val());
            const ate = formatarReal(salarioAte.val());
            salaryHidden.val(de + ' - ' + ate);
        }
    }

    function aplicarMascaraReal(input) {
        input.on('input', function(e) {
            let valor = e.target.value.replace(/\D/g, '');
            if (valor === '') {
                e.target.value = '';
                return;
            }
            valor = (parseInt(valor) / 100).toFixed(2);
            valor = valor.replace('.', ',');
            valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            e.target.value = 'R$ ' + valor;
            atualizarCampoOculto();
        });
    }

    aplicarMascaraReal(salarioValor);
    aplicarMascaraReal(salarioDe);
    aplicarMascaraReal(salarioAte);

    function atualizarCamposVisiveis() {
        const tipo = salarioTipo.val();
        if (tipo === 'A combinar') {
            valorUnicoContainer.hide();
            faixaContainer.hide();
        } else if (tipo === 'Valor') {
            valorUnicoContainer.show();
            faixaContainer.hide();
        } else if (tipo === 'Faixa') {
            valorUnicoContainer.hide();
            faixaContainer.css('display', 'flex');
        }
        atualizarCampoOculto();
    }
    salarioTipo.on('change', atualizarCamposVisiveis);
    atualizarCamposVisiveis();

    // Local de Trabalho e Sincronização
    const radiosWorkplaceType = $('input[name="workplace_type"]');
    const alertaEndereco = $('#alerta-endereco');
    const camposEndereco = $('#campos-endereco');

    function updateWorkplaceAddressVisibility() {
      const selectedType = $('input[name="workplace_type"]:checked').val();
      const showAddress = (selectedType === 'Presencial' || selectedType === 'Hibrido');

      alertaEndereco.toggleClass('d-none', !showAddress);
      camposEndereco.toggleClass('d-none', !showAddress);

      if (!showAddress) {
        camposEndereco.find('input').val('');
      }
    }

    radiosWorkplaceType.on('change', updateWorkplaceAddressVisibility);
    updateWorkplaceAddressVisibility();

    // Sincronizar Endereço
    const companyId = {{ session('company_id') ?? 'null' }};

    $('#btn-sincronizar').on('click', function () {
      if (!companyId || companyId === 'null') {
        Swal.fire({ // Substituído alert por Swal.fire
            icon: 'warning',
            title: 'Atenção!',
            text: 'ID da empresa não disponível para sincronizar o endereço. Por favor, recarregue a página ou contate o suporte.',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
        return;
      }
      const btn = $(this);
      btn.prop('disabled', true).text('Sincronizando...');

      $.ajax({
        url: `/empresa/${companyId}/endereco`, // Esta rota deve retornar os dados de endereço da empresa
        method: 'GET',
        success: function (dados) {
          $('#cep').val(dados.cep || '');
          $('#estado').val(dados.estado || '');
          $('#cidade').val(dados.cidade || '');
          $('#bairro').val(dados.bairro || '');
          $('#logradouro').val(dados.logradouro || '');
          $('#numero').val(dados.numero || '');
          $('#complemento').val(dados.complemento || '');
          Swal.fire({
              icon: 'success',
              title: 'Sincronizado!',
              text: 'Endereço da empresa sincronizado com sucesso.',
              timer: 2000,
              showConfirmButton: false
          });
        },
        error: function (xhr, status, error) {
          console.error('Erro na requisição de endereço:', xhr.status, error);
          Swal.fire({ // Substituído alert por Swal.fire
              icon: 'error',
              title: 'Erro!',
              text: `Erro ao buscar endereço: ${xhr.status} - ${error}.`,
              customClass: {
                  confirmButton: 'btn btn-primary'
              },
              buttonsStyling: false
          });
        },
        complete: function () {
          btn.prop('disabled', false).text('Sincronizar');
        }
      });
    });

    // Sincronizar com Engenharia de Cargos
    $('#btnSincronizarEngenharia').on('click', function() {
        const jobId = $('#job-select').val();
        const descricaoVagaTextarea = tinymce.get('descricaoVaga'); // Obtém a instância do TinyMCE

         const atividades = tinymce.get('atividades'); // Obtém a instância do TinyMCE
 const requisitos = tinymce.get('requisitos'); // Obtém a instância do TinyMCE
        if (!jobId) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                text: 'Por favor, selecione um Cargo antes de sincronizar.',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sincronizando...');

        $.ajax({
            url: `/api/job-roles/${jobId}/long-description`, // Exemplo de rota para buscar a descrição longa
            method: 'GET',
            success: function(response) {
                if (response.long_description) {
                    // Define o conteúdo do TinyMCE
                    if (descricaoVagaTextarea) {
                        descricaoVagaTextarea.setContent(response.long_description);
                        atividades.setContent(response.activities);
                        requisitos.setContent(response.requirements);
                        Swal.fire({
                            icon: 'success',
                            title: 'Sincronizado!',
                            text: 'Descrição da vaga sincronizada com sucesso.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // Fallback se TinyMCE não for encontrado
                        $('#descricaoVaga').val(response.long_description);
                        Swal.fire({
                            icon: 'success',
                            title: 'Sincronizado!',
                            text: 'Descrição da vaga sincronizada com sucesso (TinyMCE não encontrado, texto inserido diretamente).',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Info!',
                        text: 'Nenhuma descrição longa encontrada para este cargo.',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao sincronizar descrição:', xhr.status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: `Erro ao buscar descrição do cargo: ${xhr.status} - ${error}.`,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            },
            complete: function() {
                btn.prop('disabled', false).html('Sincronizar com a Engenharia de Cargos');
            }
        });
    });


    // Perguntas Customizadas
    const perguntasInput = document.getElementById('perguntas_json');
    const listaPerguntasBody = document.getElementById('tbodyPerguntas');
    const cardListaPerguntas = document.getElementById('cardListaPerguntas');
    const formPergunta = document.getElementById('formPergunta');
    const tipoPerguntaSelect = document.getElementById('tipoPergunta');
    const containerAlternativas = document.getElementById('containerAlternativas');
    const listaAlternativasDiv = document.getElementById('listaAlternativas');
    const btnAddAlternativa = document.getElementById('btnAddAlternativa');

    let perguntas = [];
    try {
        perguntas = JSON.parse(perguntasInput.value || '[]');
        if (!Array.isArray(perguntas)) {
            perguntas = [];
        }
    } catch (e) {
        console.error("Erro ao parsear perguntas_json:", e);
        perguntas = [];
    }

    function atualizarTabelaPerguntas() {
  const perguntasAlt = perguntas.filter(p => p.tipo === 'alternativa').slice(0, 3);
  const perguntasDis = perguntas.filter(p => p.tipo === 'dissertativa').slice(0, 2);

  // Atualiza inputs q1-q5
  $('#q1').val(perguntasAlt[0]?.texto || '');
  $('#q2').val(perguntasAlt[1]?.texto || '');
  $('#q3').val(perguntasAlt[2]?.texto || '');
  $('#q4').val(perguntasDis[0]?.texto || '');
  $('#q5').val(perguntasDis[1]?.texto || '');

  const todasPerguntas = [...perguntasAlt, ...perguntasDis];
  if (todasPerguntas.length === 0) {
    cardListaPerguntas.style.display = 'none';
    return;
  }

  cardListaPerguntas.style.display = 'block';
  listaPerguntasBody.innerHTML = '';
  todasPerguntas.forEach((p, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${p.tipo === 'alternativa' ? 'Alternativa' : 'Dissertativa'}</td>
      <td>${p.texto}</td>
      <td><button type="button" class="btn btn-sm btn-danger btn-remover" data-index="${i}">Remover</button></td>
    `;
    listaPerguntasBody.appendChild(tr);
  });
}


    $(tipoPerguntaSelect).on('change', function() {
      if($(this).val() === 'alternativa'){
        $(containerAlternativas).removeClass('d-none');
      } else {
        $(containerAlternativas).addClass('d-none');
        $(listaAlternativasDiv).html(`
            <div class="input-group mb-2 alternativa-item">
              <input type="text" class="form-control alternativa-texto" placeholder="Alternativa 1" required>
              <button type="button" class="btn btn-danger btn-remover-alternativa" title="Remover alternativa">X</button>
            </div>
        `);
      }
    });

    $(btnAddAlternativa).on('click', () => {
      const count = $(listaAlternativasDiv).find('.alternativa-item').length + 1;
      const div = $(`
        <div class="input-group mb-2 alternativa-item">
          <input type="text" class="form-control alternativa-texto" placeholder="Alternativa ${count}" required>
          <button type="button" class="btn btn-danger btn-remover-alternativa" title="Remover alternativa">X</button>
        </div>
      `);
      $(listaAlternativasDiv).append(div);
    });

    $(listaAlternativasDiv).on('click', '.btn-remover-alternativa', (e) => {
      $(e.target).closest('.alternativa-item').remove();
    });

    $(listaPerguntasBody).on('click', '.btn-remover', (e) => {
      const index = $(e.target).data('index');
      perguntas.splice(index,1);
      atualizarTabelaPerguntas();
    });

    $(formPergunta).on('submit', (e) => {
  e.preventDefault();

  let tipo = $(tipoPerguntaSelect).val();
  if (tipo === 'dissertativa') tipo = 'dissertativa';
  const texto = $('#textoPergunta').val().trim();

  if (!tipo || !texto) {
    Swal.fire({
      icon: 'warning',
      title: 'Atenção!',
      text: 'Preencha o tipo e o texto da pergunta.',
      customClass: { confirmButton: 'btn btn-primary' },
      buttonsStyling: false
    });
    return;
  }

  const countAlt = perguntas.filter(p => p.tipo === 'alternativa').length;
  const countDis = perguntas.filter(p => p.tipo === 'dissertativa').length;

  if (tipo === 'alternativa' && countAlt >= 3) {
    Swal.fire({
      icon: 'warning',
      title: 'Limite Atingido',
      text: 'Você só pode adicionar até 3 perguntas alternativas.',
      customClass: { confirmButton: 'btn btn-primary' },
      buttonsStyling: false
    });
    return;
  }

  if (tipo === 'dissertativa' && countDis >= 2) {
    Swal.fire({
      icon: 'warning',
      title: 'Limite Atingido',
      text: 'Você só pode adicionar até 2 perguntas dissertativas.',
      customClass: { confirmButton: 'btn btn-primary' },
      buttonsStyling: false
    });
    return;
  }

  let alternativas = [];
  if (tipo === 'alternativa') {
    const alternativasInputs = $(listaAlternativasDiv).find('.alternativa-texto');
    alternativasInputs.each(function () {
      const val = $(this).val().trim();
      if (val) alternativas.push(val);
    });
  }

  perguntas.push({ tipo, texto, alternativas });
  $(formPergunta)[0].reset();
  $(containerAlternativas).addClass('d-none');
  $(listaAlternativasDiv).html(`
    <div class="input-group mb-2 alternativa-item">
      <input type="text" class="form-control alternativa-texto" placeholder="Alternativa 1" required>
      <button type="button" class="btn btn-danger btn-remover-alternativa" title="Remover alternativa">X</button>
    </div>
  `);

  const modalEl = document.getElementById('modalPergunta');
  const modal = bootstrap.Modal.getInstance(modalEl);
  modal.hide();

  atualizarTabelaPerguntas();

  atualizarTabelaPerguntas();
});


    atualizarTabelaPerguntas();
  });
</script>

<script src="{{ asset('assets/functions/buscaCep.js') }}"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>

@endsection