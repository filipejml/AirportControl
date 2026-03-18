<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

@include('components.navbar')

<div class="container mt-4">

    <h3 class="fw-bold mb-4">
        📊 Relatórios do Sistema
    </h3>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Voos por Companhia</h5>
                <p class="text-muted">Quantidade de voos por empresa</p>
                <a href="#" class="btn btn-primary btn-sm">Gerar</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Movimentação de Aeroportos</h5>
                <p class="text-muted">Entradas e saídas</p>
                <a href="#" class="btn btn-primary btn-sm">Gerar</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Uso de Aeronaves</h5>
                <p class="text-muted">Frequência de uso</p>
                <a href="#" class="btn btn-primary btn-sm">Gerar</a>
            </div>
        </div>

    </div>

</div>

</body>
</html>