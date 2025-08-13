@php
$configData = Helper::appClasses();

$requestData = (!empty($calculation) && !empty($calculation->request))
    ? (json_decode($calculation->request, true) ?? [])
    : [];

$rangeValues = [];
if (is_array($requestData)) {
    $numericKeys = array_filter(array_keys($requestData), fn($k) => ctype_digit((string) $k));
    sort($numericKeys, SORT_NUMERIC);
    foreach ($numericKeys as $k) { $rangeValues[] = (int) $requestData[$k]; }
    $rangeValues = array_slice($rangeValues, 0, 10);
}

$selectedAdjectives = (isset($requestData['adjectives']) && is_array($requestData['adjectives']))
    ? array_map('strval', $requestData['adjectives'])
    : [];

$savedResponseJson = isset($requestData['response']) ? $requestData['response'] : null;

$currentDepartmentId = old('id_department', $departmento->id_department ?? $job->id_department ?? null);
$currentSeniority = old('seniority', $job->seniority ?? '');
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')
<style>
  .adjective_select{display:none}
  .adjective_name{cursor:pointer;margin:auto}
  .btn_adjective,.btn_adjective:hover,.btn_adjective_active{margin:10px;padding:10px;cursor:pointer;border:1px solid #E7E7E7;border-radius:20px;text-align:center;display:flex;align-items:center;justify-content:center}
  .btn_adjective{background:#F1F1F1;color:gray}
  .btn_adjective:hover{background:#508dfd;color:#fff}
  .btn_adjective_active{background:#508dfd;color:#fff}
  .form-range{-webkit-appearance:none;width:100%;height:8px;background:#ddd;outline:none;opacity:.9;transition:opacity .15s;position:relative;border-radius:999px}
  .form-range::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:18px;height:18px;background:#7062C5;border-radius:50%;cursor:pointer;position:relative;z-index:2;border:0}
  .form-range::-moz-range-thumb{width:18px;height:18px;background:#7062C5;border-radius:50%;cursor:pointer;border:0}
  .percent{color:#6c7dff;font-size:15px;font-weight:600}
  .pinrow{margin-top:-20px;color:#b3d1ff;font-weight:bolder;font-size:15px}
</style>

<form method="POST" action="{{ url('job/new') }}">
  @csrf

  <div class="card mb-4">
    <div class="card-body">
      <div class="row align-items-start justify-content-between">
        <div class="col-md-6 d-flex flex-column justify-content-between">
          <h4 class="mb-2">Edição Cargo</h4>
        </div>
        <div class="col-md-3 d-flex justify-content-end">
          <div class="d-flex align-items-start gap-2">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="/job" class="btn btn-outline-danger">Cancelar</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Nome do Cargo</label>
          <input name="description" type="text" value="{{ $job->description ?? '' }}" class="form-control">
        </div>

        <input hidden name="reference" value="{{ $job->reference ?? '' }}">

        <div class="col-md-4">
          <label class="form-label">Departamento</label>
          <select name="id_department" class="form-select">
            <option value=""></option>
            @foreach ($departments as $department)
              <option value="{{ $department->id_department }}" {{ (string)$currentDepartmentId === (string)$department->id_department ? 'selected' : '' }}>
                {{ $department->description }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Senioridade</label>
          <select required class="form-select control" id="seniority" name="seniority">
            <option value=""></option>
            @foreach (['Trainee','Estagiário','Júnior','Pleno','Sênior','Especialista'] as $opt)
              <option value="{{ $opt }}" {{ $currentSeniority === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Descrição do Cargo</label>
          <textarea name="resume" class="form-control">{{ $job->long_description ?? '' }}</textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Principais Atividades</label>
          <textarea name="activities" class="form-control tinymce">{{ $job->activities ?? '' }}</textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Requisitos</label>
          <textarea name="requirements" class="form-control tinymce">{{ $job->requirements ?? '' }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <h1 class="text-center">Competências</h1>
      <p class="text-center">Ajuste as barras, até encontrar o ponto de equilíbrio entre as competências desejadas.</p>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Empatia <span class="percent">(<span id="percent1-0">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-0" name="1" min="1" max="5" step="1" value="{{ $rangeValues[0] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Foco em resultado <span class="percent">(<span id="percent2-0">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Harmonia <span class="percent">(<span id="percent1-1">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-1" name="3" min="1" max="5" step="1" value="{{ $rangeValues[1] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Estrategista <span class="percent">(<span id="percent2-1">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Colaboração <span class="percent">(<span id="percent1-2">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-2" name="5" min="1" max="5" step="1" value="{{ $rangeValues[2] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Automotivação <span class="percent">(<span id="percent2-2">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Diplomacia <span class="percent">(<span id="percent1-3">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-3" name="7" min="1" max="5" step="1" value="{{ $rangeValues[3] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Intraempreendedorismo <span class="percent">(<span id="percent2-3">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Autocontrole <span class="percent">(<span id="percent1-4">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-4" name="9" min="1" max="5" step="1" value="{{ $rangeValues[4] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Proatividade <span class="percent">(<span id="percent2-4">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Precisão <span class="percent">(<span id="percent1-5">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-5" name="11" min="1" max="5" step="1" value="{{ $rangeValues[5] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Otimismo <span class="percent">(<span id="percent2-5">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Análise <span class="percent">(<span id="percent1-6">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-6" name="13" min="1" max="5" step="1" value="{{ $rangeValues[6] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Influência <span class="percent">(<span id="percent2-6">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Organização e planejamento <span class="percent">(<span id="percent1-7">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-7" name="15" min="1" max="5" step="1" value="{{ $rangeValues[7] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Criatividade <span class="percent">(<span id="percent2-7">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Disciplina <span class="percent">(<span id="percent1-8">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-8" name="17" min="1" max="5" step="1" value="{{ $rangeValues[8] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Adaptabilidade <span class="percent">(<span id="percent2-8">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <div class="row align-items-center">
            <div class="col-3 text-end">Concentração <span class="percent">(<span id="percent1-9">50</span>%)</span></div>
            <div class="col-md-6">
              <div class="container">
                <input type="range" class="form-range" id="range1-9" name="19" min="1" max="5" step="1" value="{{ $rangeValues[9] ?? 3 }}">
                <div class="d-flex justify-content-between pinrow"><span>|</span><span>|</span><span>|</span><span>|</span><span>|</span></div>
              </div>
            </div>
            <div class="col-3 text-start">Sociabilidade <span class="percent">(<span id="percent2-9">50</span>%)</span></div>
          </div>
        </div>
      </div>

      <h1 class="text-center">Adjetivos</h1>
      <p class="text-center">Selecione os adjetivos desejados.</p>

      <div class="row d-flex justify-content-center">
        @foreach ($adjectives as $adjective)
          @php $isChecked = in_array((string) $adjective['id'], $selectedAdjectives, true); @endphp
          <div class="col-md-auto btn_adjective d-flex align-items-center justify-content-center {{ $isChecked ? 'btn_adjective_active' : '' }}"
               id="adjective_{{ $adjective['id'] }}"
               onclick="toggleAdjective('adjective_{{ $adjective['id'] }}', event)">
            <input type="checkbox" class="adjective_select" name="adjectives[]" value="{{ $adjective['id'] }}" id="{{ $adjective['id'] }}" {{ $isChecked ? 'checked' : '' }}>
            <label class="adjective_name ml-2">{{ $adjective['description'] }}</label>
          </div>
        @endforeach
      </div>

      <h1 class="text-center">Resultado</h1>
      <p class="text-center">Pré-visualização do resultado.</p>

      <div class="d-flex justify-content-center">
        <button type="button" id="btn-atualizar" class="btn btn-primary">Recalcular Perfil</button>
      </div>

      <div style="display:none" id="payload-container"></div>
      <div id="result-container" class="mt-3"></div>

      <div class="row"><div id="atributes" class="apex-charts"></div></div>
      <div class="row"><div id="skills" class="apex-charts"></div></div>

      <input hidden name="response" id="response" value='@if($savedResponseJson){{ $savedResponseJson }}@endif'>
    </div>
  </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  let radarChart;
  const fixedCategories = [
    "Foco em resultado","Estrategista","Automotivação","Intraempreendedorismo","Proatividade",
    "Otimismo","Influência","Criatividade","Adaptabilidade","Sociabilidade","Diplomacia","Empatia",
    "Harmonia","Colaboração","Autocontrole","Disciplina","Concentração","Organização e planejamento",
    "Precisão","Análise"
  ];
  function renderRadarChart(categories, values){
    const orderedValues = fixedCategories.map(cat => {
      const i = categories.indexOf(cat);
      return i !== -1 ? values[i] : 0;
    });
    const options = {
      series: [{ name: '', data: orderedValues }],
      chart: { height: 560, type: 'radar' },
      dataLabels: { enabled: false },
      stroke: { width: 2 },
      markers: { size: 0 },
      fill: { opacity: 0.25 },
      yaxis: { max: 100 },
      xaxis: { categories: fixedCategories }
    };
    if (radarChart) radarChart.updateOptions(options);
    else { radarChart = new ApexCharts(document.querySelector("#skills"), options); radarChart.render(); }
  }

  let barChart;
  function renderBarChart(data){
    const options = {
      series: [{ data }],
      chart: { type: 'bar', height: 220 },
      plotOptions: { bar: { borderRadius: 6, horizontal: true } },
      legend: { show: false },
      dataLabels: { enabled: true, formatter: v => v + '%' },
      xaxis: { min: 0, max: 100, categories: ['Decisão','Detalhismo','Entusiasmo','Relacional'], labels: { formatter: () => '' } }
    };
    if (barChart) barChart.updateOptions(options);
    else { barChart = new ApexCharts(document.querySelector("#atributes"), options); barChart.render(); }
  }

  function toggleAdjective(elementId, event){
    const el = document.getElementById(elementId);
    el.classList.toggle('btn_adjective_active');
    const cb = el.querySelector('.adjective_select');
    cb.checked = !cb.checked;
    event.stopPropagation();
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.form-range').forEach(function(slider){
      const index = slider.id.split('-')[1];
      const apply = v => {
        const val = parseInt(v, 10);
        const p1 = Math.round(((val - 1) / 4) * 100);
        const p2 = 100 - p1;
        const e1 = document.getElementById('percent1-' + index);
        const e2 = document.getElementById('percent2-' + index);
        if (e1) e1.textContent = p1;
        if (e2) e2.textContent = p2;
      };
      apply(slider.value);
      slider.addEventListener('input', function(){ apply(this.value); });
    });
  });

  function buildPayloadFromCurrentUI(){
    const adjectives = Array.from(document.querySelectorAll('.adjective_select:checked')).map(cb => parseInt(cb.value));
    const skills = Array.from(document.querySelectorAll('.form-range')).map(sl => ({ id: parseInt(sl.name), points: parseInt(sl.value) }));
    return { adjectives, skills };
  }

  document.getElementById('btn-atualizar').addEventListener('click', function(){
    const selected = document.querySelectorAll('.adjective_select:checked').length;
    if (selected < 7) {
      Swal.fire({ icon: 'warning', title: 'Selecione mais adjetivos', text: 'Por favor, selecione no mínimo 7 adjetivos para continuar.' });
      return;
    }
    const payload = buildPayloadFromCurrentUI();
    document.getElementById('payload-container').innerHTML = '<pre>' + JSON.stringify(payload, null, 2) + '</pre>';
    fetch('https://api1.inperson.com.br/profiles/roles', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
      const { decision, detail, enthusiasm, relational, skills } = data;
      renderBarChart([parseFloat(decision || 0), parseFloat(detail || 0), parseFloat(enthusiasm || 0), parseFloat(relational || 0)]);
      const radarCategories = (skills || []).map(s => s.name);
      const radarValues = (skills || []).map(s => parseFloat(s.value));
      renderRadarChart(radarCategories, radarValues);
      document.getElementById('response').value = JSON.stringify(data);
    })
    .catch(err => console.error('Erro na API:', err));
  });
</script>

@if($savedResponseJson)
  <script>
    (function(){
      try {
        const resp = JSON.parse(@json($savedResponseJson));
        const { decision, detail, enthusiasm, relational, skills } = resp || {};
        if (decision || detail || enthusiasm || relational || skills) {
          renderBarChart([parseFloat(decision||0), parseFloat(detail||0), parseFloat(enthusiasm||0), parseFloat(relational||0)]);
          const radarCategories = (skills||[]).map(s=>s.name);
          const radarValues = (skills||[]).map(s=>parseFloat(s.value));
          renderRadarChart(radarCategories, radarValues);
        }
      } catch (e) {}
    })();
  </script>
@endif

<script src="{{ asset('assets/functions/buscaCep.js') }}"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>
<script src="https://cdn.tiny.cloud/1/n7b8zpu0tl0lg9ka80vagfoo3vtu97zk3rwall7rpfhg95q7/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector:'textarea.tinymce',
    height:300,
    menubar:false,
    plugins:'lists link image table code help wordcount',
    toolbar:'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    language:'pt_BR'
  });
</script>
@endsection
