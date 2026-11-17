<?php
$busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
$termo = "%" . $busca . "%";

$sql = "SELECT funcionario.*,
               (SELECT COUNT(*) FROM emprestimo WHERE emprestimo.id_func = funcionario.id_func
                                                   OR emprestimo.id_tecnico_entrega = funcionario.id_func)
             + (SELECT COUNT(*) FROM devolucao WHERE devolucao.id_tecnico_recebimento = funcionario.id_func) AS qtd_historico
        FROM funcionario
        WHERE id_func LIKE ? OR nome_completo LIKE ? OR email_corporativo LIKE ?
           OR departamento LIKE ? OR cargo LIKE ? OR status_func LIKE ?
        ORDER BY nome_completo";
$tabela = mysqli_execute_query($conexao, $sql, [$termo, $termo, $termo, $termo, $termo, $termo]);
?>

    <center>
        <h1>Funcionários</h1>

        <form method="get" action="index.php">
            <input type="hidden" name="pagina" value="funcionario">
            <input type="search" name="busca" value="<?php echo e($busca) ?>" size="45" placeholder="Buscar por ID, nome, e-mail, setor, cargo ou status">
            <button type="submit">🔍 Buscar</button>
            <?php if ($busca != "") { ?>
                <a href="index.php?pagina=funcionario">Limpar busca</a>
            <?php } ?>
        </form>
        <?php if ($busca != "") { ?>
            <p><?php echo mysqli_num_rows($tabela) ?> resultado(s) para "<?php echo e($busca) ?>"</p>
        <?php } ?>
        <br>

        <table border="1">
            <tr>
                <th>ID do Funcionário</th>
                <th>Nome Completo</th>
                <th>E-mail Corporativo</th>
                <th>Departamento/Setor</th>
                <th>Cargo</th>
                <th>Status do Funcionário</th>
                <td colspan="3" align="center">
                    <a href="dados_funcionario.php?crud=I">
                        <button>incluir</button>
                    </a>
                </td>
            </tr>

            <?php while ($linha = mysqli_fetch_array($tabela)) { ?>
                <tr>
                    <td><?php echo e($linha["id_func"]) ?></td>
                    <td><?php echo e($linha["nome_completo"]) ?></td>
                    <td><?php echo e($linha["email_corporativo"]) ?></td>
                    <td><?php echo e($linha["departamento"]) ?></td>
                    <td><?php echo e($linha["cargo"]) ?></td>
                    <td><?php echo e($linha["status_func"]) ?></td>
                    <td>
                        <a href="dados_funcionario.php?crud=A&id=<?php echo e($linha["id_func"]) ?>" title="Alterar">✏️</a>
                    </td>
                    <td>
                        <a href="index.php?pagina=historico&func=<?php echo e($linha["id_func"]) ?>" title="Ver histórico">📜</a>
                    </td>
                    <td>
                        <?php if ($linha["qtd_historico"] == 0) { ?>
                            <form action="crud_funcionario.php?crud=E" method="post" style="display:inline"
                                  onsubmit="return confirm('Tem certeza que deseja EXCLUIR o funcionário <?php echo e($linha["id_func"]) ?>? Esta ação não pode ser desfeita.');">
                                <input type="hidden" name="id_func" value="<?php echo e($linha["id_func"]) ?>">
                                <button type="submit" title="Excluir">🗑️</button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </center>
