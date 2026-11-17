<?php
$id_equip = isset($_GET["equip"]) ? (int) $_GET["equip"] : 0;
$id_func  = isset($_GET["func"])  ? (int) $_GET["func"]  : 0;

if ($id_equip > 0) {
    $item = mysqli_fetch_assoc(mysqli_execute_query($conexao, "SELECT * FROM equipamento WHERE id_equip = ?", [$id_equip]));
    $coluna = "emprestimo.id_equip";
    $id = $id_equip;
    $voltar_para = "equipamento";
} else {
    $item = mysqli_fetch_assoc(mysqli_execute_query($conexao, "SELECT * FROM funcionario WHERE id_func = ?", [$id_func]));
    $coluna = "emprestimo.id_func";
    $id = $id_func;
    $voltar_para = "funcionario";
}
?>

<center>
<?php if (!$item) { ?>

    <h1>Histórico</h1>
    <p>Registro não encontrado.</p>

<?php } else {
    $sql_historico = "SELECT emprestimo.*,
                             funcionario.nome_completo AS funcionario,
                             equipamento.tipo, equipamento.marca_modelo,
                             tec_entrega.nome_completo AS tecnico_entrega,
                             devolucao.data_devolucao, devolucao.condicoes_devolucao,
                             devolucao.status_equip AS status_devolucao,
                             tec_recebeu.nome_completo AS tecnico_recebeu,
                             DATEDIFF(CURDATE(), emprestimo.previsao_devolucao) AS dias_atraso
                      FROM emprestimo
                      JOIN funcionario                     ON funcionario.id_func = emprestimo.id_func
                      JOIN equipamento                     ON equipamento.id_equip = emprestimo.id_equip
                      JOIN funcionario AS tec_entrega      ON tec_entrega.id_func = emprestimo.id_tecnico_entrega
                      LEFT JOIN devolucao                  ON devolucao.id_emprest = emprestimo.id_emprest
                      LEFT JOIN funcionario AS tec_recebeu ON tec_recebeu.id_func = devolucao.id_tecnico_recebimento
                      WHERE $coluna = ?
                      ORDER BY emprestimo.data_retirada DESC, emprestimo.id_emprest DESC";
    $tabela = mysqli_execute_query($conexao, $sql_historico, [$id]);
    $total  = mysqli_num_rows($tabela);
?>

    <?php if ($id_equip > 0) { ?>
        <h1>Histórico do equipamento <?php echo e($item["id_equip"]) ?></h1>
        <p>
            <b><?php echo e($item["tipo"] . " " . $item["marca_modelo"]) ?></b> (série <?php echo e($item["numero_serie"]) ?>)<br>
            Status atual: <b><?php echo e($item["status_equip"]) ?></b> | Condição atual: <?php echo e($item["condicoes_atual"]) ?>
        </p>
    <?php } else {
        $atendimentos = mysqli_fetch_assoc(mysqli_execute_query($conexao,
            "SELECT (SELECT COUNT(*) FROM emprestimo WHERE id_tecnico_entrega = ?) AS entregas,
                    (SELECT COUNT(*) FROM devolucao WHERE id_tecnico_recebimento = ?) AS recebimentos",
            [$id_func, $id_func]));
    ?>
        <h1>Histórico de <?php echo e($item["nome_completo"]) ?></h1>
        <p>
            ID <?php echo e($item["id_func"]) ?> | <?php echo e($item["departamento"]) ?> | <?php echo e($item["cargo"]) ?>
            | Status: <b><?php echo e($item["status_func"]) ?></b>
        </p>
        <?php if ($atendimentos["entregas"] > 0 || $atendimentos["recebimentos"] > 0) { ?>
            <p>Como técnico: entregou <b><?php echo $atendimentos["entregas"] ?></b> equipamento(s) e recebeu <b><?php echo $atendimentos["recebimentos"] ?></b> devolução(ões).</p>
        <?php } ?>
    <?php } ?>

    <h2>Empréstimos (<?php echo $total ?>)</h2>

    <?php if ($total == 0) { ?>
        <p>Nenhum empréstimo registrado.</p>
    <?php } else { ?>
        <table border="1">
            <tr>
                <th>Nº</th>
                <th><?php echo $id_equip > 0 ? "Funcionário" : "Equipamento" ?></th>
                <th>Retirada</th>
                <th>Previsão</th>
                <th>Técnico que entregou</th>
                <th>Condição na retirada</th>
                <th>Devolução</th>
                <th>Técnico que recebeu</th>
                <th>Condição na devolução</th>
                <th>Situação</th>
            </tr>
            <?php while ($h = mysqli_fetch_assoc($tabela)) {
                $aberto   = $h["data_devolucao"] == null;
                $atrasado = $aberto && $h["dias_atraso"] > 0;
            ?>
                <tr <?php if ($atrasado) echo 'class="atrasado"' ?>>
                    <td><?php echo e($h["id_emprest"]) ?></td>
                    <td>
                        <?php if ($id_equip > 0) { ?>
                            <a href="index.php?pagina=historico&func=<?php echo e($h["id_func"]) ?>"><?php echo e($h["funcionario"]) ?></a>
                        <?php } else { ?>
                            <a href="index.php?pagina=historico&equip=<?php echo e($h["id_equip"]) ?>"><?php echo e($h["tipo"] . " " . $h["marca_modelo"]) ?></a>
                        <?php } ?>
                    </td>
                    <td><?php echo data_br($h["data_retirada"]) ?></td>
                    <td><?php echo data_br($h["previsao_devolucao"]) ?></td>
                    <td><?php echo e($h["tecnico_entrega"]) ?></td>
                    <td><?php echo e($h["condicao_retirada"]) ?></td>
                    <td><?php echo data_br($h["data_devolucao"]) ?></td>
                    <td><?php echo e($h["tecnico_recebeu"]) ?></td>
                    <td><?php echo e($h["condicoes_devolucao"]) ?></td>
                    <td>
                        <?php if ($atrasado) { ?>
                            <span class="aviso-atraso">Em aberto, atrasado há <?php echo e($h["dias_atraso"]) ?> dia(s)</span>
                        <?php } elseif ($aberto) { ?>
                            Em aberto
                        <?php } else { ?>
                            Devolvido (<?php echo e($h["status_devolucao"]) ?>)
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

<?php } ?>

    <p><a href="index.php?pagina=<?php echo e($voltar_para) ?>">Voltar</a></p>
</center>
