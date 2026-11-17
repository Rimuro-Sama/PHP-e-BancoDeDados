<?php
require "conexao.php";

$crud = isset($_GET["crud"]) ? $_GET["crud"] : "";

if (!in_array($crud, ["I", "A", "E"]) || !isset($_POST["id_func"])) {
    voltar("funcionario", "erro", "Erro: operação inválida.");
}

$id_func = (int) $_POST["id_func"];

if ($crud == "E") {
    try {
        mysqli_execute_query($conexao, "DELETE FROM funcionario WHERE id_func = ?", [$id_func]);
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            voltar("funcionario", "erro",
                "Não é possível excluir o funcionário $id_func: ele tem empréstimos ou devoluções no histórico. "
                . "Se ele saiu da empresa, altere o status para \"Desligado\".");
        }
        voltar("funcionario", "erro", "Erro ao excluir o funcionário: " . $e->getMessage());
    }

    if (mysqli_affected_rows($conexao) == 0) {
        voltar("funcionario", "erro", "Erro: funcionário $id_func não encontrado.");
    }

    voltar("funcionario", "sucesso", "Funcionário $id_func excluído com sucesso.");
}

$nome_completo     = trim($_POST["nome_completo"]);
$email_corporativo = trim($_POST["email_corporativo"]);
$departamento      = $_POST["departamento"];
$cargo             = trim($_POST["cargo"]);
$status_func       = $_POST["status_func"];

if ($id_func <= 0) {
    voltar("funcionario", "erro", "Erro: o ID do funcionário precisa ser um número maior que zero.");
}
if (!filter_var($email_corporativo, FILTER_VALIDATE_EMAIL)) {
    voltar("funcionario", "erro", "Erro: o e-mail \"$email_corporativo\" não é válido.");
}
if (!in_array($departamento, $departamentos)) {
    voltar("funcionario", "erro", "Erro: escolha um departamento da lista.");
}
if (!in_array($status_func, $status_funcionario)) {
    voltar("funcionario", "erro", "Erro: escolha um status da lista.");
}

if ($crud == "I") {
    try {
        mysqli_execute_query($conexao,
            "INSERT INTO funcionario (id_func, nome_completo, email_corporativo, departamento, cargo, status_func)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$id_func, $nome_completo, $email_corporativo, $departamento, $cargo, $status_func]);
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            voltar("funcionario", "erro", "Erro: já existe um funcionário com o ID $id_func.");
        }
        voltar("funcionario", "erro", "Erro ao cadastrar o funcionário: " . $e->getMessage());
    }

    voltar("funcionario", "sucesso", "Funcionário $nome_completo cadastrado com sucesso!");
}
else {
    if ($status_func == "Desligado") {
        $abertos = mysqli_execute_query($conexao,
            "SELECT emprestimo.id_emprest, equipamento.tipo, equipamento.marca_modelo
             FROM emprestimo
             JOIN equipamento ON emprestimo.id_equip = equipamento.id_equip
             WHERE emprestimo.id_func = ?
               AND emprestimo.id_emprest NOT IN (SELECT id_emprest FROM devolucao)
             ORDER BY emprestimo.id_emprest", [$id_func]);

        if (mysqli_num_rows($abertos) > 0) {
            $itens = [];
            while ($a = mysqli_fetch_assoc($abertos)) {
                $itens[] = "nº " . $a["id_emprest"] . " (" . $a["tipo"] . " " . $a["marca_modelo"] . ")";
            }
            voltar("funcionario", "erro",
                "Não é possível mudar o status de $nome_completo para \"Desligado\": ainda há "
                . count($itens) . " empréstimo(s) em aberto: " . implode(", ", $itens)
                . ". Registre a devolução primeiro.");
        }
    }

    try {
        mysqli_execute_query($conexao,
            "UPDATE funcionario SET
                 nome_completo     = ?,
                 email_corporativo = ?,
                 departamento      = ?,
                 cargo             = ?,
                 status_func       = ?
             WHERE id_func = ?",
            [$nome_completo, $email_corporativo, $departamento, $cargo, $status_func, $id_func]);
    } catch (mysqli_sql_exception $e) {
        voltar("funcionario", "erro", "Erro ao alterar o funcionário: " . $e->getMessage());
    }

    voltar("funcionario", "sucesso", "Dados de $nome_completo alterados com sucesso!");
}
?>
