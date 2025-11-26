<?php
// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
$isLoggedIn = isset($_SESSION['user_id']);
$userType = $_SESSION['user_type'] ?? null;
$userName = $_SESSION['user_name'] ?? '';
?>

<!-- ========================================
     TOPBAR - BARRA SUPERIOR COM DATA
     ======================================== -->
<?php
date_default_timezone_set('America/Sao_Paulo');

// Arrays com nomes PT-BR
$dias = [
    'Sunday' => 'Domingo',
    'Monday' => 'Segunda-feira',
    'Tuesday' => 'Terça-feira',
    'Wednesday' => 'Quarta-feira',
    'Thursday' => 'Quinta-feira',
    'Friday' => 'Sexta-feira',
    'Saturday' => 'Sábado'
];

$meses = [
    'January' => 'Janeiro',
    'February' => 'Fevereiro',
    'March' => 'Março',
    'April' => 'Abril',
    'May' => 'Maio',
    'June' => 'Junho',
    'July' => 'Julho',
    'August' => 'Agosto',
    'September' => 'Setembro',
    'October' => 'Outubro',
    'November' => 'Novembro',
    'December' => 'Dezembro'
];

$dataFormatada = sprintf(
    "%s | %s de %s | %s",
    $dias[date('l')],
    date('d'),
    $meses[date('F')],
    date('Y')
);
?>

<div class="container-fluid topbar-modern d-none d-lg-block">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center py-2 gap-4">
            <!-- Data Atual -->
            <div class="d-flex align-items-center">
                <i class="bi bi-calendar2-week me-2 text-primary"></i>
                <span class="text-secondary small">
                    <?= $dataFormatada ?>
                </span>
            </div>
        </div>
    </div>
</div>
<!-- FIM TOPBAR -->

<!-- ========================================
     NAVBAR - MENU DE NAVEGAÇÃO PRINCIPAL
     ======================================== -->
<div class="navbar-modern-wrapper-full sticky-top">
    <nav class="navbar navbar-expand-xl navbar-modern-glass">
        <div class="container">

            <!-- Logo -->
            <a href="/LibreNews/index.php" class="navbar-brand brand-logo-modern">
                <div class="logo-icon-modern rounded-circle">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div class="brand-text-modern">
                    <span class="brand-name-modern">LibreNews</span>
                </div>
            </a>

            <!-- Botão Mobile Toggle -->
            <button class="navbar-toggler mobile-toggle-modern rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <i class="bi bi-list"></i>
            </button>

            <!-- Menu de Navegação -->
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav nav-menu-modern mx-auto">

                    <?php
                    // Função para marcar página ativa
                    function active($page) {
                        return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
                    }
                    ?>

                    <!-- Link: Home -->
                    <a href="/LibreNews/index.php" class="nav-link nav-link-modern <?= active('index.php') ?>">
                        <i class="bi bi-house-door"></i><span>Home</span>
                    </a>

                    <!-- Link: Área Escritor (só aparece se logado como escritor) -->
                    <?php if ($isLoggedIn && $userType === 'escritor'): ?>
                    <a href="/LibreNews/escritor/dashboard.php" class="nav-link nav-link-modern">
                        <i class="bi bi-pencil-square"></i><span>Escritor</span>
                    </a>
                    <?php endif; ?>

                    <!-- Link: Área Admin (só aparece se logado como admin) -->
                    <?php if ($isLoggedIn && $userType === 'admin'): ?>
                    <a href="/LibreNews/admin/dashboard.php" class="nav-link nav-link-modern">
                        <i class="bi bi-gear"></i><span>Admin</span>
                    </a>
                    <?php endif; ?>

                    <!-- Dropdown: Categorias (Hover + Click) -->
                    <div class="nav-item dropdown" id="categoriasDropdown">
                        <a href="#" class="nav-link nav-link-modern dropdown-toggle-modern">
                            <i class="bi bi-grid-3x3-gap"></i>
                            <span>Categorias</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-modern-style rounded-4 shadow-lg">
<?php
$categoriasMenu = [
    ['Desenvolvimento', 'Web, Mobile e mais', 'bi-code-slash'],
    ['Programação', 'Linguagens e frameworks', 'bi-terminal'],
    ['Tecnologia', 'Hardware e inovação', 'bi-cpu'],
    ['Sistemas', 'DevOps e infraestrutura', 'bi-hdd-network'],
    ['Mercado', 'Tendências e carreiras', 'bi-graph-up-arrow'],
];

foreach ($categoriasMenu as $cat):
?>
    <a href="#" class="dropdown-item dropdown-item-modern-style">
        <div class="dropdown-icon-modern rounded-circle">
            <i class="bi <?= $cat[2] ?>"></i>
        </div>
        <div>
            <div class="dropdown-title-modern"><?= $cat[0] ?></div>
            <small class="dropdown-subtitle-modern"><?= $cat[1] ?></small>
        </div>
    </a>
<?php endforeach; ?>

                        </div>
                    </div>

                    <!-- Link: Equipe -->
                    <a href="/LibreNews/equipe.php" class="nav-link nav-link-modern <?= active('equipe.php') ?>">
                        <i class="bi bi-people"></i><span>Equipe</span>
                    </a>

                    <!-- Link: Sobre -->
                    <a href="/LibreNews/sobre.php" class="nav-link nav-link-modern <?= active('sobre.php') ?>">
                        <i class="bi bi-info-circle"></i><span>Sobre</span>
                    </a>

                </div>

                <!-- Ações da Navbar (Busca + Perfil/Login) -->
                <div class="d-flex align-items-center nav-actions-modern gap-3">
                    
                    <!-- Busca -->
                    <button class="search-trigger-modern rounded-circle" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="bi bi-search"></i>
                    </button>

                    <!-- Perfil ou Login -->
                    <?php if ($isLoggedIn): ?>
                        <!-- Dropdown de Perfil (Usuário Logado) -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link nav-link-modern dropdown-toggle-modern d-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-4 me-2"></i>
                                <span><?= htmlspecialchars($userName) ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-modern-style dropdown-menu-end rounded-4 shadow-lg">
                                <?php if ($userType === 'escritor'): ?>
                                    <a href="/LibreNews/escritor/perfil.php" class="dropdown-item dropdown-item-modern-style">
                                        <div class="dropdown-icon-modern rounded-circle">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div>
                                            <div class="dropdown-title-modern">Meu Perfil</div>
                                            <small class="dropdown-subtitle-modern">Editar informações</small>
                                        </div>
                                    </a>
                                    <a href="/LibreNews/escritor/minhas-noticias.php" class="dropdown-item dropdown-item-modern-style">
                                        <div class="dropdown-icon-modern rounded-circle">
                                            <i class="bi bi-file-text"></i>
                                        </div>
                                        <div>
                                            <div class="dropdown-title-modern">Minhas Notícias</div>
                                            <small class="dropdown-subtitle-modern">Ver publicações</small>
                                        </div>
                                    </a>
                                <?php elseif ($userType === 'admin'): ?>
                                    <a href="/LibreNews/admin/perfil.php" class="dropdown-item dropdown-item-modern-style">
                                        <div class="dropdown-icon-modern rounded-circle">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div>
                                            <div class="dropdown-title-modern">Meu Perfil</div>
                                            <small class="dropdown-subtitle-modern">Editar informações</small>
                                        </div>
                                    </a>
                                    <a href="/LibreNews/admin/dashboard.php" class="dropdown-item dropdown-item-modern-style">
                                        <div class="dropdown-icon-modern rounded-circle">
                                            <i class="bi bi-speedometer2"></i>
                                        </div>
                                        <div>
                                            <div class="dropdown-title-modern">Painel Admin</div>
                                            <small class="dropdown-subtitle-modern">Gerenciar sistema</small>
                                        </div>
                                    </a>
                                <?php endif; ?>
                                <hr class="dropdown-divider my-2">
                                <a href="/LibreNews/logout.php" class="dropdown-item dropdown-item-modern-style text-danger">
                                    <div class="dropdown-icon-modern rounded-circle">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </div>
                                    <div>
                                        <div class="dropdown-title-modern">Sair</div>
                                        <small class="dropdown-subtitle-modern">Encerrar sessão</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Botão de Login (Não Logado) -->
                        <a href="/LibreNews/login.php" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                        </a>
                    <?php endif; ?>

                </div>

            </div>
        </div>
    </nav>
</div>
<!-- FIM NAVBAR -->

<!-- ========================================
     MODAL DE BUSCA
     ======================================== -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <!-- Cabeçalho do Modal -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-search text-primary me-2"></i>Buscar Notícias
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body d-flex align-items-center justify-content-center" style="min-height: 50vh;">
                <div class="w-100" style="max-width: 700px;">
                    <form action="/LibreNews/busca.php" method="GET">
                        <div class="input-group input-group-lg shadow">
                            <input 
                                type="search" 
                                name="q"
                                class="form-control rounded-start-pill py-4 ps-4" 
                                placeholder="Digite sua busca..."
                                style="background-color: #1E293B; border-color: #4A5568; color: #E2E8F0;"
                                autofocus
                            >
                            <button type="submit" class="btn btn-primary rounded-end-pill px-5">
                                <i class="bi bi-search me-2"></i>Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM MODAL DE BUSCA -->

<!-- ========================================
     SCRIPTS E ESTILOS - NAVBAR E DROPDOWN
     ======================================== -->
<style>
/* Animação apenas para links não selecionados */
.nav-link-modern:not(.active):hover i,
.nav-link-modern:not(.active):hover span {
    transform: translateY(-2px);
}

.nav-link-modern i,
.nav-link-modern span {
    transition: transform 0.3s ease;
    display: inline-block;
}

/* Garantir que links ativos não tenham animação */
.nav-link-modern.active i,
.nav-link-modern.active span {
    transform: none !important;
}

/* Dropdown ativo quando aberto */
.dropdown.dropdown-open .nav-link-modern {
    color: var(--bs-primary) !important;
}
</style>

<script>
// Navbar scroll effect
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar-modern-wrapper-full');
    if (navbar) {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    }
});

// Dropdown hover + click behavior
document.addEventListener('DOMContentLoaded', function() {
    const dropdownElement = document.getElementById('categoriasDropdown');
    if (!dropdownElement) return;
    
    const dropdownToggle = dropdownElement.querySelector('.dropdown-toggle-modern');
    const dropdownMenu = dropdownElement.querySelector('.dropdown-menu');
    
    let isClicked = false;
    let hoverTimeout = null;
    
    function openDropdown() {
        dropdownMenu.classList.add('show');
        dropdownElement.classList.add('dropdown-open');
        dropdownToggle.setAttribute('aria-expanded', 'true');
    }
    
    function closeDropdown() {
        if (!isClicked) {
            dropdownMenu.classList.remove('show');
            dropdownElement.classList.remove('dropdown-open');
            dropdownToggle.setAttribute('aria-expanded', 'false');
        }
    }
    
    dropdownElement.addEventListener('mouseenter', function() {
        clearTimeout(hoverTimeout);
        hoverTimeout = setTimeout(() => {
            if (!isClicked) {
                openDropdown();
            }
        }, 100);
    });
    
    dropdownElement.addEventListener('mouseleave', function() {
        clearTimeout(hoverTimeout);
        hoverTimeout = setTimeout(() => {
            closeDropdown();
        }, 200);
    });
    
    dropdownToggle.addEventListener('click', function(e) {
        e.preventDefault();
        isClicked = !isClicked;
        
        if (isClicked) {
            openDropdown();
        } else {
            dropdownMenu.classList.remove('show');
            dropdownElement.classList.remove('dropdown-open');
            dropdownToggle.setAttribute('aria-expanded', 'false');
        }
    });
    
    document.addEventListener('click', function(e) {
        if (!dropdownElement.contains(e.target) && isClicked) {
            isClicked = false;
            dropdownMenu.classList.remove('show');
            dropdownElement.classList.remove('dropdown-open');
            dropdownToggle.setAttribute('aria-expanded', 'false');
        }
    });
    
    dropdownMenu.addEventListener('mouseenter', function() {
        clearTimeout(hoverTimeout);
    });
    
    dropdownMenu.addEventListener('mouseleave', function() {
        hoverTimeout = setTimeout(() => {
            closeDropdown();
        }, 200);
    });
});
</script>
