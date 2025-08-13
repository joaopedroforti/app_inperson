@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')




<style>

.adjective_select {
    display: none;
}

.inperson_logo {
    width: 30%;
}



.adjective_name {
    cursor: pointer;
    margin: auto;
}

.btn_adjective,
.btn_adjective:hover,
.btn_adjective_active {
    margin: 10px;
    padding: 10px;
    cursor: pointer;
    border-style: solid;
    border-color: #E7E7E7;
    border-radius: 20px;
    text-align: center; /* Adiciona centralização do texto */
        display: flex; /* Adiciona flex container */
        align-items: center; /* Centraliza verticalmente */
        justify-content: center; /* Centraliza horizontalmente */
}

.btn_adjective {
    background-color: #F1F1F1;
    color: gray;
}

.btn_adjective:hover {
    background-color: #508dfd;
    color: white;
}

.btn_adjective_active {
    background-color: #508dfd;
    color: white;
}
/* Estilo do input range */
.form-range {
    -webkit-appearance: none;
    width: 100%;
    height: 8px;
    background: #ddd;
    outline: none;
    opacity: 0.7;
    transition: opacity .15s ease-in-out;
    position: relative;
}

/* Estilo para o thumb (botão deslizante) */
.form-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #7062C5;
    border-radius: 50%;
    cursor: pointer;
    position: relative;
    z-index: 2;
}

.form-range::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #7062C5;
    border-radius: 50%;
    cursor: pointer;
}

/* Pin markers */
.form-range::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 2px;
    background: transparent;
    z-index: 1;
}

.form-range::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: space-between;
    pointer-events: none;
}

.form-range::after div {
    width: 2px;
    height: 16px; /* Tamanho do marcador */
    background-color: #4963EF; /* Cor dos marcadores */
    position: absolute;
    top: -8px; /* Posiciona o marcador em cima da linha */
}

/* Posicionamento dos pin markers para cada step (1, 2, 3, 4, 5) */
.form-range::after div:nth-child(1) { left: 0%; }
.form-range::after div:nth-child(2) { left: 25%; }
.form-range::after div:nth-child(3) { left: 50%; }
.form-range::after div:nth-child(4) { left: 75%; }
.form-range::after div:nth-child(5) { left: 100%; }


.percent {
    color: #6c7dff;
    font-size: 15px;
    font-weight: 600;
}
</style>



<!--Formulário de cadastro -->


<form method="POST" action="">
@csrf
  <!-- Header -->
  <div class="card mb-4">
  <div class="card-body">
      <div class="row align-items-start justify-content-between">
        <div class="col-md-6 d-flex flex-column justify-content-between">
          <h4 class="mb-2">Cadastro Cargo</h4>
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



  <!-- Section cadastro -->

  <!-- DADOS PESSOAIS -->
  <div class="card mb-4">
  <h5 class="card-header">
</h5>

    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Nome do Cargo</label>
          <input required name="description" type="text" class="form-control">
        </div>

        <div class="col-md-4">
      <label class="form-label">Departamento</label>
      <select required name="id_department" class="form-select">
        @foreach ($departments as $department)
        <option selected value="">Selecione um departamento</option>
          <option value="{{ $department->id_department }}">{{ $department->description }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-md-4">
          <label class="form-label">Senioridade</label>
          <select required="" class="form-select control" id="seniority" name="seniority">
    <option value=""></option>
    <option value="Trainee">Trainee</option>
<option value="Estagiário">Estagiário</option>
<option value="Júnior">Júnior</option>
<option value="Pleno">Pleno</option>
<option value="Sênior">Sênior</option>
<option value="Especialista">Especialista</option>

    </select>







        </div>











        <div class="col-12">
  <label class="form-label">Descrição do Cargo</label>
  <textarea required name="resume" class="form-control"></textarea>
</div>

<div class="col-12">
  <label class="form-label">Principais Atividades</label>
  <textarea name="activities" class="form-control tinymce"></textarea>
</div>

<div class="col-12">
  <label class="form-label">Requisitos</label>
  <textarea name="requirements" class="form-control tinymce"></textarea>
</div>
    </div>
        

      </div>



<br>
<!--Formulário de cadastro -->
<div class="align-items-start">
</div>
               <div class="container">
                  <div id="div-container" class="row">
                     <div id="div1" class="col-12">

       <div class="container d-flex justify-content-between align-items-end">
   
</div>
                    </div>
                     <div id="div3" class="col-12">
                     <div class="container text-center mb-3" >

                     











   


        <!-- Div principal com largura de 70% e centralizada horizontalmente -->

        <h1>Competências</h1>
        <p>Ajuste as barras, até encontrar o ponto de equilibrio entre as competências desejadas.</p>
<br>
        <?php foreach ($skills as $index => $skill): ?>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="row">
                <div class="col-3">
                    <?= $skill['skill2description'] ?> <span class="percent">(<span id="percent1-<?= $index ?>">0</span>%)</span>
                </div>


                

                <div class="col-md-6">





                <div class="container">
    <input type="range" class="form-range" min="1" max="5" step="1" id="range1-<?= $index ?>" name="<?= $skill['skill1id'] ?>" min="1" max="5" step="1" value="<?= $skill['skill1value'] ?? 3 ?>">
    <div class="d-flex justify-content-between" style="
    margin-top: -20px;
">
      <span style="
    color: #b3d1ff;
    font-weight: bolder;
    font-size: 15px;
">|</span>
      <span style="
    color: #b3d1ff;
    font-weight: bolder;
    font-size: 15px;
">|</span>
      <span style="
    color: #b3d1ff;
    font-weight: bolder;
    font-size: 15px;
">|</span>
      <span style="
    color: #b3d1ff;
    font-weight: bolder;
    font-size: 15px;
">|</span>
      <span style="
    color: #b3d1ff;
    font-weight: bolder;
    font-size: 15px;
">|</span>
    </div>
</div>

















                </div>




                <div class="col-3">
                    <?= $skill['skill1description'] ?> <span class="percent">(<span id="percent2-<?= $index ?>">100</span>%)</span>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.form-range').forEach(function(slider) {
            const index = slider.id.split('-')[1];
            const value = parseInt(slider.value, 10);

            // Mapear o valor do slider (1-5) para porcentagem (0-100)
            const percent1 = Math.round(((value - 1) / 4) * 100);
            const percent2 = 100 - percent1;

            // Define as porcentagens iniciais
            document.getElementById('percent1-' + index).textContent = percent1;
            document.getElementById('percent2-' + index).textContent = percent2;

            // Atualiza as porcentagens quando o slider é movido
            slider.addEventListener('input', function() {
                const value = parseInt(this.value, 10);
                const percent1 = Math.round(((value - 1) / 4) * 100);
                const percent2 = 100 - percent1;

                // Atualiza o texto das porcentagens
                document.getElementById('percent1-' + index).textContent = percent1;
                document.getElementById('percent2-' + index).textContent = percent2;
            });
        });
    });
</script>









<br>

<h1>Adjetivos</h1>
        <p>Selecione os adjetivos desejados.</p>
<br>


<div class="row d-flex justify-content-center">

    <?php foreach ($adjectives as $adjective): ?>
        <div class="col-md-auto btn_adjective d-flex align-items-center justify-content-center" 
             id="adjective_<?= $adjective['id'] ?>" 
             onclick="toggleAdjective('adjective_<?= $adjective['id'] ?>', event)">
            <input type='checkbox' class="adjective_select" name="adjectives[]" value="<?= $adjective['id'] ?>" id="<?= $adjective['id'] ?>">
            <label class="adjective_name ml-2"><?= $adjective['description'] ?></label>
        </div>
    <?php endforeach; ?>

</div>





<br>

<h1>Resultado</h1>
        <p>Pré Visualização do resultado.</p>
        <button type="button" id="btn-atualizar" class="btn btn-primary">Gerar Teste de Perfil</button>
        <div style="display: none;" id="payload-container"></div>

        <div id="result-container" class="mt-3"></div>
        <div class="row">
                     
                     <div id="atributes" class="apex-charts"></div>
                  </div>
                  <div class="row">
                     <div id="skills" class="apex-charts"></div>
                  </div>




                              
<input hidden name="response" id="response"> 
    </form>





    </div>
                     </div>
                     <div id="div4" class="col-12">
    <div class="container text-center" style="width: 70%;">
        <!-- Div principal com largura de 70% e centralizada horizontalmente -->

    
    </div>
</div>




                     
                  </div>
               </div>
            </div>
         </div>

      </div>
   </div>
</div>
</div>
</div>
    <script>
   function verificarResponse() {
            // Seleciona os elementos necessários
            const responseInput = document.getElementById('response');
            const btnAtualizar = document.getElementById('btn-atualizar');
            const btnSalvar = document.getElementById('btn-salvar');

            // Verifica se o input 'response' está vazio
            if (responseInput.value === '') {
                // Se estiver vazio, exibe "Gerar Teste de Perfil" e esconde o botão 'Salvar'
                btnAtualizar.textContent = 'Atualizar';
                btnSalvar.style.display = 'inline-block';
            } else {
                // Se tiver conteúdo, exibe "Atualizar" e mostra o botão 'Salvar'
                btnAtualizar.textContent = 'Atualizar';
                btnSalvar.style.display = '';
            }
        }

    </script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- third party end -->

<!-- Gráfico Radar -->
<script>
    let radarChart;


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
    function renderRadarChart(categories, values) {
        // Organiza os valores conforme a ordem das categorias fixas
        const orderedValues = fixedCategories.map(category => {
            const index = categories.indexOf(category);
            return index !== -1 ? values[index] : 0; // Se não encontrar a categoria, define o valor como 0
        });

        const options = {
            series: [{
                name: '',
                data: orderedValues
            }],
            chart: {
                height: 500,
                type: 'radar',
            },
            yaxis: {
                max: 100 // Define o valor máximo do eixo y como 100
            },
            xaxis: {
                categories: fixedCategories // Usa as categorias fixas
            }
        };

        if (radarChart) {
            radarChart.updateOptions(options);
        } else {
            radarChart = new ApexCharts(document.querySelector("#skills"), options);
            radarChart.render();
        }
    }
</script>

<!-- Gráfico de Barras -->
<script>
    let barChart;

    function renderBarChart(data) {
        const options = {
            series: [{
                data: data // Usando os dados em porcentagem
            }],
            chart: {
                type: 'bar',
                height: 200
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                },
                legend: {
                    show: false // Remover a legenda
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val + '%'; // Adicionar '%' aos rótulos
                }
            },
            grid: {},
            xaxis: {
                min: 0,
                max: 100, // Definir o intervalo do eixo Y de 0 a 100
                categories: ['Decisão', 'Detalhismo', 'Entusiasmo', 'Relacional'],
                labels: {
                    formatter: function(val) {
                        return ''; // Adicionar '%' aos rótulos do eixo X
                    }
                }
            }
        };

        if (barChart) {
            barChart.updateOptions(options);
        } else {
            barChart = new ApexCharts(document.querySelector("#atributes"), options);
            barChart.render();
        }
    }
</script>

<!-- Script para Requisição e Atualização -->


<script>
    document.getElementById('btn-atualizar').addEventListener('click', function() {
        // Coleta os IDs dos adjetivos selecionados
        const selectedAdjectives = Array.from(document.querySelectorAll('.adjective_select:checked')).map(checkbox => parseInt(checkbox.value));

        // Coleta os valores dos sliders
        const skillValues = Array.from(document.querySelectorAll('.form-range')).map(slider => ({
            id: parseInt(slider.name),
            points: parseInt(slider.value)
        }));

        // Cria o payload no formato desejado
        const payload = {
            adjectives: selectedAdjectives,
            skills: skillValues
        };

        // Exibe o payload no console para verificação
        console.log('Payload:', JSON.stringify(payload, null, 2));

        // Exibe o payload na tela
        document.getElementById('payload-container').innerHTML = '<pre>' + JSON.stringify(payload, null, 2) + '</pre>';

        // Envia a requisição POST usando fetch
        fetch('https://api1.inperson.com.br/profiles/roles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            // Dados recebidos da API
            const { decision, detail, enthusiasm, relational, skills } = data;

            // Atualiza o gráfico radar
            const radarCategories = skills.map(skill => skill.name);
            const radarValues = skills.map(skill => parseFloat(skill.value));
            renderRadarChart(radarCategories, radarValues);

            // Atualiza o gráfico de barras
            const barData = [parseFloat(decision), parseFloat(detail), parseFloat(enthusiasm), parseFloat(relational)];
            renderBarChart(barData);

            // Insere o valor retornado no input #response
            document.getElementById('response').value = JSON.stringify(data);

            // Exibe o resultado na div 'result-container' (opcional)
            // document.getElementById('result-container').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })

        verificarResponse();
    });
</script>




<script>
   const divs = document.querySelectorAll("#div-container .col-12");
   const btnAvancar = document.querySelector("#btn-avancar");
   let currentDivIndex = 0;
   
   // Função para validar os campos na div atual
   function validarCampos() {
       const campos = divs[currentDivIndex].querySelectorAll('input[required], select[required], textarea[required]');
       for (let campo of campos) {
           if (!campo.value) {
               return false; // Se algum campo estiver vazio, retorna falso
           }
       }
       // Verificar se há pelo menos uma opção selecionada para cada grupo de radio
       const gruposRadio = divs[currentDivIndex].querySelectorAll('input[type="radio"]');
       for (let grupo of gruposRadio) {
           const opcoesSelecionadas = divs[currentDivIndex].querySelectorAll(`input[type="radio"][name="${grupo.name}"]:checked`);
           if (opcoesSelecionadas.length === 0) {
               return false; // Se não houver opção selecionada para algum grupo de radio, retorna falso
           }
       }
       return true; // Retorna verdadeiro se todos os campos estiverem preenchidos
   }
   
   btnAvancar.addEventListener("click", () => {
       // Validar campos antes de avançar
       window.scrollTo(0, 0);
       if (validarCampos()) {
           if (currentDivIndex < divs.length - 1) {
               divs[currentDivIndex].classList.add("d-none");
               currentDivIndex++;
               divs[currentDivIndex].classList.remove("d-none");
               if(currentDivIndex === divs.length - 1){

                   btnAvancar.style.display = "none"; // Oculta o botão na última div
               }
           } else {
               // Alterar texto do botão para "Finalizar"
  
           }
       } else {
    $('#incomplete').modal('show'); // Abre o modal "incomplete"
}
   });

   // Selecionar o terceiro select em todas as divs
   const selects = document.querySelectorAll("#div-container select");
   selects.forEach(select => {
       if (select.id !== 'department') {
           select.value = '3'; // Seleciona a opção com value '3'
       }
   });
</script>
<script>
    function validarCheckbox() {
        var checkboxes = document.querySelectorAll('.adjective_select:checked');
        if (checkboxes.length < 10) {
            Swal.fire({
                icon: 'error',

                text: 'Por favor, selecione no minímo 10 adjetivos.'
            });
            return false;
        }
        return true;
    }
</script>
<script>
    function toggleAdjective(elementId, event) {
        // Adiciona a classe 'btn_adjective_active' à div clicada
        var element = document.getElementById(elementId);
        element.classList.toggle('btn_adjective_active');

        // Encontra a checkbox correspondente
        var checkbox = element.querySelector('.adjective_select');

        // Inverte o estado atual da checkbox
        checkbox.checked = !checkbox.checked;

        // Impede que o evento de clique se propague para a hierarquia do DOM
        event.stopPropagation();
    }
</script>
  <!-- Section cadastro -->




<script src="{{ asset('assets/functions/buscaCep.js') }}"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>

<script src="https://cdn.tiny.cloud/1/n7b8zpu0tl0lg9ka80vagfoo3vtu97zk3rwall7rpfhg95q7/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea.tinymce',
    height: 300,
    menubar: false,
    plugins: 'lists link image table code help wordcount',
    toolbar: 'undo redo | formatselect | bold italic backcolor | ' +
             'alignleft aligncenter alignright alignjustify | ' +
             'bullist numlist outdent indent | removeformat | help',
    language: 'pt_BR'
  });
</script>

@endsection
