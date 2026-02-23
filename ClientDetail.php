<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente</title>
</head>
<?php
require_once 'cnn.php';
global $pdo;
if (isset($_REQUEST['id'])) {
    $title = "Editar Cliente";
    $mode = "save";
    $id = intval($_REQUEST['id']);
    $sql = "select * from customer where id = :id;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch();
} else {
    $title = "Inserir Cliente";
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
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input value="<?= isset($record['name']) ? $record['name'] : '' ?>" type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input value="<?= isset($record['email']) ? $record['email'] : '' ?>" type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input value="<?= isset($record['phone']) ? $record['phone'] : '' ?>" type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Endereço</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= isset($record['address']) ? $record['address'] : '' ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" name="bt" value="<?= $mode ?>" id="bt">
                            <?= $mode === 'save' ? 'Guardar' : 'Inserir' ?>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="goClientes();">Voltar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function goClientes() {
            window.location.href = 'Clients.php';
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
                
                let modo = bt.getAttribute("value");
                let url = modo === 'save' ? 'api/customer/editCustomer.php' : 'api/customer/insertCustomer.php';
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