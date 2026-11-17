<?php
require "conexao.php";

$crud = isset($_GET["crud"]) ? $_GET["crud"] : "I";

if ($crud == "I") {
    $botao = "INCLUIR";

    $lista_func = mysqli_query($conexao,
        "SELECT id_func, nome_completo FROM funcionario WHERE status_func = 'Ativo' ORDER BY nome_completo");
    $lista_equip = mysqli_query($conexao,
        "SELECT id_equip, tipo, marca_modelo, numero_serie FROM equipamento WHERE status_equip = 'Disponível' ORDER BY tipo");
    $lista_tec = mysqli_query($conexao,
        "SELECT id_func, nome_completo FROM funcionario WHERE departamento = 'TI' AND status_func = 'Ativo' ORDER BY nome_completo");
}
else {
    $crud       = "A";
    $botao      = "PRORROGAR";
    $id_emprest = (int) $_GET["id"];

    $tabela = mysqli_execute_query($conexao,
        "SELECT emprestimo.*, funcionario.nome_completo, equipamento.tipo, equipamento.marca_modelo,
                tecnico.nome_completo AS nome_tecnico,
                (SELECT COUNT(*) FROM devolucao WHERE devolucao.id_emprest = emprestimo.id_emprest) AS devolvido
         FROM emprestimo
         JOIN funcionario ON emprestimo.id_func  = funcionario.id_func
         JOIN equipamento ON emprestimo.id_equip = equipamento.id_equip
         JOIN funcionario AS tecnico ON emprestimo.id_tecnico_entrega = tecnico.id_func
         WHERE emprestimo.id_emprest = ?", [$id_emprest]);
    $linha = mysqli_fetch_array($tabela);

    if (!$linha) {
        voltar("emprestimo", "erro", "Erro: empréstimo nº $id_emprest não encontrado.");
    }
    if ($linha["devolvido"] > 0) {
        voltar("emprestimo", "erro", "Erro: o empréstimo nº $id_emprest já foi devolvido e não pode ser alterado.");
    }

    mysqli_free_result($tabela);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empréstimo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    <script src="app.js"></script>
</head>
<body class="pagina-form">
    <center>
        <h1><?php echo $crud == "I" ? "Registrar Empréstimo" : "Prorrogar Empréstimo" ?></h1>
        <form action="crud_emprestimo.php?crud=<?php echo $crud ?>" method="post">
            <table border="1">
                <?php if ($crud == "I") { ?>
                    <tr>
                        <th>FUNCIONÁRIO:</th>
                        <td>
                            <input type="text" name="id_func" list="lista_funcionarios" size="40" placeholder="Digite o ID ou nome" required>
                            <datalist id="lista_funcionarios">
                                <?php while ($f = mysqli_fetch_array($lista_func)) { ?>
                                    <option value="<?php echo e($f["id_func"] . " - " . $f["nome_completo"]) ?>">
                                <?php } ?>
                            </datalist>
                        </td>
                    </tr>
                    <tr>
                        <th>EQUIPAMENTO:</th>
                        <td>
                            <input type="text" name="id_equip" list="lista_equipamentos" size="40" placeholder="Digite o ID, tipo, marca ou modelo" required>
                            <datalist id="lista_equipamentos">
                                <?php while ($q = mysqli_fetch_array($lista_equip)) { ?>
                                    <option value="<?php echo e($q["id_equip"] . " - " . $q["tipo"] . " " . $q["marca_modelo"] . " (série " . $q["numero_serie"] . ")") ?>">
                                <?php } ?>
                            </datalist>
                        </td>
                    </tr>
                    <tr>
                        <th>DATA DA RETIRADA:</th>
                        <td><input type="date" name="data_retirada" value="<?php echo date("Y-m-d") ?>" required></td>
                    </tr>
                    <tr>
                        <th>PREVISÃO DE DEVOLUÇÃO:</th>
                        <td><input type="date" name="previsao_devolucao" required></td>
                    </tr>
                    <tr>
                        <th>TÉCNICO QUE ENTREGOU:</th>
                        <td>
                            <input type="text" name="id_tecnico_entrega" list="lista_tecnicos" size="40" placeholder="Digite o ID ou nome do técnico" required>
                            <datalist id="lista_tecnicos">
                                <?php while ($t = mysqli_fetch_array($lista_tec)) { ?>
                                    <option value="<?php echo e($t["id_func"] . " - " . $t["nome_completo"]) ?>">
                                <?php } ?>
                            </datalist>
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <th>Nº EMPRÉSTIMO:</th>
                        <td>
                            <input type="hidden" name="id_emprest" value="<?php echo e($id_emprest) ?>">
                            <?php echo e($id_emprest) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>FUNCIONÁRIO:</th>
                        <td><?php echo e($linha["id_func"] . " - " . $linha["nome_completo"]) ?></td>
                    </tr>
                    <tr>
                        <th>EQUIPAMENTO:</th>
                        <td><?php echo e($linha["tipo"] . " " . $linha["marca_modelo"]) ?></td>
                    </tr>
                    <tr>
                        <th>DATA DA RETIRADA:</th>
                        <td><?php echo data_br($linha["data_retirada"]) ?></td>
                    </tr>
                    <tr>
                        <th>TÉCNICO QUE ENTREGOU:</th>
                        <td><?php echo e($linha["id_tecnico_entrega"] . " - " . $linha["nome_tecnico"]) ?></td>
                    </tr>
                    <tr>
                        <th>PREVISÃO ATUAL:</th>
                        <td><?php echo data_br($linha["previsao_devolucao"]) ?></td>
                    </tr>
                    <tr>
                        <th>NOVA PREVISÃO:</th>
                        <td><input type="date" name="previsao_devolucao" min="<?php echo date("Y-m-d") ?>" required></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value="<?php echo $botao ?>">
                                                <input type="reset" value="LIMPAR">
                    </td>
                </tr>
            </table>
        </form>
                <a href="index.php?pagina=emprestimo">
            <button>&larr; Voltar</button>
        </a>
    </center>
</body>
</html>
<?php
mysqli_close($conexao);
?>
