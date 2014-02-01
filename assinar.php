<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set("America/Sao_Paulo");
header('Content-Type: text/html; charset=utf-8');
ini_set("memory_limit", "1024M");

$_SESSION['recurring'][0] = array(
	"name" => "Consultoria Web ".($_GET['meses'] == 3 ? "Avançada" : "Pro"),
	"desc" => "Duração: ".$_GET['meses']." meses",
	"value" => ($_GET['meses'] == 3 ? 899 : 799),
	"meses" => $_GET['meses'],
	"qtd" => 1
);

header("Location: carrinhoAssinatura.php");
exit();
?>