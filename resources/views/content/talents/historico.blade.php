@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')

<form method="POST" action="/candidate/new" id="employeeForm">
@csrf

<div class="card mb-4">
  <div class="card-body">
    <div class="row align-items-start justify-content-between">

      <div class="col-auto text-center">
  <div style="background-color: #e8ddfb; border-radius: 100px; width: 100px; height: 100px;">
    <img
      id="profile_pic"
      name="profile_pic"
   src="{{ $person->profile_pic_base64 ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8lRbS7eKYzDq-Ftxc1p8G_TTw2unWBMEYUw&s' }}"
      alt="Foto de Perfil"
      class="rounded-circle"
      style="width: 100px; height: 100px; object-fit: cover;"
    />
  </div>
  
  <input
    type="file"
    id="profile_pic_input"
    accept="image/*"
    onchange="uploadProfilePic(this)"
    hidden
  />
  <input type="hidden" name="profile_pic_base64" id="profile_pic_base64" value="{{ $person->profile_pic_base64 ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8lRbS7eKYzDq-Ftxc1p8G_TTw2unWBMEYUw&s' }}">

 
  <small class="text-muted d-block mt-1">
    <a href="javascript:void(0)" onclick="document.getElementById('profile_pic_input').click()">Adicionar</a> |
    <a href="javascript:void(0)" onclick="resetProfilePic()">Excluir</a>
  </small>
</div>
<input hidden name="id_person" id="reference" value="{{ $person->id_person}}">




      <div class="col-md-6 d-flex flex-column justify-content-between" style="min-height: 100px;">
        <h4 class="mb-2">{{ $person->full_name ?? 'Cadastro Colaborador' }}</h4>

        <div class="d-flex align-items-center gap-3 mt-auto flex-wrap">
        <div class="text-muted d-flex align-items-center gap-1 cursor-pointer">
            <i class="mdi mdi-account-outline"></i>
            <a href="{{ route('Perfil Candidato', Crypt::encryptString($person['id_person'])) }}"><span><strong>Cadastro</strong></span></a>
          </div>
         
          <div class="text-muted d-flex align-items-center gap-1 cursor-pointer">
            <i class="mdi mdi-account-outline"></i>
            <a href="{{ route('Perfil Comportamental Candidato', Crypt::encryptString($person['id_person'])) }}"><span><strong>Perfil Comportamental</strong></span></a>
          </div>
         
         <button class="btn btn-primary d-flex align-items-center gap-1 px-3 py-2">
            <i class="mdi mdi-file-document-outline"></i>
            <span><strong>Recrutamento</strong></span>
          </button>
         

        </div>
      </div>

      <div class="col-md-3 d-flex justify-content-end">
        <div class="d-flex align-items-start gap-2">
          <button class="btn btn-primary" type="submit">Salvar</button>
          <a href="{{ url()->previous() }}" class="btn btn-outline-danger">Cancelar</a>

        </div>
      </div>

    </div>
  </div>
</div>
    

 
 <div class="card mb-4">
  <div class="card-body">
   {{-- ======== INÍCIO: HISTÓRICO DE RECRUTAMENTO ======== --}}
   @php
     $recs = isset($recruitments) ? $recruitments : collect();
   @endphp

   @if($recs->isEmpty())
     <div class="d-flex align-items-center justify-content-center" style="min-height: 160px;">
       <div class="text-center">
         <i class="mdi mdi-inbox" style="font-size: 2rem;"></i>
         <p class="mb-0 mt-2 text-muted">Nenhum registro de recrutamento encontrado para este candidato.</p>
       </div>
     </div>
   @else
     @foreach($recs as $rec)
       @php
         // Questions: array de {question, response}
         $questions = null;
         if (!empty($rec->questions)) {
           try { $questions = json_decode($rec->questions, true); } catch (\Throwable $e) { $questions = null; }
         }

         // Curriculum: JSON arbitrário (estrutura dinâmica)
         $curriculum = null;
         if (!empty($rec->curriculum)) {
           try { $curriculum = json_decode($rec->curriculum, true); } catch (\Throwable $e) { $curriculum = null; }
         }
       @endphp

       <div class="border rounded mb-4">
         <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
           <div class="mb-2 mb-md-0">
             <h6 class="mb-1">{{ $rec->vacancy_description ?? 'Vaga sem descrição' }}</h6>
             <small class="text-muted">
               Candidatura #{{ $rec->id_recruitment ?? $rec->id ?? '—' }}
               • Criado em {{ optional($rec->creation_date)->format('d/m/Y H:i') ?? '—' }}
             </small>
           </div>
           @if(!empty($rec->status))
             <span class="badge bg-primary">{{ $rec->status }}</span>
           @endif
         </div>

         <div class="p-3 pt-0">
           {{-- QUESTIONS --}}
           <div class="mb-4">
             <h6 class="mb-3">Perguntas & Respostas</h6>
             @if (is_array($questions) && count($questions))
               <div class="table-responsive">
                 <table class="table table-striped align-middle mb-0">
                   <thead>
                     <tr>
                       <th style="width:55%;">Pergunta</th>
                       <th>Resposta</th>
                     </tr>
                   </thead>
                   <tbody>
                     @foreach ($questions as $q)
                       <tr>
                         <td>{{ $q['question'] ?? '—' }}</td>
                         <td>{{ $q['response'] ?? '—' }}</td>
                       </tr>
                     @endforeach
                   </tbody>
                 </table>
               </div>
             @else
               <div class="alert alert-secondary mb-0">Nenhuma pergunta/resposta registrada.</div>
             @endif
           </div>

           {{-- CURRICULUM (JSON genérico dinâmico) --}}
           <div>
             <h6 class="mb-3">Currículo</h6>
             @if (is_array($curriculum) && count($curriculum))
               @php
                 // Renderização genérica e recursiva do JSON (sem assumir chaves):
                 $renderJsonGeneric = function ($data) use (&$renderJsonGeneric) {
                     if (is_array($data)) {
                         $isAssoc = array_keys($data) !== range(0, count($data) - 1);
                         echo '<ul class="list-group list-group-flush">';
                         foreach ($data as $key => $value) {
                             echo '<li class="list-group-item">';
                             if ($isAssoc && !is_int($key)) {
                                 echo '<strong>'.e($key).':</strong> ';
                             }
                             if (is_array($value)) {
                                 $renderJsonGeneric($value);
                             } else {
                                 echo e($value);
                             }
                             echo '</li>';
                         }
                         echo '</ul>';
                     } else {
                         echo e($data);
                     }
                 };
               @endphp
               <div class="border rounded">
                 {!! $renderJsonGeneric($curriculum) !!}
               </div>
             @else
               <div class="alert alert-secondary mb-0">Currículo não informado ou inválido.</div>
             @endif
           </div>
         </div>

         
       </div>
     @endforeach
   @endif
   {{-- ======== FIM: HISTÓRICO DE RECRUTAMENTO ======== --}}
  </div>
</div>



</form>

@endsection

@section('page-script')
<script>
  $(document).ready(function () {
    const departmentId = '{{ $person->department ?? '' }}';
    const selectedJobId = '{{ $person->role ?? '' }}';

    // Define o departamento já selecionado e carrega os cargos correspondentes
    if (departmentId) {
      $('#department-select').val(departmentId);
      carregarCargos(departmentId, selectedJobId);
    }

    // Ao trocar o departamento manualmente
    $('#department-select').on('change', function () {
      const depId = $(this).val();
      carregarCargos(depId, null); // Zera o cargo selecionado
    });

    function carregarCargos(departmentId, selectedJobId = null) {
      const jobSelect = $('#job-select');
      jobSelect.html('<option value="">Carregando...</option>');

      if (departmentId) {
        $.ajax({
          url: `/departments/${departmentId}/job-roles`,
          method: 'GET',
          success: function (data) {
            jobSelect.html('<option value="">Selecionar</option>');
            data.forEach(job => {
              const isSelected = selectedJobId && String(selectedJobId) === String(job.id_job);
              const option = `<option value="${job.id_job}" ${isSelected ? 'selected' : ''}>${job.description}</option>`;
              jobSelect.append(option);
            });
          },
          error: function () {
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
    }
  });
</script>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>
@endsection
