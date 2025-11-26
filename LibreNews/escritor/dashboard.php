<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Verificar se é escritor ou admin
requireEscritor();

$userData = getUserData();
$mensagem = '';
$erro = '';

// Buscar Idescritor
$idescritor = getIdEscritor($pdo, $userData['id']);

// Processar ações (deletar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $noticiaId = intval($_POST['noticia_id'] ?? 0);
    
    if ($acao === 'deletar' && $noticiaId > 0 && $idescritor) {
        // Verificar se a notícia pertence ao escritor
        $stmtVerifica = $pdo->prepare("SELECT Idescritor FROM Noticia WHERE Idnoticia = ?");
        $stmtVerifica->execute([$noticiaId]);
        $noticiaVerifica = $stmtVerifica->fetch();
        
        if ($noticiaVerifica && $noticiaVerifica['Idescritor'] == $idescritor) {
            $stmtDelete = $pdo->prepare("DELETE FROM Noticia WHERE Idnoticia = ?");
            $stmtDelete->execute([$noticiaId]);
            $mensagem = 'Notícia excluída com sucesso!';
        } else {
            $erro = 'Você não tem permissão para excluir esta notícia.';
        }
    }
}

// Estatísticas do escritor
$estatisticas = [
    'total' => 0,
    'publicadas' => 0,
    'pendentes' => 0,
    'reprovadas' => 0
];

if ($idescritor) {
    // Contar notícias por status
    $stmtStats = $pdo->prepare("
        SELECT status, COUNT(*) as total 
        FROM Noticia 
        WHERE Idescritor = ? 
        GROUP BY status
    ");
    $stmtStats->execute([$idescritor]);
    while ($row = $stmtStats->fetch()) {
        $estatisticas[$row['status'] . 's'] = $row['total'];
        $estatisticas['total'] += $row['total'];
    }
    
    // Corrigir nome da chave para 'publicadas'
    if (isset($estatisticas['publicadas'])) {
        $estatisticas['publicadas'] = $estatisticas['publicadas'];
    }
}

// Filtro por status
$filtro = $_GET['filtro'] ?? 'todas';

// Buscar notícias do escritor logado
if ($idescritor) {
    $query = "
        SELECT n.*, c.nomecategoria 
        FROM Noticia n 
        LEFT JOIN Categoria c ON n.Idcategoria = c.Idcategoria 
        WHERE n.Idescritor = ?
    ";
    
    if ($filtro === 'publicadas') {
        $query .= " AND n.status = 'publicada'";
    } elseif ($filtro === 'pendentes') {
        $query .= " AND n.status = 'pendente'";
    } elseif ($filtro === 'reprovadas') {
        $query .= " AND n.status = 'reprovada'";
    }
    
    $query .= " ORDER BY n.datapublicacao DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$idescritor]);
    $noticias = $stmt->fetchAll();
} else {
    $noticias = [];
}

$pageTitle = "Painel do Escritor - LibreNews";
include '../components/head.php';
include '../components/navbar.php';
?>

<!-- Dashboard do Escritor -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">
                    <i class="bi bi-pencil-square text-primary me-3"></i>Painel do Escritor
                </h1>
                <p class="text-secondary mb-0">Bem-vindo, <?= htmlspecialchars($userData['name']) ?>!</p>
            </div>
            <a href="nova-noticia.php" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-circle me-2"></i>Nova Notícia
            </a>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3B82F6 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1 small">Total</p>
                                <h4 class="text-white fw-bold mb-0"><?= $estatisticas['total'] ?></h4>
                            </div>
                            <i class="bi bi-newspaper text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22C55E !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1 small">Publicadas</p>
                                <h4 class="text-white fw-bold mb-0"><?= $estatisticas['publicadas'] ?? 0 ?></h4>
                            </div>
                            <i class="bi bi-check-circle text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(251, 191, 36, 0.1); border-left: 4px solid #FBBF24 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1 small">Pendentes</p>
                                <h4 class="text-white fw-bold mb-0"><?= $estatisticas['pendentes'] ?? 0 ?></h4>
                            </div>
                            <i class="bi bi-clock-history text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #EF4444 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1 small">Reprovadas</p>
                                <h4 class="text-white fw-bold mb-0"><?= $estatisticas['reprovadas'] ?? 0 ?></h4>
                            </div>
                            <i class="bi bi-x-circle text-danger fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensagens -->
        <?php if ($mensagem): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $erro ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: rgba(30, 41, 59, 0.8);">
            <div class="card-body p-3">
                <div class="btn-group flex-wrap" role="group">
                    <a href="?filtro=todas" class="btn <?= $filtro === 'todas' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="bi bi-list me-1"></i>Todas
                    </a>
                    <a href="?filtro=publicadas" class="btn <?= $filtro === 'publicadas' ? 'btn-success' : 'btn-outline-success' ?>">
                        <i class="bi bi-check-circle me-1"></i>Publicadas
                    </a>
                    <a href="?filtro=pendentes" class="btn <?= $filtro === 'pendentes' ? 'btn-warning' : 'btn-outline-warning' ?>">
                        <i class="bi bi-clock-history me-1"></i>Pendentes
                    </a>
                    <a href="?filtro=reprovadas" class="btn <?= $filtro === 'reprovadas' ? 'btn-danger' : 'btn-outline-danger' ?>">
                        <i class="bi bi-x-circle me-1"></i>Reprovadas
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista de Notícias -->
        <div class="row g-4">
            <?php if (empty($noticias)): ?>
                <div class="col-12">
                    <div class="alert alert-info rounded-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <?php if ($filtro !== 'todas'): ?>
                            Nenhuma notícia encontrada com o filtro selecionado.
                        <?php else: ?>
                            Você ainda não publicou nenhuma notícia. <a href="nova-noticia.php" class="alert-link">Criar primeira notícia</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($noticias as $noticia): ?>
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-lg rounded-4" style="background: rgba(30, 41, 59, 0.8);">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <!-- Imagem -->
                                    <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                                        <?php if (!empty($noticia['imagem'])): ?>
                                            <img src="/LibreNews/<?= htmlspecialchars($noticia['imagem']) ?>" 
                                                 alt="Capa" class="img-fluid rounded-3" style="height: 80px; width: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-3 d-flex align-items-center justify-content-center" style="height: 80px;">
                                                <i class="bi bi-image text-white fs-3"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Informações -->
                                    <div class="col-lg-7 col-md-5">
                                        <h5 class="text-white mb-2"><?= htmlspecialchars($noticia['titulo']) ?></h5>
                                        <div class="d-flex gap-3 flex-wrap align-items-center">
                                            <small class="text-secondary">
                                                <i class="bi bi-tag me-1"></i><?= htmlspecialchars($noticia['nomecategoria']) ?>
                                            </small>
                                            <?php if (!empty($noticia['datapublicacao'])): ?>
                                            <small class="text-secondary">
                                                <i class="bi bi-calendar me-1"></i><?= date('d/m/Y', strtotime($noticia['datapublicacao'])) ?>
                                            </small>
                                            <?php endif; ?>
                                            <span class="badge 
                                                <?= $noticia['status'] === 'publicada' ? 'bg-success' : '' ?>
                                                <?= $noticia['status'] === 'pendente' ? 'bg-warning text-dark' : '' ?>
                                                <?= $noticia['status'] === 'reprovada' ? 'bg-danger' : '' ?>">
                                                <i class="bi <?= $noticia['status'] === 'publicada' ? 'bi-check-circle' : ($noticia['status'] === 'pendente' ? 'bi-clock-history' : 'bi-x-circle') ?> me-1"></i>
                                                <?= ucfirst($noticia['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Ações -->
                                    <div class="col-lg-3 col-md-4 mt-3 mt-md-0">
                                        <div class="d-flex gap-2 flex-wrap justify-content-md-end">
                                            <?php if ($noticia['status'] === 'publicada'): ?>
                                                <a href="/LibreNews/noticia.php?id=<?= $noticia['Idnoticia'] ?>" target="_blank" class="btn btn-success btn-sm">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Botão Editar - SEMPRE visível -->
                                            <a href="editar-noticia.php?id=<?= $noticia['Idnoticia'] ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                            
                                            <?php if ($noticia['status'] === 'reprovada'): ?>
                                                <a href="editar-noticia.php?id=<?= $noticia['Idnoticia'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-send"></i> Reenviar
                                                </a>
                                            <?php endif; ?>
                                            
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta notícia?');">
                                                <input type="hidden" name="acao" value="deletar">
                                                <input type="hidden" name="noticia_id" value="<?= $noticia['Idnoticia'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </div>
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
