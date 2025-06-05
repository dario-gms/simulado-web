<?php
session_start();
if (!empty($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Simulado para Concursos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h1>Criar Conta</h1>
            
            <form id="registerForm">
                <div class="form-group">
                    <label for="username">Nome de Usuário:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Senha:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                    <a href="login.php" class="btn btn-secondary">Já tem uma conta? Login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>