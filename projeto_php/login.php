<?php
require "conexao.php";

if (isset($_SESSION["admin"])) {
    header("Location: index.php");
    exit;
}

$erro = "";
if (isset($_SESSION["erro_login"])) {
    $erro = $_SESSION["erro_login"];
    unset($_SESSION["erro_login"]);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Empréstimos TI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    <script src="app.js"></script>
</head>
<body class="pagina-login">
    <div class="login-cartao">
        <div class="marca-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        </div>
        <h1>Empréstimos TI</h1>
        <p class="sub">Acesso do administrador</p>

        <?php if ($erro != "") { ?>
            <div class="erro-login" role="alert"><?php echo e($erro) ?></div>
        <?php } ?>

        <form action="entrar.php" method="post">
            <label>Usuário
                <input type="text" name="usuario" autocomplete="username" placeholder="Digite seu usuário" required autofocus>
            </label>
            <label>Senha
                <input type="password" name="senha" autocomplete="current-password" placeholder="Digite sua senha" required>
            </label>
            <input type="submit" value="Entrar">
        </form>

        <p class="login-pe">Sistema de gerenciamento de empréstimos de equipamentos de TI</p>
    </div>
</body>
</html>
<?php
mysqli_close($conexao);
?>
