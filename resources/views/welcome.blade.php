<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Ensures optimal rendering on mobile devices. -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet Explorer compatibility -->
</head>

<body>
    @if ($mensagem = Session::get('successo'))

    <div class="w3-panel w3-green w3-display-container">

        <span onclick="this.parentElement.style.display='none'"
            class="w3-button w3-green w3-large w3-display-topright">&times;</span>

        <p>{!! $mensagem !!}</p>

    </div>


    <?php Session::forget('successo');?>


    @endif


    @if ($mensagem = Session::get('erro'))

    <div class="w3-panel w3-red w3-display-container">

        <span onclick="this.parentElement.style.display='none'"
            class="w3-button w3-red w3-large w3-display-topright">&times;</span>

        <p>{!! $mensagem !!}</p>

    </div>


    <?php Session::forget('erro');?>

    @endif
    <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="formulario-pagamento"
        action="{{ route('pagar_com_paypal') }}">

        {{ csrf_field() }}

        <h2 class="w3-text-blue">Formulário de Pagamento</h2>

        <p>Integração PayPal + Laravel DEMO</p>

        <p>

            <label class="w3-text-blue"><b>R$: </b></label>

            <input class="w3-input w3-border" name="valor" type="text"></p>

        <button class="w3-btn w3-blue">Enviar Pagamento</button></p>

    </form>
</body>