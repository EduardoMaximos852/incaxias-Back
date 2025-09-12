document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("searchInput");
    const resultsDiv = document.getElementById("searchResults");

    input.addEventListener("keyup", async function () {
        const query = input.value.trim();
        if (query.length < 2) {
            resultsDiv.innerHTML = "";
            return;
        }

        const res = await fetch(`../api/search_dashboard.php?q=${encodeURIComponent(query)}`);
        const dados = await res.json();

        if (!dados.length) {
            resultsDiv.innerHTML = "<p>Nenhum resultado encontrado.</p>";
            return;
        }

        let html = "<table><thead><tr><th>Origem</th><th>Título/Nome</th><th>Descrição</th></tr></thead><tbody>";
        dados.forEach(item => {
            html += `<tr>
                        <td>${item.origem}</td>
                        <td>${item.nome}</td>
                        <td>${item.descricao ?? ""}</td>
                     </tr>`;
        });
        html += "</tbody></table>";

        resultsDiv.innerHTML = html;
    });
});
