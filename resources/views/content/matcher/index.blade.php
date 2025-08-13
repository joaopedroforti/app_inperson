@extends('layouts/layoutMaster')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <!-- Perfil 1 -->
                        <div class="col-md-5">
                            <h5 class="text-muted mb-4">Perfil 1</h5>
                            
                            <div class="mb-3">
                                <label for="entity1-type" class="form-label text-muted">Tipo</label>
                                <select class="form-select" id="entity1-type" name="entity1-type">
                                    <option value="">Selecionar</option>
                                    <option value="person">Pessoa</option>
                                    <option value="job">Cargo</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <select class="form-select" id="entity1" name="entity1" disabled>
                                    <option value="">Selecionar</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Divisor central com texto -->
                        <div class="col-md-2 text-center position-relative">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <div class="position-relative">
                                    <div class="border-end position-absolute h-100" style="left: 50%; top: -50px; bottom: -50px;"></div>
                                    <div class="text-center position-relative bg-white px-2 text-muted">
                                        comparar<br>com
                                    </div>
                                    <div class="border-end position-absolute h-100" style="left: 50%; top: 50px; bottom: 50px;"></div>
                                   
                                </div>
                            </div>
                        </div>
                        
                        <!-- Perfil 2 -->
                        <div class="col-md-5">
                            <h5 class="text-muted mb-4">Perfil 2</h5>
                            
                            <div class="mb-3">
                                <label for="entity2-type" class="form-label text-muted">Tipo</label>
                                <select class="form-select" id="entity2-type" name="entity2-type">
                                    <option value="">Selecionar</option>
                                    <option value="person">Pessoa</option>
                                    <option value="job">Cargo</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <select class="form-select" id="entity2" name="entity2" disabled>
                                    <option value="">Selecionar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões de Comparação e Impressão -->
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary px-4 me-2" id="compare-button" style="background-color: #6366F1; border-color: #6366F1;">Comparar</button>
                     
                    </div>
                </div>
            </div>
        </div>

    
    <!-- Card para exibir os dados das entidades comparadas -->
    <div class="row justify-content-center mt-4">
        <div class="col-md">
            <div class="card shadow-sm d-none" id="comparison-result">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Resultado Matcher</h5>
                    <!--  <button type="button" class="btn btn-outline-primary px-4" id="print-button">
                            <i class="mdi mdi-printer me-1 d-none"></i>Imprimir
                        </button>-->
                </div>
                <div class="card-body">
                    <!-- Estilos para as barras -->
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
                        
                        /* Estilos para impressão */
                        @media print {
                            body * {
                                visibility: hidden;
                            }
                            
                            #printable-content, #printable-content * {
                                visibility: visible;
                            }
                            
                            #printable-content {
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                            }
                            
                            .no-print {
                                display: none !important;
                            }
                        }
                    </style>
                    
                    <div id="printable-content">
                        <div class="row">
                            <!-- Dados da Entidade 1 -->
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold" id="entity1-title">Entidade 1</h6>
                                <div id="entity1-data">
                                    <!-- Dados serão inseridos via JavaScript -->
                                </div>
                                <!-- Gráfico de barras para Entidade 1 -->
                                <div id="entity1-chart" style="margin-top: 20px;">
                                    <!-- Barras serão inseridas via JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Dados da Entidade 2 -->
                            <div class="col-md-6">
                                <h6 class="fw-bold" id="entity2-title">Entidade 2</h6>
                                <div id="entity2-data">
                                    <!-- Dados serão inseridos via JavaScript -->
                                </div>
                                <!-- Gráfico de barras para Entidade 2 -->
                                <div id="entity2-chart" style="margin-top: 20px;">
                                    <!-- Barras serão inseridas via JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gráfico de Radar para comparação de habilidades -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-center">Roda de Competência</h6>
                                <div id="radar-chart" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        // Dados dinâmicos vindos do controller
const persons = @json($persons);
const jobs = @json($jobs);

// Variável para armazenar o gráfico de radar
let radarChart = null;

// Função para atualizar os selects de entidades
function updateEntitySelect(selectId, entities, isJob) {
    const selectElement = $(selectId);
    selectElement.empty();
    selectElement.append('<option value="">Selecionar</option>');

    if (isJob) {
        entities.forEach((entity, index) => {
            selectElement.append(`<option value="${index}">${entity.job.description}</option>`);
        });
    } else {
        entities.forEach((entity, index) => {
            selectElement.append(`<option value="${index}">${entity.full_name}</option>`);
        });
    }

    selectElement.prop('disabled', entities.length === 0);
}

// Função para carregar entidades com base no tipo selecionado individualmente
function loadEntities(changed) {
    if (changed === 'entity1') {
        const entityType1 = $('#entity1-type').val();

        if (entityType1 === 'person') {
            updateEntitySelect('#entity1', persons, false);
        } else if (entityType1 === 'job') {
            updateEntitySelect('#entity1', jobs, true);
        } else {
            $('#entity1').empty().append('<option value="">Selecionar</option>');
            $('#entity1').prop('disabled', true);
        }
    }

    if (changed === 'entity2') {
        const entityType2 = $('#entity2-type').val();

        if (entityType2 === 'person') {
            updateEntitySelect('#entity2', persons, false);
        } else if (entityType2 === 'job') {
            updateEntitySelect('#entity2', jobs, true);
        } else {
            $('#entity2').empty().append('<option value="">Selecionar</option>');
            $('#entity2').prop('disabled', true);
        }
    }
}

// Listeners corrigidos para chamada individual
$('#entity1-type').change(function() {
    loadEntities('entity1');
    $('#comparison-result').addClass('d-none');
});

$('#entity2-type').change(function() {
    loadEntities('entity2');
    $('#comparison-result').addClass('d-none');
});


        // Função para criar barras CSS
        function createBarChart(entityData, containerId) {
            // Preparar dados para as barras
            const attributes = {
                decision: parseFloat(entityData.attributes.decision),
                detail: parseFloat(entityData.attributes.detail),
                enthusiasm: parseFloat(entityData.attributes.enthusiasm),
                relational: parseFloat(entityData.attributes.relational)
            };
            
            // Cores para cada categoria
            const colors = {
                decision: '#9400D3',
                detail: '#008B8B',
                enthusiasm: '#FF69B4',
                relational: '#0077B6'
            };
            
            // Criar HTML para as barras
            let html = '<div style="padding: 15px;">';
            
            // Decisão
            html += `
                <div class="bar-container">
                    <div class="label">Decisão</div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: ${attributes.decision}%; background-color: ${colors.decision};"></div>
                    </div>
                    <div class="percentage">${attributes.decision.toFixed(2)}%</div>
                </div>
            `;
            
            // Detalhismo
            html += `
                <div class="bar-container">
                    <div class="label">Detalhismo</div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: ${attributes.detail}%; background-color: ${colors.detail};"></div>
                    </div>
                    <div class="percentage">${attributes.detail.toFixed(2)}%</div>
                </div>
            `;
            
            // Entusiasmo
            html += `
                <div class="bar-container">
                    <div class="label">Entusiasmo</div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: ${attributes.enthusiasm}%; background-color: ${colors.enthusiasm};"></div>
                    </div>
                    <div class="percentage">${attributes.enthusiasm.toFixed(2)}%</div>
                </div>
            `;
            
            // Relacional
            html += `
                <div class="bar-container">
                    <div class="label">Relacional</div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: ${attributes.relational}%; background-color: ${colors.relational};"></div>
                    </div>
                    <div class="percentage">${attributes.relational.toFixed(2)}%</div>
                </div>
            `;
            
            html += '</div>';
            
            // Inserir HTML no container
            $(containerId).html(html);
        }
        
        // Função para criar gráfico de radar
        function createRadarChart(entity1Data, entity2Data, entity1Type, entity2Type) {
            // Destruir gráfico anterior se existir
            if (radarChart) {
                radarChart.destroy();
            }
            
            // Obter as 10 primeiras habilidades para o gráfico de radar
            const skills1 = entity1Data.skills.slice(0, 1000);
            const skills2 = entity2Data.skills.slice(0, 1000);
            
            // Preparar categorias e dados
            const categories = skills1.map(skill => skill.name);
            const series = [
                {
                    name: entity1Type === 'person' ? entity1Data.full_name : entity1Data.job.description,
                    data: skills1.map(skill => parseFloat(skill.value))
                },
                {
                    name: entity2Type === 'person' ? entity2Data.full_name : entity2Data.job.description,
                    data: skills2.map(skill => parseFloat(skill.value))
                }
            ];
            
            // Configurações do gráfico
            const options = {
                chart: {
                    height: 800,
                    type: 'radar',
                    toolbar: {
                        show: false
                    }
                },
                series: series,
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    min: 0,
                    max: 100,
                    tickAmount: 5
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2
                },
                fill: {
                    opacity: 0.2
                },
                markers: {
                    size: 4
                },
                colors: ['#6366F1', '#10B981']
            };
            
            // Criar o gráfico
            radarChart = new ApexCharts(document.querySelector('#radar-chart'), options);
            radarChart.render();
            
            return radarChart;
        }

        // Função para exibir os dados de uma entidade
        function displayEntityData(entityData, containerId, titleId, isPerson) {
            let title = '';
            let html = '<div class="mt-3">';
            
            if (isPerson) {
                title = entityData.full_name;
                html += `<p><strong>Perfil:</strong> ${entityData.result_name}</p>`;
            } else {
                title = entityData.job.description;
                html += `<p><strong>Perfil:</strong> ${entityData.result_name}</p>`;
            }
            
            html += '</div>';
            
            $(titleId).text(title);
            $(containerId).html(html);
        }

        // Event listeners para os selects de tipo
        $('#entity1-type, #entity2-type').change(function() {
            loadEntities();
            
            // Esconde o card de resultado quando mudar a seleção
            $('#comparison-result').addClass('d-none');
        });

        // Inicializa os selects (ambos desabilitados inicialmente)
        $('#entity1').prop('disabled', true);
        $('#entity2').prop('disabled', true);
        
        // Botão de comparação
        $('#compare-button').click(function() {
            const entity1Type = $('#entity1-type').val();
            const entity2Type = $('#entity2-type').val();
            const entity1Index = $('#entity1').val();
            const entity2Index = $('#entity2').val();
            
            if (entity1Index === "" || entity2Index === "") {
                alert('Selecione uma pessoa ou cargo em ambos os campos.');
                return;
            }
            
            // Obter os dados das entidades selecionadas
            const entity1Data = entity1Type === 'person' ? persons[entity1Index] : jobs[entity1Index];
            const entity2Data = entity2Type === 'person' ? persons[entity2Index] : jobs[entity2Index];
            
            // Exibir os dados no card
            displayEntityData(entity1Data, '#entity1-data', '#entity1-title', entity1Type === 'person');
            displayEntityData(entity2Data, '#entity2-data', '#entity2-title', entity2Type === 'person');
            
            // Criar as barras CSS
            createBarChart(entity1Data, '#entity1-chart');
            createBarChart(entity2Data, '#entity2-chart');
            
            // Criar o gráfico de radar para comparação de habilidades
            createRadarChart(entity1Data, entity2Data, entity1Type, entity2Type);
            
            // Mostrar o card de resultado
            $('#comparison-result').removeClass('d-none');
        });
        
        // Botão de impressão
        $('#print-button').click(function() {
            // Verifica se há resultados para imprimir
            if ($('#comparison-result').hasClass('d-none')) {
                alert('Realize uma comparação antes de imprimir.');
                return;
            }
            
            // Adiciona título para a impressão
            const entity1Title = $('#entity1-title').text();
            const entity2Title = $('#entity2-title').text();
            const printTitle = `<div class="d-none d-print-block text-center mb-4"><h3>Comparação: ${entity1Title} vs ${entity2Title}</h3></div>`;
            $('#printable-content').prepend(printTitle);
            
            // Executa a impressão
            window.print();
            
            // Remove o título após a impressão
            setTimeout(function() {
                $('#printable-content .d-print-block').remove();
            }, 500);
        });
    });
</script>
@endsection
