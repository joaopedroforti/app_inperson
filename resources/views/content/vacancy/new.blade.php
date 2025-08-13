@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Cadastro Nova Vaga')

@section('content')

{{-- SweetAlert2 CSS (para os alertas bonitos) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

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

 /* Linha de fundo (do centro do 1º círculo ao centro do último) */
 .multi-step-header::before{
  content:'';
  position:absolute;
  top:33px;
  left:var(--line-left,0);         /* calculado no JS */
  width:var(--line-width,100%);    /* calculado no JS */
  height:1px;
  background-color:#e0e0e0;
  z-index:1;
}

/* Linha ativa */
.multi-step-header .progress-line{
  position:absolute;
  top:33px;
  left:var(--line-left,0);         /* mesmo início da de fundo */
  width:var(--line-width,100%);    /* mesmo fim da de fundo */
  height:1px;
  background-color:#696cff;
  transform-origin:left center;
  transform:scaleX(0);             /* começa em 0% */
  transition:transform .3s ease-in-out;
  z-index:1;
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
              <input 
        required 
        type="date" 
        class="form-control" 
        id="dataAbertura" 
        name="data_abertura_vaga"
        min="{{ date('Y-m-d') }}"  {{-- Impede datas passadas --}}
    >
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
              <input type="hidden" id="salary" name="salary" value="">
            </div>


            <div class="col-md-6">
  <label class="form-label">Benefícios *</label>

  <select id="benefitsSelect" class="form-select" multiple>
    <option value="Vale-transporte">Vale-transporte</option>
    <option value="Assistência médica">Assistência médica</option>
    <option value="Vale-refeição">Vale-refeição</option>
    <option value="Vale-alimentação">Vale-alimentação</option>
    <option value="Assistência odontológica">Assistência odontológica</option>
    <option value="Seguro de vida">Seguro de vida</option>
    <option value="PLR/Bonificação">PLR/Bonificação</option>
    <option value="Auxílio Home Office">Auxílio Home Office</option>
    <option value="Horário flexível">Horário flexível</option>
    <option value="Gympass/Academia">Gympass/Academia</option>
  </select>

  <input type="hidden" name="benefits" id="benefits"
         value="{{ old('benefits', $vacancy->benefits ?? '') }}">

  <small class="text-muted">Selecione ou digite e pressione <strong>Enter</strong>.</small>
</div>

<style>
  /* Altura e borda do campo */
  .select2-container--bootstrap-5 .select2-selection--multiple {
    min-height: calc(2.55rem + 2px);
    padding: .275rem .5rem .25rem .5rem;
    border-radius: .5rem;
  }
  /* Chips (tags) */
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
    background-color: #696cff;
    border: 0;
    color: white;
    border-radius: .45rem;
    padding: 2px 8px;
    margin-top: .35rem;
    box-shadow: none;
    font-size: 12px;
  }
  /* X das tags */
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
    color: white;
    margin-right: .35rem;
    font-weight: 700;
  }
  /* Input interno (quando digitando) */
  .select2-container--bootstrap-5 .select2-search__field {
    margin-top: .3rem;
    padding: 0.2rem .25rem;
  }
  /* Hover/seleção na lista */
  .select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #696cff;
    color: #fff;
  }
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    padding-left: 0;
    margin: 0;
    list-style: none;
}
.select2-selection__choice__display{
  font-size: 12px;
  color: white;
}
</style>


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
            {{-- Botão roxo --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPergunta">
              <i class="mdi mdi-plus me-1"></i> Adicionar Pergunta
            </button>
          </div>

          {{-- campos legados --}}
          <input type="hidden" name="q1" id="q1" value="">
          <input type="hidden" name="q2" id="q2" value="">
          <input type="hidden" name="q3" id="q3" value="">
          <input type="hidden" name="q4" id="q4" value="">
          <input type="hidden" name="q5" id="q5" value="">
          <input type="hidden" id="perguntas_json" name="perguntas_json" value="[]">

          {{-- NOVO: container dos inputs hidden do array "perguntas" --}}
          <div id="perguntasHiddenContainer"></div>

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

  <input type="number" name="vacancies_number" class="form-control">
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

{{-- MODAL DE CADASTRO DE PERGUNTA --}}
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
    const $header = $('.multi-step-header');

    // linha do centro do 1º ao centro do último círculo
    function computeHeaderLine() {
      const headerEl = $header.get(0);
      if (!headerEl) return;
      const circles = headerEl.querySelectorAll('.step-item .step-circle');
      if (circles.length < 2) return;

      const headerRect = headerEl.getBoundingClientRect();
      const firstRect  = circles[0].getBoundingClientRect();
      const lastRect   = circles[circles.length - 1].getBoundingClientRect();

      const firstCenter = (firstRect.left + firstRect.right) / 2 - headerRect.left;
      const lastCenter  = (lastRect.left + lastRect.right) / 2 - headerRect.left;

      const left  = Math.max(0, firstCenter);
      const width = Math.max(0, lastCenter - firstCenter);

      headerEl.style.setProperty('--line-left',  left + 'px');
      headerEl.style.setProperty('--line-width', width + 'px');
    }

    function showStep(stepNum) {
      // Validação para não pular etapa sem validar a atual
      if (stepNum > currentStep && !validateCurrentStep(currentStep)) return;

      // Alterna conteúdos
      $('.step-content').removeClass('active');
      $(`#step-${stepNum}-content`).addClass('active');

      // Cabeçalho
      $('.multi-step-header .step-item').removeClass('active completed');
      for (let i = 1; i <= totalSteps; i++) {
        const stepItem = $(`.multi-step-header .step-item[data-step="${i}"]`);
        if (i < stepNum) stepItem.addClass('completed');
        else if (i === stepNum) stepItem.addClass('active');
      }

      currentStep = stepNum;
      updateHeaderButtonsVisibility();
      updateProgressBar();
    }

    function updateHeaderButtonsVisibility() {
  const $btnPrev = $('.btn-prev');
  const $btnNext = $('.btn-next');
  let $btnPublicar = $('#btnPublicarFinal');

  // Mostra/esconde "Anterior"
  $btnPrev.toggle(currentStep > 1);

  // Última etapa: esconde "Próximo" e mostra "Publicar"
  if (currentStep === totalSteps) {
    $btnNext.hide();
    if (!$btnPublicar.length) {
      $btnPublicar = $('<button type="button" id="btnPublicarFinal" class="btn btn-success">Publicar</button>');
      $btnPublicar.insertAfter($btnNext);
    } else {
      $btnPublicar.show();
    }
  } else {
    // Demais etapas: mostra "Próximo" e esconde "Publicar"
    $btnNext.show();
    if ($btnPublicar.length) $btnPublicar.hide();
  }
}

    function updateProgressBar() {
      const factor = (currentStep - 1) / (totalSteps - 1); // 0..1
      $progressLine.css('transform', `scaleX(${factor})`);
    }

    function validateCurrentStep(stepNum) {
      let isValid = true;
      const currentStepContent = $(`#step-${stepNum}-content`);

      currentStepContent.find('.is-invalid').removeClass('is-invalid');
      currentStepContent.find('.invalid-feedback').remove();
      currentStepContent.find('.radio-error-message').remove();

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

      tinymce.triggerSave();
      return isValid;
    }

    // Eventos Next/Prev + clique no header
    $('.btn-next').on('click', function () {
      if (currentStep < totalSteps) showStep(currentStep + 1);
    });
    $('.btn-prev').on('click', function () {
      if (currentStep > 1) showStep(currentStep - 1);
    });
    $('.multi-step-header .step-item').on('click', function() {
      const clickedStep = parseInt($(this).data('step'));
      showStep(clickedStep);
    });

    // Botão Cancelar
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
          window.location.href = '/vacancy/list';
        }
      });
    });

   // Botão Publicar
   // Botão Publicar
$(document).on('click', '#btnPublicarFinal', function (e) {
    e.preventDefault();

    if (validateCurrentStep(totalSteps)) {
        tinymce.triggerSave(); // garante que os campos TinyMCE sejam salvos
        $('#multiStepForm').submit(); // envia o form normalmente
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Campos Obrigatórios',
            text: 'Por favor, preencha todos os campos obrigatórios na última etapa antes de publicar.',
            customClass: { confirmButton: 'btn btn-primary' },
            buttonsStyling: false
        });
    }
});


    // Inicializa
    computeHeaderLine();
    showStep(1); // isso também ajusta os botões
    updateProgressBar();
    $(window).on('resize', computeHeaderLine);

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
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: 'ID da empresa não disponível para sincronizar o endereço. Por favor, recarregue a página ou contate o suporte.',
            customClass: { confirmButton: 'btn btn-primary' },
            buttonsStyling: false
        });
        return;
      }
      const btn = $(this);
      btn.prop('disabled', true).text('Sincronizando...');

      $.ajax({
        url: `/empresa/${companyId}/endereco`,
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
          Swal.fire({
              icon: 'error',
              title: 'Erro!',
              text: `Erro ao buscar endereço: ${xhr.status} - ${error}.`,
              customClass: { confirmButton: 'btn btn-primary' },
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
                customClass: { confirmButton: 'btn btn-primary' },
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
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: `Erro ao buscar descrição do cargo: ${xhr.status} - ${error}.`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
            },
            complete: function() {
                btn.prop('disabled', false).html('Sincronizar com a Engenharia de Cargos');
            }
        });
    });


    // ===== Perguntas Customizadas (array perguntas + enter foca próxima) =====
    const perguntasInput = document.getElementById('perguntas_json');
    const listaPerguntasBody = document.getElementById('tbodyPerguntas');
    const cardListaPerguntas = document.getElementById('cardListaPerguntas');
    const formPergunta = document.getElementById('formPergunta');
    const tipoPerguntaSelect = document.getElementById('tipoPergunta');
    const containerAlternativas = document.getElementById('containerAlternativas');
    const listaAlternativasDiv = document.getElementById('listaAlternativas');
    const btnAddAlternativa = document.getElementById('btnAddAlternativa');
    const perguntasHiddenContainer = document.getElementById('perguntasHiddenContainer');

    let perguntas = [];
    let editingIndex = null;

    try {
        perguntas = JSON.parse(perguntasInput.value || '[]');
        if (!Array.isArray(perguntas)) {
            perguntas = [];
        }
    } catch (e) { perguntas = []; }

    function escapeHtml(s){
      return (s||'')
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
    }

    function countByType(t){ return perguntas.filter(p => p.tipo === t).length; }

    function clampAlternatives() {
      const total = listaAlternativasDiv.querySelectorAll('.alternativa-item').length;
      if (total >= 5) btnAddAlternativa.setAttribute('disabled','disabled');
      else btnAddAlternativa.removeAttribute('disabled');
    }

    function addAlternativaInput(val='') {
      const total = listaAlternativasDiv.querySelectorAll('.alternativa-item').length;
      if (total >= 5) return;
      const row = document.createElement('div');
      row.className = 'input-group mb-2 alternativa-item';
      row.innerHTML = `
        <input type="text" class="form-control alternativa-texto" placeholder="Alternativa ${total+1}" value="${escapeHtml(val)}">
        <button type="button" class="btn btn-danger btn-remover-alternativa" title="Remover alternativa">X</button>
      `;
      listaAlternativasDiv.appendChild(row);
      clampAlternatives();
      // foca no novo input
      const inputs = listaAlternativasDiv.querySelectorAll('.alternativa-texto');
      inputs[inputs.length-1].focus();
    }

    function resetAlternativas(list=['']) {
      listaAlternativasDiv.innerHTML = '';
      (list.length ? list : ['']).forEach(v=>addAlternativaInput(v));
      clampAlternatives();
    }

    function rebuildHiddenPerguntas() {
      if (!perguntasHiddenContainer) return;
      perguntasHiddenContainer.innerHTML = '';
      perguntas.forEach((p, i) => {
        perguntasHiddenContainer.insertAdjacentHTML('beforeend',
          `<input type="hidden" name="perguntas[${i}][tipo]" value="${escapeHtml(p.tipo)}">`);
        perguntasHiddenContainer.insertAdjacentHTML('beforeend',
          `<input type="hidden" name="perguntas[${i}][pergunta]" value="${escapeHtml(p.texto)}">`);
        if (p.tipo === 'alternativa' && Array.isArray(p.alternativas)) {
          p.alternativas.forEach(alt => {
            perguntasHiddenContainer.insertAdjacentHTML('beforeend',
              `<input type="hidden" name="perguntas[${i}][alternativas][]" value="${escapeHtml(alt)}">`);
          });
        }
      });
    }

    function atualizarTabelaPerguntas() {
      const perguntasAlt = perguntas.filter(p => p.tipo === 'alternativa').slice(0, 3);
      const perguntasDis = perguntas.filter(p => p.tipo === 'objetiva').slice(0, 2);

      // inputs legados
      $('#q1').val(perguntasAlt[0]?.texto || '');
      $('#q2').val(perguntasAlt[1]?.texto || '');
      $('#q3').val(perguntasAlt[2]?.texto || '');
      $('#q4').val(perguntasDis[0]?.texto || '');
      $('#q5').val(perguntasDis[1]?.texto || '');

      // tabela
      if (!perguntas.length) {
        cardListaPerguntas.style.display = 'none';
        listaPerguntasBody.innerHTML = '';
      } else {
        cardListaPerguntas.style.display = 'block';
        listaPerguntasBody.innerHTML = '';
        perguntas.forEach((p, i) => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${p.tipo === 'alternativa' ? 'Alternativa' : 'Objetiva'}</td>
            <td class="text-truncate" style="max-width:520px;" title="${escapeHtml(p.texto)}">${escapeHtml(p.texto)}</td>
            <td class="text-nowrap">
              <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-editar" data-index="${i}">Editar</button>
              <button type="button" class="btn btn-sm btn-danger btn-remover" data-index="${i}">Remover</button>
            </td>
          `;
          listaPerguntasBody.appendChild(tr);
        });
      }

      // mantém JSON legado e monta array perguntas
      perguntasInput.value = JSON.stringify(perguntas);
      rebuildHiddenPerguntas();
    }

    $(tipoPerguntaSelect).on('change', function() {
      if($(this).val() === 'alternativa'){
        $(containerAlternativas).removeClass('d-none');
        if (!listaAlternativasDiv.querySelector('.alternativa-item')) resetAlternativas(['']);
      } else {
        $(containerAlternativas).addClass('d-none');
        resetAlternativas(['']);
      }
    });

    $(btnAddAlternativa).on('click', () => addAlternativaInput(''));

    $(listaAlternativasDiv).on('click', '.btn-remover-alternativa', (e) => {
      e.currentTarget.closest('.alternativa-item')?.remove();
      clampAlternatives();
      [...listaAlternativasDiv.querySelectorAll('.alternativa-texto')].forEach((inp,idx)=>inp.placeholder=`Alternativa ${idx+1}`);
    });

    // Enter: em alternativa cria nova e foca; textarea permite quebra de linha; evita submit
    $(listaAlternativasDiv).on('keydown', '.alternativa-texto', function(e){
      if (e.key === 'Enter') {
        e.preventDefault();
        if (listaAlternativasDiv.querySelectorAll('.alternativa-item').length < 5) {
          addAlternativaInput('');
        }
      }
    });
    $(formPergunta).on('keydown', function(e){
      if (e.key === 'Enter') {
        const t = e.target;
        if (t.tagName === 'TEXTAREA') return; // permite newline no texto da pergunta
        if (t.classList.contains('alternativa-texto')) return; // já tratado acima
        e.preventDefault(); // impede submit por Enter
      }
    });

    // editar/remover
    $(listaPerguntasBody).on('click', '.btn-editar', (e) => {
      const idx = Number($(e.currentTarget).data('index'));
      editingIndex = idx;
      const p = perguntas[idx];
      $('#tipoPergunta').val(p.tipo);
      $('#textoPergunta').val(p.texto);
      if (p.tipo === 'alternativa') {
        $(containerAlternativas).removeClass('d-none');
        resetAlternativas(p.alternativas?.length ? p.alternativas : ['']);
      } else {
        $(containerAlternativas).addClass('d-none');
        resetAlternativas(['']);
      }
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPergunta')).show();
    });
    $(listaPerguntasBody).on('click', '.btn-remover', (e) => {
      const index = Number($(e.currentTarget).data('index'));
      perguntas.splice(index,1);
      atualizarTabelaPerguntas();
    });

    // abrir modal limpo
    $(document).on('click','[data-bs-target="#modalPergunta"]', function(){
      editingIndex = null;
      formPergunta.reset();
      $(containerAlternativas).addClass('d-none');
      resetAlternativas(['']);
    });

    // SUBMIT — inclui "objetiva" corretamente + limites
    $(formPergunta).on('submit', (e) => {
      e.preventDefault();

      const tipoRaw = ($('#tipoPergunta').val() || '').trim();
      const tipo = (tipoRaw === 'alternativa') ? 'alternativa' : 'objetiva';
      const texto = ($('#textoPergunta').val() || '').trim();

      if (!texto) {
        Swal.fire({
          icon: 'warning',
          title: 'Atenção!',
          text: 'Preencha o texto da pergunta.',
          customClass: { confirmButton: 'btn btn-primary' },
          buttonsStyling: false
        });
        return;
      }

      if (editingIndex === null) {
        if (tipo === 'alternativa' && countByType('alternativa') >= 3) {
          Swal.fire({ icon:'warning', title:'Limite Atingido', text:'Você só pode adicionar até 3 perguntas alternativas.', customClass:{confirmButton:'btn btn-primary'}, buttonsStyling:false });
          return;
        }
        if (tipo === 'objetiva' && countByType('objetiva') >= 2) {
          Swal.fire({ icon:'warning', title:'Limite Atingido', text:'Você só pode adicionar até 2 perguntas objetivas.', customClass:{confirmButton:'btn btn-primary'}, buttonsStyling:false });
          return;
        }
      }

      let alternativas = [];
      if (tipo === 'alternativa') {
        const alternativasInputs = $(listaAlternativasDiv).find('.alternativa-texto');
        alternativasInputs.each(function () {
          const val = $(this).val().trim();
          if (val) alternativas.push(val);
        });
        if (!alternativas.length) {
          Swal.fire({ icon:'warning', title:'Atenção!', text:'Adicione ao menos 1 alternativa.', customClass:{confirmButton:'btn btn-primary'}, buttonsStyling:false });
          return;
        }
        if (alternativas.length > 5) alternativas = alternativas.slice(0,5);
      }

      const registro = { tipo, texto, alternativas };
      if (editingIndex === null) perguntas.push(registro);
      else perguntas[editingIndex] = registro;

      // fecha modal
      formPergunta.reset();
      $(containerAlternativas).addClass('d-none');
      resetAlternativas(['']);
      bootstrap.Modal.getInstance(document.getElementById('modalPergunta')).hide();

      atualizarTabelaPerguntas();
    });

    atualizarTabelaPerguntas(); // monta tabela + hidden do array perguntas
  });
</script>
{{-- page-script (depois do jQuery) --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(function() {
    // Inicializa Select2 com tema Bootstrap 5 + suporte a tags
    $('#benefitsSelect').select2({
      tags: true,
      tokenSeparators: [','],
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: 'Selecione ou digite e pressione Enter'
    });

    // Pre-popula a partir do hidden (se vier do banco ou old())
    const existing = ($('#benefits').val() || '')
      .split(',')
      .map(s => s.trim())
      .filter(Boolean);

    if (existing.length) {
      existing.forEach(v => {
        if (!$('#benefitsSelect').find(`option[value="${v.replace(/"/g,'\\"')}"]`).length) {
          $('#benefitsSelect').append(new Option(v, v, true, true));
        } else {
          $('#benefitsSelect').find(`option[value="${v.replace(/"/g,'\\"')}"]`).prop('selected', true);
        }
      });
      $('#benefitsSelect').trigger('change');
    }

    // Mantém o hidden sempre sincronizado (vírgulas)
    $('#benefitsSelect').on('change', function() {
      const vals = $(this).val() || [];
      $('#benefits').val(vals.join(', '));
    });
  });
</script>

<script src="{{ asset('assets/functions/buscaCep.js') }}"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>

@endsection
