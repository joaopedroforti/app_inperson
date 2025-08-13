@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('content')
<style>
  .avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px; /* Slightly larger avatar size */
    height: 48px;
  }
  .avatar i {
    font-size: 1.75rem; /* Larger icon size (increased from fs-3) */
  }
</style>


  {{-- ========== 1ª LINHA: Colaboradores + Cargos Ativos ========== --}}
  <div class="row g-4 mb-4">

    {{-- Card Colaboradores (9 colunas em LG) --}}
    <div class="col-lg-9 col-md-8 col-12">
      <div class="card h-100">
        <h5 class="card-header">Colaboradores</h5>
        <div class="card-body">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">

            {{-- Masculino --}}
            <div class="d-flex align-items-center">
              <div class="avatar bg-label-warning rounded-circle me-2">
                <i class="ti ti-user text-warning"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold">{{ $qtdmasculino }}</h5>
                <small class="text-muted">Masculino</small>
              </div>
            </div>

            {{-- Feminino --}}
            <div class="d-flex align-items-center">
              <div class="avatar bg-label-success rounded-circle me-2">
                <i class="ti ti-user text-success"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold">{{ $qtdfeminino }}</h5>
                <small class="text-muted">Feminino</small>
              </div>
            </div>

            {{-- Outros --}}
            <div class="d-flex align-items-center">
              <div class="avatar bg-label-info rounded-circle me-2">
                <i class="ti ti-user text-info"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold">{{ $qtdoutros }}</h5>
                <small class="text-muted">Outros</small>
              </div>
            </div>

            {{-- Total --}}
            <div class="d-flex align-items-center">
              <div class="avatar bg-label-dark rounded-circle me-2">
                <i class="ti ti-users text-dark"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold">{{ $qtdmasculino + $qtdfeminino + $qtdoutros }}</h5>
                <small class="text-muted">Total</small>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- Card Cargos Ativos (3 colunas em LG) --}}
    <div class="col-lg-3 col-md-4 col-12">
      <div class="card h-100 d-flex flex-column align-items-center justify-content-center p-4">
        <div class="avatar bg-label-success rounded-circle mb-3">
          <i class="ti ti-award text-success"></i>
        </div>
        <h3 class="fw-bold mb-0">{{ $activejobs }}</h3>
        <small class="text-muted">Cargos Ativos</small>
      </div>
    </div>
  </div>

  {{-- ========== 2ª LINHA: Quatro cards pequenos ========== --}}
  <div class="row g-4 mb-4">
    {{-- Vagas Abertas --}}
    <div class="col-lg-3 col-sm-6 col-12">
      <div class="card p-4 h-100 d-flex flex-column align-items-center justify-content-center">
        <div class="avatar bg-label-info rounded-circle mb-3">
          <i class="ti ti-eye text-info"></i>
        </div>
        <h3 class="fw-bold mb-0">{{ $activevacancies }}</h3>
        <small class="text-muted">Vagas Abertas</small>
      </div>
    </div>

    {{-- Candidatos --}}
    <div class="col-lg-3 col-sm-6 col-12">
      <div class="card p-4 h-100 d-flex flex-column align-items-center justify-content-center">
        <div class="avatar bg-label-warning rounded-circle mb-3">
          <i class="ti ti-message-2 text-warning"></i>
        </div>
        <h3 class="fw-bold mb-0">{{ $qtdcandidates }}</h3>
        <small class="text-muted">Candidatos</small>
      </div>
    </div>

    {{-- Entrevistas Agendadas (fixo 4) --}}
    <div class="col-lg-3 col-sm-6 col-12">
      <div class="card p-4 h-100 d-flex flex-column align-items-center justify-content-center">
        <div class="avatar bg-label-danger rounded-circle mb-3">
          <i class="ti ti-clock-2 text-danger"></i>
        </div>
        <h3 class="fw-bold mb-0">4</h3>
        <small class="text-muted">Entrevistas Agendadas</small>
      </div>
    </div>

    {{-- Banco de Talentos --}}
    <div class="col-lg-3 col-sm-6 col-12">
      <div class="card p-4 h-100 d-flex flex-column align-items-center justify-content-center">
        <div class="avatar bg-label-primary rounded-circle mb-3">
          <i class="ti ti-heart text-primary"></i>
        </div>
        <h3 class="fw-bold mb-0">{{ $qtdtalents }}</h3>
        <small class="text-muted">Banco de Talentos</small>
      </div>
    </div>
  </div>

  {{-- ========== Tabela: Últimas Vagas Abertas ========== --}}
  <div class="card mb-4">
    <h5 class="card-header">Últimas vagas abertas</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Status</th>
            <th class="text-center">Data Criação</th>
            <th>Nome da Vaga</th>
            <th class="text-center">Currículos</th>
            <th class="text-center">Encerra em</th>
            <th>Departamento</th>
            <th>Divulgação</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($vacancies as $v)
            <tr>
              <td>
                @if ($v['status'] == 1)
                  <span class="badge bg-label-success">Aberto</span>
                @else
                  <span class="badge bg-label-secondary">Fechado</span>
                @endif
              </td>
              <td class="text-center">{{ \Carbon\Carbon::parse($v['creation_date'])->format('d/m/Y') }}</td>
              <td>{{ $v['description'] }}</td>
              <td class="text-center">{{ $v['recruitment_count'] }}</td>
              <td class="text-center">{{ \Carbon\Carbon::parse($v['expiration_date'])->format('d/m/Y') }}</td>
              <td>{{ $v['department'] ?? '—' }}</td>
              <td>
                <i class="ti ti-lock"></i>
                {{ $v['confidential'] ? 'Confidencial' : 'Pública' }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- ========== Tabela: Últimos Candidatos ========== --}}
  <div class="card mb-4">
    <h5 class="card-header">Últimos Candidatos</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Candidato</th>
            <th class="text-center">Adicionado em</th>
            <th class="text-center">Perguntas</th>
            <th>Perfil Comportamental</th>
            <th>Etapa</th>
            <th>Classificação</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($candidates as $c)
            @php
              /* Simulações para perguntas respondidas e estrelas
                 → substitua por colunas reais se existirem */
              $answered = rand(0, 5);
              $stars    = rand(1, 5);
            @endphp
            <tr>
              <td>{{ $c['name'] }}</td>
              <td class="text-center">{{ \Carbon\Carbon::parse($c['recruitment_date'])->format('d/m/Y') }}</td>
              <td class="text-center">{{ $answered }}/5</td>
              <td>{{ $c['result_name'] ?? '—' }}</td>
              <td>{{ $c['step'] ?? '—' }}</td>
              <td>
                @for ($i = 1; $i <= 5; $i++)
                  @if ($i <= $stars)
                    <i class="ti ti-star-filled text-warning"></i>
                  @else
                    <i class="ti ti-star text-muted"></i>
                  @endif
                @endfor
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

 {{-- container --}}
@endsection