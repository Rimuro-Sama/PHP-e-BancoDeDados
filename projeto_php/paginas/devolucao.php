
    <center>
        <h1>Histórico de Devoluções</h1>

        <?php
        $busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
        $termo = "%" . $busca . "%";

        $sql_devolucao = "SELECT devolucao.*, emprestimo.condicao_retirada, emprestimo.data_retirada, emprestimo.id_func,
                                 funcionario.nome_completo, equipamento.tipo, equipamento.marca_modelo, tecnico.nome_completo AS nome_tecnico
                          FROM devolucao
                          JOIN emprestimo  ON devolucao.id_emprest = emprestimo.id_emprest
                          JOIN funcionario ON emprestimo.id_func   = funcionario.id_func
                          JOIN equipamento ON emprestimo.id_equip  = equipamento.id_equip
                          JOIN funcionario AS tecnico ON devolucao.id_tecnico_recebimento = tecnico.id_func
                          WHERE devolucao.id_emprest LIKE ? OR funcionario.nome_completo LIKE ? OR equipamento.tipo LIKE ?
                             OR equipamento.marca_modelo LIKE ? OR tecnico.nome_completo LIKE ?
                             OR devolucao.condicoes_devolucao LIKE ? OR devolucao.status_equip LIKE ?
                          ORDER BY devolucao.data_devolucao DESC";
        $tabela_devolucao = mysqli_execute_query($conexao, $sql_devolucao, [$termo, $termo, $termo, $termo, $termo, $termo, $termo]);
        ?>

        <form method="get" action="index.php">
            <input type="hidden" name="pagina" value="devolucao">
            <input type="search" name="busca" value="<?php echo e($busca) ?>" size="45" placeholder="Buscar por nº, funcionário, equipamento, técnico, condição ou status">
            <button type="submit">🔍 Buscar</button>
            <?php if ($busca != "") { ?>
                <a href="index.php?pagina=devolucao">Limpar busca</a>
            <?php } ?>
        </form>
        <?php if ($busca != "") { ?>
            <p><?php echo mysqli_num_rows($tabela_devolucao) ?> resultado(s) para "<?php echo e($busca) ?>"</p>
        <?php } ?>
        <br>

        <table border="1">
            <tr>
            <th>Nº Empréstimo</th>
            <th>ID - Funcionário</th>
            <th>Equipamento</th>
            <th>Data de Retirada</th>
            <th>Data de Devolução</th>
            <th>ID - Técnico que recebeu</th>
            <th>Condição na retirada</th>
            <th>Condição na devolução</th>
            <th>Status</th>
            <td align="center">
                <a href="dados_devolucao.php?crud=I">
                    <button>incluir</button>
                </a>
            </td>
        </tr>
               
            <?php
            while ($h = mysqli_fetch_array($tabela_devolucao)) {

                ?>
                <tr>
                    <td><?php echo e($h["id_emprest"]) ?></td>
                    <td><?php echo e($h["id_func"]. " - " . $h["nome_completo"]) ?></td>
                    <td><?php echo e($h["tipo"] . " " . $h["marca_modelo"]) ?></td>
                    <td><?php echo data_br($h["data_retirada"]) ?></td>
                    <td><?php echo data_br($h["data_devolucao"]) ?></td>
                    <td><?php echo e($h["id_tecnico_recebimento"]. " - " . $h["nome_tecnico"]) ?></td>
                    <td><?php echo e($h["condicao_retirada"]) ?></td>
                    <td><?php echo e($h["condicoes_devolucao"]) ?></td>
                    <td><?php echo e($h["status_equip"]) ?></td>
                    <td></td>
                </tr>    
                
                <?php } ?>           
        
        </table>
    </center>