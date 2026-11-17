<?php
require "conexao.php";

$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

function ativo($atual, $nome) {
    return $atual == $nome ? ' class="ativo"' : '';
}

$nome_usuario = $_SESSION["admin"];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empréstimos TI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    <script src="app.js"></script>
</head>

<body>
<div class="app">

    <aside class="lateral" id="lateral">
        <div class="marca">
            <div class="marca-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <strong>Empréstimos TI</strong>
                <small>Gestão de equipamentos</small>
            </div>
        </div>

        <nav class="menu">
            <ul>
                <li><a href="index.php"<?php echo ativo($pagina, 'home') ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Início</a></li>

                <li class="menu-titulo">Movimentação</li>
                <li><a href="index.php?pagina=emprestimo"<?php echo ativo($pagina, 'emprestimo') ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                    Empréstimos</a></li>
                <li><a href="index.php?pagina=devolucao"<?php echo ativo($pagina, 'devolucao') ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="17" y1="7" x2="7" y2="17"/><polyline points="17 17 7 17 7 7"/></svg>
                    Devoluções</a></li>

                <li class="menu-titulo">Cadastros</li>
                <li><a href="index.php?pagina=funcionario"<?php echo ativo($pagina, 'funcionario') ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Funcionários</a></li>
                <li><a href="index.php?pagina=equipamento"<?php echo ativo($pagina, 'equipamento') ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Equipamentos</a></li>

                <li class="menu-titulo">Situação</li>
                <li><a href="index.php?pagina=manutencao"<?php echo ativo($pagina, 'manutencao') ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    Em manutenção</a></li>
                <li><a href="index.php?pagina=descartados"<?php echo ativo($pagina, 'descartados') ?>>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                    Descartados</a></li>
            </ul>
        </nav>

        <div class="lateral-rodape">
            <a href="sair.php">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Sair
            </a>
        </div>
    </aside>

    <div class="fundo-menu" id="fundo-menu"></div>

    <div class="principal">
        <header class="topo">
            <button type="button" class="icone-btn" id="btn-menu" aria-label="Abrir menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <span class="espaco"></span>
            <span class="data"><?php echo date("d/m/Y") ?></span>
            <button type="button" class="icone-btn" id="btn-tema" aria-label="Alternar tema claro/escuro" title="Alternar tema">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
            <div class="usuario">
                <div class="avatar"><?php echo e(mb_substr($nome_usuario, 0, 1)) ?></div>
                <span class="nome"><?php echo e($nome_usuario) ?></span>
            </div>
        </header>

        <main class="conteudo">

<?php
if (isset($_SESSION["mensagem"])) {
    $mensagem = $_SESSION["mensagem"];
    unset($_SESSION["mensagem"]);
?>
    <div class="mensagem <?php echo $mensagem["tipo"] == "sucesso" ? "sucesso" : "erro" ?>">
        <?php echo htmlspecialchars($mensagem["texto"]) ?>
    </div>
<?php } ?>

<?php

switch ($pagina) {
    case 'emprestimo':
        include('paginas/emprestimo.php');
        break;

    case 'devolucao':
        include('paginas/devolucao.php');
        break;

    case 'funcionario':
        include('paginas/funcionario.php');
        break;

    case 'equipamento':
        include('paginas/equipamento.php');
        break;

    case 'manutencao':
        $situacao = "Em manutenção";
        include('paginas/consulta_equipamentos.php');
        break;

    case 'descartados':
        $situacao = "Obsoleto/descartado";
        include('paginas/consulta_equipamentos.php');
        break;

    case 'historico':
        include('paginas/historico.php');
        break;

    default:
        $pagina = 'home';
        include('paginas/home.php');
        break;
}
?>

        </main>
    </div>
</div>
</body>

</html>

<?php mysqli_close($conexao); ?>
