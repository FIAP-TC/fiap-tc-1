<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Aprovação da Ordem de Serviço</title>
</head>

<body style="
    margin:0;
    padding:40px;
    background:#f5f5f5;
    font-family:Arial, Helvetica, sans-serif;
">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" style="
    background:#ffffff;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
">

                    <tr>
                        <td style="
    background:#1f2937;
    color:#fff;
    padding:24px;
    text-align:center;
">
                            <h1 style="margin:0;font-size:24px;">
                                Oficina Mecânica
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:40px;">

                            <h2 style="margin-top:0;color:#111827;">
                                Olá!
                            </h2>

                            <p style="color:#4b5563;font-size:16px;line-height:1.6;">
                                Sua Ordem de Serviço
                                <strong>#{{ $serviceOrderId }}</strong>
                                está pronta e aguardando sua aprovação.
                            </p>

                            <p style="color:#4b5563;font-size:16px;line-height:1.6;">
                                Clique em uma das opções abaixo:
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                                <tr>

                                    <td align="center">

                                        <a href="{{ url('/api/service-orders/approve?token=' . $token) }}" style="
display:inline-block;
padding:14px 28px;
background:#22c55e;
color:#ffffff;
text-decoration:none;
border-radius:8px;
font-weight:bold;
">
                                            ✔ Aprovar orçamento
                                        </a>

                                    </td>

                                    <td align="center">

                                        <a href="{{ url('/api/service-orders/reject?token=' . $token) }}" style="
display:inline-block;
padding:14px 28px;
background:#ef4444;
color:#ffffff;
text-decoration:none;
border-radius:8px;
font-weight:bold;
">
                                            ✖ Rejeitar orçamento
                                        </a>

                                    </td>

                                </tr>
                            </table>

                            <p style="
margin-top:40px;
font-size:13px;
color:#9ca3af;
text-align:center;
">
                                Caso você não tenha solicitado este serviço, ignore este e-mail.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>