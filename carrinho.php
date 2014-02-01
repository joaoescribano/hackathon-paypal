<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set("America/Sao_Paulo");
header('Content-Type: text/html; charset=utf-8');
ini_set("memory_limit", "1024M");
?>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Tri Force - Carrinho</title>
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

	<?php
	if (isset($_GET['erro'])) {
		?>
		<div class="row">
			<div class="large-12 columns">
				<br/>
				<div data-alert class="alert-box warning radius">
					<b>Atenção: </b><?php echo $_GET['erro']; ?>
					<a href="#" class="close">&times;</a>
				</div>
			</div>
		</div>
		<?php
	}
	?>

	<?php
	if (isset($_GET['sucesso'])) {
		?>
		<div class="row">
			<div class="large-12 columns">
				<br/>
				<div data-alert class="alert-box success radius">
					<b>Sucesso: </b><?php echo $_GET['sucesso']; if (isset($_GET['status']) && $_GET['status'] == "Completed") { echo "<br/><br/>Caso deseje cancelar esta compra, <a href='cancelarCompra.php'>Clique Aqui</a><br/><small>OBS: Somente 75% do valor será devolvido</small>"; } ?>
					<a href="#" class="close">&times;</a>
				</div>
			</div>
		</div>
		<?php
	}
	?>

	<div class="row">
		<div class="large-12 columns">
			<table width="100%" style="margin-top: 2em;">
				<thead>
					<tr>
						<th>Item</th>
						<th class="text-center">Quantidade</th>
						<th class="text-center">Valor</th>
						<th class="text-center">Valor Total</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total = 0;
					foreach ($_SESSION['cart'] as $key => $produto) {
						$total += ($produto['value'] * $produto['qtd']);
						?>
						<tr>
							<td><?php echo $produto['name']."<br/><small>".$produto['desc']."</small>"; ?></td>
							<td class="text-center"><?php echo $produto['qtd']; ?></td>
							<td class="text-center">R$ <?php echo number_format($produto['value'], 2, ",", "."); ?></td>
							<td class="text-center">R$ <?php echo number_format(($produto['value'] * $produto['qtd']), 2, ",", "."); ?></td>
							<td class="text-center"><a href="remover.php?item=<?php echo $key; ?>" class="button radius tiny alert">X</a></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="large-12 columns">
				<div class="panel">
					<h5>Detalhes da Compra</h5>

					<b>Valor Total:</b> R$ <?php echo number_format($total, 2, ",", ".")?><br/>
				</div>
				<br/>
				<br/>
				<a href="checkout.php" class="right" style="margin-left: 1em;"><img src="img/botao-checkout_horizontal_finalizecom_ap.png" alt="Finalizar Compra com PayPal"></a>
				<a href="carrinho.php?erro=Funcionalidade Inexistente" class="button radius tiny right" style="margin-left: 1em;">Finalizar Compra</a>
				<a href="index.php" class="button radius tiny right success">Voltar as Compra</a>
			</div>
		</div>

		<section id="robo">
			<div class="row">
				<div class="large-12 columns">
					<p class="texto">&nbsp;</p>

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
