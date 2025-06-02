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
    <title>Login - Simulado para Concursos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h1>Login</h1>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Usuário ou Email:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                    <a href="register.php" class="btn btn-secondary">Criar Conta</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>