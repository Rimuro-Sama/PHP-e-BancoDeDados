<?php
require "conexao.php";

$crud = isset($_GET["crud"]) ? $_GET["crud"] : "I";

if ($crud == "I") {
    $botao           = "INCLUIR";
    $id_equip        = "";
    $numero_serie    = "";
    $tipo            = "";
    $marca_modelo    = "";
    $condicoes_atual = "";
    $status_equip    = "Disponível";
    $opcoes_status   = $status_cadastro;
}
else {
    $crud     = "A";
    $botao    = "ALTERAR";
    $id_equip = (int) $_GET["id"];

    $tabela = mysqli_execute_query($conexao, "SELECT * FROM equipamento WHERE id_equip = ?", [$id_equip]);
    $linha  = mysqli_fetch_array($tabela);

    if (!$linha) {
        voltar("equipamento", "erro", "Erro: equipamento $id_equip não encontrado.");
    }
    if ($linha["status_equip"] == "Obsoleto/descartado") {
        voltar("equipamento", "erro", "Este equipamento foi descartado e não pode mais ser alterado.");
    }

    $numero_serie    = $linha["numero_serie"];
    $tipo            = $linha["tipo"];
    $marca_modelo    = $linha["marca_modelo"];
    $condicoes_atual = $linha["condicoes_atual"];
    $status_equip    = $linha["status_equip"];
    $opcoes_status   = $status_devolucao;

    mysqli_free_result($tabela);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipamento</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    <script src="app.js"></script>
</head>
<body class="pagina-form">
    <center>
        <h1>Dados do Equipamento</h1>
        <form action="crud_equipamento.php?crud=<?php echo $crud ?>" method="post"
              onsubmit="var s = document.getElementById('status_equip'); if (s && s.value == 'Obsoleto/descartado') { return confirm('Tem certeza? O equipamento NÃO poderá mais ser alterado nem voltar a ficar disponível.'); }">
            <table border="1">
                <tr>
                    <th>ID EQUIPAMENTO:</th>
                    <td>
                        <?php if ($crud == "I") { ?>
                            <input type="number" name="id_equip" min="1" required>
                        <?php } else { ?>
                            <input type="hidden" name="id_equip" value="<?php echo e($id_equip) ?>">
                            <?php echo e($id_equip) ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>NÚMERO DE SÉRIE:</th>
                    <td><input type="text" name="numero_serie" value="<?php echo e($numero_serie) ?>" maxlength="15" required></td>
                </tr>
                <tr>
                    <th>TIPO:</th>
                    <td><input type="text" name="tipo" value="<?php echo e($tipo) ?>" maxlength="15" placeholder="Notebook, tablet, mouse, celular..." required></td>
                </tr>
                <tr>
                    <th>MARCA / MODELO:</th>
                    <td><input type="text" name="marca_modelo" value="<?php echo e($marca_modelo) ?>" maxlength="20" required></td>
                </tr>
                <tr>
                    <th>CONDIÇÃO ATUAL:</th>
                    <td><input type="text" name="condicoes_atual" value="<?php echo e($condicoes_atual) ?>" maxlength="100" placeholder="Estado físico atual (excelente, marca de uso, tela trincada...)" required></td>
                </tr>
                <tr>
                    <th>STATUS:</th>
                    <td>
                        <?php if ($status_equip == "Emprestado") { ?>
                            Emprestado (só muda pela devolução)
                            <input type="hidden" name="status_equip" value="Emprestado">
                        <?php } else { ?>
                            <select name="status_equip" id="status_equip" required>
                                <?php foreach ($opcoes_status as $s) { ?>
                                    <option value="<?php echo e($s) ?>" <?php if ($status_equip == $s) echo "selected" ?>><?php echo e($s) ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
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
                <a href="index.php?pagina=equipamento">
            <button>&larr; Voltar</button>
        </a>
    </center>
</body>
</html>
<?php
mysqli_close($conexao);
?>
