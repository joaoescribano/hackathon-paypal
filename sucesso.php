<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set("America/Sao_Paulo");
header('Content-Type: text/html; charset=utf-8');
ini_set("memory_limit", "1024M");

require_once("paypal.class.php");
$pagamentos = new Pagamentos();
$expressCheckout = $pagamentos::getExpressCheckout($_GET['token']);

$arquivo = (isset($_GET['assinatura']) ? "carrinhoAssinatura" : "carrinho").".php";

if (!$expressCheckout) {
	header("Location: {$arquivo}?erro=Algum erro ocorreu durante a checagem do pagamento.");
} else {
	$doCheckout = (isset($_GET['assinatura']) ? $pagamentos::doRecurringPayment($expressCheckout, $_SESSION['recurring']) : $pagamentos::doExpressCheckout($expressCheckout, $_SESSION['cart']));

	if (!$doCheckout) {
		header("Location: {$arquivo}?erro=Algum problema ocorreu durante o processamento do pagamento.");
	} else {
		if (isset($_GET['assinatura'])) {
			if ($doCheckout['PROFILESTATUS'] == "ActiveProfile") {
				$_SESSION['recurring'] = array();
				header("Location: {$arquivo}?sucesso=Sua assinatura foi realizada com sucesso.&assinatura=1&status=Completed");
			} else if ($doCheckout['PROFILESTATUS'] == "PendingProfile") {
				$_SESSION['recurring'] = array();
				header("Location: {$arquivo}?erro=Sua assinatura foi realizada e esta em processo de analizize pela PayPal.&assinatura=1");
			} else {
				header("Location: {$arquivo}?erro=Algum erro ocorreu durante a finalização de sua assinatura.&assinatura=1");
			}
		} else {
			if (in_array($doCheckout['PAYMENTINFO_0_PAYMENTSTATUS'], array("None", "Denied", "Expired", "Failed"))) {
				header("Location: {$arquivo}?erro=O pagamento não foi aprovado pela PayPal.");
			} else if (in_array($doCheckout['PAYMENTINFO_0_PAYMENTSTATUS'], array("In-Progress", "Pending"))) {
				$_SESSION['cart'] = array();
				header("Location: {$arquivo}?sucesso=Seu pagamento foi realizado e está em processo de avaliação.");
			} else if (in_array($doCheckout['PAYMENTINFO_0_PAYMENTSTATUS'], array("Completed"))) {
				$_SESSION['cart'] = array();
				header("Location: {$arquivo}?sucesso=Seu pagamento foi concluido com sucesso.&status=Completed");
			} else {
				header("Location: {$arquivo}?erro=Algum erro ocorreu durante a finalização do pagamento.");
			}
		}
	}
}
exit();
?>