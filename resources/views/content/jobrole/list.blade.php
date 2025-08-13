@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')

<!-- CSS Personalizado -->
<link href="{{ asset('assets/css/custom-table.css') }}" rel="stylesheet" />

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<!-- Formulário de cadastro -->
<form method="POST" action="">
  @csrf

  <div class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <!-- Bloco de filtros e ações -->
      <div class="p-3 pb-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="d-flex flex-grow-1 gap-2">
          <!-- Campo de busca -->
          <div class="input-group">
            <input type="text" id="searchColaboradores" class="form-control" placeholder=" buscar por Nome ou departamento" />
            <span class="input-group-text"><i class="ti ti-search"></i></span>
          </div>

          
        </div>

        <div class="d-flex gap-2">
          <!-- Link Teste (Copia ao clicar) -->

          <!-- Botão Adicionar -->
          <a href="/job/new" class="btn btn-primary">
            Adicionar Cargo
          </a>
        </div>
      </div>
    </div>
  </div>
</div>


  <div class="card mb-4">

    <div class="card-body">
      <div class="row g-3">

        <!-- Botão Adicionar + -->

        <div class="custom-table-container">
          <div class="table-responsive">
            <table class="table custom-table" id="jobRolesTable">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Departamento</th>
                  <th>Perfil</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
  @foreach ($jobRoles as $job)
    <tr class="clickable-row" data-href="/jobrole/edit/{{ $job->reference }}">
      <td>{{ $job->description }}</td>
      <td>{{ $job->department->description ?? 'N/A' }}</td>
      <td>{{ $job->calculationResultName->result_name ?? '' }}</td>
      <td>
        <div class="dropdown">
          <button type="button" class="btn p-0 dropdown-toggle hide-arrow action-btn" data-bs-toggle="dropdown">
            <i class="ti ti-dots-vertical"></i>
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item waves-effect" href="/jobrole/edit/{{ $job->reference }}">
              <i class="ti ti-pencil me-1"></i> Editar
            </a>
          </div>
        </div>
      </td>
    </tr>
  @endforeach
</tbody>

            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</form>

@endsection

@section('vendor-script')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="{{ asset('assets/functions/buscaCep.js') }}"></script>
<script src="{{ asset('assets/functions/uploadProfilePic.js') }}"></script>
@endsection

@section('page-script')
<!-- Inicializa DataTable -->
<script>
  $(document).ready(function () {
    if ($.fn.DataTable) {
      $('#jobRolesTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        // Customização do DataTable
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        pagingType: 'simple_numbers',
        lengthMenu: [5, 10, 25, 50],
        pageLength: 5,
        drawCallback: function() {
          // Adiciona classes customizadas à paginação
          $('.dataTables_paginate').addClass('custom-pagination');
          $('.paginate_button').addClass('page-item');
          $('.paginate_button a').addClass('page-link');
        }
      });
    } else {
      console.error('DataTable não está disponível. Verifique se os scripts foram carregados corretamente.');
    }
  });
</script>
@section('page-script')
<script>
  $(document).ready(function () {
    const tabela = $('#jobRolesTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
      },
      dom: '<"row"<"col-sm-6"l>>rtip', // remove o search interno
      pagingType: 'simple_numbers',
      lengthMenu: [5, 10, 25, 50],
      pageLength: 5,
      drawCallback: function () {
        $('.dataTables_paginate').addClass('custom-pagination');
        $('.paginate_button').addClass('page-item');
        $('.paginate_button a').addClass('page-link');
      }
    });

    // Filtro superior externo
    $('#searchColaboradores').on('keyup', function () {
      tabela.search(this.value).draw();
    });
  });

// Clique em linha (exceto dropdown)
$('#jobRolesTable tbody').on('click', 'tr.clickable-row', function (e) {
  if (
    $(e.target).closest('.dropdown').length === 0 && 
    $(e.target).closest('.dropdown-menu').length === 0 &&
    $(e.target).closest('.dropdown-toggle').length === 0
  ) {
    window.location.href = $(this).data('href');
  }
});


</script>

@endsection

@endsection
