<?php

session_start();
date_default_timezone_set("America/Sao_Paulo");

$admin_usuario    = 'admin';
$admin_senha_hash = '$2y$10$L3FDMzLdM4CHQ1VYFVomr.V1YQ0kakvVNU../OxSemYV9D0UGFtRq';

$paginas_livres = ['login.php', 'entrar.php'];

if (!isset($_SESSION["admin"]) && !in_array(basename($_SERVER["SCRIPT_NAME"]), $paginas_livres)) {
    header("Location: login.php");
    exit;
}

$conexao = mysqli_connect("localhost", "root", "", "bd_emprestimos_ti") or die("Falha na conexão");
mysqli_set_charset($conexao, "utf8mb4");

$departamentos      = ['Administrativo', 'Comercial', 'Financeiro', 'Jurídico', 'Logística', 'Marketing', 'RH', 'Suporte', 'TI'];
$status_funcionario = ['Ativo', 'Afastado', 'Desligado'];
$status_equipamento = ['Disponível', 'Emprestado', 'Em manutenção', 'Obsoleto/descartado'];
$status_devolucao   = ['Disponível', 'Em manutenção', 'Obsoleto/descartado'];
$status_cadastro    = ['Disponível', 'Em manutenção'];

function voltar($pagina, $tipo, $texto) {
    $_SESSION["mensagem"] = ["tipo" => $tipo, "texto" => $texto];
    header("Location: index.php?pagina=" . $pagina);
    exit;
}

function e($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

function data_br($data) {
    return $data ? date("d/m/Y", strtotime($data)) : "";
}
?>
