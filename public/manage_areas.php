<?php
require_once __DIR__ . '/../src/controllers/AreaController.php';

$areaController = new AreaController();
$areas = $areaController->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_area'])) {
        $nome = trim($_POST['nome']);
        if (!empty($nome)) {
            $success = $areaController->create($nome);
            if ($success) {
                header("Location: manage_areas.php?success=1");
                exit();
            } else {
                $error = "Erro ao adicionar área";
            }
        } else {
            $error = "Nome da área não pode ser vazio";
        }
    } elseif (isset($_POST['delete_area'])) {
        $id = $_POST['area_id'];
        $success = $areaController->delete($id);
        if ($success) {
            header("Location: manage_areas.php?success=2");
            exit();
        } else {
            $error = "Erro ao remover área";
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
</head>
<body>
    <div class="container">
        <h1>Gerenciar Áreas</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">
                <?= $_GET['success'] == 1 ? 'Área adicionada com sucesso!' : 'Área removida com sucesso!' ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <div class="grid-container">
            <div class="card">
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
                </div>
            </div>
        </div>

        <div class="footer">
            <a href="index.php" class="btn btn-secondary">Voltar ao Início</a>
        </div>
    </div>
</body>
</html>