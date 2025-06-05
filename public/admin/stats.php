<?php
session_start();
if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: ../../login.php");
    exit;
}
require_once __DIR__ . '/../partials/menu.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas - Simulado para Concursos</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="admin-header">
            <h1>Estatísticas dos Usuários</h1>
        </header>
        
        <div class="card">
            <h2>Desempenho por Área</h2>
            <div class="table-container">
                <table id="statsTable">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Área</th>
                            <th>Questões</th>
                            <th>Acertos</th>
                            <th>Percentual</th>
                            <th>Última Tentativa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- As estatísticas serão carregadas via JavaScript -->
                        <tr><td colspan="6">Carregando estatísticas...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
</body>
</html>