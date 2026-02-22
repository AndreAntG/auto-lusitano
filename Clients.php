<!DOCTYPE html>
<html lang="en">

<head>
    <link href="geral.css" rel="stylesheet" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
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
        <h3 class="text-center mb-4">Lista de Clientes</h3>
        <div class="d-flex justify-content-center mb-3">
            <a href="ClientDetail.php" class="btn btn-primary">Novo Cliente</a>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="showInactive">
            <label class="form-check-label" for="showInactive">
                Mostrar clientes desativados
            </label>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" id="filterName" placeholder="Filtrar por Nome">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" id="filterEmail" placeholder="Filtrar por Email">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th class="sortable" data-sort="name">Nome <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-sort="email">Email <i class="fas fa-sort sort-icon"></i></th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th class="sortable" data-sort="created_at">Data de Criação <i class="fas fa-sort sort-icon"></i></th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="linhas">
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

        // METODO PARA CARREGAR A TABELA COM OS DADOS DA API
        const linhas = document.getElementById("linhas");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const pageInfo = document.getElementById("pageInfo");
        const showInactive = document.getElementById("showInactive");
        let originalData = [];
        let currentPage = 1;
        const pageSize = 10;
        let sortField = 'name';
        let sortOrder = 'asc';
        function loadData() {
            let url = "api/customer/getCustomersList.php";
            if (showInactive.checked) {
                url += "?includeInactive=true";
            }
            fetch(url, {
                method: 'get'
            }).then(response => response.json()).then(data => {
                console.log(data);
                originalData = data;
                currentPage = 1; // Reset to first page
                filterTable();
                updateSortIcons();
            }).catch(erro => {
                Swal.fire('Erro', erro.toString(), 'error');
            });
        }
        window.onload = function () {
            loadData();
        }

        //METODO PARA FILTRAR A TABELA 
        function filterTable() {
            currentPage = 1; // Reset to first page when filtering
            updateTable();
        }
        function updateTable() {
            let nameFilter = document.getElementById('filterName').value.toLowerCase();
            let emailFilter = document.getElementById('filterEmail').value.toLowerCase();
            let filtered = originalData.filter(v => {
                let nameMatch = v.name.toLowerCase().includes(nameFilter);
                let emailMatch = v.email.toLowerCase().includes(emailFilter);
                return nameMatch && emailMatch;
            });
            // Sort
            filtered.sort((a, b) => {
                let aVal = a[sortField];
                let bVal = b[sortField];
                if (sortField === 'created_at') {
                    aVal = new Date(aVal);
                    bVal = new Date(bVal);
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
                let status = v.status || 0; // Default to inactive if null
                let statusIcon = status === 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';
                let address = v.address || '-'; // Default to dash if null
                let createdAt = v.created_at ? new Date(v.created_at).toLocaleDateString('pt-PT') : '-'; // Format date or default to dash
                return `<tr><td>${v.id}</td><td>${v.name}</td><td>${v.email}</td><td>${v.phone || '-'}</td><td>${address}</td><td>${createdAt}</td><td class="text-center">${statusIcon}</td><td>${btStatus(v.id, status)} ${btedit(v.id)}</td></tr>`;
            });
            console.log(records);
            linhas.innerHTML = records.join("");
            // Update pagination
            pageInfo.textContent = `Página ${currentPage} de ${totalPages}`;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
          

            //ADICONA EVENTOS AOS BOTÕES DE EDITAR E ATIVAR/DESATIVAR

            const btedits = document.querySelectorAll("[name='btedit']");
            btedits.forEach(v => {
                v.addEventListener('click', (evt) => {
                    evt.preventDefault();
                    let id = evt.target.id;
                    location = "ClientDetail.php?id=" + id;
                });
            });

            const statusButtons = document.querySelectorAll("[name='btStatus']");
            statusButtons.forEach(v => {
                v.addEventListener("click", (evt) => {
                    evt.preventDefault();
                    let id = evt.target.id;
                    let action = evt.target.innerText;
                    Swal.fire({
                        title: 'Confirmar',
                        text: `Quer ${action.toLowerCase()} o cliente?`,
                        showCancelButton: true,
                        confirmButtonText: 'Sim',
                        cancelButtonText: 'Não'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("api/customer/updateCustomerStatus.php", {
                                method: 'post',
                                body: new URLSearchParams({id: id})
                            }).then(response => response.json()).then(data => {
                                console.log(data);
                                if (data.total > 0) {
                                    location.reload(); // Reload to update the button text
                                } else {
                                    Swal.fire('Erro', 'Erro ao atualizar estado', 'error');
                                }
                            }).catch(erro => {
                                Swal.fire('Erro', erro, 'error');
                            });
                        }
                    });
                });
            });
        }
       
        //ADICONA EVENTOS AOS BOTÕES DE EDITAR E ATIVAR/DESATIVAR
        document.getElementById('filterName').addEventListener('input', filterTable);
        document.getElementById('filterEmail').addEventListener('input', filterTable);
        // Checkbox for showing inactive
        showInactive.addEventListener('change', loadData);
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
                updateTable();
            }
        });
        nextBtn.addEventListener('click', () => {
            let nameFilter = document.getElementById('filterName').value.toLowerCase();
            let emailFilter = document.getElementById('filterEmail').value.toLowerCase();
            let filtered = originalData.filter(v => {
                let nameMatch = v.name.toLowerCase().includes(nameFilter);
                let emailMatch = v.email.toLowerCase().includes(emailFilter);
                return nameMatch && emailMatch;
            });
            let totalPages = Math.ceil(filtered.length / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();
            }
        });
        function btStatus(id, status) {
            // Handle numeric status: 1 = active, 0 = inactive
            status = status || 0; // Default to inactive if null/undefined
            let text = status === 1 ? 'DESATIVAR' : 'ATIVAR';
            let btnClass = status === 1 ? 'btn-danger' : 'btn-success';
            return `<button id='${id}' name='btStatus' class='btn ${btnClass} btn-sm' data-id='${id}'>${text}</button>`;
        }
        function btedit(id) {
            return `<button id='${id}' name='btedit' class='btn btn-warning btn-sm' data-id='${id}'>EDITAR</button>`;
        }
    </script>
    </body>
</html>