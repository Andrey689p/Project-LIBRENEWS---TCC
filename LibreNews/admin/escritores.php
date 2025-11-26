<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Verificar se é admin
requireAdmin();

$userData = getUserData();
$mensagem = '';

// Verificar mensagem da URL
if (isset($_GET['msg']) && $_GET['msg'] === 'removido') {
    $mensagem = 'Escritor removido com sucesso!';
}

// Buscar todos os escritores
$stmt = $pdo->query("
    SELECT e.*, c.nomeusuario, c.email, c.senha,
           (SELECT COUNT(*) FROM Noticia n WHERE n.Idescritor = e.Idescritor) as total_noticias,
           (SELECT COUNT(*) FROM Noticia n WHERE n.Idescritor = e.Idescritor AND n.status = 'publicada') as publicadas
    FROM Escritor e
    LEFT JOIN Conta c ON e.Idconta = c.Idconta
    ORDER BY c.nomeusuario ASC
");
$escritores = $stmt->fetchAll();

// Estatísticas gerais
$totalEscritores = count($escritores);
$totalNoticias = array_sum(array_column($escritores, 'total_noticias'));

$pageTitle = "Gerenciar Escritores - Admin";
include '../components/head.php';
include '../components/navbar.php';
?>

<!-- Gerenciar Escritores -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">
                    <i class="bi bi-people text-primary me-3"></i>Gerenciar Escritores
                </h1>
                <p class="text-secondary mb-0">Visualizar, editar e remover escritores</p>
            </div>
            <div class="d-flex gap-2">
                <a href="candidatos.php" class="btn btn-warning rounded-pill">
                    <i class="bi bi-person-plus me-2"></i>Ver Candidatos
                </a>
                <a href="dashboard.php" class="btn btn-secondary rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>

        <!-- Mensagem -->
        <?php if ($mensagem): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 rounded-4" style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22C55E !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1 small">Total de Escritores</p>
                                <h3 class="text-white fw-bold mb-0"><?= $totalEscritores ?></h3>
                            </div>
                            <i class="bi bi-people text-success fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 rounded-4" style="background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3B82F6 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1 small">Total de Notícias</p>
                                <h3 class="text-white fw-bold mb-0"><?= $totalNoticias ?></h3>
                            </div>
                            <i class="bi bi-newspaper text-primary fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Escritores -->
        <div class="row g-4">
            <?php if (empty($escritores)): ?>
                <div class="col-12">
                    <div class="alert alert-info rounded-3">
                        <i class="bi bi-info-circle me-2"></i>Nenhum escritor cadastrado.
                        <a href="candidatos.php" class="alert-link">Ver candidatos pendentes</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($escritores as $escritor): ?>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-lg rounded-4 h-100" style="background: rgba(30, 41, 59, 0.8);">
                            <div class="card-body p-4">
                                
                                <!-- Cabeçalho -->
                                <div class="d-flex align-items-start mb-3">
                                    <div class="rounded-circle p-3 me-3" style="background: rgba(34, 197, 94, 0.2);">
                                        <i class="bi bi-person text-success fs-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="text-white mb-1"><?= htmlspecialchars($escritor['nomeusuario']) ?></h5>
                                        <p class="text-primary mb-1 small">
                                            <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($escritor['email']) ?>
                                        </p>
                                        <p class="text-secondary mb-0 small">
                                            <i class="bi bi-key me-1"></i>Senha: <code class="text-warning"><?= htmlspecialchars($escritor['senha']) ?></code>
                                        </p>
                                    </div>
                                </div>

                                <!-- Estatísticas -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="p-2 rounded-3 text-center" style="background: rgba(59, 130, 246, 0.1);">
                                            <h5 class="text-primary mb-0"><?= $escritor['total_noticias'] ?></h5>
                                            <small class="text-secondary">Total</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 rounded-3 text-center" style="background: rgba(34, 197, 94, 0.1);">
                                            <h5 class="text-success mb-0"><?= $escritor['publicadas'] ?></h5>
                                            <small class="text-secondary">Publicadas</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ações -->
                                <div class="d-flex gap-2">
                                    <a href="editar-escritor.php?id=<?= $escritor['Idescritor'] ?>" 
                                       class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="bi bi-pencil-square me-1"></i>Editar
                                    </a>
                                    <a href="noticias.php?escritor=<?= $escritor['Idescritor'] ?>" 
                                       class="btn btn-info btn-sm flex-grow-1">
                                        <i class="bi bi-newspaper me-1"></i>Notícias
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include '../components/footer.php'; ?>
