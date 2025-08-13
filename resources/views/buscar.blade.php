<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Busca Inteligente de Candidatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">🔍 Buscar Candidatos</h2>

    // Atualiza o Blade com campo ZIP
    
<form method="post" class="mb-4">
    @csrf
    <div class="row g-2">
        <div class="col-md-2">
            <input type="number" name="id_vacancy" class="form-control" placeholder="ID da Vaga" value="{{ old('id_vacancy') }}">
        </div>
        <div class="col-md-7">
            <input type="text" name="search" class="form-control" placeholder="Ex: Pessoas com mais de 30 anos" value="{{ old('search') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="zip" class="form-control" placeholder="ZIP" value="{{ old('zip') }}">
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary w-100" type="submit">Buscar</button>
        </div>
    </div>
</form>

    @if ($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if ($where)
        <div class="alert alert-secondary">
            <strong>Filtro SQL gerado:</strong><br>
            <code>{{ $where }}</code>
        </div>
    @endif

    @if (!empty($results))
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <strong>{{ count($results) }} resultado(s) encontrado(s)</strong>
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($results as $r)
                    <li class="list-group-item">
                        <strong>{{ $r->name }}</strong><br>
                        📍 {{ $r->adress_city }} |
                        💼 {{ $r->job_position }} |
                        📊 {{ $r->result_name }}
                    </li>
                @endforeach
            </ul>
        </div>
    @elseif (request()->isMethod('post') && !$error)
        <div class="alert alert-warning">Nenhum resultado encontrado.</div>
    @endif

    @if ($apiDebug)
        <div class="mt-5">
            <h4>🧪 Resposta completa da OpenAI</h4>
            <pre class="bg-dark text-light p-3 rounded" style="max-height: 500px; overflow:auto;">
{{ json_encode($apiDebug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
            </pre>
        </div>
    @endif
</div>
</body>
</html>
