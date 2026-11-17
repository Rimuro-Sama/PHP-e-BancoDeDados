<?php
require "conexao.php";

$crud = isset($_GET["crud"]) ? $_GET["crud"] : "";

if ($crud != "I" || !isset($_POST["id_emprest"])) {
    voltar("devolucao", "erro", "Erro: operação inválida. Devoluções só podem ser incluídas.");
}

$id_emprest             = (int) $_POST["id_emprest"];
$data_devolucao         = $_POST["data_devolucao"];
$condicoes_devolucao    = trim($_POST["condicoes_devolucao"]);
$id_tecnico_recebimento = (int) $_POST["id_tecnico_recebimento"];
$status_equip           = $_POST["status_equip"];

$emp = mysqli_fetch_assoc(mysqli_execute_query($conexao,
    "SELECT id_equip, data_retirada FROM emprestimo WHERE id_emprest = ?", [$id_emprest]));
if (!$emp) {
    voltar("devolucao", "erro", "Erro: empréstimo não encontrado. Escolha um da lista.");
}
$id_equip = $emp["id_equip"];

if (!in_array($status_equip, $status_devolucao)) {
    voltar("devolucao", "erro", "Erro: escolha um status da lista.");
}

$tec = mysqli_fetch_assoc(mysqli_execute_query($conexao,
    "SELECT departamento, status_func FROM funcionario WHERE id_func = ?", [$id_tecnico_recebimento]));
if (!$tec || $tec["departamento"] != "TI" || $tec["status_func"] != "Ativo") {
    voltar("devolucao", "erro", "Erro: o técnico que recebeu precisa ser um funcionário ativo do TI.");
}

if ($data_devolucao < $emp["data_retirada"]) {
    voltar("devolucao", "erro", "Erro: a data de devolução não pode ser antes da retirada (" . data_br($emp["data_retirada"]) . ").");
}

try {
    mysqli_execute_query($conexao,
        "INSERT INTO devolucao (id_emprest, data_devolucao, condicoes_devolucao, id_tecnico_recebimento, status_equip)
         VALUES (?, ?, ?, ?, ?)",
        [$id_emprest, $data_devolucao, $condicoes_devolucao, $id_tecnico_recebimento, $status_equip]);

    mysqli_execute_query($conexao,
        "UPDATE equipamento SET status_equip = ?, condicoes_atual = ? WHERE id_equip = ?",
        [$status_equip, $condicoes_devolucao, $id_equip]);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        voltar("devolucao", "erro", "Erro: o empréstimo nº $id_emprest já foi devolvido.");
    }
    voltar("devolucao", "erro", "Erro ao registrar a devolução: " . $e->getMessage());
}

voltar("devolucao", "sucesso", "Devolução do empréstimo nº $id_emprest registrada com sucesso!");
?>
