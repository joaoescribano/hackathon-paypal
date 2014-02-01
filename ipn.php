<?php
define("LOG_FILE", "./logs/IPN-".date("Y_m_d_H_i_s").".txt");

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();

foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}

$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}

foreach ($myPost as $key => $value) {
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}

$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";

$ch = curl_init($paypal_url);
if ($ch == FALSE) {
	return FALSE;
}

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

$res = curl_exec($ch);

if (strcmp ($res, "VERIFIED") == 0) {
	error_log("Horário: " . date("D/m/Y - H:i:s") . PHP_EOL, 3, LOG_FILE);
	error_log("Email do Vendedor: " . $_POST['receiver_email'] . PHP_EOL, 3, LOG_FILE);
	error_log("Email do Comprador: " . $_POST['payer_email'] . PHP_EOL, 3, LOG_FILE);

	if (isset($_POST['payment_cycle'])) {
		error_log("Assinatura #". $_POST['recurring_payment_id'] . PHP_EOL, 3, LOG_FILE);
		if ($_POST['txn_type'] == "recurring_payment_profile_created") {
			error_log("Status: Criada" . PHP_EOL, 3, LOG_FILE);
		} else if ($_POST['txn_type'] == "recurring_payment") {
			error_log("Status: Mensalidade Recebida" . PHP_EOL, 3, LOG_FILE);
		} else if ($_POST['txn_type'] == "recurring_payment_profile_skipped") {
			error_log("Status: Atrasada" . PHP_EOL, 3, LOG_FILE);
		} else if ($_POST['txn_type'] == "recurring_payment_profile_failed") {
			error_log("Status: Erro no pagamento" . PHP_EOL, 3, LOG_FILE);
		} else if ($_POST['txn_type'] == "recurring_payment_suspended_due_to_max_failed_payment") {
			error_log("Status: Cancelada por falta de pagamento" . PHP_EOL, 3, LOG_FILE);
		} else if ($_POST['txn_type'] == "recurring_payment_profile_cancel") {
			error_log("Status: Cancelada pelo cliente" . PHP_EOL, 3, LOG_FILE);
		}
	} else {
		error_log("Transiction ID: " . $_POST['txn_id'] . PHP_EOL , 3, LOG_FILE);
		error_log("Status do pagamento: " . $_POST['payment_status'] . PHP_EOL, 3, LOG_FILE);
	}
} else if (strcmp ($res, "INVALID") == 0) {
	error_log("IPN Inválido", 3, LOG_FILE);
}

?>