<?php
require "conexao.php";

$usuario = isset($_POST["usuario"]) ? trim($_POST["usuario"]) : "";
$senha   = isset($_POST["senha"]) ? $_POST["senha"] : "";

if ($usuario === $admin_usuario && password_verify($senha, $admin_senha_hash)) {
    session_regenerate_id(true);
    $_SESSION["admin"] = $admin_usuario;
    header("Location: index.php");
    exit;
}

$_SESSION["erro_login"] = "Usuário ou senha incorretos.";
header("Location: login.php");
exit;
?>
