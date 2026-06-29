<!DOCTYPE html>
<html>

<body>

    <h2>Olá!</h2>

    <p>
        Sua Ordem de Serviço <strong>#{{ $serviceOrderId }}</strong>
        está aguardando aprovação.
    </p>

    <p>
        <a href="{{ url('/api/service-orders/approve?token='.$token) }}">
            Aprovar orçamento
        </a>
    </p>

    <p>
        <a href="{{ url('/api/service-orders/reject?token='.$token) }}">
            Rejeitar orçamento
        </a>
    </p>

</body>
</html>