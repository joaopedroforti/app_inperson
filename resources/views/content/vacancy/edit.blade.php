@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Editar Vaga')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/n7b8zpu0tl0lg9ka80vagfoo3vtu97zk3rwall7rpfhg95q7/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-body pb-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div>
                <h4 class="mb-1">{{ $vacancy['description'] }}</h4>
                <p class="mb-1 text-muted">{{ $job['description'] }} - {{ $department['description'] ?? '' }} - {{ $job['seniority'] ?? '' }}</p>
                <small class="text-muted">Vaga aberta há {{ \Carbon\Carbon::parse($vacancy['created_at'])->diffInDays() }} dias</small>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2 mt-3 mt-md-0">
                <a href="#" onclick="copiarLinkVaga()" class="text-primary fw-semibold me-3">Copiar link da Vaga</a>

                <a href="{{ url('vacancy/' . $vacancy['reference']) }}" class="btn btn-outline-secondary btn-sm" id="candidatosBtn">
                    <i class="bi bi-people me-1"></i> Candidatos
                </a>

                <a href="{{ url('vacancy/edit/' . $vacancy['reference']) }}" class="btn btn-primary btn-sm" id="editarVagaBtn">
                    <i class="bi bi-pencil-square me-1"></i> Editar Vaga
                </a>

                <a href="{{ url('vacancy/competences/' . $vacancy['reference']) }}" class="btn  btn-outline-secondary btn-sm" id="rodaCompetenciaBtn">
                    <i class="bi bi-clock-history me-1"></i> Roda de Competência do Cargo
                </a>
            </div>
        </div>
    </div>
</div>

    <div id="editar-vaga-content">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Editar Vaga</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/vacancy/new">
                    @csrf
                    <div class="row g-3 mb-4">
                    <input hidden type="text" class="form-control" name="reference" value="{{ $vacancy['reference'] }}" required>
                        <div class="col-md-6">
                            <label class="form-label">Título da Vaga*</label>
                            <input type="text" class="form-control" name="description" value="{{ $vacancy['description'] }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Local de Trabalho*</label>
                            <select class="form-select" name="local" required>
                                @foreach(['Presencial', 'Remoto', 'Híbrido'] as $tipo)
                                    <option value="{{ $tipo }}" {{ $vacancy['local'] == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jornada de Trabalho*</label>
                            <input type="text" class="form-control" name="working_hours" value="{{ $vacancy['working_hours'] ?? '44h semanais' }}" required>
                        </div>
                        @php
  // usa o valor do model; se não existir, default = 0 
  $conf = optional($vacancy)->confidential ?? 0;
@endphp

<div class="col-md-4">
  <label class="form-label">Divulgar no Porta de Vagas?*</label><br>

  <div class="form-check form-check-inline mt-2">
    <input class="form-check-input" type="radio" name="confidential" value="0" @checked($conf == 0)>
    <label class="form-check-label">Sim</label>
  </div>

  <div class="form-check form-check-inline mt-2">
    <input class="form-check-input" type="radio" name="confidential" value="1" @checked($conf ==1)>
    <label class="form-check-label">Não</label>
  </div>
</div>



                        <div class="col-md-4">
                            <label class="form-label">Vagas*</label>
                            <input class="form-control" type="number" name="vacancies_number" required value="{{ $vacancy['vacancies_number'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Encerramento da Vaga*</label>
                            <input type="date" class="form-control" name="expiration_date" value="{{ date('Y-m-d', strtotime($vacancy['expiration_date'])) }}" required>
                        </div>
                        <div class="col-md-4">
  <label for="salary" class="form-label">Oferta Salarial*</label>
  <input
    type="text"
    class="form-control"
    name="salary"
    id="salary"
    value="{{ ($vacancy->salary ?? '') ?: 'R$ ' }}"
    autocomplete="off"
  >
</div>


{{-- CSS do Select2 (pode ficar no <head> via layout) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

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

  {{-- Hidden enviado no form (string separada por vírgulas) --}}
  <input type="hidden" name="benefits" id="benefits"
         value="{{ old('benefits', $vacancy->benefits ?? '') }}">

  <small class="text-muted">Selecione ou digite e pressione <strong>Enter</strong>.</small>
</div>

{{-- JS do Select2: carregue ANTES da inicialização abaixo. Se o layout já traz, remova esta linha. --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  (function initBenefits() {
    // Se houver jQuery duplicado, isso ajuda a diagnosticar
    if (!window.jQuery) { console.error('jQuery não está carregado'); return; }
    if (!jQuery.fn || !jQuery.fn.select2) { 
      console.error('Select2 não carregado ou conflito de jQuery duplicado');
      return;
    }

    const $el = $('#benefitsSelect');
    if (!$el.length) return; // evita "of null" se o select não existe nesta página

    $el.select2({
      tags: true,
      tokenSeparators: [','],
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: 'Selecione ou digite e pressione Enter'
    });

    // Pre-popula a partir do hidden (ex.: "VT, VR, Plano de saúde")
    const existingRaw = ($('#benefits').val() || '');
    const existing = existingRaw.split(',').map(s => s.trim()).filter(Boolean);

    if (existing.length) {
      existing.forEach(v => {
        const safe = v.replace(/"/g, '\\"');
        const $opt = $el.find(`option[value="${safe}"]`);
        if ($opt.length) {
          $opt.prop('selected', true);
        } else {
          $el.append(new Option(v, v, true, true));
        }
      });
      $el.trigger('change');
    }

    // Mantém o hidden sincronizado
    $el.on('change', function () {
      const vals = $(this).val() || [];
      $('#benefits').val(vals.join(', '));
    });
  })();
</script>

<style>
  /* Mesmo visual do outro blade */
  .select2-container--bootstrap-5 .select2-selection--multiple {
    min-height: calc(2.55rem + 2px);
    padding: .275rem .5rem .25rem .5rem;
    border-radius: .5rem;
  }
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
    background-color: #696cff;
    border: 0;
    color: #fff;
    border-radius: .45rem;
    padding: 2px 8px;
    margin-top: .35rem;
    box-shadow: none;
    font-size: 12px;
  }
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff;
    margin-right: .35rem;
    font-weight: 700;
  }
  .select2-container--bootstrap-5 .select2-search__field {
    margin-top: .3rem;
    padding: 0.2rem .25rem;
  }
  .select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #696cff;
    color: #fff;
  }
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
    display: flex;
    flex-wrap: wrap;
    padding-left: 0;
    margin: 0;
    list-style: none;
  }
  .select2-selection__choice__display {
    font-size: 12px;
    color: #fff;
  }
</style>

                    </div>

                    <div class="card mb-4">
                        <h5 class="card-header">📄 Descrição da Vaga</h5>
                        <div class="card-body">
                            <div class="row g-3">
  <div class="col-12">
    <label class="form-label">Resumo</label>
    <textarea id="resumeEditor" class="form-control tinymce-simple" name="resume" rows="6">{{ $vacancy->resume ?? '' }}</textarea>
  </div>

  <div class="col-12">
    <label class="form-label">Principais Atividades</label>
    <textarea id="atividadess" class="form-control tinymce-simple" name="activities" rows="6">{{ $vacancy->activities ?? '' }}</textarea>
  </div>

  <div class="col-12">
    <label class="form-label">Requisitos</label>
    <textarea id="requisitoss" class="form-control tinymce-simple" name="requirements" rows="6">{{ $vacancy->requirements ?? '' }}</textarea>
  </div>
</div>
                        </div>


                    </div>



                    @php
    // Normaliza vacancy->questions
    $qraw = $vacancy->questions ?? null;

    if (is_string($qraw)) {
        $questions = json_decode($qraw, true) ?? [];
    } elseif (is_array($qraw)) {
        $questions = $qraw;
    } else {
        $questions = [];
    }

    // Helper de tipo
    $tipoBadge = function($t) {
        $t = strtolower((string)$t);
        return $t === 'alternativa'
            ? ['Alternativa','primary']
            : ['Objetiva','secondary'];
    };
@endphp

@if(!empty($questions))
<div class="card mb-4">
    <h5 class="card-header">📄 Perguntas</h5>
    <div class="card-body">
        <ul class="list-group">
            @foreach($questions as $idx => $q)
                @php
                    $tipo = $q['tipo']   ?? 'objetiva';
                    $texto = $q['texto'] ?? 'Pergunta sem texto';
                    $alts = is_array($q['alternativas'] ?? null) ? $q['alternativas'] : [];
                    [$labelTipo, $badgeClass] = $tipoBadge($tipo);
                    $groupId = 'qgroup_'.$idx.'_'.\Illuminate\Support\Str::random(4);
                @endphp

                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0 me-2">{{ $texto }}</h6>
                        <span class="badge bg-{{ $badgeClass }}">{{ $labelTipo }}</span>
                    </div>

                    @if(strtolower($tipo) === 'alternativa')
                        @if(count($alts))
                            <div class="d-grid gap-2">
                                @foreach($alts as $i => $alt)
                                    @php $inputId = $groupId.'_opt_'.$i; @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="{{ $groupId }}" id="{{ $inputId }}" disabled>
                                        <label class="form-check-label" for="{{ $inputId }}">
                                            {{ $alt }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <textarea class="form-control" rows="3" placeholder="Resposta do candidato..." disabled></textarea>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif








                    <div class="mb-4 text-end">
                  
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipo = document.getElementById('salario_tipo');
        const valor = document.getElementById('salario_valor');
        const de = document.getElementById('salario_de');
        const ate = document.getElementById('salario_ate');
        const hidden = document.getElementById('salary');

        function atualizar() {
            const t = tipo.value;
            valor.classList.add('d-none');
            de.classList.add('d-none');
            ate.classList.add('d-none');
            if (t === 'Valor') {
                valor.classList.remove('d-none');
                hidden.value = valor.value;
            } else if (t === 'Faixa') {
                de.classList.remove('d-none');
                ate.classList.remove('d-none');
                hidden.value = `${de.value} - ${ate.value}`;
            } else {
                hidden.value = 'A combinar';
            }
        }

        [tipo, valor, de, ate].forEach(el => el.addEventListener('input', atualizar));
        atualizar();

        tinymce.init({
            selector: '#atividadess, #requisitoss',
            plugins: 'lists link image table code',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
            height: 300,
            language: 'pt_BR'
        });
    });
</script>
<script>
  $(function () {
    const $s = $('#salary');
    const PREFIX = 'R$ ';

    const ensurePrefix = v => {
      v = (v || '').toString();
      if (!v.startsWith(PREFIX)) v = PREFIX + v.replace(/^R\$\s*/,'');
      return v;
    };

    // Estado inicial
    $s.val(ensurePrefix($s.val()));

    // Nunca deixa perder o "R$ "
    $s.on('input blur', function () {
      this.value = ensurePrefix(this.value);
    });

    // Bloqueia apagar o prefixo
    $s.on('keydown', function (e) {
      const start = this.selectionStart, end = this.selectionEnd;
      const blockBack = e.key === 'Backspace' && start <= PREFIX.length && end <= PREFIX.length;
      const blockDel  = e.key === 'Delete'    && start <  PREFIX.length;
      if (blockBack || blockDel) {
        e.preventDefault();
        this.setSelectionRange(PREFIX.length, PREFIX.length);
      }
    });

    // Garante o cursor depois do prefixo
    $s.on('focus', function () {
      if ((this.selectionStart || 0) < PREFIX.length) {
        this.setSelectionRange(PREFIX.length, PREFIX.length);
      }
    });
  });
</script>
<script src="https://cdn.tiny.cloud/1/n7b8zpu0tl0lg9ka80vagfoo3vtu97zk3rwall7rpfhg95q7/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    tinymce.init({
      selector: 'textarea.tinymce-simple',
      menubar: false,
      statusbar: false,
      branding: false,
      height: 220,
      plugins: 'lists link code',
      toolbar: 'bold italic underline | bullist numlist | link | removeformat | code',
      content_style: 'body{font-family:var(--bs-body-font-family);font-size:14px;line-height:1.45}',
      setup(editor){
        editor.on('change input', () => tinymce.triggerSave());
      }
    });
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
@endsection
