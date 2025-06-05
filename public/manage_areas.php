<?php
session_start();
if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: ../login.php");
    exit;
}
require_once __DIR__ . '/../src/controllers/AreaController.php';
require_once __DIR__ . '/partials/menu.php';

$areaController = new AreaController();
$areas = $areaController->getAll();

// Variáveis para mensagens
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_area'])) {
        $nome = trim($_POST['nome']);
        if (!empty($nome)) {
            if ($areaController->create($nome)) {
                $success_message = 'Área adicionada com sucesso!';
                // Recarrega as áreas após adição
                $areas = $areaController->getAll();
            } else {
                $error_message = "Erro ao adicionar área";
            }
        } else {
            $error_message = "Nome da área não pode ser vazio";
        }
    } elseif (isset($_POST['delete_area'])) {
        $id = $_POST['area_id'];
        if ($areaController->delete($id)) {
            $success_message = 'Área removida com sucesso!';
            // Recarrega as áreas após remoção
            $areas = $areaController->getAll();
        } else {
            $error_message = "Erro ao remover área";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Áreas</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Mantém a posição do scroll após recarregar a página
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash === '#form-area') {
                document.querySelector('#form-area').scrollIntoView();
            }
            
            // Fecha mensagens de alerta após 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.display = 'none';
                });
            }, 5000);
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Gerenciar Áreas</h1>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert success">
                <?= $success_message ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert error"><?= $error_message ?></div>
        <?php endif; ?>

        <div class="grid-container">
            <div class="card" id="form-area">
                <h2>Adicionar Nova Área</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="nome">Nome da Área:</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <button type="submit" name="add_area" class="btn btn-primary">Adicionar Área</button>
                </form>
            </div>

            <div class="card">
                <h2>Áreas Existentes</h2>
                <div class="table-container">
                    <?php if (empty($areas)): ?>
                        <p>Nenhuma área cadastrada.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($areas as $area): ?>
                                <tr>
                                    <td><?= $area['id'] ?></td>
                                    <td><?= htmlspecialchars($area['nome']) ?></td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta área?');">
                                            <input type="hidden" name="area_id" value="<?= $area['id'] ?>">
                                            <button type="submit" name="delete_area" class="btn btn-danger">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer">
            <a href="index.php" class="btn btn-secondary">Voltar ao Início</a>
        </div>
    </div>
</body>
</html>