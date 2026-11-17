<?php
require "conexao.php";

$crud = isset($_GET["crud"]) ? $_GET["crud"] : "";

if (!in_array($crud, ["I", "A", "E"]) || !isset($_POST["id_equip"])) {
    voltar("equipamento", "erro", "Erro: operação inválida.");
}

$id_equip = (int) $_POST["id_equip"];

if ($crud != "I") {
    $atual = mysqli_fetch_assoc(mysqli_execute_query($conexao,
        "SELECT status_equip FROM equipamento WHERE id_equip = ?", [$id_equip]));

    if (!$atual) {
        voltar("equipamento", "erro", "Erro: equipamento $id_equip não encontrado.");
    }
    if ($atual["status_equip"] == "Obsoleto/descartado") {
        voltar("equipamento", "erro", "Equipamentos descartados não podem ser alterados nem excluídos.");
    }
}

if ($crud == "E") {
    try {
        mysqli_execute_query($conexao, "DELETE FROM equipamento WHERE id_equip = ?", [$id_equip]);
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            voltar("equipamento", "erro",
                "Não é possível excluir o equipamento $id_equip: ele tem empréstimos no histórico. "
                . "Se ele não será mais usado, altere o status para \"Obsoleto/descartado\".");
        }
        voltar("equipamento", "erro", "Erro ao excluir o equipamento: " . $e->getMessage());
    }

    voltar("equipamento", "sucesso", "Equipamento $id_equip excluído com sucesso.");
}

$numero_serie    = trim($_POST["numero_serie"]);
$tipo            = trim($_POST["tipo"]);
$marca_modelo    = trim($_POST["marca_modelo"]);
$condicoes_atual = trim($_POST["condicoes_atual"]);
$status_equip    = $_POST["status_equip"];

if ($crud == "I") {
    if ($id_equip <= 0) {
        voltar("equipamento", "erro", "Erro: o ID do equipamento precisa ser um número maior que zero.");
    }
    if (!in_array($status_equip, $status_cadastro)) {
        voltar("equipamento", "erro", "Erro: um equipamento novo só pode ser cadastrado como \"Disponível\" ou \"Em manutenção\".");
    }

    try {
        mysqli_execute_query($conexao,
            "INSERT INTO equipamento (id_equip, numero_serie, tipo, marca_modelo, condicoes_atual, status_equip)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$id_equip, $numero_serie, $tipo, $marca_modelo, $condicoes_atual, $status_equip]);
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            voltar("equipamento", "erro", "Erro: já existe um equipamento com o ID $id_equip.");
        }
        voltar("equipamento", "erro", "Erro ao cadastrar o equipamento: " . $e->getMessage());
    }

    voltar("equipamento", "sucesso", "Equipamento $tipo $marca_modelo cadastrado com sucesso!");
}
else {
    if ($atual["status_equip"] == "Emprestado") {
        $status_equip = "Emprestado";
    } elseif (!in_array($status_equip, $status_devolucao)) {
        voltar("equipamento", "erro", "Erro: escolha um status da lista.");
    }

    try {
        mysqli_execute_query($conexao,
            "UPDATE equipamento SET
                 numero_serie    = ?,
                 tipo            = ?,
                 marca_modelo    = ?,
                 condicoes_atual = ?,
                 status_equip    = ?
             WHERE id_equip = ?",
            [$numero_serie, $tipo, $marca_modelo, $condicoes_atual, $status_equip, $id_equip]);
    } catch (mysqli_sql_exception $e) {
        voltar("equipamento", "erro", "Erro ao alterar o equipamento: " . $e->getMessage());
    }

    voltar("equipamento", "sucesso", "Equipamento $id_equip alterado com sucesso!");
}
?>
