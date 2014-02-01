<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set("America/Sao_Paulo");
header('Content-Type: text/html; charset=utf-8');
ini_set("memory_limit", "1024M");

require_once("paypal.class.php");
$pagamentos = new Pagamentos();
$refund = $pagamentos::refund();

if (!$refund) {
	header("Location: carrinho.php?erro=Algum erro ocorreu durante o cancelamento da compra.");
} else {
	header("Location: carrinho.php?sucesso=Compra cancelada com sucesso!");
}
?>