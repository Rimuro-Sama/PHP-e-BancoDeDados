<?php
$titulo = $situacao == "Em manutenção" ? "Equipamentos em manutenção" : "Equipamentos descartados";

$sql_consulta = "SELECT equipamento.*,
                        (SELECT COUNT(*) FROM emprestimo AS e3 WHERE e3.id_equip = equipamento.id_equip) AS qtd_emprestimos,
                        devolucao.data_devolucao,
                        devolucao.condicoes_devolucao,
                        usuario.nome_completo AS ultimo_usuario,
                        tecnico.nome_completo AS tecnico_recebeu
                 FROM equipamento
                 LEFT JOIN emprestimo ON emprestimo.id_emprest = (
                         SELECT e2.id_emprest
                         FROM emprestimo AS e2
                         JOIN devolucao AS d2 ON d2.id_emprest = e2.id_emprest
                         WHERE e2.id_equip = equipamento.id_equip
                           AND d2.status_equip = equipamento.status_equip
                         ORDER BY d2.data_devolucao DESC, e2.id_emprest DESC
                         LIMIT 1)
                 LEFT JOIN devolucao              ON devolucao.id_emprest = emprestimo.id_emprest
                 LEFT JOIN funcionario AS usuario ON usuario.id_func      = emprestimo.id_func
                 LEFT JOIN funcionario AS tecnico ON tecnico.id_func      = devolucao.id_tecnico_recebimento
                 WHERE equipamento.status_equip = ?
                 ORDER BY equipamento.tipo, equipamento.marca_modelo";

$tabela = mysqli_execute_query($conexao, $sql_consulta, [$situacao]);
$total  = mysqli_num_rows($tabela);
?>

<center>
    <h1><?php echo e($titulo) ?> (<?php echo $total ?>)</h1>

    <?php if ($situacao == "Em manutenção") { ?>
        <p>Para tirar um equipamento da manutenção, vá em <a href="index.php?pagina=equipamento">Equipamentos</a>, use o ✏️ e mude o status para "Disponível".</p>
    <?php } else { ?>
        <p>Equipamentos descartados não podem ser editados, emprestados nem excluídos. Ficam aqui para manter o histórico.</p>
    <?php } ?>

    <?php if ($total == 0) { ?>
        <p>Nenhum equipamento nesta situação.</p>
    <?php } else { ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Número de Série</th>
                <th>Equipamento</th>
                <th>Condição atual</th>
                <th>Devolvido nesta situação em</th>
                <th>Quem usava</th>
                <th>Técnico que recebeu</th>
                <th>Condição na devolução</th>
                <th>Histórico</th>
            </tr>
            <?php while ($c = mysqli_fetch_assoc($tabela)) { ?>
                <tr>
                    <td><?php echo e($c["id_equip"]) ?></td>
                    <td><?php echo e($c["numero_serie"]) ?></td>
                    <td><?php echo e($c["tipo"] . " " . $c["marca_modelo"]) ?></td>
                    <td><?php echo e($c["condicoes_atual"]) ?></td>
                    <?php if ($c["data_devolucao"]) { ?>
                        <td><?php echo data_br($c["data_devolucao"]) ?></td>
                        <td><?php echo e($c["ultimo_usuario"]) ?></td>
                        <td><?php echo e($c["tecnico_recebeu"]) ?></td>
                        <td><?php echo e($c["condicoes_devolucao"]) ?></td>
                    <?php } elseif ($c["qtd_emprestimos"] == 0) { ?>
                        <td colspan="4"><i>Nunca foi emprestado</i></td>
                    <?php } else { ?>
                        <td colspan="4"><i>Situação definida pela edição do equipamento (✏️), não por uma devolução</i></td>
                    <?php } ?>
                    <td><a href="index.php?pagina=historico&equip=<?php echo e($c["id_equip"]) ?>" title="Ver histórico">📜</a></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

    
</center>
