/* Empréstimos TI — comportamento da interface (tema, menu, tabelas) */

// Tema: aplica antes de renderizar para evitar "piscada"
(function () {
    var salvo = null;
    try { salvo = localStorage.getItem("tema"); } catch (e) {}
    var tema = salvo || (window.matchMedia && matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light");
    document.documentElement.setAttribute("data-theme", tema);
})();

document.addEventListener("DOMContentLoaded", function () {

    // Alternar tema claro/escuro
    var btnTema = document.getElementById("btn-tema");
    if (btnTema) {
        btnTema.addEventListener("click", function () {
            var atual = document.documentElement.getAttribute("data-theme");
            var novo = atual === "dark" ? "light" : "dark";
            document.documentElement.setAttribute("data-theme", novo);
            try { localStorage.setItem("tema", novo); } catch (e) {}
        });
    }

    // Menu lateral no celular
    var fecharMenu = function () { document.body.classList.remove("menu-aberto"); };
    var btnMenu = document.getElementById("btn-menu");
    if (btnMenu) {
        btnMenu.addEventListener("click", function () { document.body.classList.toggle("menu-aberto"); });
    }
    var fundo = document.getElementById("fundo-menu");
    if (fundo) { fundo.addEventListener("click", fecharMenu); }
    document.addEventListener("keydown", function (ev) { if (ev.key === "Escape") { fecharMenu(); } });

    // Selos coloridos para status
    var selos = {
        "Disponível": "ok", "Ativo": "ok",
        "Emprestado": "info",
        "Em manutenção": "warn", "Afastado": "warn",
        "Obsoleto/descartado": "neutro",
        "Desligado": "bad"
    };

    var tabelas = document.querySelectorAll(".conteudo table");
    tabelas.forEach(function (tabela) {
        var linhas = Array.prototype.slice.call(tabela.rows);
        if (!linhas.length) { return; }

        var cabecalho = linhas[0];
        var titulos = Array.prototype.map.call(cabecalho.querySelectorAll("th"), function (th) {
            return th.textContent.trim();
        });

        // Move o botão "incluir" para o topo da página
        var celulaIncluir = null;
        Array.prototype.forEach.call(cabecalho.querySelectorAll("td"), function (td) {
            var botao = td.querySelector("a > button");
            if (botao && /incluir/i.test(botao.textContent)) { celulaIncluir = td; }
        });

        if (celulaIncluir) {
            var link = celulaIncluir.querySelector("a");
            var botao = link.querySelector("button");
            botao.textContent = "+ Incluir";

            var h1 = document.querySelector(".conteudo h1");
            if (h1) {
                var topo = document.createElement("div");
                topo.className = "cabecalho-pagina";
                h1.parentNode.insertBefore(topo, h1);
                topo.appendChild(h1);
                topo.appendChild(link);
            }
            celulaIncluir.remove();
        }

        // Colunas de ação (sem título): ✏️ 📜 🗑️ ↩️
        var corpo = linhas.slice(1);
        var maxCelulas = corpo.reduce(function (m, tr) { return Math.max(m, tr.cells.length); }, 0);
        var extras = maxCelulas - cabecalho.cells.length;
        if (extras > 0) {
            var vazio = document.createElement("th");
            vazio.className = "vazio";
            vazio.colSpan = extras;
            cabecalho.appendChild(vazio);
        }

        cabecalho.classList.add("linha-cab");

        corpo.forEach(function (tr) {
            Array.prototype.forEach.call(tr.cells, function (td, i) {
                if (td.hasAttribute("colspan")) {
                    td.setAttribute("data-label", "");
                    return;
                }
                if (i < titulos.length) {
                    td.setAttribute("data-label", titulos[i]);
                    var texto = td.textContent.trim();
                    if (selos[texto] && !td.children.length) {
                        td.innerHTML = '<span class="selo selo-' + selos[texto] + '"></span>';
                        td.firstChild.textContent = texto;
                    }
                } else {
                    td.classList.add("acao");
                }
            });
        });

        // Envolve para rolagem horizontal / cartões no celular
        tabela.classList.add("cartoes");
        var wrap = document.createElement("div");
        wrap.className = "tabela-wrap";
        tabela.parentNode.insertBefore(wrap, tabela);
        wrap.appendChild(tabela);
    });
});
