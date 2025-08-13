@php
  $containerNav = 'container-fluid';
  $navbarDetached = $navbarDetached ?? '';
@endphp

@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme" id="layout-navbar">
@else
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="{{ $containerNav }}">
@endif

  {{-- Botão toggle do menu lateral --}}
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="ti ti-menu-2 ti-sm"></i>
    </a>
  </div>

  {{-- Conteúdo do lado direito --}}
  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    {{-- Botão voltar (ícone) e nome da rota --}}
    <div class="d-flex align-items-center me-4">
      <button onclick="window.history.back()" class="btn btn-icon btn-sm btn-outline-secondary me-2">
        <i class="ti ti-arrow-left"></i>
      </button>
      <h5 class="mb-0">
        {{ Route::currentRouteName() ?? 'Rota desconhecida' }}
      </h5>
    </div>

    {{-- Menu do usuário --}}
    <ul class="navbar-nav flex-row align-items-center ms-auto">
      {{-- Bloco com o nome do usuário e o nome da empresa --}}
      <li class="nav-item me-3 d-flex flex-column align-items-end">
        @php
          $nomes = explode(' ', Auth::user()->name ?? 'John Doe');
          $primeirosNomes = implode(' ', array_slice($nomes, 0, 2));
        @endphp
        <span class="fw-medium d-block">{{ $primeirosNomes }}</span>
        <small class="text-muted">{{ session('company_name') ?? 'Empresa Genérica' }}</small>
      </li>
      
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}" class="h-auto rounded-circle" width="40" height="40" />
          </div>
        </a>

        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="javascript:void(0);">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}" class="h-auto rounded-circle" width="40" height="40" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-medium d-block">{{ $primeirosNomes }}</span>
                  <small class="text-muted">{{ session('rule') ?? 'Empresa Genérica' }}</small>
                </div>
              </div>
            </a>
          </li>
          <li><div class="dropdown-divider"></div></li>
          <li>
            <a class="dropdown-item" href="/settings">
              <i class="ti ti-settings me-2"></i>
              <span class="align-middle">Configurações</span>
            </a>
          </li>
          <li><div class="dropdown-divider"></div></li>
          <li>
            <a class="dropdown-item" href="{{ route('logout') }}">
              <i class="ti ti-logout me-2"></i>
              <span class="align-middle">Sair</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>

  </div>

@if(!isset($navbarDetached))
  </div>
@endif
</nav>