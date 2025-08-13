@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Perfil Comportamental')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>







<!--Formulário de cadastro -->
<form>

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
   src="{{ $person->profile_pic_base64 }}"
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

</div>

      <!-- Coluna do Título e Abas alinhadas ao fim -->
      <div class="col-md-6 d-flex flex-column justify-content-between" style="min-height: 100px;">
        <h4 class="mb-2">{{ $person->full_name ?? '' }}</h4>
        <!-- Menu de Abas -->
        <!-- Menu de Abas -->
        <div class="d-flex align-items-center gap-3 mt-auto flex-wrap">
          
            <i class="mdi mdi-file-document-outline"></i>
            <a href="{{ route('Perfil Candidato', Crypt::encryptString($person['id_person'])) }}"><span><strong>Cadastro</strong></span></a>
         
         
          <div class="text-muted d-flex align-items-center gap-1 cursor-pointer">
          <button class="btn btn-primary d-flex align-items-center gap-1 px-3 py-2">
            <i class="mdi mdi-file-document-outline"></i>
            <span><strong>Perfil Comportamental</strong></span>
          </button>


          <i class="mdi mdi-file-document-outline"></i>
          <a href="{{ route('Historico do Candidato', Crypt::encryptString($person['id_person'])) }}"><span><strong>Recrutamentos</strong></span></a>
         
          
          </div>
         
        </div>
      </div>

      <!-- Coluna dos Botões -->
      <div class="col-md-3 d-flex justify-content-end">

      </div>

    </div>
  </div>
</div>
<!-- /Header -->
@if (!empty($lastCalculation->result_name))

<div class="row">



  <div class="col-md-6">
    <div class="card mb-4">
      <h5 class="card-header">Perfil Comportamental: {{$lastCalculation->result_name}} </h5>
      <div class="card-body">
      


        @php
    $attributes = json_decode($lastCalculation->attributes, true);
@endphp

<style>
    .bar-container {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        gap: 10px;
    }

    .label {
        width: 100px;
        text-align: right;
        font-weight: bold;
    }

    .bar-wrapper {
        flex-grow: 1;
        background-color: #e0e0e0;
        height: 16px;
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        border-radius: 10px;
    }

    .percentage {
        width: 60px;
        text-align: right;
        font-weight: bold;
    }
</style>

<div style="padding: 15px;">

    {{-- Decisão --}}
    <div class="bar-container">
        <div class="label">Decisão</div>
        <div class="bar-wrapper">
            <div class="bar-fill" style="width: {{ $attributes['decision'] }}%; background-color: #9400D3;"></div>
        </div>
        <div class="percentage">{{ number_format($attributes['decision'], 2) }}%</div>
    </div>

    {{-- Detalhismo --}}
    <div class="bar-container">
        <div class="label">Detalhismo</div>
        <div class="bar-wrapper">
            <div class="bar-fill" style="width: {{ $attributes['detail'] }}%; background-color: #008B8B;"></div>
        </div>
        <div class="percentage">{{ number_format($attributes['detail'], 2) }}%</div>
    </div>

    {{-- Entusiasmo --}}
    <div class="bar-container">
        <div class="label">Entusiasmo</div>
        <div class="bar-wrapper">
            <div class="bar-fill" style="width: {{ $attributes['enthusiasm'] }}%; background-color: #FF69B4;"></div>
        </div>
        <div class="percentage">{{ number_format($attributes['enthusiasm'], 2) }}%</div>
    </div>

    {{-- Relacional --}}
    <div class="bar-container">
        <div class="label">Relacional</div>
        <div class="bar-wrapper">
            <div class="bar-fill" style="width: {{ $attributes['relational'] }}%; background-color: #0077B6;"></div>
        </div>
        <div class="percentage">{{ number_format($attributes['relational'], 2) }}%</div>
    </div>

</div>








      </div>
    </div>
  </div>

  









  <div class="col-md-6">
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="fw-bold mb-0">Relatórios:</h6>
      <a href="#" class="text-decoration-none" style="font-size: 13px;">Copiar Link de Mapeamento</a>
    </div>
    <div class="card-body px-0 pt-2 pb-0">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr class="text-muted" style="font-size: 13px;">
              <th class="ps-4">DATA</th>
              <th>PERFIL</th>
              <th>RELATÓRIO</th>
              <th class="pe-4 text-end"> </th>
            </tr>
          </thead>
          <tbody>
            @foreach ($relatorios as $relatorio)
              <tr>
                <td class="ps-4 align-middle">{{ $relatorio->calculed_at }}</td>
                <td class="align-middle">{{ $relatorio->result_name }}</td>
                <td class="align-middle">{{ $relatorio->tipo == 'completo' ? 'Completo' : 'Simplificado' }}</td>
                <td class="pe-4 text-end align-middle">
                  <a href="{{ asset('assets/profiles/' . $relatorio->arquivo) }}" target="_blank">
                    <button class="btn btn-primary btn-sm rounded-pill px-3">Baixar</button>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Paginação funcional -->
      <div class="d-flex justify-content-center my-3">
        {{ $relatorios->links('pagination::bootstrap-4') }}
      </div>
    </div>
  </div>
</div>








  

<div class="row">



  <div class="col-md-12">
    <div class="card mb-4">
      <h5 class="card-header">Roda de Competencias </h5>
      <div class="card-body">
     




      @php
    // Garante que está decodificado
    $skills = is_string($lastCalculation['skills'])
        ? json_decode($lastCalculation['skills'], true)
        : $lastCalculation['skills'];

    // Ordem conforme a imagem
    $orderedLabels = [
        "Foco em resultado", "Estrategista", "Automotivação", "Intraempreendedorismo", "Proatividade",
        "Otimismo", "Influência", "Criatividade", "Adaptabilidade", "Sociabilidade",
        "Diplomacia", "Empatia", "Harmonia", "Colaboração", "Autocontrole",
        "Disciplina", "Concentração", "Organização e planejamento", "Precisão", "Análise"
    ];

    // Indexa os valores por nome
    $skillsMap = collect($skills)->pluck('value', 'name');

    // Reordena e converte os valores em float
    $labels = $orderedLabels;
    $values = array_map(function($label) use ($skillsMap) {
        return isset($skillsMap[$label]) ? floatval($skillsMap[$label]) : 0;
    }, $orderedLabels);
@endphp

<!-- Contêiner do gráfico -->
<div id="radarSkillsChart" style="max-width: 700px; margin: auto;"></div>

<!-- Script do ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const options = {
            chart: {
                height: 700,
                type: 'radar',
                toolbar: { show: false }
            },
            series: [{
                name: 'Habilidades',
                data: @json($values)
            }],
            xaxis: {
                categories: @json($labels),
                labels: {
                    style: {
                        fontSize: '16px' // Aumente ou ajuste conforme necessário
                    }
                }
            },
            yaxis: {
                min: 0,
                max: 100,
                tickAmount: 5,
                labels: {
                    formatter: val => val
                }
            },
            dataLabels: {
                enabled: false,
                formatter: val => val.toFixed(0) + '%'
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['#0d6efd']
            },
            fill: {
                opacity: 0.2,
                colors: ['#0d6efd']
            },
            markers: {
                size: 4,
                colors: ['#0d6efd'],
                strokeColors: '#fff',
                strokeWidth: 2
            }
        };

        new ApexCharts(document.querySelector("#radarSkillsChart"), options).render();
    });
</script>



<!--<button type="button" class="btn btn-primary" onclick="window.print()">
  Imprimir
</button> -->





  </div>
  </div>
  </div>
  </div>



@endif
<script src="{{ asset('assets/functions/buscaCep.js') }}"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>



@endsection
