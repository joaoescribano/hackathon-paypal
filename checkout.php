<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set("America/Sao_Paulo");
header('Content-Type: text/html; charset=utf-8');
ini_set("memory_limit", "1024M");

require_once("paypal.class.php");
$pagamentos = new Pagamentos();
$urlPayPal = $pagamentos::SetExpressCheckout($_SESSION['cart']);

if (!$urlPayPal) {
	header("Location: carrinho.php?erro=Algum erro ocorreu durante a autenticaçao com a PayPal");
} else {
	header("Location: ". $urlPayPal);
}
exit();
?>