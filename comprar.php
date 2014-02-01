<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set("America/Sao_Paulo");
header('Content-Type: text/html; charset=utf-8');
ini_set("memory_limit", "1024M");

$_SESSION['cart'][] = array(
	"name" => "Acessoria Web Simples",
	"desc" => "Duração: 1 mês",
	"value" => 999,
	"qtd" => 1
);

header("Location: carrinho.php");
exit();
?>