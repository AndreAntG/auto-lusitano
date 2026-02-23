<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carro - Auto Lusitano</title>
</head>
<?php
require_once 'api/cnn.php';
global $pdo;
if (isset($_REQUEST['id'])) {
    $title = "Editar Carro";
    $mode = "save";
    $id = intval($_REQUEST['id']);
    $sql = "select * from cars where id = :id;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch();
} else {
    $title = "Registar Carro";
    $mode = "insert";
}
?>

<body>
    <?php include 'header.html' ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mb-4">
                    <?= $title ?>
                </h2>
                <form id="frm" name="frm">
                    <input type="hidden" name="id" value="<?= isset($record['id']) ? $record['id'] : '' ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="make" class="form-label">Marca</label>
                            <input value="<?= isset($record['make']) ? $record['make'] : '' ?>" type="text" class="form-control" id="make" name="make" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="model" class="form-label">Modelo</label>
                            <input value="<?= isset($record['model']) ? $record['model'] : '' ?>" type="text" class="form-control" id="model" name="model" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="year" class="form-label">Ano</label>
                            <input value="<?= isset($record['year']) ? $record['year'] : '' ?>" type="number" class="form-control" id="year" name="year" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Preço (€)</label>
                            <input value="<?= isset($record['price']) ? $record['price'] : '' ?>" type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= isset($record['description']) ? $record['description'] : '' ?></textarea>
                    </div>

                    <!-- Image Upload Section -->
                    <div class="mb-3">
                        <label class="form-label">Imagem do Carro</label>
                        <div class="card">
                            <div class="card-body">
                                <div id="currentImage" class="mb-3">
                                    <?php if (isset($record['image_filename']) && !empty($record['image_filename'])): ?>
                                        <div class="text-center">
                                            <img id="carImage" src="images/<?= htmlspecialchars($record['image_filename']) ?>" alt="Car Image" class="img-fluid rounded" style="max-height: 200px;">
                                            <p class="mt-2 text-muted small">Imagem atual: <?= htmlspecialchars($record['image_filename']) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-3x mb-2"></i>
                                            <p>Nenhuma imagem carregada</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <input type="file" class="form-control" id="car_image" name="car_image" accept="image/*">
                                    <div class="form-text">Formatos aceites: JPG, PNG, GIF, WebP. Tamanho máximo: 5MB.</div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success btn-sm" id="uploadBtn">
                                        <i class="fas fa-upload"></i> Carregar Imagem
                                    </button>
                                    <?php if (isset($record['image_filename']) && !empty($record['image_filename'])): ?>
                                        <button type="button" class="btn btn-danger btn-sm" id="deleteBtn">
                                            <i class="fas fa-trash"></i> Apagar Imagem
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="available" <?= (isset($record['status']) && $record['status'] == 'available') ? 'selected' : '' ?>>Disponível</option>
                                <option value="sold" <?= (isset($record['status']) && $record['status'] == 'sold') ? 'selected' : '' ?>>Vendido</option>
                                <option value="rented" <?= (isset($record['status']) && $record['status'] == 'rented') ? 'selected' : '' ?>>Alugado</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="daily_rent_price" class="form-label">Preço Diária Aluguer (€)</label>
                            <input value="<?= isset($record['daily_rent_price']) ? $record['daily_rent_price'] : '' ?>" type="number" step="0.01" class="form-control" id="daily_rent_price" name="daily_rent_price">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_for_sale" name="is_for_sale" value="1" <?= (isset($record['is_for_sale']) && $record['is_for_sale']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_for_sale">
                                    Disponível para Venda
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_for_rent" name="is_for_rent" value="1" <?= (isset($record['is_for_rent']) && $record['is_for_rent']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_for_rent">
                                    Disponível para Aluguer
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" name="bt" value="<?= $mode ?>" id="bt">
                            <?= $mode === 'save' ? 'Guardar' : 'Inserir' ?>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="goCars();">Voltar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function goCars() {
            window.location.href = 'Cars.php';
        }

        // Image upload functionality
        function uploadImage() {
            const fileInput = document.getElementById('car_image');
            const carId = document.querySelector('input[name="id"]').value;

            if (!fileInput.files[0]) {
                Swal.fire('Erro', 'Por favor, selecione uma imagem para carregar.', 'error');
                return;
            }

            if (!carId) {
                Swal.fire('Erro', 'Por favor, guarde o carro primeiro antes de carregar uma imagem.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('car_image', fileInput.files[0]);
            formData.append('car_id', carId);

            fetch('api/cars/upload_image.php', {
                method: 'POST',
                body: formData
            })
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
                        Swal.fire('Sucesso', data.message, 'success');
                        // Update the image display
                        const currentImageDiv = document.getElementById('currentImage');
                        currentImageDiv.innerHTML = `
                            <div class="text-center">
                                <img id="carImage" src="images/${data.filename}" alt="Car Image" class="img-fluid rounded" style="max-height: 200px;">
                                <p class="mt-2 text-muted small">Imagem atual: ${data.filename}</p>
                            </div>
                        `;
                        // Clear file input
                        fileInput.value = '';
                        // Show delete button if not already shown
                        if (!document.getElementById('deleteBtn')) {
                            const buttonContainer = document.querySelector('.d-flex.gap-2');
                            const deleteBtn = document.createElement('button');
                            deleteBtn.type = 'button';
                            deleteBtn.className = 'btn btn-danger btn-sm';
                            deleteBtn.id = 'deleteBtn';
                            deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Apagar Imagem';
                            buttonContainer.appendChild(deleteBtn);
                            deleteBtn.addEventListener('click', deleteImage);
                        }
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                } catch (jsonError) {
                    console.error('JSON parse error:', jsonError);
                    Swal.fire('Erro', 'Resposta inválida do servidor: ' + text, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Erro', 'Erro ao carregar imagem: ' + error.message, 'error');
            });
        }

        // Image delete functionality
        function deleteImage() {
            const carId = document.querySelector('input[name="id"]').value;

            if (!carId) {
                Swal.fire('Erro', 'ID do carro não encontrado.', 'error');
                return;
            }

            Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta ação irá apagar permanentemente a imagem do carro.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, apagar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('car_id', carId);

                    fetch('api/cars/delete_image.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('Delete response status:', response.status);
                        return response.text();
                    })
                    .then(text => {
                        console.log('Delete raw response:', text);
                        try {
                            const data = JSON.parse(text);
                            if (data.success) {
                                Swal.fire('Sucesso', data.message, 'success');
                                // Update the image display
                                const currentImageDiv = document.getElementById('currentImage');
                                currentImageDiv.innerHTML = `
                                    <div class="text-center text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>Nenhuma imagem carregada</p>
                                    </div>
                                `;
                                // Remove delete button
                                const deleteBtn = document.getElementById('deleteBtn');
                                if (deleteBtn) {
                                    deleteBtn.remove();
                                }
                            } else {
                                Swal.fire('Erro', data.message, 'error');
                            }
                        } catch (jsonError) {
                            console.error('JSON parse error:', jsonError);
                            Swal.fire('Erro', 'Resposta inválida do servidor: ' + text, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Erro', 'Erro ao apagar imagem: ' + error.message, 'error');
                    });
                }
            });
        }

        window.onload = function () {
            const bt = document.getElementById('bt');
            bt.addEventListener('click', (evt) => {
                evt.preventDefault();

                // Validation
                let form = document.getElementById('frm');
                if (!form.checkValidity()) {
                    Swal.fire('Erro', 'Por favor, preencha todos os campos obrigatórios.', 'error');
                    return;
                }

                let price = document.getElementById('price').value;
                if (parseFloat(price) <= 0) {
                    Swal.fire('Erro', 'O preço deve ser maior que zero.', 'error');
                    return;
                }

                let modo = bt.getAttribute("value");
                let url = modo === 'save' ? 'api/cars/editCar.php' : 'api/cars/insertCar.php';
                let dados = new FormData(document.getElementById('frm'));
                fetch(url, {
                    method: 'post',
                    body: dados
                }).then(response => response.json()).then(rslt => {
                    console.log(rslt);
                    if (rslt.msg) {
                        Swal.fire('Sucesso', 'Registo guardado com sucesso', 'success');
                        // If this was an insert, update the hidden ID field
                        if (modo === 'insert' && rslt.id) {
                            document.querySelector('input[name="id"]').value = rslt.id;
                            bt.setAttribute("value", "save");
                            bt.textContent = "Guardar";
                        }
                    } else {
                        Swal.fire('Erro', 'Erro ao guardar', 'error');
                    }
                }).catch(erro => {
                    Swal.fire('Erro', erro, 'error');
                });
            });

            // Add event listeners for image buttons
            const uploadBtn = document.getElementById('uploadBtn');
            if (uploadBtn) {
                uploadBtn.addEventListener('click', uploadImage);
            }

            const deleteBtn = document.getElementById('deleteBtn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', deleteImage);
            }
        }
    </script>
</body>

</html>