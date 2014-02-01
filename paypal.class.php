<?php

class Pagamentos {

    public static $configuracoes = array(
        'sandbox' => true,
        'user' => 'heisenberg_api1.gmail.com',
        'userMail' => 'heisenberg@gmail.com',
        'pswd' => '1391214871',
        'signature' => 'AqKjLt1ZdGGImK1gy5xP2as6g.awApxiLrJfFvsF5A8a67vbmpyDjZHK',
        'paypalURL' => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
        'return' => array(
            'success' => "http://hackathon.3force.com.br/sucesso.php",
            'cancel' => "http://hackathon.3force.com.br/erro.php",
            'ipn' => "http://hackathon.3force.com.br/ipn.php"
            )
        );


    public static function sendRequest(array $request, $sandbox = false) {
        //Endpoint da API
        $apiEndpoint  = 'https://api-3t.' . ($sandbox? 'sandbox.': null);
        $apiEndpoint .= 'paypal.com/nvp';

        //Executando a operação
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request));

        $response = urldecode(curl_exec($curl));

        curl_close($curl);

        //Tratando a resposta
        $responseNvp = array();

        if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
            foreach ($matches['name'] as $offset => $name) {
                $responseNvp[$name] = $matches['value'][$offset];
            }
        }

        //Verificando se deu tudo certo e, caso algum erro tenha ocorrido,
        //gravamos um log para depuração.
        if (isset($responseNvp['ACK']) && $responseNvp['ACK'] != 'Success') {
            for ($i = 0; isset($responseNvp['L_ERRORCODE' . $i]); ++$i) {
                $message = sprintf("PayPal NVP %s[%d]: %s\n",
                   $responseNvp['L_SEVERITYCODE' . $i],
                   $responseNvp['L_ERRORCODE' . $i],
                   $responseNvp['L_LONGMESSAGE' . $i]);

                error_log($message);
            }
        }

        return $responseNvp;
    }


    public static function SetExpressCheckout($itens, $recurring = false) {
        $total = 0;
        foreach ($itens as $key => $item) {
            $total += ($item['value'] * $item['qtd']);
        }

        $request = array(
            'USER' => self::$configuracoes['user'],
            'PWD' => self::$configuracoes['pswd'],
            'SIGNATURE' => self::$configuracoes['signature'],

            'VERSION' => '108.0',
            'METHOD'=> 'SetExpressCheckout',
            );

        $request['PAYMENTREQUEST_0_PAYMENTACTION'] = ($recurring ? "Authorization" : 'SALE');
        $request['PAYMENTREQUEST_0_AMT'] = number_format($total, 2);
        $request['PAYMENTREQUEST_0_CURRENCYCODE'] = 'BRL';
        $request['PAYMENTREQUEST_0_ITEMAMT'] = number_format($total, 2);
        $request['PAYMENTREQUEST_0_INVNUM'] = rand(1111,9999);

        foreach ($itens as $key => $item) {
            $request['L_PAYMENTREQUEST_0_NAME'.$key] = $item['name'];
            $request['L_PAYMENTREQUEST_0_DESC'.$key] = $item['desc'];
            $request['L_PAYMENTREQUEST_0_AMT'.$key] = number_format($item['value'], 2);
            $request['L_PAYMENTREQUEST_0_QTY'.$key] = $item['qtd'];
            if ($recurring) {
                $request['L_PAYMENTREQUEST_0_ITEMCATEGORY'.$key] = 'Digital';
            }
        }

        if ($recurring === true) {
            $request['DESC'] = "Assinatura Mensal";
            $request['L_BILLINGTYPE0'] = 'RecurringPayments';
            $request['L_BILLINGAGREEMENTDESCRIPTION0'] = 'Assinatura mensal';
        }

        $request['RETURNURL'] = self::$configuracoes['return']['success'].($recurring ? "?assinatura=1" : "");
        $request['CANCELURL'] = self::$configuracoes['return']['cancel'].($recurring ? "?assinatura=1" : "");

        $response = self::sendRequest($request, self::$configuracoes['sandbox']);

        if (isset($response['ACK']) && $response['ACK'] == 'Success') {
            $query = array(
                'cmd'    => '_express-checkout',
                'token'  => $response['TOKEN']
                );

            $_SESSION['paypal']['TOKEN'] = $response['TOKEN'];

            return sprintf('%s?%s', self::$configuracoes['paypalURL'], http_build_query($query));
        } else {
            return false;
        }
    }

    public static function getExpressCheckout($token) {
        $request = array(
            'USER' => self::$configuracoes['user'],
            'PWD' => self::$configuracoes['pswd'],
            'SIGNATURE' => self::$configuracoes['signature'],
            'VERSION' => "108.0",
            'SUBJECT' => self::$configuracoes['userMail'],
            'METHOD' => "GetExpressCheckoutDetails",
            'TOKEN' => $token
            );

        $response = self::sendRequest($request, self::$configuracoes['sandbox']);

        if (isset($response['ACK']) && $response['ACK'] == 'Success') {
            $_SESSION['paypal']['CORRELATIONID'] = $response['CORRELATIONID'];
            return $response;
        } else {
            return false;
        }
    }

    public static function doExpressCheckout($info, $itens) {
        $total = 0;
        foreach ($itens as $key => $item) {
            $total += ($item['value'] * $item['qtd']);
        }

        $request = array(
            'USER' => self::$configuracoes['user'],
            'PWD' => self::$configuracoes['pswd'],
            'SIGNATURE' => self::$configuracoes['signature'],
            'VERSION' => '108.0',
            'METHOD' => "DoExpressCheckoutPayment",
            'TOKEN' => $info['TOKEN'],
            'PAYERID' => $info['PAYERID'],
            'NOTIFYURL' =>  self::$configuracoes['return']['ipn'],
            'PAYMENTREQUEST_0_PAYMENTACTION'  => "SALE",
            'PAYMENTREQUEST_0_AMT' => number_format($total,2),
            'PAYMENTREQUEST_0_CURRENCYCODE' => "BRL",
            'PAYMENTREQUEST_0_ITEMAMT' => number_format($total,2),
            'PAYMENTREQUEST_0_INVNUM' => $info['INVNUM']
            );

        foreach ($itens as $key => $item) {
            $request['L_PAYMENTREQUEST_0_NAME'.$key] = $item['name'];
            $request['L_PAYMENTREQUEST_0_DESC'.$key] = $item['desc'];
            $request['L_PAYMENTREQUEST_0_AMT'.$key] = number_format($item['value'], 2);
            $request['L_PAYMENTREQUEST_0_QTY'.$key] = $item['qtd'];
        }

        $response = self::sendRequest($request, self::$configuracoes['sandbox']);

        if (isset($response['ACK']) && $response['ACK'] == 'Success') {
            $_SESSION['paypal']['TRANSACTIONID'] = $response['PAYMENTINFO_0_TRANSACTIONID'];
            $_SESSION['paypal']['status'] = $response['PAYMENTINFO_0_PAYMENTSTATUS'];
            $_SESSION['paypal']['total'] = $total;
            return $response;
        } else {
            return false;
        }
    }

    public static function doRecurringPayment($info, $itens) {
        $total = 0;
        foreach ($itens as $key => $item) {
            $total += ($item['value'] * $item['qtd']);
        }

        $request = array(
            'USER' => self::$configuracoes['user'],
            'PWD' => self::$configuracoes['pswd'],
            'SIGNATURE' => self::$configuracoes['signature'],
            'METHOD' => 'CreateRecurringPaymentsProfile',
            'VERSION' => '108.0',

            'TOKEN' => $info['TOKEN'],
            'PAYERID' => $info['PAYERID'],
            'NOTIFYURL' =>  self::$configuracoes['return']['ipn'],

            'PROFILESTARTDATE' => date("Y-m-d").'T'.date("H:i:s").'Z',
            'DESC' => 'Assinatura mensal',
            'BILLINGPERIOD' => 'Month',
            'BILLINGFREQUENCY' => '1',
            'TOTALBILLINGCYCLES' => $itens[0]['meses'],
            'AMT' => number_format($total,2),
            'CURRENCYCODE' => 'BRL',
            'COUNTRYCODE' => 'BR',
            'MAXFAILEDPAYMENTS' => 3
            );

        $response = self::sendRequest($request, self::$configuracoes['sandbox']);

        if (isset($response['ACK']) && $response['ACK'] == 'Success') {
            $_SESSION['paypal']['PROFILEID'] = $response['PROFILEID'];
            return $response;
        } else {
            return false;
        }
    }

    public static function refund() {
        $request = array(
            'USER' => self::$configuracoes['user'],
            'PWD' => self::$configuracoes['pswd'],
            'SIGNATURE' => self::$configuracoes['signature'],
            'VERSION' => "108.0",
            'METHOD' => "RefundTransaction",
            'TRANSACTIONID' => $_SESSION['paypal']['TRANSACTIONID'],
            'REFUNDTYPE' => "PARTIAL",
            'NOTE' => "Pedido de cancelamento realizado pelo cliente",
            'AMT' => number_format(($_SESSION['paypal']['total'] * 0.75), 2)
            );

        $response = self::sendRequest($request, self::$configuracoes['sandbox']);

        if (isset($response['ACK']) && $response['ACK'] == 'Success') {
            $_SESSION['paypal'] = array();
            return $response;
        } else {
            return false;
        }
    }

    public static function cancelRecurring() {
        $request = array(
            'USER' => self::$configuracoes['user'],
            'PWD' => self::$configuracoes['pswd'],
            'SIGNATURE' => self::$configuracoes['signature'],
            'VERSION' => "108.0",
            'METHOD' => "ManageRecurringPaymentsProfileStatus",
            'PROFILEID' => $_SESSION['paypal']['PROFILEID'],
            'ACTION' => "Cancel",
            'NOTE' => "Pedido de cancelamento de assinatura realizado pelo cliente"
            );

        $response = self::sendRequest($request, self::$configuracoes['sandbox']);

        if (isset($response['ACK']) && $response['ACK'] == 'Success') {
            $_SESSION['paypal'] = array();
            return $response;
        } else {
            return false;
        }
    }
}
?>