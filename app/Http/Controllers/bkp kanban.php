bkp kanban

@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Candidatos')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
{{-- SweetAlert2 CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="card mb-4">
<!-- Cabeçalho da Vaga -->
  <div class="card-body pb-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
      <div>
        <h4 class="mb-1">{{ $vacancy['description'] }}</h4>
        <p class="mb-1 text-muted">Departamento {{ $departments }}</p>
        <small class="text-muted">Vaga aberta há {{ \Carbon\Carbon::parse($vacancy['created_at'])->diffInDays() }} dias</small>
      </div>

      <div class="d-flex align-items-center flex-wrap gap-2 mt-3 mt-md-0">
        <a href="#" onclick="copiarLinkVaga()" class="text-primary fw-semibold me-3">Copiar link da Vaga</a>

        <button type="button" class="btn btn-outline-primary btn-sm" id="candidatosBtn">
          <i class="bi bi-people me-1"></i> Candidatos
        </button>

        <button type="button" class="btn btn-outline-secondary btn-sm " id="editarVagaBtn">
          <i class="bi bi-pencil-square me-1"></i> Editar Vaga
        </button>

        <button type="button" class="btn btn-outline-secondary btn-sm" id="rodaCompetenciaBtn">
          <i class="bi bi-clock-history me-1"></i> Roda de Competência do Cargo
        </button>
      </div>
    </div>
</div>

<script>
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
    });
  }
</script>

</div>












<div class="card">
    <!-- Cabeçalho da Vaga -->

    <!-- Navegação e Botões -->
    
    <!-- Conteúdo das Abas -->
    <div id="abas-content">
        <!-- Aba Candidatos -->
        <div id="candidatos-content">
    <!-- Filtros e Visualização -->
    <div class="card mb-4">
  <div class="card-body py-2 px-3">
    <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-2">
      
      <!-- Campo de busca -->
      <div class="flex-fill">
      <form method="POST" action="{{ route('buscar') }}">
    @csrf

    <input type="hidden" name="id_vacancy" value="1">

    <div class="input-group">
      <input
        type="text"
        name="search"
        class="form-control"
        placeholder="Buscar candidatos..."
        aria-label="Buscar candidatos"
      >
      <button class="btn btn-primary" type="submit">
        <i class="ti ti-search"></i> Pesquisar
      </button>
    </div>
  </form>
      </div>

      <!-- Botões de ação -->
      <div class="d-flex flex-wrap gap-2 justify-content-md-end">
 


        <div class="btn-group" role="group" aria-label="Visualização">
          <input type="radio" class="btn-check" name="viewToggle" id="kanbanView" autocomplete="off" checked>
          <label class="btn btn-outline-primary" for="kanbanView">Kanban</label>

          <input type="radio" class="btn-check" name="viewToggle" id="listView" autocomplete="off">
          <label class="btn btn-outline-primary" for="listView">Lista</label>
        </div>
      </div>
    </div>
  </div>
</div>
            <!-- Visualização Kanban -->
            <div id="kanbanContainer" class="mb-4">
                <div class="row g-4">
                    <!-- Coluna: Candidatos -->
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Candidatos <span class="badge bg-primary rounded-pill ms-1" id="candidatos-count">0</span></h5>
                            </div>
                            <div class="card-body p-2 kanban-column" data-step="Candidato">
                                <!-- Cards de candidatos serão gerados dinamicamente via JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Coluna: Análise Inicial -->
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Análise Inicial <span class="badge bg-primary rounded-pill ms-1" id="analise-count">0</span></h5>
                            </div>
                            <div class="card-body p-2 kanban-column" data-step="Análise Inicial">
                                <!-- Cards de candidatos serão gerados dinamicamente via JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Coluna: Teste de Perfil -->
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Teste de Perfil <span class="badge bg-primary rounded-pill ms-1" id="teste-count">0</span></h5>
                            </div>
                            <div class="card-body p-2 kanban-column" data-step="Teste de Perfil">
                                <!-- Cards de candidatos serão gerados dinamicamente via JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Coluna: Entrevista -->
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Entrevista <span class="badge bg-primary rounded-pill ms-1" id="entrevista-count">0</span></h5>
                            </div>
                            <div class="card-body p-2 kanban-column" data-step="Entrevista">
                                <!-- Cards de candidatos serão gerados dinamicamente via JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Coluna: Aprovado -->
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Aprovado <span class="badge bg-primary rounded-pill ms-1" id="aprovado-count">0</span></h5>
                            </div>
                            <div class="card-body p-2 kanban-column" data-step="Aprovado">
                                <!-- Cards de candidatos serão gerados dinamicamente via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visualização em Lista (inicialmente oculta) -->
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
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($candidates as $candidate)
                                    <tr class="candidate-row" data-cpf="{{ $candidate['person']['cpf'] }}" data-index="{{ $loop->index }}">
                                        <td>
                                            @if(isset($candidate['person']['step']) && $candidate['person']['step'] == 'Entrevista')
                                            <i class="bi bi-circle-fill text-success me-1" style="font-size: 10px;"></i>
                                            @else
                                            <i class="bi bi-circle-fill text-secondary me-1" style="font-size: 10px;"></i>
                                            @endif
                                            <a href="#" class="candidate-link">{{ $candidate['person']['full_name'] }}</a>
                                        </td>
                                        <td>
                                            @php
                                                $date = new DateTime($candidate['recruitment']['creation_date']);
                                                echo $date->format('d/m/Y');
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                // Verificar se há perguntas respondidas
                                                $questions = json_decode($candidate['recruitment']['questions'] ?? '[]', true);
                                                $total = count($questions);
                                                $answered = $total; // Assumindo que todas foram respondidas
                                                echo $answered . '/' . $total;
                                            @endphp
                                        </td>
                                        <td>
                                            @if(isset($candidate['calculation_result']))
                                                {{ $candidate['calculation_result']['result_name'] }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $candidate['person']['step'] ?? 'Candidato' }}
                                        </td>
                                        <td>
                                            <div class="text-warning">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star{{ isset($candidate['calculation_result']) ? '-fill' : '' }}"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item candidate-link" href="#">Ver detalhes</a></li>
                                                    <li><a class="dropdown-item" href="#">Editar</a></li>
                                                    <li><a class="dropdown-item" href="#">Mover etapa</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        <div class="d-flex justify-content-between align-items-center p-3 border-top">
                            <div class="text-muted small">
                                Mostrando 1-{{ count($candidates) }} de {{ $recruitment_count }}
                            </div>
                            <nav aria-label="Navegação de página">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item">
                                        <a class="page-link" href="#" aria-label="Anterior">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    @if($recruitment_count > 15)
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    @endif
                                    @if($recruitment_count > 30)
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="#" aria-label="Próximo">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba Editar Vaga (inicialmente oculta) -->
        <div id="editar-vaga-content" class="d-none">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Editar Vaga</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/vacancy/new">
                        @csrf
                        <!-- Informações Básicas -->
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="titulo" class="form-label">Título da Vaga*</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" value="{{ $vacancy['description'] }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="senioridade" class="form-label">Senioridade</label>
                                    <select class="form-select" id="senioridade" name="senioridade">
                                        <option value="">Selecionar</option>
                                        <option value="Junior" {{ $vacancy['seniority'] == 'Junior' ? 'selected' : '' }}>Júnior</option>
                                        <option value="Pleno" {{ $vacancy['seniority'] == 'Pleno' ? 'selected' : '' }}>Pleno</option>
                                        <option value="Senior" {{ $vacancy['seniority'] == 'Senior' ? 'selected' : '' }}>Sênior</option>
                                        <option value="Especialista" {{ $vacancy['seniority'] == 'Especialista' ? 'selected' : '' }}>Especialista</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Divulgar no Portal de Vagas?*</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="divulgar" id="divulgarSim" value="1" {{ $vacancy['publish'] == '1' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="divulgarSim">Sim</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="divulgar" id="divulgarNao" value="0" {{ $vacancy['publish'] == '0' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="divulgarNao">Não</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="posicoes" class="form-label">Posições* <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip" title="Número de vagas disponíveis"></i></label>
                                    <input class="form-control" type="number" id="posicoes" name="posicoes" required value="{{ $vacancy['vacancies_number'] ?? '' }}">

                                    

                                </div>
                                
                                <div class="col-md-6">
                                    <label for="local" class="form-label">Local de Trabalho*</label>
                                    <select class="form-select" id="local" name="local" required>
                                        <option value="">Selecionar</option>
                                        <option value="Presencial" {{ $vacancy['local'] == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                                        <option value="Remoto" {{ $vacancy['local'] == 'Remoto' ? 'selected' : '' }}>Remoto</option>
                                        <option value="Híbrido" {{ $vacancy['local'] == 'Híbrido' ? 'selected' : '' }}>Híbrido</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="jornada" class="form-label">Jornada de Trabalho* <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip" title="Carga horária semanal"></i></label>
                                    <input type="text" class="form-control" id="jornada" name="jornada" value="{{ $vacancy['working_hours'] ?? '44h semanais' }}" required>
                                </div>







                                <div class="col-md-6">
                <label for="salario_tipo" class="form-label">Oferta Salarial*</label>
                <div class="input-group mb-3">
                    <select class="form-select" id="salario_tipo" name="salario_tipo" style="max-width: 150px;">
                        <option value="Valor">Valor</option>
                        <option value="Faixa">Faixa</option>
                        <option value="A combinar">A combinar</option>
                    </select>
                    
                    <!-- Campo para valor único -->
                    <div id="valor_unico_container">
                        <input type="text" class="form-control" id="salario_valor" placeholder="Valor">
                    </div>
                    
                    <!-- Campos para faixa salarial -->
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
                
                <!-- Campo oculto para armazenar o valor formatado -->
                <input type="hidden" id="salary" name="salary" value="">
            </div>

                           <script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const salarioTipo = document.getElementById('salario_tipo');
    const valorUnicoContainer = document.getElementById('valor_unico_container');
    const faixaContainer = document.getElementById('faixa_container');
    const salarioValor = document.getElementById('salario_valor');
    const salarioDe = document.getElementById('salario_de');
    const salarioAte = document.getElementById('salario_ate');
    const salaryHidden = document.getElementById('salary');

    // Função para formatar valor em Real brasileiro
    function formatarReal(valor) {
        if (!valor) return 'R$ 0,00';
        
        // Remove caracteres não numéricos
        valor = valor.replace(/\D/g, '');
        
        // Converte para número e divide por 100 para considerar centavos
        valor = (parseInt(valor) / 100).toFixed(2);
        
        // Formata com separador de milhar e vírgula para decimal
        valor = valor.replace('.', ',');
        valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        
        return 'R$ ' + valor;
    }

    // Função para atualizar o campo oculto com o valor formatado
    function atualizarCampoOculto() {
        const tipo = salarioTipo.value;
        
        if (tipo === 'A combinar') {
            salaryHidden.value = 'A combinar';
        } else if (tipo === 'Valor') {
            salaryHidden.value = formatarReal(salarioValor.value);
        } else if (tipo === 'Faixa') {
            const de = formatarReal(salarioDe.value);
            const ate = formatarReal(salarioAte.value);
            salaryHidden.value = de + ' - ' + ate;
        }
    }

    // Função para aplicar máscara de Real nos inputs
    function aplicarMascaraReal(input) {
        input.addEventListener('input', function(e) {
            let valor = e.target.value.replace(/\D/g, '');
            
            if (valor === '') {
                e.target.value = '';
                return;
            }
            
            // Formata o valor como Real
            valor = (parseInt(valor) / 100).toFixed(2);
            valor = valor.replace('.', ',');
            valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            
            e.target.value = 'R$ ' + valor;
            
            // Atualiza o campo oculto
            atualizarCampoOculto();
        });
    }

    // Aplicar máscara nos campos de valor
    aplicarMascaraReal(salarioValor);
    aplicarMascaraReal(salarioDe);
    aplicarMascaraReal(salarioAte);

    // Função para exibir/ocultar campos conforme o tipo selecionado
    function atualizarCamposVisiveis() {
        const tipo = salarioTipo.value;
        
        if (tipo === 'A combinar') {
            valorUnicoContainer.style.display = 'none';
            faixaContainer.style.display = 'none';
        } else if (tipo === 'Valor') {
            valorUnicoContainer.style.display = 'block';
            faixaContainer.style.display = 'none';
        } else if (tipo === 'Faixa') {
            valorUnicoContainer.style.display = 'none';
            faixaContainer.style.display = 'flex';
        }
        
        // Atualiza o campo oculto
        atualizarCampoOculto();
    }

    // Evento de mudança no select
    salarioTipo.addEventListener('change', atualizarCamposVisiveis);

    // Inicializar campos
    atualizarCamposVisiveis();
});

                            </script>
                                
                              
        <div class="col-md-4">
          <label class="form-label">Benefícios</label>
          <input name="benefits" type="text" class="form-control" value="{{ $vacancy['benefits'] }}">
        </div>
                                
                                <div class="col-md-6">
                                    <label for="departamento" class="form-label">Departamento</label>
                                    <select class="form-select" id="departamento" name="departamento">
                                        <option value="">Selecionar</option>
                                        <option value="1" {{ $departments == '1' ? 'selected' : '' }}>Comercial</option>
                                        <option value="2">Marketing</option>
                                        <option value="3">Recursos Humanos</option>
                                        <option value="4">Financeiro</option>
                                        <option value="5">Tecnologia</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="cargo" class="form-label">Cargo</label>
                                    <select class="form-select" id="cargo" name="cargo">
                                        <option value="">Selecionar</option>
                                        <option value="1" {{ $vacancy['id_job'] == '1' ? 'selected' : '' }}>Auxiliar de Vendas</option>
                                        <option value="2">Analista de Marketing</p>
                                        <option value="3">Desenvolvedor</option>
                                        <option value="4">Gerente Comercial</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="encerramento" class="form-label">Encerramento da Vaga*</label>
                                    <input type="date" class="form-control" id="encerramento" name="encerramento" value="{{ date('Y-m-d', strtotime($vacancy['expiration_date'])) }}" required>
                                </div>
                            </div>
                        </div>











 <script src="https://cdn.tiny.cloud/1/n7b8zpu0tl0lg9ka80vagfoo3vtu97zk3rwall7rpfhg95q7/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<!-- CARD DE DESCRIÇÃO DA VAGA -->
<div class="card mb-4">
  <h5 class="card-header d-flex align-items-center gap-2">
    📄 Descrição da Vaga
  </h5>

  <div class="card-body">
    <!-- Campo Resumo -->
    <div class="mb-3">
      <label for="resumo" class="form-label">Resumo</label>
      <textarea class="form-control" id="resumo" name="resumo" rows="3" placeholder="">{{ $vacancy['resume'] }}</textarea>
    </div>

  <!-- Campo Principais Atividades -->
<div class="mb-3">
  <label for="atividades" class="form-label">Principais Atividades</label>
  <textarea id="atividadess" name="atividadess">{{ $vacancy['activities'] }}</textarea>
</div>

<!-- Campo Requisitos -->
<div class="mb-3">
  <label for="requisitos" class="form-label">Requisitos</label>
  <textarea id="requisitoss" name="requisitoss">{{ $vacancy['requirements'] }}</textarea>
</div>


    <!-- Botão de Submissão -->
    
  </div>
</div>


















                        
                    

                        <!-- Perguntas (somente leitura) -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Perguntas para os Candidatos</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="text-muted mb-3">Estas perguntas serão apresentadas aos candidatos durante o processo de inscrição.</p>
                                    
                                    @if(!empty($vacancy['q1']))
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Pergunta 1:</label>
                                        <p class="mb-0">{{ $vacancy['q1'] }}</p>
                                    </div>
                                    @endif
                                    
                                    @if(!empty($vacancy['q2']))
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Pergunta 2:</label>
                                        <p class="mb-0">{{ $vacancy['q2'] }}</p>
                                    </div>
                                    @endif
                                    
                                    @if(!empty($vacancy['q3']))
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Pergunta 3:</label>
                                        <p class="mb-0">{{ $vacancy['q3'] }}</p>
                                    </div>
                                    @endif
                                    
                                    @if(!empty($vacancy['q4']))
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Pergunta 4:</label>
                                        <p class="mb-0">{{ $vacancy['q4'] }}</p>
                                    </div>
                                    @endif
                                    
                                    @if(!empty($vacancy['q5']))
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Pergunta 5:</label>
                                        <p class="mb-0">{{ $vacancy['q5'] }}</p>
                                    </div>
                                    @endif
                                    
                                    @if(empty($vacancy['q1']) && empty($vacancy['q2']) && empty($vacancy['q3']) && empty($vacancy['q4']) && empty($vacancy['q5']))
                                    <p class="text-center mb-0">Nenhuma pergunta cadastrada para esta vaga.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-secondary me-2">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aba Roda de Competência (inicialmente oculta) -->
        <div id="roda-competencia-content" class="d-none">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Roda de Competência do Cargo</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <p class="text-muted">Esta visualização permite analisar as competências necessárias para o cargo de {{ $vacancy['description'] }}.</p>
                    </div>

                    














                    
                    
                    <!-- Placeholder para o gráfico de radar -->
                    <div id="competencia-chart" style="height: 800px;"></div>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes do Candidato -->
<div class="modal fade" id="candidateModal" tabindex="-1" aria-labelledby="candidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header pt-3">
                <div>
                    <h5 class="modal-title" id="candidateModalLabel">
                        <span id="candidateName"></span>
                        <span class="text-warning ms-2">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </span>
                    </h5>
                    <div class="text-muted small">
                        Vaga: <span id="candidateJob">{{ $vacancy['description'] }}</span>
                        <br>Cadastrou em: <span id="candidateDate"></span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <!--<button type="button" class="btn btn-primary btn-sm" id="scheduleInterviewBtn">Agendar Entrevista</button>-->
                    </div>
                    <div class="d-flex align-items-center me-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="prevCandidateBtn">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span id="candidateCounter" class="small text-muted">1 de {{ count($candidates) }}</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="nextCandidateBtn">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div><br><br><br><br><br><br><br><br><br>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="location.reload()"></button>

                </div>
            </div>
            <div class="modal-body p-0">
                <!-- Vaga atual -->
                <div class="px-4 py-2 border-bottom">

                </div>
                
                <!-- Abas de navegação -->
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
                
                <!-- Etapas do processo -->
                <div class="px-4 py-3">
                    <div class="progress-steps">
                        <div class="step-item active" data-step="Candidato">
                            <div class="step-circle">1</div>
                            <div class="step-text">Candidato</div>
                        </div>
                        
                        <div class="step-item" data-step="Análise Inicial">
                            <div class="step-circle">2</div>
                            <div class="step-text">Análise Inicial</div>
                        </div>
                        <div class="step-item" data-step="Teste de Perfil">
                            <div class="step-circle">3</div>
                            <div class="step-text">Teste Perfil</div>
                        </div>
                        <div class="step-item" data-step="Entrevista">
                            <div class="step-circle">4</div>
                            <div class="step-text">Entrevista</div>
                        </div>
                        <div class="step-item" data-step="Aprovado">
                            <div class="step-circle">5</div>
                            <div class="step-text">Aprovado</div>
                        </div>
                        <div class="step-item" data-step="Documentação">
                            <div class="step-circle">6</div>
                            <div class="step-text">Documentação</div>
                        </div>
                    </div>
                </div>
                
                <!-- Conteúdo das abas -->
                <div class="tab-content px-4 py-3" id="candidateTabsContent">
                    <!-- Aba: Dados do Candidato -->
                    <div class="tab-pane fade show active" id="dados-tab-pane" role="tabpanel" aria-labelledby="dados-tab" tabindex="0">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">E-mail:</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope me-2"></i>
                                        <a href="#" id="candidateEmail"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">WhatsApp:</label>
                                    <div class="d-flex align-items-center">
  <i class="bi bi-whatsapp me-2 text-success"></i>
  <a href="#" id="candidateWhatsapp" target="_blank" class="text-decoration-none text-dark"></a>
</div>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">LinkedIn:</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-linkedin me-2 text-primary"></i>
                                        <a href="#" id="candidateLinkedin" target="_blank"></a>
                                        <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Contato</h6>
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
                                <h6 class="card-title">Endereço</h6>
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
                    
                    <!-- Aba: Currículo -->
                    <div class="tab-pane fade" id="curriculo-tab-pane" role="tabpanel" aria-labelledby="curriculo-tab" tabindex="0">
                        <div id="curriculoContent">
                            <!-- O conteúdo do currículo será preenchido dinamicamente via JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Aba: Perguntas -->
                    <div class="tab-pane fade" id="perguntas-tab-pane" role="tabpanel" aria-labelledby="perguntas-tab" tabindex="0">
                        <div id="perguntasContent" class="card">
                            <div class="card-body">
                                <p class="text-muted mb-4">Respostas do candidato às perguntas da vaga.</p>
                                <div id="listaPerguntas">
                                    <!-- As perguntas e respostas serão preenchidas dinamicamente via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba: Teste de Perfil -->
                    <div class="tab-pane fade" id="teste-tab-pane" role="tabpanel" aria-labelledby="teste-tab" tabindex="0">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Perfil Comportamental: <span id="perfilComportamental" class="fw-normal"></span></h6>
                            <button class="btn btn-sm btn-outline-primary" id="downloadReportBtn">Baixar Relatório de Teste de Perfil</button>
                        </div>
                        
                        <!-- Barras de perfil -->
                        <div class="mb-4">
                            <div class="bar-container">
                                <div class="label">Decisão</div>
                                <div class="bar-wrapper">
                                    <div class="bar-fill" id="decisaoBar" style="background-color: #9400D3;"></div>
                                </div>
                                <div class="percentage" id="decisaoPercentage"></div>
                            </div>
                            
                            <div class="bar-container">
                                <div class="label">Detalhismo</div>
                                <div class="bar-wrapper">
                                    <div class="bar-fill" id="detalhismoBar" style="background-color: #008B8B;"></div>
                                </div>
                                <div class="percentage" id="detalhismoPercentage"></div>
                            </div>
                            
                            <div class="bar-container">
                                <div class="label">Entusiasmo</div>
                                <div class="bar-wrapper">
                                    <div class="bar-fill" id="entusiasmoBar" style="background-color: #FF69B4;"></div>
                                </div>
                                <div class="percentage" id="entusiasmoPercentage"></div>
                            </div>
                            
                            <div class="bar-container">
                                <div class="label">Relacional</div>
                                <div class="bar-wrapper">
                                    <div class="bar-fill" id="relacionalBar" style="background-color: #0077B6;"></div>
                                </div>
                                <div class="percentage" id="relacionalPercentage"></div>
                            </div>
                        </div>
                        
                        <!-- Roda de Competências -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0">Roda de Competências:</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="matchWithJob" checked>
                                        <label class="form-check-label" for="matchWithJob">Matcher com Cargo</label>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <span class="badge rounded-pill text-bg-info">Candidato</span>
                                </div>
                                <div id="competenciasChart" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba: Anotações -->
                    <div class="tab-pane fade" id="anotacoes-tab-pane" role="tabpanel" aria-labelledby="anotacoes-tab" tabindex="0">
                        <div class="card-body">
                            <!-- Seção para adicionar anotação -->
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
                            
                            <!-- Seção RH -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="section-title mb-0">RH</h6>
                                </div>
                                <div id="rhOccurrences">
                                    <!-- Última anotação RH será exibida aqui -->
                                </div>
                            </div>
                            
                            <!-- Seção Gestor -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="section-title mb-0">Gestor</h6>
                                </div>
                                <div id="gestorOccurrences">
                                    <!-- Última anotação Gestor será exibida aqui -->
                                </div>
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
                    <script>
// Adicionar event listener para o botão de reprovar
document.getElementById('reprovarBtn').addEventListener('click', function() {
    // Obter o CPF do candidato atual
    const candidateCpf = candidatesData[currentCandidateIndex].person.cpf;
    
    // Confirmar a reprovação
    Swal.fire({
        title: 'Tem certeza?',
        text: "Você não poderá reverter esta ação!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, reprovar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Fazer chamada AJAX para a API
            $.ajax({
                url: `/updatestep/${candidateCpf}/inactive`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire(
                        'Reprovado!',
                        'Candidato reprovado com sucesso.',
                        'success'
                    );
                    
                    // Atualizar dados locais
                    candidatesData[currentCandidateIndex].person.step = 'inactive';
                    
                    // Fechar modal
                    bootstrap.Modal.getInstance(document.getElementById('candidateModal')).hide();
                    
                    // Atualizar Kanban
                    initKanban();
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Erro!',
                        'Não foi possível reprovar o candidato. Tente novamente.',
                        'error'
                    );
                    console.error('Erro ao reprovar candidato:', error);
                }
            });
        }
    });
});
</script>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-success"  data-bs-dismiss="modal">
                            <i class="bi bi-arrow-right me-1"></i>Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Histórico de Anotações (Único para RH e Gestor) -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Histórico Completo de Anotações</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyContent">
                    <!-- O histórico de anotações será carregado aqui -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados dos candidatos recebidos do controller
    const candidatesData = @json($candidates);
    const vacancy = @json($vacancy);
    
    // Variável para armazenar o índice do candidato atualmente exibido no modal
    let currentCandidateIndex = 0;

    // Função para formatar o currículo
    function formatCurriculum(curriculumJson) {
        try {
            const curriculum = JSON.parse(curriculumJson);
            let html = '<div class="curriculum-content">';
            
            // Informações Pessoais
            if (curriculum['Informações Pessoais']) {
                html += '<div class="mb-4"><h5 class="fw-bold">Informações Pessoais</h5>';
                const infoPessoais = curriculum['Informações Pessoais'];
                
                for (const key in infoPessoais) {
                    if (key === 'Contatos') {
                        html += '<p><strong>Contatos:</strong></p><ul>';
                        for (const contactKey in infoPessoais[key]) {
                            html += `<li><strong>${contactKey}:</strong> ${infoPessoais[key][contactKey]}</li>`;
                        }
                        html += '</ul>';
                    } else {
                        html += `<p><strong>${key}:</strong> ${infoPessoais[key]}</p>`;
                    }
                }
                html += '</div>';
            }
            
            // Objetivo
            if (curriculum['Objetivo']) {
                html += '<div class="mb-4"><h5 class="fw-bold">Objetivo</h5>';
                const objetivo = curriculum['Objetivo'];
                
                for (const key in objetivo) {
                    html += `<p><strong>${key}:</strong> ${objetivo[key]}</p>`;
                }
                html += '</div>';
            }
            
            // Resumo Profissional
            if (curriculum['Resumo Profissional']) {
                html += '<div class="mb-4"><h5 class="fw-bold">Resumo Profissional</h5>';
                const resumo = curriculum['Resumo Profissional'];
                
                for (const key in resumo) {
                    if (Array.isArray(resumo[key])) {
                        html += `<p><strong>${key}:</strong></p><ul>`;
                        resumo[key].forEach(item => {
                            html += `<li>${item}</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += `<p><strong>${key}:</strong> ${resumo[key]}</p>`;
                    }
                }
                html += '</div>';
            }
            
            // Formação
            if (curriculum['Formação']) {
                html += '<div class="mb-4"><h5 class="fw-bold">Formação</h5>';
                const formacao = curriculum['Formação'];
                
                for (const key in formacao) {
                    html += `<p><strong>${key}</strong></p>`;
                    if (typeof formacao[key] === 'object' && formacao[key] !== null) {
                        for (const subKey in formacao[key]) {
                            html += `<p class="ms-3"><strong>${subKey}:</strong> ${formacao[key][subKey]}</p>`;
                        }
                    }
                }
                html += '</div>';
            }
            
            // Experiência Profissional
            if (curriculum['Experiência Profissional']) {
                html += '<div class="mb-4"><h5 class="fw-bold">Experiência Profissional</h5>';
                const experiencia = curriculum['Experiência Profissional'];
                
                for (const key in experiencia) {
                    html += `<div class="mb-3"><p class="fw-bold mb-1">${key}</p>`;
                    if (typeof experiencia[key] === 'object' && experiencia[key] !== null) {
                        for (const subKey in experiencia[key]) {
                            html += `<p class="ms-3 mb-1"><strong>${subKey}:</strong> ${experiencia[key][subKey]}</p>`;
                        }
                    }
                    html += '</div>';
                }
                html += '</div>';
            }
            
            // Cursos Complementares
            if (curriculum['Cursos Complementares']) {
                html += '<div class="mb-4"><h5 class="fw-bold">Cursos Complementares</h5><ul>';
                const cursos = curriculum['Cursos Complementares'];
                
                for (const key in cursos) {
                    html += `<li>${key}</li>`;
                }
                html += '</ul></div>';
            }
            
            html += '</div>';
            return html;
        } catch (e) {
            console.error('Erro ao formatar currículo:', e);
            return '<div class="alert alert-warning">Não foi possível carregar o currículo corretamente.</div>';
        }
    }
    
    // Função para formatar as perguntas e respostas
    function formatQuestions(questionsJson) {
        try {
            const questions = JSON.parse(questionsJson);
            let html = '<div class="questions-list">';
            
            questions.forEach((item, index) => {
                html += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Pergunta ${index + 1}:</h6>
                        <p class="card-text fw-bold">${item.question}</p>
                        <div class="mt-2">
                            <h6 class="text-muted">Resposta:</h6>
                            <p>${item.response}</p>
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

    // Função para formatar e exibir as anotações
    function displayOccurrences(occurrences, containerId, showLatestOnly = true) {
        const container = document.getElementById(containerId);
        let html = '';
        const currentUser = '{{ Auth::user()->name ?? "" }}'; // Pega o nome do usuário logado

        if (occurrences && occurrences.length > 0) {
            const sortedOccurrences = occurrences.sort((a, b) => new Date(b.date) - new Date(a.date));
            const occurrencesToDisplay = showLatestOnly ? [sortedOccurrences[0]] : sortedOccurrences;

            occurrencesToDisplay.forEach(note => {
                const noteDate = new Date(note.date);
                const formattedDate = noteDate.toLocaleDateString('pt-BR');
                const canEdit = note.writer === currentUser; // Verifica se o usuário pode editar

                html += `
                <div class="annotation-item" data-id-occurrence="${note.id_occourrence}">
                    <div class="annotation-header">
                        <span class="annotation-name">${note.writer}</span>
                        <span class="annotation-date">${formattedDate}</span>
                    </div>
                    <div class="annotation-text-display">
                        <p class="annotation-text">${note.text}</p>
                        ${canEdit ? `<button type="button" class="btn btn-sm btn-outline-secondary edit-note-btn"><i class="bi bi-pencil"></i></button>` : ''}
                    </div>
                    ${canEdit ? `
                    <div class="annotation-edit-area d-none">
                        <textarea class="form-control mb-2">${note.text}</textarea>
                        <button type="button" class="btn btn-success btn-sm save-note-btn">Salvar</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn">Cancelar</button>
                    </div>
                    ` : ''}
                </div>`;
            });
        } else {
            html = '<div class="alert alert-info">Nenhuma anotação disponível.</div>';
        }
        container.innerHTML = html;
    }

    // Função para preencher os dados do candidato no modal
    function fillCandidateModal(candidateIndex) {
        currentCandidateIndex = candidateIndex; // Atualiza o índice global
        const candidate = candidatesData[candidateIndex];
        
        if (!candidate) return;
        
        // Dados básicos
        document.getElementById('candidateName').textContent = candidate.person.full_name;
        document.getElementById('candidateJob').textContent = vacancy.description;
        
        // Formatar data
        const creationDate = new Date(candidate.recruitment.creation_date);
        document.getElementById('candidateDate').textContent = creationDate.toLocaleDateString('pt-BR');
        
        // Contador de candidatos
        document.getElementById('candidateCounter').textContent = `${candidateIndex + 1} de ${candidatesData.length}`;
        
        // Dados de contato
        document.getElementById('candidateEmail').textContent = candidate.person.personal_email;
        document.getElementById('candidateEmail').href = `mailto:${candidate.person.personal_email}`;
        
        if (candidate?.person?.cellphone) {
            const phone = candidate.person.cellphone.replace(/\D/g, ''); // remove caracteres não numéricos
            const link = document.getElementById('candidateWhatsapp');
            link.href = `https://wa.me/55${phone}`; // Adiciona o código do país
            link.textContent = candidate.person.cellphone;
        } else {
            document.getElementById('candidateWhatsapp').textContent = 'N/A';
            document.getElementById('candidateWhatsapp').href = '#';
        }

        document.getElementById('candidateLinkedin').textContent = candidate.person.linkedin || 'N/A';
        document.getElementById('candidateLinkedin').href = candidate.person.linkedin || '#';
        
        // Preencher campos de contato
        document.getElementById('contactName').value = candidate.person.full_name;
        document.getElementById('contactCPF').value = candidate.person.cpf;
        document.getElementById('contactBirthdate').value = new Date(candidate.person.birth_date).toLocaleDateString('pt-BR');
        document.getElementById('contactEmail').value = candidate.person.personal_email;
        document.getElementById('contactPhone').value = candidate.person.cellphone;
        
        // Preencher campos de endereço
        document.getElementById('addressZip').value = candidate.person.zip_code;
        document.getElementById('addressState').value = candidate.person.address_state;
        document.getElementById('addressCity').value = candidate.person.address_city;
        document.getElementById('addressNeighborhood').value = candidate.person.address_district;
        document.getElementById('addressNumber').value = candidate.person.address_number;
        document.getElementById('addressComplement').value = candidate.person.address_complement || '';
        
        // Currículo
        if (candidate.recruitment.curriculum) {
            document.getElementById('curriculoContent').innerHTML = formatCurriculum(candidate.recruitment.curriculum);
        } else {
            document.getElementById('curriculoContent').innerHTML = '<div class="alert alert-info">Currículo não disponível.</div>';
        }
        
        // Perguntas
        if (candidate.recruitment.questions) {
            document.getElementById('listaPerguntas').innerHTML = formatQuestions(candidate.recruitment.questions);
        } else {
            document.getElementById('listaPerguntas').innerHTML = '<div class="alert alert-info">Nenhuma pergunta respondida.</div>';
        }
        
        // Teste de perfil
        if (candidate.calculation_result) {
            document.getElementById('perfilComportamental').textContent = candidate.calculation_result.result_name;
            
            // Preencher barras de perfil
            try {
                const attributes = JSON.parse(candidate.calculation_result.attributes);
                
                document.getElementById('decisaoBar').style.width = `${attributes.decision}%`;
                document.getElementById('decisaoPercentage').textContent = `${parseFloat(attributes.decision).toFixed(1)}%`;
                
                document.getElementById('detalhismoBar').style.width = `${attributes.detail}%`;
                document.getElementById('detalhismoPercentage').textContent = `${parseFloat(attributes.detail).toFixed(1)}%`;
                
                document.getElementById('entusiasmoBar').style.width = `${attributes.enthusiasm}%`;
                document.getElementById('entusiasmoPercentage').textContent = `${parseFloat(attributes.enthusiasm).toFixed(1)}%`;
                
                document.getElementById('relacionalBar').style.width = `${attributes.relational}%`;
                document.getElementById('relacionalPercentage').textContent = `${parseFloat(attributes.relational).toFixed(1)}%`;
                
                // Configurar gráfico de competências
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

        // Anotações: Exibir apenas a última de cada tipo
        displayOccurrences(candidate.rh_occurrences, 'rhOccurrences', true);
        displayOccurrences(candidate.gestor_occurrences, 'gestorOccurrences', true);
        
        // Atualizar etapa ativa no progresso
        document.querySelectorAll('.step-item').forEach(item => {
            item.classList.remove('active');
            if (item.dataset.step === candidate.person.step) {
                item.classList.add('active');
            }
        });
    }
    
    // Configurar gráfico de competências
    function setupCompetenciasChart(skills) {
        const categories = skills.map(skill => skill.name);
        const values = skills.map(skill => parseFloat(skill.value));
        
        const options = {
            series: [{
                name: 'Competência',
                data: values
            }],
            chart: {
                height: 400, // Ajuste a altura para melhor visualização dentro do modal
                type: 'radar',
            },
            xaxis: {
                categories: categories
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
                show: true,
                width: 2,
                colors: ['#4F46E5'],
                dashArray: 0
            },
            markers: {
                size: 5,
                colors: ['#4F46E5'],
                hover: {
                    size: 7
                }
            }
        };
        
        // Limpar gráfico existente
        document.getElementById('competenciasChart').innerHTML = '';
        
        // Criar novo gráfico
        const chart = new ApexCharts(document.getElementById('competenciasChart'), options);
        chart.render();
    }
    
    // Configurar gráfico de competências do cargo
    function setupCargoCompetenciasChart() {
        const options = {
            series: [{
                name: 'Cargo',
                data: [80, 70, 65, 75, 85, 60, 55, 70, 75, 65, 80, 75, 60, 65, 75, 70, 65, 75, 70, 65]
            }],
            chart: {
                height: 800,
                type: 'radar',
            },
            xaxis: {
                categories: [
                    'Foco em resultado', 'Estrategista', 'Automotivação', 'Intraempreendedorismo', 'Proatividade',
                    'Precisão', 'Análise', 'Organização e planejamento', 'Disciplina', 'Concentração',
                    'Otimismo', 'Influência', 'Criatividade', 'Adaptabilidade', 'Sociabilidade',
                    'Empatia', 'Harmonia', 'Colaboração', 'Diplomacia', 'Autocontrole'
                ]
            },
            yaxis: {
                show: false,
                min: 0,
                max: 100
            },
            fill: {
                opacity: 0.5,
                colors: ['#0EA5E9']
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['#0EA5E9'],
                dashArray: 0
            },
            markers: {
                size: 5,
                colors: ['#0EA5E9'],
                hover: {
                    size: 7
                }
            }
        };
        
        // Criar gráfico
        const chart = new ApexCharts(document.getElementById('competencia-chart'), options);
        chart.render();
    }
    
    // Inicializar Kanban
    function initKanban() {
        // Limpar colunas
        document.querySelectorAll('.kanban-column').forEach(column => {
            column.innerHTML = '';
        });
        
        // Contadores para cada etapa
        const counters = {
            'Candidato': 0,
            'Análise Inicial': 0,
            'Teste de Perfil': 0,
            'Entrevista': 0,
            'Aprovado': 0
        };
        
        // Adicionar candidatos às colunas correspondentes
        candidatesData.forEach((candidate, index) => {
            const step = candidate.person.step || 'Candidato';
            const column = document.querySelector(`.kanban-column[data-step="${step}"]`);
            
            if (column) {
                counters[step]++;
                
                // Criar card do candidato
                const card = document.createElement('div');
                card.className = 'card mb-2 kanban-card';
                card.dataset.index = index;
                card.dataset.cpf = candidate.person.cpf; // Adicionar CPF do candidato para referência
                
                // Formatar data
                const creationDate = new Date(candidate.recruitment.creation_date);
                const formattedDate = creationDate.toLocaleDateString('pt-BR');
                
                // Verificar se tem resultado de teste
                const hasTestResult = candidate.calculation_result ? true : false;
                
                card.innerHTML = `
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0">${candidate.person.full_name}</h6>
                        <div class="text-warning">
                            <i class="bi bi-star-fill" style="font-size: 0.8rem;"></i>
                            <i class="bi bi-star${hasTestResult ? '-fill' : ''}" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>
                    <div class="text-muted small mb-2">Adicionado em ${formattedDate}</div>
                    ${hasTestResult ? `<div class="badge bg-info text-white mb-2">${candidate.calculation_result.result_name}</div>` : ''}
                    <div class="d-flex justify-content-between align-items-center">
                        <button class="btn btn-sm btn-outline-primary view-candidate-btn" data-index="${index}">
                            <i class="bi bi-eye"></i> Ver
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item view-candidate-btn" href="#" data-index="${index}">Ver detalhes</a></li>
                                <li><a class="dropdown-item move-step-btn" href="#">Mover etapa</a></li>
                            </ul>
                        </div>
                    </div>
                </div>`;
                
                column.appendChild(card);
            }
        });
        
        // Atualizar contadores
        for (const step in counters) {
            const countElement = document.getElementById(`${step.toLowerCase().replace(' ', '-')}-count`);
            if (countElement) {
                countElement.textContent = counters[step];
            }
        }
        
        // Configurar Kanban com jQuery UI
        $('.kanban-column').sortable({
            connectWith: '.kanban-column',
            placeholder: 'card-placeholder',
            cursor: 'move',
            revert: true,
            start: function(event, ui) {
                ui.item.addClass('dragging');
                // Armazenar a coluna de origem para referência
                ui.item.data('originColumn', this);
            },
            stop: function(event, ui) {
                ui.item.removeClass('dragging');
            },
            receive: function(event, ui) {
                const newStep = $(this).data('step');
                const candidateIndex = ui.item.data('index');
                const candidateCpf = ui.item.data('cpf');
                
                // Chamar API para atualizar a etapa do candidato
                updateCandidateStep(candidateCpf, newStep, function(success) {
                    if (success) {
                        // Atualizar dados locais
                        if (candidateIndex !== undefined && candidatesData[candidateIndex]) {
                            candidatesData[candidateIndex].person.step = newStep;
                        }
                        
                        // Atualizar contadores
                        updateKanbanCounters();
                    } else {
                        // Reverter movimento em caso de erro
                        Swal.fire(
                            'Erro!',
                            'Não foi possível atualizar a etapa do candidato. Tente novamente.',
                            'error'
                        );
                        
                        // Reverter para a coluna original
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
    
    // Função para atualizar a etapa do candidato via API
    function updateCandidateStep(candidateCpf, newStep, callback) {
        // Fazer chamada AJAX para a API com a nova rota
        $.ajax({
            url: `/updatestep/${candidateCpf}/${newStep}`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Etapa atualizada com sucesso:', response);
                if (callback) callback(true);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao atualizar etapa:', error);
                if (callback) callback(false);
            }
        });
    }
    
    // Atualizar contadores do Kanban
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
        
        // Atualizar contadores
        for (const step in counters) {
            const countElement = document.getElementById(`${step.toLowerCase().replace(' ', '-')}-count`);
            if (countElement) {
                countElement.textContent = counters[step];
            }
        }
    }
    
    // Inicializar componentes
    initKanban();
    setupCargoCompetenciasChart();
    
    // Event Listeners
    
    // Alternar entre visualizações Kanban e Lista
    document.getElementById('kanbanView').addEventListener('change', function() {
        document.getElementById('kanbanContainer').classList.remove('d-none');
        document.getElementById('listContainer').classList.add('d-none');
    });
    
    document.getElementById('listView').addEventListener('change', function() {
        document.getElementById('kanbanContainer').classList.add('d-none');
        document.getElementById('listContainer').classList.remove('d-none');
    });
    
    // Alternar entre abas
    document.getElementById('candidatosBtn').addEventListener('click', function() {
        document.getElementById('candidatos-content').classList.remove('d-none');
        document.getElementById('editar-vaga-content').classList.add('d-none');
        document.getElementById('roda-competencia-content').classList.add('d-none');
        this.classList.add('btn-primary');
        this.classList.remove('btn-outline-primary');
        document.getElementById('editarVagaBtn').classList.add('btn-outline-primary');
        document.getElementById('editarVagaBtn').classList.remove('btn-primary');
        document.getElementById('rodaCompetenciaBtn').classList.add('btn-outline-primary');
        document.getElementById('rodaCompetenciaBtn').classList.remove('btn-primary');
    });
    
    document.getElementById('editarVagaBtn').addEventListener('click', function() {
        document.getElementById('candidatos-content').classList.add('d-none');
        document.getElementById('editar-vaga-content').classList.remove('d-none');
        document.getElementById('roda-competencia-content').classList.add('d-none');
        this.classList.add('btn-primary');
        this.classList.remove('btn-outline-primary');
        document.getElementById('candidatosBtn').classList.add('btn-outline-primary');
        document.getElementById('candidatosBtn').classList.remove('btn-primary');
        document.getElementById('rodaCompetenciaBtn').classList.add('btn-outline-primary');
        document.getElementById('rodaCompetenciaBtn').classList.remove('btn-primary');
    });
    
    document.getElementById('rodaCompetenciaBtn').addEventListener('click', function() {
        document.getElementById('candidatos-content').classList.add('d-none');
        document.getElementById('editar-vaga-content').classList.add('d-none');
        document.getElementById('roda-competencia-content').classList.remove('d-none');
        this.classList.add('btn-primary');
        this.classList.remove('btn-outline-primary');
        document.getElementById('candidatosBtn').classList.add('btn-outline-primary');
        document.getElementById('candidatosBtn').classList.remove('btn-primary');
        document.getElementById('editarVagaBtn').classList.add('btn-outline-primary');
        document.getElementById('editarVagaBtn').classList.remove('btn-primary');
    });
    
    // Copiar link da vaga
    document.getElementById('copyLinkBtn').addEventListener('click', function() {
        const vacancyLink = `${window.location.origin}/vacancy/apply/${vacancy.reference}`;
        navigator.clipboard.writeText(vacancyLink).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Link Copiado!',
                text: 'O link da vaga foi copiado para a área de transferência.',
                timer: 2000,
                showConfirmButton: false
            });
        });
    });
    
    // Abrir modal de candidato
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-candidate-btn') || e.target.closest('.view-candidate-btn') || e.target.classList.contains('candidate-link') || e.target.closest('.candidate-link')) {
            e.preventDefault();
            
            // Obter índice do candidato
            let candidateIndex = 0;
            if (e.target.dataset.index) {
                candidateIndex = parseInt(e.target.dataset.index);
            } else if (e.target.closest('[data-index]')) {
                candidateIndex = parseInt(e.target.closest('[data-index]').dataset.index);
            } else if (e.target.closest('tr')) {
                candidateIndex = parseInt(e.target.closest('tr').dataset.index);
            }
            
            // Preencher modal com dados do candidato
            fillCandidateModal(candidateIndex);
            
            // Abrir modal
            const candidateModal = new bootstrap.Modal(document.getElementById('candidateModal'));
            candidateModal.show();
        }
    });
    
    // Navegação entre candidatos no modal
    
    document.getElementById('prevCandidateBtn').addEventListener('click', function() {
        currentCandidateIndex = (currentCandidateIndex - 1 + candidatesData.length) % candidatesData.length;
        fillCandidateModal(currentCandidateIndex);
    });
    
    document.getElementById('nextCandidateBtn').addEventListener('click', function() {
        currentCandidateIndex = (currentCandidateIndex + 1) % candidatesData.length;
        fillCandidateModal(currentCandidateIndex);
    });

    // Funcionalidade do editor de anotações (simplificado)
    const newNoteContent = document.getElementById('newNoteContent');
    document.querySelectorAll('.note-btn').forEach(button => {
        button.addEventListener('click', function() {
            const command = this.dataset.command;
            document.execCommand(command, false, null);
            newNoteContent.focus();
        });
    });

    // Adicionar Anotação
    document.getElementById('addNoteBtn').addEventListener('click', function() {
        const noteText = newNoteContent.innerHTML; // Pega o HTML do conteúdo
        if (noteText.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                text: 'A anotação não pode estar vazia.',
            });
            return;
        }

        const candidate = candidatesData[currentCandidateIndex]; 
        // Corrigido para buscar id_person, que é o nome da coluna no banco de dados
        const candidateId = candidate && candidate.person ? candidate.person.id_person : null; 
        
        // Verifica se o id_person é válido antes de prosseguir
        if (candidateId === null || typeof candidateId === 'undefined') {
            console.error('Erro: id_person não pôde ser recuperado para o candidato atual. Verifique se o objeto "person" e sua propriedade "id_person" existem nos dados do candidato.');
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível identificar o candidato para adicionar a anotação. Por favor, tente novamente ou recarregue a página.',
            });
            return; // Impede o envio da requisição se o id_person for inválido
        }

        // Obtendo writer e rule_writer do Blade (PHP)
        const writer = '{{ Auth::user()->name ?? "Usuário Desconhecido" }}';
        const rule_writer = '{{ session('rule') ?? "Desconhecido" }}';

        // Loga os dados a serem enviados para depuração
        console.log('Dados a serem enviados para /api/occurrences:', {
            id_person: candidateId,
            writer: writer,
            rule_writer: rule_writer,
            text: noteText
        });

        $.ajax({
            url: `/api/occurrences`, // Esta rota deve ser definida no seu Laravel
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id_person: candidateId, // Este é o campo que precisa ser enviado
                writer: writer,
                rule_writer: rule_writer,
                text: noteText
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Anotação adicionada com sucesso!',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Adiciona a nova anotação aos dados locais e atualiza a exibição
                const newOccurrence = response.occurrence; // Supondo que a API retorne a ocorrência criada
                if (newOccurrence.rule_writer === 'RH') {
                    if (!candidatesData[currentCandidateIndex].rh_occurrences) {
                        candidatesData[currentCandidateIndex].rh_occurrences = [];
                    }
                    candidatesData[currentCandidateIndex].rh_occurrences.push(newOccurrence);
                    displayOccurrences(candidatesData[currentCandidateIndex].rh_occurrences, 'rhOccurrences');
                } else if (newOccurrence.rule_writer === 'Gestor') {
                    if (!candidatesData[currentCandidateIndex].gestor_occurrences) {
                        candidatesData[currentCandidateIndex].gestor_occurrences = [];
                    }
                    candidatesData[currentCandidateIndex].gestor_occurrences.push(newOccurrence);
                    displayOccurrences(candidatesData[currentCandidateIndex].gestor_occurrences, 'gestorOccurrences');
                }
                
                newNoteContent.innerHTML = ''; // Limpa o editor
            },
            error: function(xhr, status, error) {
                console.error('Erro ao adicionar anotação:', xhr.responseJSON || error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Não foi possível adicionar a anotação. Verifique o console para mais detalhes.',
                });
            }
        });
    });

    // Abrir modal de histórico completo de anotações
    document.getElementById('viewAllHistoryBtn').addEventListener('click', function() {
        const candidate = candidatesData[currentCandidateIndex];
        let allOccurrences = [];

        if (candidate.rh_occurrences) {
            allOccurrences = allOccurrences.concat(candidate.rh_occurrences);
        }
        if (candidate.gestor_occurrences) {
            allOccurrences = allOccurrences.concat(candidate.gestor_occurrences);
        }

        // Classificar todas as anotações por data, da mais recente para a mais antiga
        allOccurrences.sort((a, b) => new Date(b.date) - new Date(a.date));

        const historyContent = document.getElementById('historyContent');
        let historyHtml = '';
        const currentUser = '{{ Auth::user()->name ?? "" }}'; // Pega o nome do usuário logado

        if (allOccurrences.length > 0) {
            // Agrupa as anotações por rule_writer para exibir os títulos "RH" e "Gestor"
            const rhNotes = allOccurrences.filter(note => note.rule_writer === 'RH');
            const gestorNotes = allOccurrences.filter(note => note.rule_writer === 'Gestor');

            if (rhNotes.length > 0) {
                historyHtml += '<h6 class="section-title">RH</h6>';
                rhNotes.forEach(note => {
                    const noteDate = new Date(note.date);
                    const formattedDate = noteDate.toLocaleDateString('pt-BR');
                    const canEdit = note.writer === currentUser;

                    historyHtml += `
                    <div class="annotation-item" data-id-occurrence="${note.id_occourrence}">
                        <div class="annotation-header">
                            <span class="annotation-name">${note.writer}</span>
                            <span class="annotation-date">${formattedDate}</span>
                        </div>
                        <div class="annotation-text-display">
                            <p class="annotation-text">${note.text}</p>
                            ${canEdit ? `<button type="button" class="btn btn-sm btn-outline-secondary edit-note-btn"><i class="bi bi-pencil"></i></button>` : ''}
                        </div>
                        ${canEdit ? `
                        <div class="annotation-edit-area d-none">
                            <textarea class="form-control mb-2">${note.text}</textarea>
                            <button type="button" class="btn btn-success btn-sm save-note-btn">Salvar</button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn">Cancelar</button>
                        </div>
                        ` : ''}
                    </div>`;
                });
            }

            if (gestorNotes.length > 0) {
                historyHtml += '<h6 class="section-title mt-4">Gestor</h6>'; // Adiciona margem superior para separar
                gestorNotes.forEach(note => {
                    const noteDate = new Date(note.date);
                    const formattedDate = noteDate.toLocaleDateString('pt-BR');
                    const canEdit = note.writer === currentUser;

                    historyHtml += `
                    <div class="annotation-item" data-id-occurrence="${note.id_occourrence}">
                        <div class="annotation-header">
                            <span class="annotation-name">${note.writer}</span>
                            <span class="annotation-date">${formattedDate}</span>
                        </div>
                        <div class="annotation-text-display">
                            <p class="annotation-text">${note.text}</p>
                            ${canEdit ? `<button type="button" class="btn btn-sm btn-outline-secondary edit-note-btn"><i class="bi bi-pencil"></i></button>` : ''}
                        </div>
                        ${canEdit ? `
                        <div class="annotation-edit-area d-none">
                            <textarea class="form-control mb-2">${note.text}</textarea>
                            <button type="button" class="btn btn-success btn-sm save-note-btn">Salvar</button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn">Cancelar</button>
                        </div>
                        ` : ''}
                    </div>`;
                });
            }
        } else {
            historyHtml = '<div class="alert alert-info">Nenhuma anotação neste histórico.</div>';
        }
        historyContent.innerHTML = historyHtml;

        const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
        historyModal.show();
    });

    // Event listeners para edição de anotações (delegados para o corpo do documento)
    document.body.addEventListener('click', function(e) {
        // Botão Editar
        if (e.target.classList.contains('edit-note-btn') || e.target.closest('.edit-note-btn')) {
            const editButton = e.target.closest('.edit-note-btn');
            const annotationItem = editButton.closest('.annotation-item');
            const displayArea = annotationItem.querySelector('.annotation-text-display');
            const editArea = annotationItem.querySelector('.annotation-edit-area');
            const textarea = editArea.querySelector('textarea');

            displayArea.classList.add('d-none');
            editArea.classList.remove('d-none');
            textarea.focus();
        }

        // Botão Cancelar
        if (e.target.classList.contains('cancel-edit-btn') || e.target.closest('.cancel-edit-btn')) {
            const cancelButton = e.target.closest('.cancel-edit-btn');
            const annotationItem = cancelButton.closest('.annotation-item');
            const displayArea = annotationItem.querySelector('.annotation-text-display');
            const editArea = annotationItem.querySelector('.annotation-edit-area');
            const textDisplayP = displayArea.querySelector('.annotation-text');
            const textarea = editArea.querySelector('textarea');

            // Restaura o texto original no textarea antes de ocultar
            textarea.value = textDisplayP.innerHTML; 
            displayArea.classList.remove('d-none');
            editArea.classList.add('d-none');
        }

        // Botão Salvar
        if (e.target.classList.contains('save-note-btn') || e.target.closest('.save-note-btn')) {
            const saveButton = e.target.closest('.save-note-btn');
            const annotationItem = saveButton.closest('.annotation-item');
            const id_occourrence = annotationItem.dataset.idOccurrence;
            const newText = annotationItem.querySelector('.annotation-edit-area textarea').value;

            if (newText.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção!',
                    text: 'A anotação não pode ficar vazia.',
                });
                return;
            }

            // Requisição AJAX para atualizar a anotação
            $.ajax({
                url: `/api/occurrences/${id_occourrence}`, // Rota para atualizar (você precisará criar esta no backend)
                method: 'PUT', // Ou PATCH
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    text: newText
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Anotação atualizada com sucesso!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Atualiza o texto exibido na interface
                    const textDisplayP = annotationItem.querySelector('.annotation-text-display .annotation-text');
                    textDisplayP.innerHTML = newText; // Use innerHTML para preservar formatação se houver
                    
                    // Oculta a área de edição e mostra a de visualização
                    annotationItem.querySelector('.annotation-text-display').classList.remove('d-none');
                    annotationItem.querySelector('.annotation-edit-area').classList.add('d-none');

                    // Opcional: Atualizar os dados em candidatesData para refletir a mudança
                    // Isso pode ser complexo se a anotação estiver em rh_occurrences ou gestor_occurrences
                    // Uma forma mais simples é recarregar o modal do candidato ou a página se a complexidade for alta.
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao atualizar anotação:', xhr.responseJSON || error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Não foi possível atualizar a anotação. Verifique o console para mais detalhes.',
                    });
                }
            });
        }
    });
});
</script>
<style>
/* Estilos para o Kanban */
.kanban-column {
    min-height: 300px;
    max-height: 600px;
    overflow-y: auto;
}

.kanban-card {
    cursor: grab;
}

.kanban-card.dragging {
    opacity: 0.5;
}

.card-placeholder {
    border: 2px dashed #ccc;
    margin-bottom: 0.5rem;
    height: 100px;
    border-radius: 0.25rem;
}

/* Estilos para as barras de perfil */
.bar-container {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.label {
    width: 100px;
    font-weight: 500;
}

.bar-wrapper {
    flex-grow: 1;
    height: 20px;
    background-color: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
    margin: 0 15px;
}

.bar-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.5s ease;
}

.percentage {
    width: 50px;
    text-align: right;
    font-weight: 500;
}

/* Estilos para as etapas do processo */
.progress-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
}

.step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    width: 100%;
    height: 2px;
    background-color: #e0e0e0;
    left: 50%;
    z-index: 0;
}

.step-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #fff;
    z-index: 1;
    margin-bottom: 5px;
}

.step-text {
    font-size: 12px;
    color: #666;
    text-align: center;
}

.step-item.active .step-circle {
    background-color: #0d6efd;
}

.step-item.active .step-text {
    color: #0d6efd;
    font-weight: 500;
}

.step-item.active ~ .step-item::after {
    background-color: #e0e0e0;
}

.step-item.active::after {
    background-color: #0d6efd;
}

/* Estilos para o editor de anotações */
.note-editor {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

.note-toolbar {
    background-color: #f8f9fa;
    padding: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.note-toolbar .note-btn {
    border: none;
    background: transparent;
    padding: 0.25rem 0.5rem;
    font-size: 1rem;
    cursor: pointer;
}

.note-toolbar .note-btn:hover {
    background-color: #e9ecef;
}

.note-editable {
    min-height: 100px;
    padding: 0.75rem;
    overflow-y: auto;
}

.note-editable:focus {
    outline: none;
}

/* Estilos para as anotações (inspirado na imagem) */
.annotation-item {
    background-color: #f8f9fa; /* Um cinza claro para o fundo */
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Sombra sutil */
}

.annotation-header {
    margin-bottom: 0.5rem;
    display: flex; /* Para alinhar nome e data na mesma linha */
    justify-content: space-between; /* Para empurrar a data para a direita */
    align-items: center;
}

.annotation-name {
    font-weight: bold;
    color: #333; /* Cor mais escura para o nome */
    margin-right: 0.5rem;
}

.annotation-date {
    font-size: 0.875rem;
    color: #6c757d; /* Cor mais suave para a data */
}

.annotation-text {
    color: #495057; /* Cor do texto da anotação */
    line-height: 1.5;
}

.section-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 1rem;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
}

.annotation-text-display {
    display: flex;
    justify-content: space-between;
    align-items: flex-start; /* Alinha o texto ao topo, botão ao lado */
    gap: 10px; /* Espaço entre o texto e o botão */
}

.annotation-text-display .annotation-text {
    flex-grow: 1;
    margin-bottom: 0; /* Remove margem inferior padrão do parágrafo */
}

/* SweetAlert2 z-index para sobrepor tudo */
.swal2-container {
    z-index: 99999 !important;
}
</style>
<script>
  $(document).ready(function () {
    tinymce.init({
      selector: '#atividadess, #requisitoss',
      plugins: 'lists link image table code',
      toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
      height: 300,
      language: 'pt_BR',
      content_langs: [
        { title: 'Portuguese', code: 'pt' }
      ]
    });
  });
</script>


@endsection