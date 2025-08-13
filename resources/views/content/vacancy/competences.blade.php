@php
    $configData = Helper::appClasses();

    $calc = $vacancy->calculation ?? $calculation;
    $skills = $calc?->skills ? json_decode($calc->skills, true) : [];

    $fixedCategories = [
        "Foco em resultado", "Estrategista", "Automotivação", "Intraempreendedorismo", "Proatividade",
        "Otimismo", "Influência", "Criatividade", "Adaptabilidade", "Sociabilidade", "Diplomacia", "Empatia",
        "Harmonia", "Colaboração", "Autocontrole", "Disciplina", "Concentração", "Organização e planejamento", "Precisão", "Análise"
    ];

    $skillValues = array_fill(0, count($fixedCategories), 0);
    foreach ($skills as $skill) {
        $index = array_search($skill['name'], $fixedCategories);
        if ($index !== false) {
            $skillValues[$index] = (float) $skill['value'];
        }
    }
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Roda de Competências')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
<style>
    .card-body .chart {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 300px;
    }
    #competencyChart {
        max-width: 800px;
        margin: 0 auto;
    }
</style>

<div class="card mb-4">
    <div class="card-body pb-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div>
                <h4 class="mb-1">{{ $vacancy['description'] }}</h4>
                <p class="mb-1 text-muted">Departamento {{ $vacancy['department'] }}</p>
                <small class="text-muted">Vaga aberta há {{ \Carbon\Carbon::parse($vacancy['created_at'])->diffInDays() }} dias</small>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2 mt-3 mt-md-0">
                <a href="#" onclick="copiarLinkVaga()" class="text-primary fw-semibold me-3">Copiar link da Vaga</a>
                <a href="{{ url('vacancy/' . $vacancy['reference']) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-people me-1"></i> Candidatos</a>
                <a href="{{ url('vacancy/edit/' . $vacancy['reference']) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil-square me-1"></i> Editar Vaga</a>
                <a href="{{ url('vacancy/competences/' . $vacancy['reference']) }}" class="btn btn-primary btn-sm"><i class="bi bi-clock-history me-1"></i> Roda de Competência do Cargo</a>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Roda de Competências</h5>
    </div>
    <div class="card-body chart">
        <div id="competencyChart"></div> {{-- Substitui o <canvas> por <div> para ApexCharts --}}
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const skillLabels = @json($fixedCategories);
    const skillValues = @json($skillValues);

    const options = {
        series: [{
            name: 'Perfil Comportamental',
            data: skillValues
        }],
        chart: {
            height: 600,
            type: 'radar',
            toolbar: { show: false }
        },
        xaxis: {
            categories: skillLabels,
            labels: {
                style: {
                    colors: '#333',
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            show: false,
            min: 0,
            max: 100
        },
        fill: {
            opacity: 0.5,
            colors: ['#4F46E5']
        },
        stroke: {
            show: false
        },
        markers: {
            size: 0
        },
        colors: ['#4F46E5'],
        legend: {
            show: false
        }
    };

    const chart = new ApexCharts(document.querySelector("#competencyChart"), options);
    chart.render();
});
</script>
@endsection
