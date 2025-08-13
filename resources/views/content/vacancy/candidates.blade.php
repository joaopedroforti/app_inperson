@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Candidatos')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/candidates.css') }}">
@endsection

@section('vendor-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@endsection

@section('content')
<div class="modal fade" id="advancedSearchModal" tabindex="-1" aria-labelledby="advancedSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary" id="modalPesquisaAvancadaLabel">
                    <i class="bi bi-search"></i> Pesquisa Avançada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="advancedSearchForm" method="POST" action="/pesquisa/candidatos" class="flex-grow-1 me-2">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="reference" value="{{ $vacancy->reference ?? '' }}">
                    <div class="mb-3">
                        <label for="criteriosPesquisa" class="form-label visually-hidden">Digite os critérios para pesquisa</label>
                        <textarea class="form-control" name="search" id="criteriosPesquisa" rows="3" placeholder="Digite aqui os critérios para pesquisa" value="{{ $input ?? '' }}">{{ $input ?? '' }}</textarea>
                    </div>
                    <div class="mb-2">
                        <h6 class="fw-bold text-primary">Como pesquisar?</h6>
                        <p class="text-muted small mb-1">
                            Use a pesquisa avançada para encontrar candidatos com características específicas. Você pode incluir informações como localização, habilidades técnicas ou experiências anteriores.
                        </p>
                        <p class="text-muted small">
                            <strong>Exemplo:</strong> mora até 10 km, conhece Excel, já trabalhou na área financeira.
                        </p>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle text-primary mt-1"></i>
                            <div>
                                <h6 class="fw-semibold text-primary mb-1">Dica importante:</h6>
                                <p class="text-muted small mb-0">
                                    Inclua apenas os critérios mais essenciais. Pesquisas muito detalhadas ou com muitos filtros podem não trazer resultados.
                                    <br>Pense nos pontos realmente indispensáveis para a vaga.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2 mt-4">
                    <button type="button" id="btnPesquisar" class="btn btn-success px-4">Pesquisar</button>
                    <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('advancedSearchForm');
        const searchButton = document.getElementById('btnPesquisar');
        searchButton.addEventListener('click', function(event) {
            form.submit();
        });
    });
</script>

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
                <a href="{{ url('vacancy/' . $vacancy['reference']) }}" class="btn btn-primary btn-sm" id="candidatosBtn">
                    <i class="bi bi-people me-1"></i> Candidatos
                </a>
                <a href="{{ url('vacancy/edit/' . $vacancy['reference']) }}" class="btn btn-outline-secondary btn-sm" id="editarVagaBtn">
                    <i class="bi bi-pencil-square me-1"></i> Editar Vaga
                </a>
                <a href="{{ url('vacancy/competences/' . $vacancy['reference']) }}" class="btn btn-outline-secondary btn-sm" id="rodaCompetenciaBtn">
                    <i class="bi bi-clock-history me-1"></i> Roda de Competência do Cargo
                </a>
            </div>
        </div>
    </div>
</div>
@if(empty($candidates) || count($candidates) === 0)
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-center align-items-center p-4" style="min-height: 150px;">
            <h5 class="mb-0 text-muted text-center">Nenhum Candidato Encontrado</h5>
        </div>
    </div>
@else
<div id="abas-content">
    <div id="candidatos-content">
        <div class="card mb-4">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <form method="get" action="" class="flex-grow-1 me-2">
                        @csrf
                        <input type="hidden" name="id_vacancy" value="1">
                        <div class="input-group">
                            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Buscar candidatos..." aria-label="Buscar candidatos">
                            <button class="btn btn-primary">
                                <i class="ti ti-search"></i> Pesquisar
                            </button>
                        </div>
                    </form>
                    <button class="btn btn-primary d-flex align-items-center gap-1 waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#advancedSearchModal">
                        Pesquisa Avançada <i class="ti ti-filter"></i>
                    </button>
                    <div class="btn-group" role="group" aria-label="Visualização">
                        <input type="radio" class="btn-check" name="viewToggle" id="kanbanView" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="kanbanView">Kanban</label>
                        <input type="radio" class="btn-check" name="viewToggle" id="listView" autocomplete="off">
                        <label class="btn btn-outline-primary" for="listView">Lista</label>
                    </div>
                </div>
            </div>
        </div>
        <div id="kanbanContainer" class="mb-4">
            <div class="row g-4">
                @foreach(['Candidato', 'Análise Inicial', 'Teste de Perfil', 'Entrevista', 'Aprovado'] as $step)
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $step }} <span class="badge bg-primary rounded-pill ms-1" id="{{ Str::slug($step, '-') }}-count">0</span></h5>
                            </div>
                            <div class="card-body p-2 kanban-column" data-step="{{ $step }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div id="listContainer" class="d-none">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Candidatos</th>
                                    <th>Adicionado em</th>
                                    <th>Perguntas</th>
                                    <th>Perfil Comportamental</th>
                                    <th>Etapa</th>
                                    <th>Classificação</th>
                                </tr>
                            </thead>
                            <tbody id="candidateTableBody">
                                @foreach($candidates as $candidate)
                                    <tr class="candidate-row" data-cpf="{{ $candidate['person']['cpf'] }}" data-index="{{ $loop->index }}">
                                        <td>
                                            @if(isset($candidate['person']['step']) && $candidate['person']['step'] == 'Entrevista')
                                                <i class="bi bi-circle-fill text-success me-1" style="font-size: 10px;"></i>
                                            @else
                                                <i class="bi bi-circle-fill text-secondary me-1" style="font-size: 10px;"></i>
                                            @endif
                                            <a href="#" class="candidate-link view-candidate-btn" data-index="{{ $loop->index }}">{{ $candidate['person']['full_name'] }}</a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($candidate['recruitment']['creation_date'])->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $questions = json_decode($candidate['recruitment']['questions'] ?? '[]', true);
                                                $total = count($questions);
                                                $answered = $total;
                                            @endphp
                                            {{ $answered }}/{{ $total }}
                                        </td>
                                        <td>{{ $candidate['calculation_result']['result_name'] ?? 'N/A' }}</td>
                                        <td>{{ $candidate['person']['step'] ?? 'Candidato' }}</td>
                                        <td>
                                            <div class="text-warning">
                                                @php
                                                    $stars = $candidate['person']['stars'] ?? 0;
                                                @endphp
                                                <i class="bi bi-star{{ $stars >= 1 ? '-fill' : '' }} star" data-star="1" data-cpf="{{ $candidate['person']['cpf'] }}" data-index="{{ $loop->index }}"></i>
                                                <i class="bi bi-star{{ $stars >= 2 ? '-fill' : '' }} star" data-star="2" data-cpf="{{ $candidate['person']['cpf'] }}" data-index="{{ $loop->index }}"></i>
                                                <i class="bi bi-star{{ $stars >= 3 ? '-fill' : '' }} star" data-star="3" data-cpf="{{ $candidate['person']['cpf'] }}" data-index="{{ $loop->index }}"></i>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                        <div class="text-muted small" id="paginationInfo">
                            Mostrando 1-{{ count($candidates) }} de {{ $recruitment_count }}
                        </div>
                        <nav aria-label="Navegação de página">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Anterior">&laquo;</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                @if($recruitment_count > 15)
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                @endif
                                @if($recruitment_count > 30)
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Próximo">&raquo;</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="candidateModal" tabindex="-1" aria-labelledby="candidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="background-color: #f8f8f8;">
            <div class="modal-header pt-3">
                <div>
                    <h5 class="modal-title" id="candidateModalLabel">
                        <span id="candidateName"></span>
                        <span class="stars">
                            <i class="bi bi-star{{ $stars >= 1 ? '-fill' : '' }} star" data-star="1" data-cpf="{{ $candidate['person']['cpf'] }}"></i>
                            <i class="bi bi-star{{ $stars >= 2 ? '-fill' : '' }} star" data-star="2" data-cpf="{{ $candidate['person']['cpf'] }}" ></i>
                            <i class="bi bi-star{{ $stars >= 3 ? '-fill' : '' }} star" data-star="3" data-cpf="{{ $candidate['person']['cpf'] }}" ></i>
                        </span>
                    </h5>
                    <div class="text-muted small">
                        Vaga: <span id="candidateJob"></span>
                        <br>Cadastrou em: <span id="candidateDate"></span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="prevCandidateBtn">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span id="candidateCounter" class="small text-muted">1 de {{ count($candidates) }}</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="nextCandidateBtn">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="px-4 pt-3 pb-0">
                    <ul class="nav nav-tabs" id="candidateTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados-tab-pane" type="button" role="tab" aria-controls="dados-tab-pane" aria-selected="true">
                                <i class="bi bi-person me-1"></i> Dados do Candidato
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="curriculo-tab" data-bs-toggle="tab" data-bs-target="#curriculo-tab-pane" type="button" role="tab" aria-controls="curriculo-tab-pane" aria-selected="false">
                                <i class="bi bi-file-text me-1"></i> Currículo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="perguntas-tab" data-bs-toggle="tab" data-bs-target="#perguntas-tab-pane" type="button" role="tab" aria-controls="perguntas-tab-pane" aria-selected="false">
                                <i class="bi bi-question-circle me-1"></i> Perguntas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="teste-tab" data-bs-toggle="tab" data-bs-target="#teste-tab-pane" type="button" role="tab" aria-controls="teste-tab-pane" aria-selected="false">
                                <i class="bi bi-graph-up me-1"></i> Teste de Perfil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="anotacoes-tab" data-bs-toggle="tab" data-bs-target="#anotacoes-tab-pane" type="button" role="tab" aria-controls="anotacoes-tab-pane" aria-selected="false">
                                <i class="bi bi-journal-text me-1"></i> Anotações
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="px-4 py-3">
                    <div class="progress-steps px-4 py-3 rounded shadow-sm bg-white position-relative" id="modalSteps">
                        <div class="position-absolute top-50 start-0 end-0 translate-middle-y" style="height: 2px;background-color: #eeeded;z-index: 0;"></div>
                        <div class="d-flex justify-content-between align-items-center w-100">
                            @foreach(['Candidato', 'Análise Inicial', 'Teste de Perfil', 'Entrevista', 'Aprovado'] as $step)
                                <div class="step-item text-center flex-fill position-relative" data-step="{{ $step }}" style="z-index: 1;">
                                    <div class="step-circle mx-auto mb-1">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="step-text small text-muted">{{ $step }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="tab-content px-4 py-3" id="candidateTabsContent">
                    <div class="tab-pane fade show active" id="dados-tab-pane" role="tabpanel" aria-labelledby="dados-tab" tabindex="0">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">E-mail</label>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-envelope me-2"></i>
                                            <a href="#" id="candidateEmail"></a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">WhatsApp</label>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-whatsapp me-2 text-success"></i>
                                            <a href="#" id="candidateWhatsapp" target="_blank" class="text-decoration-none text-dark"></a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">LinkedIn</label>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-linkedin me-2 text-primary"></i>
                                            <a href="#" id="candidateLinkedin" target="_blank"></a>
                                            <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="form-section">CONTATO</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nome</label>
                                        <input type="text" class="form-control" id="contactName" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="contactCPF" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Data de Nascimento</label>
                                        <input type="text" class="form-control" id="contactBirthdate" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">E-mail</label>
                                        <input type="email" class="form-control" id="contactEmail" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Telefone</label>
                                        <input type="text" class="form-control" id="contactPhone" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h6 class="form-section">ENDEREÇO</h6>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">CEP</label>
                                        <input type="text" class="form-control" id="addressZip" readonly>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Estado</label>
                                        <input type="text" class="form-control" id="addressState" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="addressCity" readonly>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Bairro</label>
                                        <input type="text" class="form-control" id="addressNeighborhood" readonly>
                                    </div>
                                    <div class="col-md-7 mb-3">
                                        <label class="form-label">Logradouro</label>
                                        <input type="text" class="form-control" id="addressStreet" readonly>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Núm</label>
                                        <input type="text" class="form-control" id="addressNumber" readonly>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Complemento</label>
                                        <input type="text" class="form-control" id="addressComplement" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="curriculo-tab-pane" role="tabpanel" aria-labelledby="curriculo-tab" tabindex="0">
                        <div id="curriculoContent"></div>
                    </div>
                    <div class="tab-pane fade" id="perguntas-tab-pane" role="tabpanel" aria-labelledby="perguntas-tab" tabindex="0">
                        <div id="perguntasContent" class="card">
                            <div class="card-body">
                                <p class="text-muted mb-4">Respostas do candidato às perguntas da vaga.</p>
                                <div id="listaPerguntas"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="teste-tab-pane" role="tabpanel" aria-labelledby="teste-tab" tabindex="0">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Perfil Comportamental: <span id="perfilComportamental" class="fw-normal"></span></h6>
                        </div>
                        <div class="mb-4" style="padding: 15px;">
                            <div class="bar-container">
                                <div class="label">Decisão</div>
                                <div class="bar-wrapper">
                                    <div class="bar-fill" id="decisaoBar"></div>
                                </div>
                                <div class="percentage" id="decisaoPercentage"></div>
                            </div>
                            <div class="bar-container">
                                <div class="label">Detalhismo</div>
                                <div class="bar-wrapper">
                                    <div class="bar-fill" id="detalhismoBar"></div>
                                </div>
                                <div class="percentage" id="detalhismoPercentage"></div>
                            </div>
                            <div class="bar-container">
                                <div class="label">Entusiasmo</div>
                                <div class="bar-wrapper">
                                    <div class="bar-fill" id="entusiasmoBar"></div>
                                </div>
                                <div class="percentage" id="entusiasmoPercentage"></div>
                            </div>
                            <div class="bar-container">
                                <div class="label">Relacional</div>
                                <div class="bar-wrapper">
                                    <div class="bar-fill" id="relationalBar"></div>
                                </div>
                                <div class="percentage" id="relationalPercentage"></div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0">Roda de Competências:</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="matchWithJob" checked>
                                        <label class="form-check-label" for="matchWithJob">Matcher com Cargo</label>
                                    </div>
                                </div>
                                <div id="competenciasChart" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="anotacoes-tab-pane" role="tabpanel" aria-labelledby="anotacoes-tab" tabindex="0">
                        <div class="card-body">
                            <div class="mb-4">
                                <h5 class="mb-3">Adicionar Anotação</h5>
                                <div class="note-editor border rounded">
                                    <div class="note-toolbar border-bottom p-2">
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="bold"><i class="bi bi-type-bold"></i></button>
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="italic"><i class="bi bi-type-italic"></i></button>
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="underline"><i class="bi bi-type-underline"></i></button>
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="strikeThrough"><i class="bi bi-type-strikethrough"></i></button>
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="insertUnorderedList"><i class="bi bi-list-ul"></i></button>
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="insertOrderedList"><i class="bi bi-list-ol"></i></button>
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="justifyLeft"><i class="bi bi-text-left"></i></button>
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="justifyCenter"><i class="bi bi-text-center"></i></button>
                                        <button type="button" class="btn btn-sm btn-light note-btn" data-command="justifyRight"><i class="bi bi-text-right"></i></button>
                                    </div>
                                    <div class="note-editable p-2" contenteditable="true" id="newNoteContent"></div>
                                </div>
                                <div class="text-end mt-2">
                                    <button class="btn btn-success" id="addNoteBtn">Inserir</button>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="section-title mb-0">RH</h6>
                                </div>
                                <div id="rhOccurrences"></div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="section-title mb-0">Gestor</h6>
                                </div>
                                <div id="gestorOccurrences"></div>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-outline-secondary" id="viewAllHistoryBtn">Ver Histórico Completo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        <button type="button" class="btn btn-outline-danger" id="reprovarBtn">
                            <i class="bi bi-x-circle me-1"></i>Reprovar
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-right me-1"></i>Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Histórico Completo de Anotações</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!$('meta[name="csrf-token"]').length) {
                console.error('CSRF token meta tag is missing!');
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'CSRF token não encontrado. Por favor, verifique a configuração do layout.',
                });
            }

            const candidatesData = @json($candidates);
            const vacancy = @json($vacancy);
            const vacancyCalculation = @json($vacancycalculation);
            let currentCandidateIndex = 0;
            let filteredCandidates = [...candidatesData];

            function copiarLinkVaga() {
                const link = '{{ url("/vaga/" . $vacancy["id_vacancy"]) }}';
                navigator.clipboard.writeText(link).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Link copiado!',
                        text: 'O link da vaga foi copiado para a área de transferência.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }).catch(err => {
                    console.error('Erro ao copiar link:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Não foi possível copiar o link.',
                    });
                });
            }

            function formatCurriculum(curriculumJson) {
    try {
        const curriculum = JSON.parse(curriculumJson || '{}');
        let html = '<div class="curriculum-content">';

        // Percorre todas as seções do currículo
        for (const sectionName in curriculum) {
            const sectionData = curriculum[sectionName];

            // Ignora valores nulos, vazios ou objetos sem conteúdo
            if (
                sectionData === null ||
                sectionData === '' ||
                (typeof sectionData === 'object' && Object.keys(sectionData).length === 0)
            ) {
                continue;
            }

            html += `<div class="mb-4"><h5 class="fw-bold">${sectionName}</h5>`;

            // Função recursiva para renderizar qualquer tipo de dado
            const renderData = (data, indent = 0) => {
                const padding = indent > 0 ? `ms-${indent}` : '';

                if (Array.isArray(data)) {
                    html += `<ul class="${padding}">`;
                    data.forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += `</ul>`;
                } else if (typeof data === 'object' && data !== null) {
                    for (const key in data) {
                        if (Array.isArray(data[key])) {
                            html += `<p class="${padding}"><strong>${key}:</strong></p>`;
                            renderData(data[key], indent + 3);
                        } else if (typeof data[key] === 'object' && data[key] !== null) {
                            html += `<p class="${padding}"><strong>${key}:</strong></p>`;
                            renderData(data[key], indent + 3);
                        } else {
                            html += `<p class="${padding}"><strong>${key}:</strong> ${data[key]}</p>`;
                        }
                    }
                } else {
                    html += `<p class="${padding}">${data}</p>`;
                }
            };

            // Renderiza os dados da seção
            renderData(sectionData);

            html += '</div>';
        }

        html += '</div>';
        return html;
    } catch (e) {
        console.error('Erro ao formatar currículo:', e);
        return '<div class="alert alert-warning">Não foi possível carregar o currículo corretamente.</div>';
    }
}


            function formatQuestions(questionsJson) {
                try {
                    const questions = JSON.parse(questionsJson || '[]');
                    let html = '<div class="questions-list">';
                    questions.forEach((item, index) => {
                        html += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Pergunta ${index + 1}:</h6>
                                <p class="card-text fw-bold">${item.question || 'N/A'}</p>
                                <div class="mt-2">
                                    <h6 class="text-muted">Resposta:</h6>
                                    <p>${item.response || 'N/A'}</p>
                                </div>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                    return html;
                } catch (e) {
                    console.error('Erro ao formatar perguntas:', e);
                    return '<div class="alert alert-warning">Não foi possível carregar as perguntas corretamente.</div>';
                }
            }

            function displayOccurrences(occurrences, containerId, showLatestOnly = true) {
                const container = document.getElementById(containerId);
                if (!container) {
                    console.error(`Container ${containerId} não encontrado.`);
                    return;
                }
                let html = '';
                if (occurrences && occurrences.length > 0) {
                    const sortedOccurrences = occurrences.sort((a, b) => new Date(b.date) - new Date(a.date));
                    const occurrencesToDisplay = showLatestOnly ? [sortedOccurrences[0]] : sortedOccurrences;
                    occurrencesToDisplay.forEach(note => {
                        const noteDate = new Date(note.date);
                        const formattedDate = noteDate.toLocaleDateString('pt-BR');
                        html += `
                        <div class="annotation-item" data-id-occurrence="${note.id_occurrence || note.id_occourrence}">
                            <div class="annotation-header">
                                <span class="annotation-name">${note.writer || 'N/A'}</span>
                                <span class="annotation-date">${formattedDate}</span>
                            </div>
                            <div class="annotation-text-display">
                                <p class="annotation-text">${note.text || 'N/A'}</p>
                            </div>
                        </div>`;
                    });
                } else {
                    html = '<div class="alert alert-info">Nenhuma anotação disponível.</div>';
                }
                container.innerHTML = html;
            }

            function updateStarsDisplay(stars, modalStars, kanbanCard, tableRow) {
                const starIconsModal = modalStars ? modalStars.querySelectorAll('.star') : [];
                const starIconsKanban = kanbanCard ? kanbanCard.querySelectorAll('.kanban-stars i') : [];
                const starIconsTable = tableRow ? tableRow.querySelectorAll('.text-warning i') : [];
                
                starIconsModal.forEach((icon, index) => {
                    icon.className = `bi bi-star${index < stars ? '-fill' : ''} star`;
                    icon.dataset.star = index + 1;
                });
                
                starIconsKanban.forEach((icon, index) => {
                    icon.className = `ti ti-star${index < stars ? '-filled' : ''} star`;
                });
                
                starIconsTable.forEach((icon, index) => {
                    icon.className = `bi bi-star${index < stars ? '-fill' : ''} star`;
                });
            }

            function updateCandidateStars(candidateCpf, stars, callback) {
                if (!candidateCpf || stars === undefined) {
                    console.error('CPF ou estrelas inválidos:', candidateCpf, stars);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Dados inválidos para atualização das estrelas.',
                    });
                    callback(false);
                    return;
                }
                $.ajax({
                    url: `/updatestars/${candidateCpf}/${stars}`,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            console.log('Estrelas atualizadas:', response);
                            callback(true);
                        } else {
                            console.error('Erro na resposta do servidor:', response.message);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.message || 'Não foi possível atualizar a classificação.',
                            });
                            callback(false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao atualizar estrelas:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: xhr.responseJSON?.message || 'Erro ao atualizar a classificação do candidato.',
                        });
                        callback(false);
                    }
                });
            }

            function fillCandidateModal(candidateIndex, initialTab = 'dados-tab') {
                currentCandidateIndex = candidateIndex;
                const candidate = filteredCandidates[candidateIndex];
                if (!candidate || !candidate.person) {
                    console.error('Candidato não encontrado no índice:', candidateIndex);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Candidato não encontrado.',
                    });
                    return;
                }

                document.getElementById('candidateName').textContent = candidate.person.full_name || 'N/A';
                document.getElementById('candidateJob').textContent = vacancy.description || 'N/A';
                const creationDate = new Date(candidate.recruitment.creation_date);
                document.getElementById('candidateDate').textContent = creationDate.toLocaleDateString('pt-BR') || 'N/A';
                document.getElementById('candidateCounter').textContent = `${candidateIndex + 1} de ${filteredCandidates.length}`;
                document.getElementById('candidateEmail').textContent = candidate.person.personal_email || 'N/A';
                document.getElementById('candidateEmail').href = candidate.person.personal_email ? `mailto:${candidate.person.personal_email}` : '#';
                if (candidate.person.cellphone) {
                    const phone = candidate.person.cellphone.replace(/\D/g, '');
                    const link = document.getElementById('candidateWhatsapp');
                    link.href = `https://wa.me/55${phone}`;
                    link.textContent = candidate.person.cellphone;
                } else {
                    document.getElementById('candidateWhatsapp').textContent = 'N/A';
                    document.getElementById('candidateWhatsapp').href = '#';
                }
                document.getElementById('candidateLinkedin').textContent = candidate.person.linkedin || 'N/A';
                document.getElementById('candidateLinkedin').href = candidate.person.linkedin || '#';
                document.getElementById('contactName').value = candidate.person.full_name || '';
                document.getElementById('contactCPF').value = candidate.person.cpf || '';
                document.getElementById('contactBirthdate').value = candidate.person.birth_date ? new Date(candidate.person.birth_date).toLocaleDateString('pt-BR') : '';
                document.getElementById('contactEmail').value = candidate.person.personal_email || '';
                document.getElementById('contactPhone').value = candidate.person.cellphone || '';
                document.getElementById('addressZip').value = candidate.person.zip_code || '';
                document.getElementById('addressState').value = candidate.person.address_state || '';
                document.getElementById('addressCity').value = candidate.person.address_city || '';
                document.getElementById('addressNeighborhood').value = candidate.person.address_district || '';
                document.getElementById('addressStreet').value = candidate.person.address || '';
                document.getElementById('addressNumber').value = candidate.person.address_number || '';
                document.getElementById('addressComplement').value = candidate.person.address_complement || '';
                document.getElementById('curriculoContent').innerHTML = candidate.recruitment.curriculum ? formatCurriculum(candidate.recruitment.curriculum) : '<div class="alert alert-info">Currículo não disponível.</div>';
                document.getElementById('listaPerguntas').innerHTML = candidate.recruitment.questions ? formatQuestions(candidate.recruitment.questions) : '<div class="alert alert-info">Nenhuma pergunta respondida.</div>';
                if (candidate.calculation_result) {
                    document.getElementById('perfilComportamental').textContent = candidate.calculation_result.result_name || 'N/A';
                    try {
                        const attributes = JSON.parse(candidate.calculation_result.attributes || '{}');
                        document.getElementById('decisaoBar').style.width = `${attributes.decision || 0}%`;
                        document.getElementById('decisaoPercentage').textContent = `${parseFloat(attributes.decision || 0).toFixed(1)}%`;
                        document.getElementById('detalhismoBar').style.width = `${attributes.detail || 0}%`;
                        document.getElementById('detalhismoPercentage').textContent = `${parseFloat(attributes.detail || 0).toFixed(1)}%`;
                        document.getElementById('entusiasmoBar').style.width = `${attributes.enthusiasm || 0}%`;
                        document.getElementById('entusiasmoPercentage').textContent = `${parseFloat(attributes.enthusiasm || 0).toFixed(1)}%`;
                        document.getElementById('relationalBar').style.width = `${attributes.relational || 0}%`;
                        document.getElementById('relationalPercentage').textContent = `${parseFloat(attributes.relational || 0).toFixed(1)}%`;
                        if (candidate.calculation_result.skills) {
                            const skills = JSON.parse(candidate.calculation_result.skills);
                            setupCompetenciasChart(skills);
                        }
                    } catch (e) {
                        console.error('Erro ao processar atributos:', e);
                        document.getElementById('teste-tab-pane').innerHTML = '<div class="alert alert-info">Dados de perfil incompletos ou inválidos.</div>';
                    }
                } else {
                    document.getElementById('perfilComportamental').textContent = 'Não realizado';
                    document.getElementById('teste-tab-pane').innerHTML = '<div class="alert alert-info">Teste de perfil não realizado.</div>';
                }
                displayOccurrences(candidate.rh_occurrences || [], 'rhOccurrences', true);
                displayOccurrences(candidate.gestor_occurrences || [], 'gestorOccurrences', true);

                // Atualiza a exibição das estrelas e define data-index
                const modalStars = document.querySelector('#candidateModal .stars');
                const kanbanCard = document.querySelector(`.kanban-card[data-cpf="${candidate.person.cpf}"]`);
                const tableRow = document.querySelector(`.candidate-row[data-cpf="${candidate.person.cpf}"]`);
                updateStarsDisplay(candidate.person.stars || 0, modalStars, kanbanCard, tableRow);
                // Adiciona data-index aos elementos de estrela do modal
                const starIconsModal = modalStars.querySelectorAll('.star');
                starIconsModal.forEach(icon => {
                    icon.dataset.index = candidateIndex;
                });

                // Atualiza a classe 'active' para a etapa atual
                const currentStep = candidate.person.step || 'Candidato';
                document.querySelectorAll('#modalSteps .step-item').forEach(item => {
                    const circle = item.querySelector('.step-circle');
                    circle.classList.remove('active');
                    if (item.dataset.step === currentStep) {
                        circle.classList.add('active');
                    }
                });

                // Ativa a aba especificada
                const tabToActivate = document.getElementById(initialTab);
                if (tabToActivate) {
                    const tabPane = document.querySelector(tabToActivate.getAttribute('data-bs-target'));
                    if (tabPane) {
                        document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
                        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
                        tabToActivate.classList.add('active');
                        tabPane.classList.add('show', 'active');
                    }
                }
            }

            function setupCompetenciasChart(skills) {
                if (!skills || !Array.isArray(skills)) {
                    console.error('Dados de habilidades inválidos:', skills);
                    return;
                }
                const fixedCategories = [
                    "Foco em resultado",
                    "Estrategista",
                    "Automotivação",
                    "Intraempreendedorismo",
                    "Proatividade",
                    "Otimismo",
                    "Influência",
                    "Criatividade",
                    "Adaptabilidade",
                    "Sociabilidade",
                    "Diplomacia",
                    "Empatia",
                    "Harmonia",
                    "Colaboração",
                    "Autocontrole",
                    "Disciplina",
                    "Concentração",
                    "Organização e planejamento",
                    "Precisão",
                    "Análise"
                ];
                const candidateValues = fixedCategories.map(category => {
                    const skill = skills.find(s => s.name === category);
                    return skill ? parseFloat(skill.value) : 0;
                });
                const vacancySkills = vacancyCalculation && vacancyCalculation.skills ? JSON.parse(vacancyCalculation.skills) : [];
                const vacancyValues = fixedCategories.map(category => {
                    const skill = vacancySkills.find(s => s.name === category);
                    return skill ? parseFloat(skill.value) : 0;
                });
                const series = [{
                    name: 'Candidato',
                    data: candidateValues
                }];
                const matchWithJobCheckbox = document.getElementById('matchWithJob');
                if (matchWithJobCheckbox.checked) {
                    series.push({
                        name: 'Cargo',
                        data: vacancyValues
                    });
                }
                const options = {
                    series: series,
                    chart: {
                        height: 600,
                        type: 'radar'
                    },
                    xaxis: {
                        categories: fixedCategories
                    },
                    yaxis: {
                        show: false,
                        min: 0,
                        max: 100
                    },
                    fill: {
                        opacity: [0.5, 0.3]
                    },
                    stroke: {
                        show: false
                    },
                    markers: {
                        size: 0
                    },
                    colors: ['#4F46E5', '#F59E0B'],
                    legend: {
                        show: true
                    }
                };
                document.getElementById('competenciasChart').innerHTML = '';
                try {
                    const chart = new ApexCharts(document.getElementById('competenciasChart'), options);
                    chart.render();
                    matchWithJobCheckbox.addEventListener('change', function() {
                        const updatedSeries = [{
                            name: 'Candidato',
                            data: candidateValues
                        }];
                        if (this.checked) {
                            updatedSeries.push({
                                name: 'Cargo',
                                data: vacancyValues
                            });
                        }
                        chart.updateSeries(updatedSeries);
                    });
                } catch (e) {
                    console.error('Erro ao renderizar gráfico de competências:', e);
                }
            }

            function normalizeStep(step) {
                return step.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/\s+/g, '-');
            }

            function initKanban(candidates = candidatesData) {
                document.querySelectorAll('.kanban-column').forEach(column => {
                    column.innerHTML = '';
                });
                const counters = {
                    'Candidato': 0,
                    'Análise Inicial': 0,
                    'Teste de Perfil': 0,
                    'Entrevista': 0,
                    'Aprovado': 0
                };
                candidates.forEach((candidate, index) => {
                    const step = candidate.person.step || 'Candidato';
                    const column = document.querySelector(`.kanban-column[data-step="${step}"]`);
                    if (column) {
                        counters[step]++;
                        const card = document.createElement('div');
                        card.className = 'kanban-card';
                        card.dataset.index = index;
                        card.dataset.cpf = candidate.person.cpf;
                        const creationDate = new Date(candidate.recruitment.creation_date);
                        const daysSinceCreation = Math.floor((new Date() - creationDate) / (1000 * 60 * 60 * 24));
                        let starsHtml = '';
                        const stars = candidate.person.stars || 0;
                        for (let i = 0; i < 3; i++) {
                            starsHtml += `<i class="ti ti-star${i < stars ? '-filled' : ''} star" data-star="${i + 1}" data-cpf="${candidate.person.cpf}" data-index="${index}" style="color: #F59E0B;"></i>`;
                        }
card.innerHTML = `
  <div class="candidate-card">
    <div class="title-line">
      <div class="title-left">
        <h6 class="candidate-name">${candidate.person.full_name || 'N/A'}</h6>
        ${
          candidate.person.linkedin_url
            ? `<a href="${candidate.person.linkedin_url}" target="_blank" class="linkedin-badge" title="LinkedIn">in</a>`
            : ''
        }
      </div>
      <div class="candidate-date d-flex align-items-center">
        <i class="ti ti-clock me-1"></i><span>${daysSinceCreation} dias</span>
      </div>
    </div>

    <div class="bottom-line">
      <div class="kanban-stars d-flex align-items-center">
        ${starsHtml}
      </div>
      <div class="d-flex align-items-center">
        <a href="#" class="me-2 icon-btn icon-btn-primary view-candidate-btn" data-index="${index}" title="Possui Teste de Perfil ">
          <i class="ti ti-users"></i>
        </a>
        <a href="#" class="icon-btn icon-btn-muted curriculum-btn" data-index="${index}" title="Ver Currículo">
          <i class="ti ti-file-text"></i>
        </a>
      </div>
    </div>
  </div>
`;


                        column.appendChild(card);
                    }
                });
                for (const step in counters) {
                    const countElement = document.getElementById(`${normalizeStep(step)}-count`);
                    if (countElement) {
                        countElement.textContent = counters[step];
                    }
                }
                $('.kanban-column').sortable({
                    connectWith: '.kanban-column',
                    placeholder: 'card-placeholder',
                    cursor: 'grab',
                    revert: true,
                    start: function(event, ui) {
                        ui.item.addClass('dragging');
                        ui.item.data('originColumn', this);
                    },
                    stop: function(event, ui) {
                        ui.item.removeClass('dragging');
                    },
                    receive: function(event, ui) {
                        const newStep = $(this).data('step');
                        const candidateIndex = ui.item.data('index');
                        const candidateCpf = ui.item.data('cpf');
                        updateCandidateStep(candidateCpf, newStep, function(success) {
                            if (success) {
                                if (candidateIndex !== undefined && filteredCandidates[candidateIndex]) {
                                    filteredCandidates[candidateIndex].person.step = newStep;
                                }
                                updateKanbanCounters();
                                if (document.getElementById('candidateModal').classList.contains('show')) {
                                    fillCandidateModal(currentCandidateIndex);
                                }
                            } else {
                               
                                const originColumn = ui.item.data('originColumn');
                                if (originColumn) {
                                    $(originColumn).append(ui.item);
                                }
                                updateKanbanCounters();
                            }
                        });
                    }
                }).disableSelection();
            }

            function updateKanbanCounters() {
                const counters = {
                    'Candidato': 0,
                    'Análise Inicial': 0,
                    'Teste de Perfil': 0,
                    'Entrevista': 0,
                    'Aprovado': 0
                };
                document.querySelectorAll('.kanban-column').forEach(column => {
                    const step = column.dataset.step;
                    const count = column.querySelectorAll('.kanban-card').length;
                    counters[step] = count;
                });
                for (const step in counters) {
                    const countElement = document.getElementById(`${normalizeStep(step)}-count`);
                    if (countElement) {
                        countElement.textContent = counters[step];
                    }
                }
            }

            function filterCandidates(searchTerm) {
                searchTerm = searchTerm.toLowerCase().trim();
                filteredCandidates = candidatesData.filter(candidate =>
                    candidate.person.full_name.toLowerCase().includes(searchTerm) ||
                    (candidate.person.cpf && candidate.person.cpf.includes(searchTerm)) ||
                    (candidate.calculation_result?.result_name && candidate.calculation_result.result_name.toLowerCase().includes(searchTerm)) ||
                    (candidate.person.step && candidate.person.step.toLowerCase().includes(searchTerm))
                );
                initKanban(filteredCandidates);
                updateCandidateList(filteredCandidates);
                updatePaginationInfo();
            }

            function updateCandidateList(candidates) {
                const tbody = document.getElementById('candidateTableBody');
                tbody.innerHTML = '';
                candidates.forEach((candidate, index) => {
                    const creationDate = new Date(candidate.recruitment.creation_date).toLocaleDateString('pt-BR');
                    const questions = JSON.parse(candidate.recruitment.questions || '[]');
                    const totalQuestions = questions.length;
                    const answeredQuestions = totalQuestions;
                    const stars = candidate.person.stars || 0;
                    let starsHtml = '';
                    for (let i = 0; i < 3; i++) {
                        starsHtml += `<i class="bi bi-star${i < stars ? '-fill' : ''} star" data-star="${i + 1}" data-cpf="${candidate.person.cpf}" data-index="${index}"></i>`;
                    }
                    const row = `
                        <tr class="candidate-row" data-cpf="${candidate.person.cpf}" data-index="${index}">
                            <td>
                                ${candidate.person.step === 'Entrevista' ? 
                                    '<i class="bi bi-circle-fill text-success me-1" style="font-size: 10px;"></i>' : 
                                    '<i class="bi bi-circle-fill text-secondary me-1" style="font-size: 10px;"></i>'}
                                <a href="#" class="candidate-link view-candidate-btn" data-index="${index}">
                                    ${candidate.person.full_name}
                                </a>
                            </td>
                            <td>${creationDate}</td>
                            <td>${answeredQuestions}/${totalQuestions}</td>
                            <td>${candidate.calculation_result?.result_name || 'N/A'}</td>
                            <td>${candidate.person.step || 'Candidato'}</td>
                            <td>
                                <div class="text-warning">
                                    ${starsHtml}
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item view-candidate-btn" href="#" data-index="${index}">Ver detalhes</a></li>
                                        <li><a class="dropdown-item" href="#">Editar</a></li>
                                        <li><a class="dropdown-item" href="#">Mover etapa</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>`;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            }

            function updatePaginationInfo() {
                const paginationInfo = document.getElementById('paginationInfo');
                paginationInfo.textContent = `Mostrando 1-${filteredCandidates.length} de ${filteredCandidates.length}`;
            }

            function updateCandidateStep(candidateCpf, newStep, callback) {
                $.ajax({
                    url: `/updatestep/${candidateCpf}/${newStep}`,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        console.log('Etapa atualizada:', response);
                        callback(true);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao atualizar etapa:', error);
                        callback(false);
                    }
                });
            }

            function updateCandidateStatus(candidateCpf, status, callback) {
                $.ajax({
                    url: `/updatestatus/${candidateCpf}/${status}`,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        console.log('Status atualizado:', response);
                        callback(true);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao atualizar status:', error);
                        callback(false);
                    }
                });
            }

            document.getElementById('searchInput').addEventListener('input', function(e) {
                filterCandidates(e.target.value);
            });

            document.getElementById('advancedSearchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const searchName = document.getElementById('searchName')?.value.toLowerCase() || '';
                const searchCPF = document.getElementById('searchCPF')?.value || '';
                const searchPerfil = document.getElementById('searchPerfil')?.value.toLowerCase() || '';
                const searchStep = document.getElementById('searchStep')?.value.toLowerCase() || '';

                filteredCandidates = candidatesData.filter(candidate => {
                    const matchesName = !searchName || (candidate.person.full_name && candidate.person.full_name.toLowerCase().includes(searchName));
                    const matchesCPF = !searchCPF || (candidate.person.cpf && candidate.person.cpf.includes(searchCPF));
                    const matchesPerfil = !searchPerfil || (candidate.calculation_result?.result_name && candidate.calculation_result.result_name.toLowerCase().includes(searchPerfil));
                    const matchesStep = !searchStep || (candidate.person.step && candidate.person.step.toLowerCase().includes(searchStep));
                    return matchesName && matchesCPF && matchesPerfil && matchesStep;
                });

                initKanban(filteredCandidates);
                updateCandidateList(filteredCandidates);
                updatePaginationInfo();
                const modal = bootstrap.Modal.getInstance(document.getElementById('advancedSearchModal'));
                modal.hide();
            });

            document.getElementById('kanbanView').addEventListener('change', function() {
                document.getElementById('kanbanContainer').classList.remove('d-none');
                document.getElementById('listContainer').classList.add('d-none');
            });

            document.getElementById('listView').addEventListener('change', function() {
                document.getElementById('kanbanContainer').classList.add('d-none');
                document.getElementById('listContainer').classList.remove('d-none');
            });

            document.body.addEventListener('click', function(e) {
                const star = e.target.closest('.star');
                const card = e.target.closest('.kanban-card');
                const viewDetailsBtn = e.target.closest('.view-candidate-btn');
                const curriculumBtn = e.target.closest('.curriculum-btn');
                const linkedinLink = e.target.closest('.ti-brand-linkedin');

                if (star) {
                    e.preventDefault();
                    const candidateIndex = parseInt(star.dataset.index);
                    const candidateCpf = star.dataset.cpf;
                    const selectedStar = parseInt(star.dataset.star);
                    const candidate = filteredCandidates[candidateIndex];
                    if (!candidate || !candidate.person) {
                        console.error('Candidato não encontrado:', candidateIndex, candidateCpf);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Candidato não encontrado.',
                        });
                        return;
                    }
                    const currentStars = candidate.person.stars || 0;
                    const newStars = (selectedStar === currentStars) ? 0 : selectedStar;

                    updateCandidateStars(candidateCpf, newStars, function(success) {
                        if (success) {
                            candidate.person.stars = newStars;
                            const modalStars = document.querySelector('#candidateModal .stars');
                            const kanbanCard = document.querySelector(`.kanban-card[data-cpf="${candidateCpf}"]`);
                            const tableRow = document.querySelector(`.candidate-row[data-cpf="${candidateCpf}"]`);
                            updateStarsDisplay(newStars, modalStars, kanbanCard, tableRow);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Não foi possível atualizar a classificação do candidato.',
                            });
                        }
                    });
                } else if (card && !viewDetailsBtn && !curriculumBtn && !linkedinLink) {
                    const index = parseInt(card.dataset.index);
                    if (!isNaN(index) && filteredCandidates[index]) {
                        fillCandidateModal(index);
                        const modalElement = document.getElementById('candidateModal');
                        const modal = new bootstrap.Modal(modalElement, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        modal.show();
                    }
                } else if (viewDetailsBtn) {
                    e.preventDefault();
                    const index = parseInt(viewDetailsBtn.dataset.index);
                    if (!isNaN(index) && filteredCandidates[index]) {
                        fillCandidateModal(index);
                        const modalElement = document.getElementById('candidateModal');
                        const modal = new bootstrap.Modal(modalElement, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        modal.show();
                    }
                } else if (curriculumBtn) {
                    e.preventDefault();
                    const index = parseInt(curriculumBtn.dataset.index);
                    if (!isNaN(index) && filteredCandidates[index]) {
                        fillCandidateModal(index, 'curriculo-tab');
                        const modalElement = document.getElementById('candidateModal');
                        const modal = new bootstrap.Modal(modalElement, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        modal.show();
                    }
                }
            });

            document.getElementById('prevCandidateBtn').addEventListener('click', function() {
                currentCandidateIndex = (currentCandidateIndex - 1 + filteredCandidates.length) % filteredCandidates.length;
                fillCandidateModal(currentCandidateIndex);
            });

            document.getElementById('nextCandidateBtn').addEventListener('click', function() {
                currentCandidateIndex = (currentCandidateIndex + 1) % filteredCandidates.length;
                fillCandidateModal(currentCandidateIndex);
            });

            document.getElementById('reprovarBtn').addEventListener('click', function() {
                const candidate = filteredCandidates[currentCandidateIndex];
                const candidateCpf = candidate.person.cpf;
                updateCandidateStatus(candidateCpf, 'inactive', function(success) {
                    if (success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: 'Candidato foi reprovado com sucesso.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Não foi possível reprovar o candidato.',
                        });
                    }
                });
            });

            const newNoteContent = document.getElementById('newNoteContent');
            document.querySelectorAll('.note-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const command = this.dataset.command;
                    document.execCommand(command, false, null);
                    newNoteContent301.focus();
                });
            });

            document.getElementById('addNoteBtn').addEventListener('click', function() {
                const noteText = newNoteContent.innerHTML;
                if (!noteText.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção!',
                        text: 'A anotação não pode estar vazia.',
                    });
                    return;
                }
                const candidate = filteredCandidates[currentCandidateIndex];
                const candidateId = candidate && candidate.person ? candidate.person.id_person : null;
                if (!candidateId) {
                    console.error('Erro: id_person não encontrado.');
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Não foi possível identificar o candidato.',
                    });
                    return;
                }
                const writer = '{{ Auth::user()->name ?? "Usuário Desconhecido" }}';
                const rule = '{{ session("rule") }}';
                $.ajax({
                    url: '/api/occurrences',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    contentType: 'application/json',
                    data: JSON.stringify({
                        id_person: candidateId,
                        writer: writer,
                        rule_writer: rule,
                        text: noteText
                    }),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: 'Anotação adicionada com sucesso!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        newNoteContent.innerHTML = '';
                        candidate.gestor_occurrences = candidate.gestor_occurrences || [];
                        candidate.gestor_occurrences.push({
                            id_occurrence: response.id_occurrence || response.id,
                            writer: writer,
                            date: new Date().toISOString(),
                            text: noteText
                        });
                        displayOccurrences(candidate.gestor_occurrences, 'gestorOccurrences', true);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao adicionar anotação:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Não foi possível adicionar a anotação: ' + (xhr.responseJSON?.message || error),
                        });
                    }
                });
            });

            document.getElementById('viewAllHistoryBtn').addEventListener('click', function() {
                const candidate = filteredCandidates[currentCandidateIndex];
                const allOccurrences = [...(candidate.rh_occurrences || []), ...(candidate.gestor_occurrences || [])];
                displayOccurrences(allOccurrences, 'historyContent', false);
                const historyModal = new bootstrap.Modal(document.getElementById('historyModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                historyModal.show();
            });

            document.getElementById('modalSteps').addEventListener('click', function(e) {
                const stepItem = e.target.closest('.step-item');
                if (stepItem) {
                    const newStep = stepItem.dataset.step;
                    const candidate = filteredCandidates[currentCandidateIndex];
                    const candidateCpf = candidate.person.cpf;
                    updateCandidateStep(candidateCpf, newStep, function(success) {
                        if (success) {
                            candidate.person.step = newStep;
                            document.querySelectorAll('#modalSteps .step-item').forEach(item => {
                                const circle = item.querySelector('.step-circle');
                                circle.classList.remove('active');
                                if (item.dataset.step === newStep) {
                                    circle.classList.add('active');
                                }
                            });
                            fillCandidateModal(currentCandidateIndex);
                            initKanban(filteredCandidates);
                        } else {
                        
                        }
                    });
                }
            });

            initKanban();
        });
    </script>
@endsection