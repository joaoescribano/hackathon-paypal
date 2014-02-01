<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set("America/Sao_Paulo");
header('Content-Type: text/html; charset=utf-8');
ini_set("memory_limit", "1024M");
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Tri Force - Loja Virtual</title>
	<link rel="stylesheet" href="css/foundation.css" />
	<link rel="stylesheet" href="css/estilo.css" />
	<script src="js/vendor/modernizr.js"></script>
</head>
<body>

	<header>
		<div class="row">
			<div class="large-3 medium-3 small-8 large-centered medium-centered small-centered columns">
				<a href="index.php"><img src="img/9-Logo-Tri-Force.png" alt="Tri Force - Soluções Digitais" /></a>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="large-12 columns">
			<h3 class="linha">Precisando de uma Acessoria Web Completa para o seu negócio?</h3>
		</div>
	</div>

	<div class="row" id="produtos">
		<div class="large-4 columns">
			<ul class="pricing-table">
				<li class="title"><img src="img/1-chamada-1.png" alt="" /></li>
				<li class="description">Acessoria Web Simples<br/>Duração: 1 mês</li>
				<li class="price">R$ 999,00</li>
				<li class="cta-button"><a class="button" href="comprar.php">Comprar</a></li>
			</ul>
		</div>
		<div class="large-4 columns">
			<ul class="pricing-table">
				<li class="title"><img src="img/2-chamada-2.png" alt="" /></li>
				<li class="description">Acessoria Web Avançada<br/>Duração: 3 meses</li>
				<li class="price">R$ 899,00 <small>/mês</small></li>
				<li class="cta-button"><a class="button" href="assinar.php?meses=3">Comprar</a></li>
			</ul>
		</div>
		<div class="large-4 columns">
			<ul class="pricing-table">
				<li class="title"><img src="img/3-chamada-3.png" alt="" /></li>
				<li class="description">Acessoria Web Pro<br/>Duração: 6 meses</li>
				<li class="price">R$ 799,00 <small>/mês</small></li>
				<li class="cta-button"><a class="button" href="assinar.php?meses=6">Comprar</a></li>
			</ul>
		</div>
	</div>

	<section id="robo">
		<div class="row">
			<div class="large-12 columns">
				<p class="texto">
					Conheça nosso planos mensais, nossos serviços e encontre a solução ideal para alavancar seus negócios.
				</p>

				<h2>Seja Bem Vindo!</h2>
				<p class="bemvindo">
					Somos uma empresa especialisada em soluções digitais.<br/>
					Prestamos acessoria web e desenvolvemos websites com técnologia responsiva.<br/>
					Navege em nosso site para mais informações.
				</p>
			</div>
		</div>
		<img src="img/5-robo.jpg" alt="" />
	</section>

	<footer>
		<div class="row">
			<div class="large-6 columns sessao">
				<img src="img/01-Paypal-selo-pagamento.png" alt="" />
			</div>
			<div class="large-6 columns sessao">
				<img src="img/02-pay-pal-seguranca.png" alt="" />
			</div>
			<div class="large-6 columns sessao">
				<img src="img/03-facebook.png" alt="" />
			</div>
			<div class="large-6 columns sessao">
				<img src="img/04-contato.png" alt="" />
			</div>
		</div>
	</footer>

	<script src="js/vendor/jquery.js"></script>
	<script src="js/foundation.min.js"></script>
	<script>
		$(document).foundation();
	</script>
</body>
</html>