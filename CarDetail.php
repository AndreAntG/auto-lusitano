<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carro - Auto Lusitano</title>
    <link href="geral.css" rel="stylesheet" />
</head>
<?php
require_once 'cnn.php';
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
    $title = "Inserir Carro";
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
                    } else {
                        Swal.fire('Erro', 'Erro ao guardar', 'error');
                    }
                }).catch(erro => {
                    Swal.fire('Erro', erro, 'error');
                });
            });
        }
    </script>
</body>

</html>