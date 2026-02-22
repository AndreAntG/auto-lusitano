<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venda - Auto Lusitano</title>
    <link href="geral.css" rel="stylesheet" />
</head>
<?php
require_once 'cnn.php';
global $pdo;

// Get cars and customers for dropdowns
$cars_sql = 'SELECT id, make, model, year FROM cars WHERE status = "available" ORDER BY make, model';
$cars_stmt = $pdo->query($cars_sql);
$cars = $cars_stmt->fetchAll(PDO::FETCH_ASSOC);

$customers_sql = 'SELECT id, name FROM customer WHERE status = 1 ORDER BY name';
$customers_stmt = $pdo->query($customers_sql);
$customers = $customers_stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_REQUEST['id'])) {
    $title = "Editar Venda";
    $mode = "save";
    $id = intval($_REQUEST['id']);
    $sql = "SELECT * FROM sales WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch();
    
    // Ensure the current car is in the list, even if not available
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
    $title = "Inserir Venda";
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
                            <label for="sale_price" class="form-label">Preço de Venda (€)</label>
                            <input value="<?= isset($record['sale_price']) ? $record['sale_price'] : '' ?>" type="number" step="0.01" class="form-control" id="sale_price" name="sale_price" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="seller_id" class="form-label">Vendedor</label>
                            <select class="form-control" id="seller_id" name="seller_id" required>
                                <option value="">Selecione um vendedor...</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" <?= (isset($record['seller_id']) && $record['seller_id'] == $customer['id']) ? 'selected' : '' ?>>
                                        <?= $customer['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="buyer_id" class="form-label">Comprador</label>
                            <select class="form-control" id="buyer_id" name="buyer_id" required>
                                <option value="">Selecione um comprador...</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" <?= (isset($record['buyer_id']) && $record['buyer_id'] == $customer['id']) ? 'selected' : '' ?>>
                                        <?= $customer['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="sale_date" class="form-label">Data da Venda</label>
                        <input value="<?= isset($record['sale_date']) ? date('Y-m-d\TH:i', strtotime($record['sale_date'])) : date('Y-m-d\TH:i') ?>" type="datetime-local" class="form-control" id="sale_date" name="sale_date" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" name="bt" value="<?= $mode ?>" id="bt">
                            <?= $mode === 'save' ? 'Guardar' : 'Inserir' ?>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="goSales();">Voltar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function goSales() {
            window.location.href = 'Sales.php';
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
                
                let salePrice = document.getElementById('sale_price').value;
                if (parseFloat(salePrice) <= 0) {
                    Swal.fire('Erro', 'O preço de venda deve ser maior que zero.', 'error');
                    return;
                }
                
                let modo = bt.getAttribute("value");
                let url = modo === 'save' ? 'api/sales/editSale.php' : 'api/sales/insertSale.php';
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