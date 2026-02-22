<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carros - Auto Lusitano</title>
    <style>
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: #f8f9fa;
        }
        .sort-icon {
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <?php include 'header.html' ?>
    <div class="container mt-5">
        <h3 class="text-center mb-4">Lista de Carros</h3>
        <div class="d-flex justify-content-center mb-3">
            <a href="CarDetail.php" class="btn btn-primary">Novo Carro</a>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="showUnavailable">
            <label class="form-check-label" for="showUnavailable">
                Mostrar carros não disponíveis
            </label>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" id="filterMake" placeholder="Filtrar por Marca">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" id="filterModel" placeholder="Filtrar por Modelo">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th class="sortable" data-sort="make">Marca <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-sort="model">Modelo <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-sort="year">Ano <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-sort="price">Preço <i class="fas fa-sort sort-icon"></i></th>
                        <th>Estado</th>
                        <th>À Venda</th>
                        <th>Para Aluguer</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="carros">
                </tbody>
            </table>
        </div>
        <div id="pagination" class="d-flex justify-content-center mt-3">
            <button id="prevBtn" class="btn btn-secondary me-2">Anterior</button>
            <span id="pageInfo" class="align-self-center me-2"></span>
            <button id="nextBtn" class="btn btn-secondary">Próximo</button>
        </div>
        <hr />
    </div>
    <script>
        const carros = document.getElementById("carros");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const pageInfo = document.getElementById("pageInfo");
        const showUnavailable = document.getElementById("showUnavailable");
        let originalData = [];
        let currentPage = 1;
        const pageSize = 10;
        let sortField = 'make';
        let sortOrder = 'asc';
        function loadData() {
            let url = "api/cars/getCarsList.php";
            if (showUnavailable.checked) {
                url += "?includeUnavailable=true";
            }
            fetch(url, {
                method: 'get'
            }).then(response => response.json()).then(data => {
                console.log(data);
                originalData = data;
                currentPage = 1; // Reset to first page
                updateTable();
                updateSortIcons();
            }).catch(erro => {
                Swal.fire('Erro', erro.toString(), 'error');
            });
        }
        window.onload = function () {
            loadData();
        }
        function filterTable() {
            currentPage = 1; // Reset to first page when filtering
            updateTable();
        }
        function updateTable() {
            let makeFilter = document.getElementById('filterMake').value.toLowerCase();
            let modelFilter = document.getElementById('filterModel').value.toLowerCase();
            let filtered = originalData.filter(v => {
                let makeMatch = v.make.toLowerCase().includes(makeFilter);
                let modelMatch = v.model.toLowerCase().includes(modelFilter);
                return makeMatch && modelMatch;
            });            // Sort
            filtered.sort((a, b) => {
                let aVal = a[sortField];
                let bVal = b[sortField];
                if (sortField === 'price') {
                    aVal = parseFloat(aVal);
                    bVal = parseFloat(bVal);
                } else if (sortField === 'year') {
                    aVal = parseInt(aVal);
                    bVal = parseInt(bVal);
                }
                if (aVal < bVal) return sortOrder === 'asc' ? -1 : 1;
                if (aVal > bVal) return sortOrder === 'asc' ? 1 : -1;
                return 0;
            });            let totalItems = filtered.length;
            let totalPages = Math.ceil(totalItems / pageSize);
            let start = (currentPage - 1) * pageSize;
            let end = start + pageSize;
            let pageData = filtered.slice(start, end);
            records = pageData.map(v => {
                let status = v.status || 'available';
                let statusText = status === 'available' ? 'Disponível' : status === 'sold' ? 'Vendido' : 'Alugado';
                let statusClass = status === 'available' ? 'text-success' : status === 'sold' ? 'text-danger' : 'text-warning';
                let forSaleIcon = v.is_for_sale ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>';
                let forRentIcon = v.is_for_rent ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>';
                return `<tr><td>${v.id}</td><td>${v.make}</td><td>${v.model}</td><td>${v.year}</td><td>€${parseFloat(v.price).toFixed(2)}</td><td class="${statusClass}">${statusText}</td><td class="text-center">${forSaleIcon}</td><td class="text-center">${forRentIcon}</td><td>${btEdit(v.id)}</td></tr>`;
            });
            console.log(records);
            carros.innerHTML = records.join("");
            // Update pagination
            pageInfo.textContent = `Página ${currentPage} de ${totalPages}`;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
            // Reattach event listeners
            const btedits = document.querySelectorAll("[name='btedit']");
            btedits.forEach(v => {
                v.addEventListener('click', (evt) => {
                    evt.preventDefault();
                    let id = evt.target.id;
                    location = "CarDetail.php?id=" + id;
                });
            });
        }
        // Add event listeners for filters
        document.getElementById('filterMake').addEventListener('input', filterTable);
        document.getElementById('filterModel').addEventListener('input', filterTable);
        // Checkbox for showing unavailable
        showUnavailable.addEventListener('change', loadData);
        // Sortable headers
        document.querySelectorAll('.sortable').forEach(th => {
            th.addEventListener('click', () => {
                const field = th.dataset.sort;
                if (sortField === field) {
                    sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    sortField = field;
                    sortOrder = 'asc';
                }
                currentPage = 1; // Reset to first page when sorting
                updateSortIcons();
                updateTable();
            });
        });
        function updateSortIcons() {
            document.querySelectorAll('.sortable .sort-icon').forEach(icon => {
                icon.className = 'fas fa-sort sort-icon';
            });
            const activeTh = document.querySelector(`[data-sort="${sortField}"] .sort-icon`);
            if (activeTh) {
                activeTh.className = sortOrder === 'asc' ? 'fas fa-sort-up sort-icon' : 'fas fa-sort-down sort-icon';
            }
        }
        // Pagination buttons
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                filterTable();
            }
        });
        nextBtn.addEventListener('click', () => {
            let makeFilter = document.getElementById('filterMake').value.toLowerCase();
            let modelFilter = document.getElementById('filterModel').value.toLowerCase();
            let filtered = originalData.filter(v => {
                let makeMatch = v.make.toLowerCase().includes(makeFilter);
                let modelMatch = v.model.toLowerCase().includes(modelFilter);
                return makeMatch && modelMatch;
            });
            let totalPages = Math.ceil(filtered.length / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                filterTable();
            }
        });
        function btEdit(id) {
            return `<button id='${id}' name='btedit' class='btn btn-warning btn-sm' data-id='${id}'>EDITAR</button>`;
        }
    </script>
</body>

</html>