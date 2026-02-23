<!DOCTYPE html>
<html lang="pt-PT">

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
        /* Make carousel control icons black */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #000;
            border-radius: 50%;
            padding: 10px;
        }
        /* Fixed height for carousel items */
        .carousel-item {
            height: 400px;
            display: flex;
            align-items: center;
        }
        .carousel-item img {
            height: 100%;
            width: 100%;
            object-fit: cover;
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
        
    

        <!-- Car Gallery Carousel -->
        <div class="row justify-content-center mt-4 mb-4">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="card-title mb-0"><i class="fas fa-images"></i> Galeria de Carros</h3>
                    </div>
                    <div class="card-body">
                        <div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner" id="carouselInner">
                                <!-- Car images will be loaded here -->
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">Navegue pelas imagens dos nossos carros disponíveis</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<div class="form-check mt-5 mb-3">
            <input class="form-check-input" type="checkbox" id="showUnavailable">
            <label class="form-check-label" for="showUnavailable">
                Mostrar carros não disponíveis
            </label>
        </div>
        <div class="row mt-1 mb-3">
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
                filterTable();
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
            console.log('Updating pagination - currentPage:', currentPage, 'totalPages:', totalPages);
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
            console.log('Prev button clicked, currentPage:', currentPage);
            if (currentPage > 1) {
                currentPage--;
                console.log('Going to page:', currentPage);
                updateTable();
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
            console.log('Next button clicked, currentPage:', currentPage, 'totalPages:', totalPages);
            if (currentPage < totalPages) {
                currentPage++;
                console.log('Going to page:', currentPage);
                updateTable();
            }
        });
        function btEdit(id) {
            return `<button id='${id}' name='btedit' class='btn btn-warning btn-sm' data-id='${id}'>EDITAR</button>`;
        }

        // Load car gallery carousel
        function loadCarGallery() {
            console.log('Loading car gallery...');
            fetch('api/cars/get_cars_with_images.php')
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.text(); // Get as text first to debug
                })
                .then(text => {
                    console.log('Raw response:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            const carouselInner = document.getElementById('carouselInner');
                            const cars = data.cars;

                            if (cars.length === 0) {
                                carouselInner.innerHTML = `
                                    <div class="carousel-item active">
                                        <div class="text-center p-5">
                                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Nenhuma imagem disponível</h5>
                                            <p class="text-muted">As imagens dos carros serão exibidas aqui quando disponíveis.</p>
                                        </div>
                                    </div>
                                `;
                                return;
                            }

                            let carouselItems = '';
                            cars.forEach((car, index) => {
                                const activeClass = index === 0 ? 'active' : '';
                                const statusText = car.status === 'available' ? 'Disponível' :
                                                 car.status === 'sold' ? 'Vendido' : 'Alugado';
                                const statusClass = car.status === 'available' ? 'text-success' :
                                                  car.status === 'sold' ? 'text-danger' : 'text-warning';

                                carouselItems += `
                                    <div class="carousel-item ${activeClass}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <img src="images/${car.image_filename}"
                                                     class="d-block w-100 rounded"
                                                     alt="${car.make} ${car.model}">
                                            </div>
                                            <div class="col-md-6 d-flex align-items-center">
                                                <div class="w-100">
                                                    <h4 class="text-primary">${car.make} ${car.model}</h4>
                                                    <h5 class="text-muted">${car.year}</h5>
                                                    <p class="mb-2"><strong>Preço:</strong> €${parseFloat(car.price).toFixed(2)}</p>
                                                    <p class="mb-2"><strong>Estado:</strong> <span class="${statusClass}">${statusText}</span></p>
                                                    ${car.is_for_sale ? '<p class="mb-2"><i class="fas fa-shopping-cart text-success"></i> Disponível para venda</p>' : ''}
                                                    ${car.is_for_rent ? `<p class="mb-2"><i class="fas fa-calendar-check text-info"></i> Disponível para aluguer (€${parseFloat(car.daily_rent_price).toFixed(2)}/dia)</p>` : ''}
                                                    ${car.description ? `<p class="mb-2"><strong>Descrição:</strong> ${car.description}</p>` : ''}
                                                    ${car.owner_name ? `<p class="mb-0"><small class="text-muted">Proprietário: ${car.owner_name}</small></p>` : ''}
                                                    <div class="mt-3">
                                                        <a href="CarDetail.php?id=${car.id}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-eye"></i> Ver Detalhes
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            carouselInner.innerHTML = carouselItems;
                        } else {
                            console.error('Error loading car gallery:', data.message);
                            document.getElementById('carouselInner').innerHTML = `
                                <div class="carousel-item active">
                                    <div class="text-center p-5">
                                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                        <h5 class="text-warning">Erro ao carregar galeria</h5>
                                        <p class="text-muted">${data.message}</p>
                                    </div>
                                </div>
                            `;
                        }
                    } catch (jsonError) {
                        console.error('JSON parse error:', jsonError);
                        document.getElementById('carouselInner').innerHTML = `
                            <div class="carousel-item active">
                                <div class="text-center p-5">
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                    <h5 class="text-danger">Erro de resposta</h5>
                                    <p class="text-muted">Resposta inválida do servidor: ${text}</p>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('carouselInner').innerHTML = `
                        <div class="carousel-item active">
                            <div class="text-center p-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h5 class="text-danger">Erro de conexão</h5>
                                <p class="text-muted">Não foi possível carregar a galeria de carros.</p>
                                <small class="text-muted">Erro: ${error.message}</small>
                            </div>
                        </div>
                    `;
                });
        }

        // Load gallery when page loads
        document.addEventListener('DOMContentLoaded', loadCarGallery);
    </script>
</body>

</html>