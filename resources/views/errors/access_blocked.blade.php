@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', $title ?? 'Acesso Negado')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card shadow-sm border text-center">
        <div class="card-body p-4">
          <div class="mb-4">
            <i class="bi bi-shield-exclamation text-danger" style="font-size: 3rem;"></i>
          </div>
          <h4 class="mb-3 fw-bold text-primary">{{ $title ?? 'Acesso negado' }}</h4>
          <p class="mb-4 text-muted fs-5">
            {{ $text ?? 'Você não possui permissão ou créditos suficientes para acessar este recurso.' }}
          </p>

          @if(!empty($button_link))
            <a href="{{ $button_link }}" class="btn btn-primary px-4">
              {{ $button_text ?? 'Fale com o Suporte' }}
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
