<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alugueres - Auto Lusitano</title>
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
        <h3 class="text-center mb-4">Lista de Alugueres</h3>
        <div class="d-flex justify-content-center mb-3">
            <a href="RentalDetail.php" class="btn btn-primary">Novo Aluguer</a>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="showCompleted">
            <label class="form-check-label" for="showCompleted">
                Mostrar alugueres concluídos/cancelados
            </label>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" id="filterRenter" placeholder="Filtrar por Locatário">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" id="filterCar" placeholder="Filtrar por Carro">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Carro</th>
                        <th>Locatário</th>
                        <th class="sortable" data-sort="start_date">Data Início <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-sort="end_date">Data Fim <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-sort="total_price">Preço Total <i class="fas fa-sort sort-icon"></i></th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="alugueis">
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
        const alugueis = document.getElementById("alugueis");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const pageInfo = document.getElementById("pageInfo");
        const showCompleted = document.getElementById("showCompleted");
        let originalData = [];
        let currentPage = 1;
        const pageSize = 10;
        let sortField = 'start_date';
        let sortOrder = 'desc';
        function loadData() {
            let url = "api/rentals/getRentalsList.php";
            if (showCompleted.checked) {
                url += "?includeCompleted=true";
            }
            fetch(url, {
                method: 'get'
            }).then(response => response.json()).then(data => {
                console.log(data);
                originalData = data;
                currentPage = 1; // Reset to first page
                filterTable();
                updateIcons();
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
            let renterFilter = document.getElementById('filterRenter').value.toLowerCase();
            let carFilter = document.getElementById('filterCar').value.toLowerCase();
            let filtered = originalData.filter(r => {
                let renterMatch = r.renter_name.toLowerCase().includes(renterFilter);
                let carMatch = (r.car_make + ' ' + r.car_model).toLowerCase().includes(carFilter);
                return renterMatch && carMatch;
            });
            // Sort
            filtered.sort((a, b) => {
                let aVal = a[sortField];
                let bVal = b[sortField];
                if (sortField === 'start_date' || sortField === 'end_date') {
                    aVal = new Date(aVal);
                    bVal = new Date(bVal);
                } else if (sortField === 'total_price') {
                    aVal = parseFloat(aVal);
                    bVal = parseFloat(bVal);
                }
                if (aVal < bVal) return sortOrder === 'asc' ? -1 : 1;
                if (aVal > bVal) return sortOrder === 'asc' ? 1 : -1;
                return 0;
            });
            let totalItems = filtered.length;
            let totalPages = Math.ceil(totalItems / pageSize);
            let start = (currentPage - 1) * pageSize;
            let end = start + pageSize;
            let pageData = filtered.slice(start, end);
            records = pageData.map(r => {
                let startDate = new Date(r.start_date).toLocaleDateString('pt-PT');
                let endDate = new Date(r.end_date).toLocaleDateString('pt-PT');
                return `<tr><td>${r.id}</td><td>${r.car_make} ${r.car_model} (${r.car_year})</td><td>${r.renter_name}</td><td>${startDate}</td><td>${endDate}</td><td>€${parseFloat(r.total_price).toFixed(2)}</td><td>${r.status}</td><td>${btEdit(r.id)}</td></tr>`;
            });
            console.log(records);
            alugueis.innerHTML = records.join("");
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
                    location = "RentalDetail.php?id=" + id;
                });
            });
        }
        function updateIcons() {
            document.querySelectorAll('.sortable i').forEach(icon => {
                icon.className = 'fas fa-sort';
            });
            let activeHeader = document.querySelector(`.sortable[data-sort="${sortField}"] i`);
            if (activeHeader) {
                activeHeader.className = sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
            }
        }
        // Add event listeners for filters
        document.getElementById('filterRenter').addEventListener('input', filterTable);
        document.getElementById('filterCar').addEventListener('input', filterTable);
        // Checkbox for showing completed
        showCompleted.addEventListener('change', loadData);
        // Event listeners for sortable headers
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                let field = this.getAttribute('data-sort');
                if (sortField === field) {
                    sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    sortField = field;
                    sortOrder = 'asc';
                }
                currentPage = 1; // Reset to first page when sorting
                updateIcons();
                updateTable();
            });
        });
        // Pagination buttons
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable();
            }
        });
        nextBtn.addEventListener('click', () => {
            let renterFilter = document.getElementById('filterRenter').value.toLowerCase();
            let carFilter = document.getElementById('filterCar').value.toLowerCase();
            let filtered = originalData.filter(r => {
                let renterMatch = r.renter_name.toLowerCase().includes(renterFilter);
                let carMatch = (r.car_make + ' ' + r.car_model).toLowerCase().includes(carFilter);
                return renterMatch && carMatch;
            });
            let totalPages = Math.ceil(filtered.length / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();
            }
        });
        function btEdit(id) {
            return `<button id='${id}' name='btedit' class='btn btn-warning btn-sm' data-id='${id}'>EDITAR</button>`;
        }
    </script>
</body>

</html>