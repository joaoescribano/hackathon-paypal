<?php
session_start();
if (isset($_GET['assinatura'])) {
	unset($_SESSION['recurring'][$_GET['item']]);
	sort($_SESSION['recurring']);
	header("Location: carrinhoAssinatura.php?sucesso=Item removido do carrinho");
} else {
	unset($_SESSION['cart'][$_GET['item']]);
	sort($_SESSION['cart']);
	header("Location: carrinho.php?sucesso=Item removido do carrinho");
}
exit();
?>