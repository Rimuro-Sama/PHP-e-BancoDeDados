<?php
require "conexao.php";

$crud = isset($_GET["crud"]) ? $_GET["crud"] : "I";

if ($crud == "I") {
    $botao             = "INCLUIR";
    $id_func           = "";
    $nome_completo     = "";
    $email_corporativo = "";
    $departamento      = "";
    $cargo             = "";
    $status_func       = "Ativo";
    $qtd_abertos       = 0;
}
else {
    $crud    = "A";
    $botao   = "ALTERAR";
    $id_func = (int) $_GET["id"];

    $tabela = mysqli_execute_query($conexao, "SELECT * FROM funcionario WHERE id_func = ?", [$id_func]);
    $linha  = mysqli_fetch_array($tabela);

    if (!$linha) {
        voltar("funcionario", "erro", "Erro: funcionário $id_func não encontrado.");
    }

    $nome_completo     = $linha["nome_completo"];
    $email_corporativo = $linha["email_corporativo"];
    $departamento      = $linha["departamento"];
    $cargo             = $linha["cargo"];
    $status_func       = $linha["status_func"];

    mysqli_free_result($tabela);

    $abertos = mysqli_fetch_assoc(mysqli_execute_query($conexao,
        "SELECT COUNT(*) AS qtd FROM emprestimo
         WHERE id_func = ? AND id_emprest NOT IN (SELECT id_emprest FROM devolucao)", [$id_func]));
    $qtd_abertos = $abertos["qtd"];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionário</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    <script src="app.js"></script>
</head>
<body class="pagina-form">
    <center>
        <h1>Dados do Funcionário</h1>

        <?php if ($qtd_abertos > 0) { ?>
            <p style="color: #721c24; font-weight: bold;">
                ⚠️ Este funcionário tem <?php echo e($qtd_abertos) ?> equipamento(s) emprestado(s).
                <a href="index.php?pagina=historico&func=<?php echo e($id_func) ?>">Ver empréstimos</a>
            </p>
        <?php } ?>

        <form action="crud_funcionario.php?crud=<?php echo $crud ?>" method="post"
              onsubmit="if (<?php echo (int) $qtd_abertos ?> > 0 && this.status_func.value == 'Desligado') { alert('Não é possível desligar: este funcionário ainda tem <?php echo (int) $qtd_abertos ?> equipamento(s) emprestado(s). Registre a devolução primeiro.'); return false; }">
            <table border="1">
                <tr>
                    <th>ID FUNCIONÁRIO:</th>
                    <td>
                        <?php if ($crud == "I") { ?>
                            <input type="number" name="id_func" min="1" max="999999" required>
                        <?php } else { ?>
                            <input type="hidden" name="id_func" value="<?php echo e($id_func) ?>">
                            <?php echo e($id_func) ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>NOME:</th>
                    <td><input type="text" name="nome_completo" value="<?php echo e($nome_completo) ?>" maxlength="50" required></td>
                </tr>
                <tr>
                    <th>E-MAIL CORPORATIVO:</th>
                    <td><input type="email" name="email_corporativo" value="<?php echo e($email_corporativo) ?>" maxlength="30" placeholder="nome@empresa.com" required></td>
                </tr>
                <tr>
                    <th>DEPARTAMENTO:</th>
                    <td>
                        <select name="departamento" required>
                            <option value="">-- Selecione --</option>
                            <?php foreach ($departamentos as $d) { ?>
                                <option value="<?php echo e($d) ?>" <?php if ($departamento == $d) echo "selected" ?>><?php echo e($d) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>CARGO:</th>
                    <td><input type="text" name="cargo" value="<?php echo e($cargo) ?>" maxlength="20" required></td>
                </tr>
                <tr>
                    <th>STATUS:</th>
                    <td>
                        <select name="status_func" required>
                            <?php foreach ($status_funcionario as $s) { ?>
                                <option value="<?php echo e($s) ?>" <?php if ($status_func == $s) echo "selected" ?>><?php echo e($s) ?></option>
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
                <a href="index.php?pagina=funcionario">
            <button>&larr; Voltar</button>
        </a>
    </center>
</body>
</html>
<?php
mysqli_close($conexao);
?>
