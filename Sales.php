<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas - Auto Lusitano</title>
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
        <h3 class="text-center mb-4">Lista de Vendas</h3>
        <div class="d-flex justify-content-center mb-3">
            <a href="SaleDetail.php" class="btn btn-primary">Nova Venda</a>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" id="filterSeller" placeholder="Filtrar por Vendedor">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="filterBuyer" placeholder="Filtrar por Comprador">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="filterCar" placeholder="Filtrar por Carro">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Carro</th>
                        <th>Vendedor</th>
                        <th>Comprador</th>
                        <th class="sortable" data-sort="sale_price">Preço de Venda <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-sort="sale_date">Data da Venda <i class="fas fa-sort sort-icon"></i></th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="vendas">
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
        const vendas = document.getElementById("vendas");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const pageInfo = document.getElementById("pageInfo");
        let originalData = [];
        let currentPage = 1;
        const pageSize = 10;
        let sortField = 'sale_date';
        let sortOrder = 'desc';
        function loadData() {
            fetch("api/sales/getSalesList.php", {
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
            let sellerFilter = document.getElementById('filterSeller').value.toLowerCase();
            let buyerFilter = document.getElementById('filterBuyer').value.toLowerCase();
            let carFilter = document.getElementById('filterCar').value.toLowerCase();
            let filtered = originalData.filter(v => {
                let sellerMatch = v.seller_name.toLowerCase().includes(sellerFilter);
                let buyerMatch = v.buyer_name.toLowerCase().includes(buyerFilter);
                let carMatch = (v.car_make + ' ' + v.car_model).toLowerCase().includes(carFilter);
                return sellerMatch && buyerMatch && carMatch;
            });
            // Sort
            filtered.sort((a, b) => {
                let aVal = a[sortField];
                let bVal = b[sortField];
                if (sortField === 'sale_date') {
                    aVal = new Date(aVal);
                    bVal = new Date(bVal);
                } else if (sortField === 'sale_price') {
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
            records = pageData.map(v => {
                let saleDate = new Date(v.sale_date).toLocaleDateString('pt-PT');
                return `<tr><td>${v.id}</td><td>${v.car_make} ${v.car_model} (${v.car_year})</td><td>${v.seller_name}</td><td>${v.buyer_name}</td><td>€${parseFloat(v.sale_price).toFixed(2)}</td><td>${saleDate}</td><td>${btEdit(v.id)}</td></tr>`;
            });
            console.log(records);
            vendas.innerHTML = records.join("");
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
                    location = "SaleDetail.php?id=" + id;
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
        document.getElementById('filterSeller').addEventListener('input', filterTable);
        document.getElementById('filterBuyer').addEventListener('input', filterTable);
        document.getElementById('filterCar').addEventListener('input', filterTable);
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
        });        // Pagination buttons
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable();
            }
        });
        nextBtn.addEventListener('click', () => {
            let sellerFilter = document.getElementById('filterSeller').value.toLowerCase();
            let buyerFilter = document.getElementById('filterBuyer').value.toLowerCase();
            let carFilter = document.getElementById('filterCar').value.toLowerCase();
            let filtered = originalData.filter(v => {
                let sellerMatch = v.seller_name.toLowerCase().includes(sellerFilter);
                let buyerMatch = v.buyer_name.toLowerCase().includes(buyerFilter);
                let carMatch = (v.car_make + ' ' + v.car_model).toLowerCase().includes(carFilter);
                return sellerMatch && buyerMatch && carMatch;
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