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

<div class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <!-- Bloco de filtros e ações -->
      <div class="p-3 pb-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="d-flex flex-grow-1 gap-2">
          <!-- Campo de busca -->
          <div class="input-group">
            <input type="text" id="searchColaboradores" class="form-control" placeholder="Busque por nome, cargo ou departamento" />
            <span class="input-group-text"><i class="ti ti-search"></i></span>
          </div>

          <!-- Botão Filtros
          <button class="btn btn-primary d-flex align-items-center gap-1">
            Filtros <i class="ti ti-filter"></i>
          </button>-->
        </div>

        <div class="d-flex gap-2">
          <!-- Link Teste (Copia ao clicar) -->
          <button onclick="copiarLinkTeste()" class="btn border border-primary text-primary">
            Link Teste Perfil Comportamental
          </button>

          <!-- Botão Adicionar -->
          <a href="/employee/new" class="btn btn-primary">
            Adicionar Colaborador
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Formulário de cadastro -->
<form method="POST" action="">
  @csrf

  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="custom-table-container">
          <div class="table-responsive">
            <table class="table custom-table" id="personsTable">
              <thead>
                <tr>
                  <th></th>
                  <th>Colaborador</th>
                  <th>Perfil comportamental</th>
                  <th>Departamento</th>
                  <th>Cargo</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($persons as $person)
              <tr class="clickable-row" data-url="/employee/edit/{{ $person['cpf'] }}" style="cursor: pointer;">
                <td>
                  <img src="{{ $person['profile_pic_base64'] ?: 'https://uploads.promoview.com.br/2023/12/b72a1cfe.png' }}"
                       alt="Foto" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                </td>
                <td>{{ $person['full_name'] }}</td>
                <td>{{ $person['result_name'] }}</td>
                <td>{{ $person['department'] }}</td>
                <td>{{ $person['role'] }}</td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow action-btn" data-bs-toggle="dropdown">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item waves-effect" href="/employee/edit/{{ $person['cpf'] }}" style="text-decoration: none;">
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')
<script>
  function copiarLinkTeste() {
    const link = `https://questionaries.inperson.com.br/?questionarie={{ session('company_reference') }}`;
    navigator.clipboard.writeText(link).then(() => {
      Swal.fire({
        title: 'Link Copiado!',
        text: 'Atenção!! Esse link deve ser compartilhado apenas com colaboradores da empresa!',
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    });
  }

  $(document).ready(function () {
    if ($.fn.DataTable) {
      const dataTable = $('#personsTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        dom: '<"row"<"col-sm-6"l>>rtip',
        pagingType: 'simple_numbers',
        lengthMenu: [5, 10, 25, 50],
        pageLength: 5,
        drawCallback: function () {
          $('.dataTables_paginate').addClass('custom-pagination');
          $('.paginate_button').addClass('page-item');
          $('.paginate_button a').addClass('page-link');
        }
      });

      $('#searchColaboradores').on('keyup', function () {
        dataTable.search(this.value).draw();
      });
    }

    $('.clickable-row').on('click', function (e) {
      if (!$(e.target).closest('.dropdown, a, button').length) {
        window.location = $(this).data('url');
      }
    });
  });
</script>
@endsection