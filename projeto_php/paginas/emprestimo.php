
    <center>
        <h1>Empréstimos em aberto</h1>

        <?php
        $sql_atrasados = "SELECT COUNT(*) AS total FROM emprestimo
                          WHERE id_emprest NOT IN (SELECT id_emprest FROM devolucao)
                            AND previsao_devolucao < CURDATE()";
        $atrasados = mysqli_fetch_assoc(mysqli_query($conexao, $sql_atrasados));

        if ($atrasados["total"] > 0) { ?>
            <p class="aviso-atraso">⚠️ Há <?php echo e($atrasados["total"]) ?> empréstimo(s) atrasado(s), destacado(s) em vermelho abaixo.</p>
        <?php } ?>

        <?php
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $termo = "%" . $busca . "%";

        $sql_emprestimo = "SELECT emprestimo.*, funcionario.nome_completo, equipamento.tipo, equipamento.marca_modelo, tecnico.nome_completo AS nome_tecnico,
                                  DATEDIFF(CURDATE(), emprestimo.previsao_devolucao) AS dias_atraso
                          FROM emprestimo
                          JOIN funcionario ON emprestimo.id_func   = funcionario.id_func
                          JOIN equipamento ON emprestimo.id_equip  = equipamento.id_equip
                          JOIN funcionario AS tecnico ON emprestimo.id_tecnico_entrega = tecnico.id_func
                          WHERE emprestimo.id_emprest NOT IN (SELECT id_emprest FROM devolucao)
                            AND (emprestimo.id_emprest LIKE ? OR funcionario.nome_completo LIKE ? OR equipamento.tipo LIKE ?
                                 OR equipamento.marca_modelo LIKE ? OR tecnico.nome_completo LIKE ?)
                          ORDER BY emprestimo.data_retirada DESC";
        $tabela_emprestimo = mysqli_execute_query($conexao, $sql_emprestimo, [$termo, $termo, $termo, $termo, $termo]);
        ?>

        <form method="get" action="index.php">
            <input type="hidden" name="pagina" value="emprestimo">
            <input type="search" name="busca" value="<?php echo e($busca) ?>" size="45" placeholder="Buscar por nº, funcionário, equipamento ou técnico">
            <button type="submit">🔍 Buscar</button>
            <?php if ($busca != "") { ?>
                <a href="index.php?pagina=emprestimo">Limpar busca</a>
            <?php } ?>
        </form>
        <?php if ($busca != "") { ?>
            <p><?php echo mysqli_num_rows($tabela_emprestimo) ?> resultado(s) para "<?php echo e($busca) ?>"</p>
        <?php } ?>
        <br>

        <table border="1">
            <tr>
                <th>Nº Empréstimo</th>
                <th>ID - Funcionário</th>
                <th>Equipamento</th>
                <th>Data de Retirada</th>
                <th>Previsão de Devolução</th>
                <th>ID - Técnico que entregou</th>
                <th>Condição na retirada</th>
                <td colspan="2" align="center">
                    <a href="dados_emprestimo.php?crud=I">
                        <button>incluir</button>
                    </a>
                </td>
            </tr>

            <?php while ($linha = mysqli_fetch_array($tabela_emprestimo)) {
                $atrasado = $linha["dias_atraso"] > 0;
            ?>
                <tr <?php if ($atrasado) echo 'class="atrasado"' ?>>
                    <td><?php echo e($linha["id_emprest"]) ?></td>
                    <td><a href="index.php?pagina=historico&func=<?php echo e($linha["id_func"]) ?>"><?php echo e($linha["id_func"] . " - " . $linha["nome_completo"]) ?></a></td>
                    <td><a href="index.php?pagina=historico&equip=<?php echo e($linha["id_equip"]) ?>"><?php echo e($linha["tipo"] . " " . $linha["marca_modelo"]) ?></a></td>
                    <td><?php echo data_br($linha["data_retirada"]) ?></td>
                    <td>
                        <?php echo data_br($linha["previsao_devolucao"]) ?>
                        <?php if ($atrasado) { ?>
                            <br><span class="aviso-atraso">⚠️ ATRASADO há <?php echo e($linha["dias_atraso"]) ?> dia(s)</span>
                        <?php } ?>
                    </td>
                    <td><?php echo e($linha["id_tecnico_entrega"] . " - " . $linha["nome_tecnico"]) ?></td>
                    <td><?php echo e($linha["condicao_retirada"]) ?></td>
                    <td>
                        <a href="dados_emprestimo.php?crud=A&id=<?php echo e($linha["id_emprest"]) ?>" title="Prorrogar">✏️</a>
                    </td>
                    <td>
                        <a href="dados_devolucao.php?crud=I&id=<?php echo e($linha["id_emprest"]) ?>" title="Registrar devolução">↩️</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </center>
