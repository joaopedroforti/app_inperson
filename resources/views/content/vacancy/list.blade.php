@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')

<!-- CSS -->
<link href="{{ asset('assets/css/custom-table.css') }}" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

@csrf

<div class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <div class="p-3 pb-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="d-flex flex-grow-1 gap-2">
          <div class="input-group">
            <input type="text" id="searchColaboradores" class="form-control" placeholder="Buscar por Nome ou Departamento" />
            <span class="input-group-text"><i class="ti ti-search"></i></span>
          </div>
        </div>
        <div class="d-flex gap-2">
          <a href="/vacancy/new" class="btn btn-primary">
            Adicionar Vaga
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <div class="custom-table-container">
        <div class="table-responsive">
          <table class="table custom-table" id="vacancyTable">
            <thead>
              <tr>
                <th>Status</th>
                <th>Data Criação</th>
                <th>Nome da Vaga</th>
                <th>Currículos</th>
                <th>Encerra em</th>
                <th>Departamento</th>
                <th>Divulgação</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($vacancies as $vacancy)
              <tr class="row-click" data-url="/vacancy/{{ $vacancy['reference'] }}" style="cursor: pointer;">
                <td>
                  @if ($vacancy['status'] == 1)
                    <span class="status-badge status-aberto">Aberto</span>
                  @else
                    <span class="status-badge status-finalizado">Finalizado</span>
                  @endif
                </td>
                <td>{{ $vacancy['data_criacao'] }}</td>
                <td>{{ $vacancy['nome_vaga'] }}</td>
                <td class="text-center align-middle">{{ $vacancy['recruitmentCount'] }}</td>
                <td>{{ $vacancy['data_encerramento'] }}</td>
                <td>{{ $vacancy['departamento'] }}</td>
                <td>
                  @if ($vacancy['confidential'] == 0)
                    <span class="icon-publica">
                      <i class="ti ti-wifi"></i> Pública
                    </span>
                  @else
                    <span class="icon-confidencial">
                      <i class="ti ti-lock"></i> Confidencial
                    </span>
                  @endif
                </td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow action-btn" data-bs-toggle="dropdown" onclick="event.stopPropagation();">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu" onclick="event.stopPropagation();">
                      <li>
                        <a href="#" class="dropdown-item"
                           onclick="copiarLink(this)"
                           data-link="https://vagas.inperson.com.br/vaga/{{ $vacancy['reference'] }}">
                          <i class="ti ti-link me-1"></i> Copiar link da Vaga
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="/vacancy/{{ $vacancy['reference'] }}">
                          <i class="ti ti-pencil me-1"></i> Editar
                        </a>
                      </li>
                    </ul>
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

@endsection

@section('vendor-script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')
<script>
  function copiarLink(el) {
    event.preventDefault();
    event.stopPropagation();

    const link = el.getAttribute('data-link');

    navigator.clipboard.writeText(link)
      .then(() => {
        Swal.fire({
          title: 'Link copiado!',
          text: 'Deseja abrir a vaga agora?',
          icon: 'success',
          showCancelButton: true,
          confirmButtonText: 'Ir para a vaga',
          cancelButtonText: 'Ficar aqui',
          reverseButtons: true,
          customClass: {
            popup: 'shadow-sm rounded',
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-secondary me-2'
          },
          buttonsStyling: false
        }).then(result => {
          if (result.isConfirmed) {
            window.open(link, '_blank');
          }
        });
      })
      .catch(() => {
        Swal.fire('Erro', 'Não foi possível copiar o link.', 'error');
      });
  }

  $(document).ready(function () {
    const tabela = $('#vacancyTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
      },
      dom: '<"row"<"col-sm-6"l><"col-sm-6">>rtip',
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
      tabela.search(this.value).draw();
    });

    $(document).on('click', '.row-click', function (e) {
      if ($(e.target).closest('.dropdown-menu').length || $(e.target).closest('.dropdown-toggle').length) return;
      const url = $(this).data('url');
      if (url) window.location.href = url;
    });
  });
</script>
@endsection
