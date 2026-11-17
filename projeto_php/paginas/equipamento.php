<?php
$busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";
$termo = "%" . $busca . "%";

$sql = "SELECT equipamento.*,
               (SELECT COUNT(*) FROM emprestimo WHERE emprestimo.id_equip = equipamento.id_equip) AS qtd_emprestimos
        FROM equipamento
        WHERE status_equip != 'Obsoleto/descartado'
          AND (id_equip LIKE ? OR numero_serie LIKE ? OR tipo LIKE ? OR marca_modelo LIKE ?
               OR condicoes_atual LIKE ? OR status_equip LIKE ?)
        ORDER BY tipo, marca_modelo";
$tabela = mysqli_execute_query($conexao, $sql, [$termo, $termo, $termo, $termo, $termo, $termo]);
?>

    <center>
        <h1>Equipamentos</h1>

        <form method="get" action="index.php">
            <input type="hidden" name="pagina" value="equipamento">
            <input type="search" name="busca" value="<?php echo e($busca) ?>" size="45" placeholder="Buscar por ID, série, tipo, modelo, condição ou status">
            <button type="submit">🔍 Buscar</button>
            <?php if ($busca != "") { ?>
                <a href="index.php?pagina=equipamento">Limpar busca</a>
            <?php } ?>
        </form>
        <?php if ($busca != "") { ?>
            <p><?php echo mysqli_num_rows($tabela) ?> resultado(s) para "<?php echo e($busca) ?>"</p>
        <?php } ?>
        <br>

        <p>
            Ver equipamentos <a href="index.php?pagina=manutencao">em manutenção</a>
            ou <a href="index.php?pagina=descartados">descartados</a>
        </p>
        <p>OBS.: Apenas equipamentos sem histórico de empréstimo podem ser excluídos da lista</p>

        <table border="1">
            <tr>
                <th>ID do Equipamento</th>
                <th>Número de Série</th>
                <th>Tipo</th>
                <th>Modelo/Marca</th>
                <th>Condições Atuais</th>
                <th>Status do Equipamento</th>
                <td colspan="3" align="center">
                    <a href="dados_equipamento.php?crud=I">
                        <button>incluir</button>
                    </a>
                </td>
            </tr>

            <?php while ($linha = mysqli_fetch_array($tabela)) { ?>
                <tr>
                    <td><?php echo e($linha["id_equip"]) ?></td>
                    <td><?php echo e($linha["numero_serie"]) ?></td>
                    <td><?php echo e($linha["tipo"]) ?></td>
                    <td><?php echo e($linha["marca_modelo"]) ?></td>
                    <td><?php echo e($linha["condicoes_atual"]) ?></td>
                    <td><?php echo e($linha["status_equip"]) ?></td>
                    <td>
                        <a href="dados_equipamento.php?crud=A&id=<?php echo e($linha["id_equip"]) ?>" title="Alterar">✏️</a>
                    </td>
                    <td>
                        <a href="index.php?pagina=historico&equip=<?php echo e($linha["id_equip"]) ?>" title="Ver histórico">📜</a>
                    </td>
                    <td>
                        <?php if ($linha["qtd_emprestimos"] == 0) { ?>
                            <form action="crud_equipamento.php?crud=E" method="post" style="display:inline"
                                  onsubmit="return confirm('Tem certeza que deseja EXCLUIR o equipamento <?php echo e($linha["id_equip"]) ?>? Esta ação não pode ser desfeita.');">
                                <input type="hidden" name="id_equip" value="<?php echo e($linha["id_equip"]) ?>">
                                <button type="submit" title="Excluir">🗑️</button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        
    </center>
