<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aluguer - Auto Lusitano</title>
</head>
<?php
require_once 'api/cnn.php';
global $pdo;

// Get cars and customers for dropdowns
$cars_sql = 'SELECT id, make, model, year FROM cars WHERE is_for_rent = TRUE ORDER BY make, model';
$cars_stmt = $pdo->query($cars_sql);
$cars = $cars_stmt->fetchAll(PDO::FETCH_ASSOC);

$customers_sql = 'SELECT id, name FROM customer WHERE status = 1 ORDER BY name';
$customers_stmt = $pdo->query($customers_sql);
$customers = $customers_stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_REQUEST['id'])) {
    $title = "Editar Aluguer";
    $mode = "save";
    $id = intval($_REQUEST['id']);
    $sql = "SELECT * FROM rentals WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch();
    
    // Ensure the current car is in the list, even if not for rent
    $car_in_list = false;
    foreach ($cars as $car) {
        if ($car['id'] == $record['car_id']) {
            $car_in_list = true;
            break;
        }
    }
    if (!$car_in_list && $record['car_id']) {
        $car_sql = 'SELECT id, make, model, year FROM cars WHERE id = :id';
        $car_stmt = $pdo->prepare($car_sql);
        $car_stmt->execute([':id' => $record['car_id']]);
        $current_car = $car_stmt->fetch(PDO::FETCH_ASSOC);
        if ($current_car) {
            $cars[] = $current_car;
        }
    }
} else {
    $title = "Registar Aluguer";
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
                            <label for="car_id" class="form-label">Carro</label>
                            <select class="form-control" id="car_id" name="car_id" required>
                                <option value="">Selecione um carro...</option>
                                <?php foreach ($cars as $car): ?>
                                    <option value="<?= $car['id'] ?>" <?= (isset($record['car_id']) && $record['car_id'] == $car['id']) ? 'selected' : '' ?>>
                                        <?= $car['make'] . ' ' . $car['model'] . ' (' . $car['year'] . ')' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="renter_id" class="form-label">Locatário</label>
                            <select class="form-control" id="renter_id" name="renter_id" required>
                                <option value="">Selecione um locatário...</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" <?= (isset($record['renter_id']) && $record['renter_id'] == $customer['id']) ? 'selected' : '' ?>>
                                        <?= $customer['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Data de Início</label>
                            <input value="<?= isset($record['start_date']) ? $record['start_date'] : '' ?>" type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Data de Fim</label>
                            <input value="<?= isset($record['end_date']) ? $record['end_date'] : '' ?>" type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="total_price" class="form-label">Preço Total (€)</label>
                            <input value="<?= isset($record['total_price']) ? $record['total_price'] : '' ?>" type="number" step="0.01" class="form-control" id="total_price" name="total_price" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active" <?= (isset($record['status']) && $record['status'] == 'active') ? 'selected' : (!isset($record['status']) ? 'selected' : '') ?>>Ativo</option>
                                <option value="completed" <?= (isset($record['status']) && $record['status'] == 'completed') ? 'selected' : '' ?>>Concluído</option>
                                <option value="cancelled" <?= (isset($record['status']) && $record['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" name="bt" value="<?= $mode ?>" id="bt">
                            <?= $mode === 'save' ? 'Guardar' : 'Inserir' ?>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="goRentals();">Voltar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function goRentals() {
            window.location.href = 'Rentals.php';
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
                
                let startDate = document.getElementById('start_date').value;
                let endDate = document.getElementById('end_date').value;
                let totalPrice = document.getElementById('total_price').value;
                
                if (new Date(startDate) >= new Date(endDate)) {
                    Swal.fire('Erro', 'A data de início deve ser anterior à data de fim.', 'error');
                    return;
                }
                
                if (parseFloat(totalPrice) <= 0) {
                    Swal.fire('Erro', 'O preço total deve ser maior que zero.', 'error');
                    return;
                }
                
                let modo = bt.getAttribute("value");
                let url = modo === 'save' ? 'api/rentals/editRental.php' : 'api/rentals/insertRental.php';
                let dados = new FormData(document.getElementById('frm'));
                
                // Log the parameters being sent
                console.log('Parameters being sent:');
                for (let [key, value] of dados.entries()) {
                    console.log(key + ': ' + value);
                }
                
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