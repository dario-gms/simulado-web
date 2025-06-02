<?php
$base_url = '/simulado/public/'; // Ajuste conforme sua estrutura
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = !empty($_SESSION['user']['is_admin']);
?>

<nav class="main-menu">
    <div class="menu-container">
        <!-- Logo/Marca -->
        <a href="<?= $base_url ?>index.php" class="menu-brand">
            <i class="menu-brand-icon fas fa-graduation-cap"></i>
            <span>Simulado Concursos</span>
        </a>
        
        <!-- Botão Mobile -->
        <button class="menu-toggle" aria-label="Abrir menu">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Navegação Principal -->
        <div class="menu-nav">
            <ul class="menu-list">
                <!-- Item Início -->
                <li class="menu-item <?= ($current_page == 'index.php') ? 'active' : '' ?>">
                    <a href="<?= $base_url ?>index.php" class="menu-link">
                        <i class="menu-link-icon fas fa-home"></i>
                        <span>Início</span>
                    </a>
                </li>
                
                <!-- Item Perfil -->
                <li class="menu-item <?= ($current_page == 'profile.php') ? 'active' : '' ?>">
                    <a href="<?= $base_url ?>profile.php" class="menu-link">
                        <i class="menu-link-icon fas fa-user"></i>
                        <span>Meu Perfil</span>
                    </a>
                </li>
                
                <!-- Menu Admin (apenas para administradores) -->
                <?php if ($is_admin): ?>
                <!-- Gerenciar Usuários -->
                <li class="menu-item <?= ($current_page == 'users.php') ? 'active' : '' ?>">
                    <a href="<?= $base_url ?>admin/users.php" class="menu-link">
                        <i class="menu-link-icon fas fa-users-cog"></i>
                        <span>Gerenciar Usuários</span>
                    </a>
                </li>
                
                <!-- Adicionar Questões -->
                <li class="menu-item <?= ($current_page == 'add_question.php') ? 'active' : '' ?>">
                    <a href="<?= $base_url ?>add_question.php" class="menu-link">
                        <i class="menu-link-icon fas fa-question-circle"></i>
                        <span>Adicionar Questões</span>
                    </a>
                </li>
                
                <!-- Gerenciar Áreas -->
                <li class="menu-item <?= ($current_page == 'manage_areas.php') ? 'active' : '' ?>">
                    <a href="<?= $base_url ?>manage_areas.php" class="menu-link">
                        <i class="menu-link-icon fas fa-layer-group"></i>
                        <span>Gerenciar Áreas</span>
                    </a>
                </li>
                
                <!-- Estatísticas -->
                <li class="menu-item <?= ($current_page == 'stats.php') ? 'active' : '' ?>">
                    <a href="<?= $base_url ?>admin/stats.php" class="menu-link">
                        <i class="menu-link-icon fas fa-chart-bar"></i>
                        <span>Estatísticas</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <!-- Área do Usuário e Logout -->
            <div class="user-menu">
                <div class="user-greeting">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                    <?php if ($is_admin): ?>
                    <span class="user-role">Administrador</span>
                    <?php endif; ?>
                </div>
                
                <form id="logoutForm" class="logout-form">
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="logout-text">Sair</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Script para o menu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle menu mobile
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.menu-nav').classList.toggle('active');
            this.classList.toggle('active');
        });
    }

    // Logout via AJAX
    const logoutForm = document.getElementById('logoutForm');
    if (logoutForm) {
        logoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('<?= $base_url ?>api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'logout' })
                });
                
                const data = await response.json();
                
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erro ao fazer logout');
                }
                
                window.location.href = '<?= $base_url ?>login.php';
            } catch (error) {
                console.error('Erro no logout:', error);
                alert('Erro ao fazer logout: ' + error.message);
            }
        });
    }

    // Fecha o menu ao clicar fora (mobile)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 992) {
            if (!e.target.closest('.menu-container')) {
                document.querySelector('.menu-nav').classList.remove('active');
                document.querySelector('.menu-toggle').classList.remove('active');
            }
        }
    });
});
</script>