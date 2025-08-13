@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Novo departamento')

@section('content')
            <div class="card">

                <div class="card-body">
                    <form method="POST" action="/department/new">
                        @csrf
<input hidden name="reference" value="{{$department->reference }}">
                        <div class="form-group row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Título do Departamento') }}</label>

                            <div class="col-md-6">
                            <input id="description" type="text" class="form-control @error('description') is-invalid @enderror"
       name="description"
       value="{{ old('description', $department->description) }}"
       required autocomplete="description">


                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="id_manager" class="col-md-4 col-form-label text-md-right">{{ __('Responsável') }}</label>

                            <div class="col-md-6">
                            <select id="id_manager" class="form-control @error('id_manager') is-invalid @enderror" name="id_manager" required>
    <option value="">Selecione um responsável</option>
    @foreach($persons as $person)
        <option value="{{ $person->id_person }}"
            @if(old('id_manager', $department->id_manager) == $person->id_person) selected @endif>
            {{ $person->full_name }}
        </option>
    @endforeach
</select>


                                @error('id_manager')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Salvar') }}
                                </button>
                                <a href="/departments" class="btn btn-secondary">
                                    {{ __('Cancelar') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

@endsection
