<?php
$numeros = mysqli_fetch_assoc(mysqli_query($conexao,
    "SELECT
        (SELECT COUNT(*) FROM equipamento WHERE status_equip = 'Disponível')          AS disponiveis,
        (SELECT COUNT(*) FROM equipamento WHERE status_equip = 'Emprestado')          AS emprestados,
        (SELECT COUNT(*) FROM equipamento WHERE status_equip = 'Em manutenção')       AS manutencao,
        (SELECT COUNT(*) FROM equipamento WHERE status_equip = 'Obsoleto/descartado') AS descartados,
        (SELECT COUNT(*) FROM equipamento)                                            AS total_equip,
        (SELECT COUNT(*) FROM funcionario WHERE status_func = 'Ativo')                AS func_ativos,
        (SELECT COUNT(*) FROM funcionario)                                            AS total_func,
        (SELECT COUNT(*) FROM emprestimo WHERE id_emprest NOT IN (SELECT id_emprest FROM devolucao)) AS abertos,
        (SELECT COUNT(*) FROM emprestimo WHERE id_emprest NOT IN (SELECT id_emprest FROM devolucao)
                                           AND previsao_devolucao < CURDATE())        AS atrasados,
        (SELECT COUNT(*) FROM devolucao)                                              AS devolucoes"));

$sql_abertos = "SELECT emprestimo.id_emprest, emprestimo.id_func, emprestimo.id_equip, emprestimo.previsao_devolucao,
                       funcionario.nome_completo, equipamento.tipo, equipamento.marca_modelo,
                       DATEDIFF(CURDATE(), emprestimo.previsao_devolucao) AS dias
                FROM emprestimo
                JOIN funcionario ON funcionario.id_func = emprestimo.id_func
                JOIN equipamento ON equipamento.id_equip = emprestimo.id_equip
                WHERE emprestimo.id_emprest NOT IN (SELECT id_emprest FROM devolucao)";

$lista_atrasados = mysqli_query($conexao, $sql_abertos . " AND emprestimo.previsao_devolucao < CURDATE() ORDER BY emprestimo.previsao_devolucao");
$lista_proximos  = mysqli_query($conexao, $sql_abertos . " AND emprestimo.previsao_devolucao BETWEEN CURDATE() AND CURDATE() + INTERVAL 7 DAY ORDER BY emprestimo.previsao_devolucao");
?>

<center>
    <h1>Sistema de Empréstimos de Equipamentos de TI</h1>
    <p>Situação em <?php echo date("d/m/Y") ?></p>

    <h2>Equipamentos (<?php echo $numeros["total_equip"] ?>)</h2>
    <div class="cards">
        <a class="card-num c-ok" href="index.php?pagina=equipamento&busca=Dispon%C3%ADvel">
            <span>Disponíveis</span><strong><?php echo $numeros["disponiveis"] ?></strong>
        </a>
        <a class="card-num c-info" href="index.php?pagina=emprestimo">
            <span>Emprestados</span><strong><?php echo $numeros["emprestados"] ?></strong>
        </a>
        <a class="card-num c-warn" href="index.php?pagina=manutencao">
            <span>Em manutenção</span><strong><?php echo $numeros["manutencao"] ?></strong>
        </a>
        <a class="card-num c-neutro" href="index.php?pagina=descartados">
            <span>Descartados</span><strong><?php echo $numeros["descartados"] ?></strong>
        </a>
    </div>

    <h2>Empréstimos e funcionários</h2>
    <div class="cards">
        <a class="card-num c-acc" href="index.php?pagina=emprestimo">
            <span>Empréstimos em aberto</span><strong><?php echo $numeros["abertos"] ?></strong>
        </a>
        <div class="card-num <?php echo $numeros["atrasados"] > 0 ? "c-bad" : "c-ok" ?>">
            <span>Atrasados</span><strong><?php echo $numeros["atrasados"] ?></strong>
        </div>
        <a class="card-num c-acc" href="index.php?pagina=devolucao">
            <span>Devoluções registradas</span><strong><?php echo $numeros["devolucoes"] ?></strong>
        </a>
        <a class="card-num c-acc" href="index.php?pagina=funcionario">
            <span>Funcionários ativos</span><strong><?php echo $numeros["func_ativos"] ?> <small>de <?php echo $numeros["total_func"] ?></small></strong>
        </a>
    </div>

    <h2>⚠️ Empréstimos atrasados (<?php echo mysqli_num_rows($lista_atrasados) ?>)</h2>
    <?php if (mysqli_num_rows($lista_atrasados) == 0) { ?>
        <p>Nenhum empréstimo atrasado.</p>
    <?php } else { ?>
        <table border="1">
            <tr>
                <th>Nº</th>
                <th>Funcionário</th>
                <th>Equipamento</th>
                <th>Previsão</th>
                <th>Atraso</th>
            </tr>
            <?php while ($a = mysqli_fetch_assoc($lista_atrasados)) { ?>
                <tr class="atrasado">
                    <td><?php echo e($a["id_emprest"]) ?></td>
                    <td><a href="index.php?pagina=historico&func=<?php echo e($a["id_func"]) ?>"><?php echo e($a["nome_completo"]) ?></a></td>
                    <td><a href="index.php?pagina=historico&equip=<?php echo e($a["id_equip"]) ?>"><?php echo e($a["tipo"] . " " . $a["marca_modelo"]) ?></a></td>
                    <td><?php echo data_br($a["previsao_devolucao"]) ?></td>
                    <td><?php echo e($a["dias"]) ?> dia(s)</td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <h2>📅 Devoluções previstas para os próximos 7 dias (<?php echo mysqli_num_rows($lista_proximos) ?>)</h2>
    <?php if (mysqli_num_rows($lista_proximos) == 0) { ?>
        <p>Nenhuma devolução prevista para os próximos 7 dias.</p>
    <?php } else { ?>
        <table border="1">
            <tr>
                <th>Nº</th>
                <th>Funcionário</th>
                <th>Equipamento</th>
                <th>Previsão</th>
                <th>Faltam</th>
            </tr>
            <?php while ($p = mysqli_fetch_assoc($lista_proximos)) { ?>
                <tr>
                    <td><?php echo e($p["id_emprest"]) ?></td>
                    <td><a href="index.php?pagina=historico&func=<?php echo e($p["id_func"]) ?>"><?php echo e($p["nome_completo"]) ?></a></td>
                    <td><a href="index.php?pagina=historico&equip=<?php echo e($p["id_equip"]) ?>"><?php echo e($p["tipo"] . " " . $p["marca_modelo"]) ?></a></td>
                    <td><?php echo data_br($p["previsao_devolucao"]) ?></td>
                    <td><?php echo $p["dias"] == 0 ? "vence hoje" : e(-$p["dias"]) . " dia(s)" ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</center>
