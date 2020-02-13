<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Session;
use Illuminate\Http\Request;
use Redirect;
use URL;
use Config;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PagamentoController extends Controller
{
    public function __construct()
    {
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );

        $this->_api_context->setConfig($paypal_conf['settings']);
    }
    public function pagarComPayPal(Request $request)
    {
        $pagador = new Payer();
        $pagador->setPaymentMethod('paypal');

        $item_1 = new Item();

        $item_1->setName('Item 1')->setCurrency('BRL')->setQuantity(1)->setPrice($request->get('amount'));

        $lista_itens = new ItemList();

        $lista_itens->setItems(array($item_1));

        $valor = new Amount();

        $valor->setCurrency('BRL')->setTotal($request->get('amount'));

        $transacao = new Transaction();

        $transacao->setAmount($valor)->setItemList($lista_itens)->setDescription('Your transaction description');

        $urls_redirecionamento = new RedirectUrls();
        $urls_redirecionamento->setReturnUrl(URL::route('status'))->setCancelUrl(URL::route('status'));

        $pagamento = new Payment();

        $pagamento->setIntent('Sale')->setPayer($pagador)->setRedirectUrls($urls_redirecionamento)->setTransactions(array($transacao));
        $payment = $pagamento->create($this->_api_context);
        dd($payment);
        try {


        } catch (\PayPal\Exception\PPConnectionException $e) {

            dd($e);
            if (\Config::get('app.debug')) {\

                Session::put('error', 'Tempo Limite de Conexão Excedido');

                return Redirect::route('home');
            } else {\
                Session::put('error', 'Serviço fora do ar, tente novamente mais tarde.');
                return Redirect::route('home');
            }
        }

        foreach ($pagamento->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $url_redirecionar = $link->getHref();
                break;
            }
        }
    }

    public function statusPagamento()
    {
        $id_pagamento = Session::get('pagamento_paypal_id');
        Session::forget('pagamento_paypal_id');

        if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {

            \Session::put('erro', 'Falha na transação.');

            return Redirect::route('home');

        }

        $pagamento = Payment::get($id_pagamento, $this->_api_context);

        $execucao_pagamento = new PaymentExecution();

        $execucao_pagamento->setPayerId(Input::get('PayerID'));
        $result = $pagamento->execute($execucao_pagamento, $this->_api_context);

        if ($result->getState() == 'approved') {

            \Session::put('successo', 'Pagamento realizado com sucesso!');

            return Redirect::route('home');

        }

        \Session::put('erro', 'Falha na transação.');

        return Redirect::route('home');

    }
}
