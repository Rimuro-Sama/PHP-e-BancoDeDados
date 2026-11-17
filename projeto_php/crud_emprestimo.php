<?php
require "conexao.php";

$crud = isset($_GET["crud"]) ? $_GET["crud"] : "";

if ($crud == "I" && isset($_POST["id_func"])) {
    $id_func            = (int) $_POST["id_func"];
    $id_equip           = (int) $_POST["id_equip"];
    $data_retirada      = $_POST["data_retirada"];
    $previsao_devolucao = $_POST["previsao_devolucao"];
    $id_tecnico_entrega = (int) $_POST["id_tecnico_entrega"];

    $func = mysqli_fetch_assoc(mysqli_execute_query($conexao,
        "SELECT status_func FROM funcionario WHERE id_func = ?", [$id_func]));
    if (!$func) {
        voltar("emprestimo", "erro", "Erro: funcionário não encontrado. Escolha um nome da lista.");
    }
    if ($func["status_func"] != "Ativo") {
        voltar("emprestimo", "erro", "Erro: só é possível emprestar para funcionários com status \"Ativo\".");
    }

    $equip = mysqli_fetch_assoc(mysqli_execute_query($conexao,
        "SELECT status_equip, condicoes_atual FROM equipamento WHERE id_equip = ?", [$id_equip]));
    if (!$equip) {
        voltar("emprestimo", "erro", "Erro: equipamento não encontrado. Escolha um da lista.");
    }
    if ($equip["status_equip"] != "Disponível") {
        voltar("emprestimo", "erro", "Erro: este equipamento não está disponível (status atual: " . $equip["status_equip"] . ").");
    }

    $tec = mysqli_fetch_assoc(mysqli_execute_query($conexao,
        "SELECT departamento, status_func FROM funcionario WHERE id_func = ?", [$id_tecnico_entrega]));
    if (!$tec || $tec["departamento"] != "TI" || $tec["status_func"] != "Ativo") {
        voltar("emprestimo", "erro", "Erro: o técnico que entregou precisa ser um funcionário ativo do TI.");
    }

    if ($previsao_devolucao < $data_retirada) {
        voltar("emprestimo", "erro", "Erro: a previsão de devolução não pode ser antes da data de retirada.");
    }

    $condicao_retirada = $equip["condicoes_atual"];

    try {
        mysqli_execute_query($conexao,
            "INSERT INTO emprestimo (id_func, id_equip, data_retirada, previsao_devolucao, id_tecnico_entrega, condicao_retirada)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$id_func, $id_equip, $data_retirada, $previsao_devolucao, $id_tecnico_entrega, $condicao_retirada]);

        $numero = mysqli_insert_id($conexao);

        mysqli_execute_query($conexao,
            "UPDATE equipamento SET status_equip = 'Emprestado' WHERE id_equip = ?", [$id_equip]);
    } catch (mysqli_sql_exception $e) {
        voltar("emprestimo", "erro", "Erro ao registrar o empréstimo: " . $e->getMessage());
    }

    voltar("emprestimo", "sucesso", "Empréstimo nº $numero registrado com sucesso!");
}
elseif ($crud == "A" && isset($_POST["id_emprest"])) {
    $id_emprest    = (int) $_POST["id_emprest"];
    $nova_previsao = $_POST["previsao_devolucao"];

    $emp = mysqli_fetch_assoc(mysqli_execute_query($conexao,
        "SELECT data_retirada, previsao_devolucao,
                (SELECT COUNT(*) FROM devolucao WHERE devolucao.id_emprest = emprestimo.id_emprest) AS devolvido
         FROM emprestimo WHERE id_emprest = ?", [$id_emprest]));

    if (!$emp) {
        voltar("emprestimo", "erro", "Erro: empréstimo nº $id_emprest não encontrado.");
    }
    if ($emp["devolvido"] > 0) {
        voltar("emprestimo", "erro", "Erro: o empréstimo nº $id_emprest já foi devolvido e não pode ser prorrogado.");
    }
    if (!strtotime($nova_previsao)) {
        voltar("emprestimo", "erro", "Erro: informe uma data válida para a nova previsão.");
    }
    if ($nova_previsao < date("Y-m-d")) {
        voltar("emprestimo", "erro", "Erro: a nova previsão não pode ser uma data que já passou.");
    }
    if ($nova_previsao == $emp["previsao_devolucao"]) {
        voltar("emprestimo", "erro", "Erro: a nova previsão é igual à atual (" . data_br($nova_previsao) . ").");
    }

    try {
        mysqli_execute_query($conexao,
            "UPDATE emprestimo SET previsao_devolucao = ? WHERE id_emprest = ?",
            [$nova_previsao, $id_emprest]);
    } catch (mysqli_sql_exception $e) {
        voltar("emprestimo", "erro", "Erro ao prorrogar o empréstimo: " . $e->getMessage());
    }

    voltar("emprestimo", "sucesso",
        "Empréstimo nº $id_emprest prorrogado: previsão mudou de " . data_br($emp["previsao_devolucao"]) . " para " . data_br($nova_previsao) . ".");
}

voltar("emprestimo", "erro", "Erro: operação inválida.");
?>
