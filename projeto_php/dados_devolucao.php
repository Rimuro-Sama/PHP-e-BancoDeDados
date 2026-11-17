<?php
require "conexao.php";

$crud  = "I";
$botao = "INCLUIR";

$id_emprest = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$escolhido  = "";

$lista_abertos = mysqli_query($conexao,
    "SELECT emprestimo.id_emprest, funcionario.nome_completo, equipamento.tipo, equipamento.marca_modelo
     FROM emprestimo
     JOIN funcionario ON emprestimo.id_func  = funcionario.id_func
     JOIN equipamento ON emprestimo.id_equip = equipamento.id_equip
     WHERE emprestimo.id_emprest NOT IN (SELECT id_emprest FROM devolucao)
     ORDER BY emprestimo.id_emprest");

$abertos = [];
while ($a = mysqli_fetch_array($lista_abertos)) {
    $texto = $a["id_emprest"] . " - " . $a["nome_completo"] . " - " . $a["tipo"] . " " . $a["marca_modelo"];
    $abertos[] = $texto;
    if ($a["id_emprest"] == $id_emprest) {
        $escolhido = $texto;
    }
}

$lista_tec = mysqli_query($conexao,
    "SELECT id_func, nome_completo FROM funcionario WHERE departamento = 'TI' AND status_func = 'Ativo' ORDER BY nome_completo");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolução</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    <script src="app.js"></script>
</head>
<body class="pagina-form">
    <center>
        <h1>Registrar Devolução</h1>
        <form action="crud_devolucao.php?crud=<?php echo $crud ?>" method="post"
              onsubmit="if (document.getElementById('status_equip').value == 'Obsoleto/descartado') { return confirm('Tem certeza? O equipamento NÃO poderá mais ser alterado nem voltar a ficar disponível.'); }">
            <table border="1">
                <tr>
                    <th>EMPRÉSTIMO:</th>
                    <td>
                        <input type="text" name="id_emprest" value="<?php echo e($escolhido) ?>" list="lista_emprestimos" size="45" placeholder="Digite o nº, nome ou equipamento" required>
                        <datalist id="lista_emprestimos">
                            <?php foreach ($abertos as $texto) { ?>
                                <option value="<?php echo e($texto) ?>">
                            <?php } ?>
                        </datalist>
                    </td>
                </tr>
                <tr>
                    <th>DATA DA DEVOLUÇÃO:</th>
                    <td><input type="date" name="data_devolucao" value="<?php echo date("Y-m-d") ?>" required></td>
                </tr>
                <tr>
                    <th>CONDIÇÃO NA DEVOLUÇÃO:</th>
                    <td><input type="text" name="condicoes_devolucao" maxlength="100" size="45" placeholder="Ex.: tela trincada, sem carregador" required></td>
                </tr>
                <tr>
                    <th>TÉCNICO QUE RECEBEU:</th>
                    <td>
                        <input type="text" name="id_tecnico_recebimento" list="lista_tecnicos" size="45" placeholder="Digite o ID ou nome do técnico" required>
                        <datalist id="lista_tecnicos">
                            <?php while ($t = mysqli_fetch_array($lista_tec)) { ?>
                                <option value="<?php echo e($t["id_func"] . " - " . $t["nome_completo"]) ?>">
                            <?php } ?>
                        </datalist>
                    </td>
                </tr>
                <tr>
                    <th>STATUS DO EQUIPAMENTO:</th>
                    <td>
                        <select name="status_equip" id="status_equip" required>
                            <?php foreach ($status_devolucao as $s) { ?>
                                <option value="<?php echo e($s) ?>"><?php echo e($s) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value="<?php echo $botao ?>">
                                                <input type="reset" value="LIMPAR">
                    </td>
                </tr>
            </table>
        </form>
                <a href="index.php?pagina=devolucao">
            <button>&larr; Voltar</button>
        </a>
    </center>
</body>
</html>
<?php
mysqli_close($conexao);
?>
