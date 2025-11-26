<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Verificar se é admin
requireAdmin();

$userData = getUserData();

$pageTitle = "Painel Admin - LibreNews";
$baseUrl = '/LibreNews/';
include '../components/head.php';
include '../components/navbar.php';

?>

<!-- Dashboard Admin -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">
        
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-12">
                <h1 class="text-white fw-bold mb-2">
                    <i class="bi bi-speedometer2 text-primary me-3"></i>Painel Administrativo
                </h1>
                <p class="text-secondary">Bem-vindo, <?= htmlspecialchars($userData['name']) ?>!</p>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row g-4 mb-5">
            
            <!-- Card: Total de Notícias -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3B82F6 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1">Total de Notícias</p>
                                <h3 class="text-white fw-bold mb-0">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM Noticia");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </h3>
                            </div>
                            <div class="rounded-circle p-3" style="background: rgba(59, 130, 246, 0.2);">
                                <i class="bi bi-newspaper text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Notícias Pendentes -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(251, 191, 36, 0.1); border-left: 4px solid #FBBF24 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1">Pendentes</p>
                                <h3 class="text-white fw-bold mb-0">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM Noticia WHERE status = 'pendente'");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </h3>
                            </div>
                            <div class="rounded-circle p-3" style="background: rgba(251, 191, 36, 0.2);">
                                <i class="bi bi-clock-history text-warning fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Escritores Ativos -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22C55E !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1">Escritores</p>
                                <h3 class="text-white fw-bold mb-0">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM Escritor");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </h3>
                            </div>
                            <div class="rounded-circle p-3" style="background: rgba(34, 197, 94, 0.2);">
                                <i class="bi bi-people text-success fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Candidatos -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #EF4444 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1">Candidatos</p>
                                <h3 class="text-white fw-bold mb-0">
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM Candidato WHERE status = 'pendente'");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </h3>
                            </div>
                            <div class="rounded-circle p-3" style="background: rgba(239, 68, 68, 0.2);">
                                <i class="bi bi-person-plus text-danger fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Menu de Ações -->
        <div class="row g-4">
            
            <!-- Gerenciar Notícias -->
            <div class="col-lg-4 col-md-6">
                <a href="noticias.php" class="text-decoration-none">
                    <div class="card border-0 shadow-lg rounded-4 h-100" style="background: rgba(30, 41, 59, 0.8); transition: transform 0.3s;">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle p-4 mx-auto mb-3" style="width: 80px; height: 80px; background: rgba(59, 130, 246, 0.2);">
                                <i class="bi bi-newspaper text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="text-white fw-bold mb-2">Gerenciar Notícias</h5>
                            <p class="text-secondary mb-0">Aprovar, editar e excluir notícias</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Gerenciar Escritores -->
            <div class="col-lg-4 col-md-6">
                <a href="escritores.php" class="text-decoration-none">
                    <div class="card border-0 shadow-lg rounded-4 h-100" style="background: rgba(30, 41, 59, 0.8);">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle p-4 mx-auto mb-3" style="width: 80px; height: 80px; background: rgba(34, 197, 94, 0.2);">
                                <i class="bi bi-people text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="text-white fw-bold mb-2">Gerenciar Escritores</h5>
                            <p class="text-secondary mb-0">Adicionar e remover escritores</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Candidatos -->
            <div class="col-lg-4 col-md-6">
                <a href="candidatos.php" class="text-decoration-none">
                    <div class="card border-0 shadow-lg rounded-4 h-100" style="background: rgba(30, 41, 59, 0.8);">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle p-4 mx-auto mb-3" style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.2);">
                                <i class="bi bi-person-plus text-danger" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="text-white fw-bold mb-2">Avaliar Candidatos</h5>
                            <p class="text-secondary mb-0">Aprovar ou rejeitar candidatos</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>

    </div>
</div>

<?php include '../components/footer.php'; ?>
