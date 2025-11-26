<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Verificar se é admin
requireAdmin();

$userData = getUserData();
$mensagem = '';
$erro = '';

// Processar ações (aprovar, reprovar, excluir)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $noticiaId = intval($_POST['noticia_id'] ?? 0);
    
    if ($noticiaId > 0) {
        if ($acao === 'aprovar') {
            $stmt = $pdo->prepare("UPDATE Noticia SET status = 'publicada', datapublicacao = NOW() WHERE Idnoticia = ?");
            $stmt->execute([$noticiaId]);
            $mensagem = 'Notícia aprovada e publicada com sucesso!';
        } elseif ($acao === 'reprovar') {
            $stmt = $pdo->prepare("UPDATE Noticia SET status = 'reprovada' WHERE Idnoticia = ?");
            $stmt->execute([$noticiaId]);
            $mensagem = 'Notícia reprovada. O escritor será notificado.';
        } elseif ($acao === 'despublicar') {
            $stmt = $pdo->prepare("UPDATE Noticia SET status = 'reprovada' WHERE Idnoticia = ?");
            $stmt->execute([$noticiaId]);
            $mensagem = 'Notícia despublicada com sucesso.';
        } elseif ($acao === 'excluir') {
            $stmt = $pdo->prepare("DELETE FROM Noticia WHERE Idnoticia = ?");
            $stmt->execute([$noticiaId]);
            $mensagem = 'Notícia excluída permanentemente!';
        }
    }
}

// Estatísticas
$stmtStats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
        SUM(CASE WHEN status = 'publicada' THEN 1 ELSE 0 END) as publicadas,
        SUM(CASE WHEN status = 'reprovada' THEN 1 ELSE 0 END) as reprovadas
    FROM Noticia
");
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

// Filtro por status
$filtro = $_GET['filtro'] ?? 'todas';

// Paginação
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 10;
$offset = ($pagina - 1) * $porPagina;

$query = "SELECT n.*, c.nomeusuario as autor, cat.nomecategoria 
          FROM Noticia n 
          LEFT JOIN Escritor e ON n.Idescritor = e.Idescritor 
          LEFT JOIN Conta c ON e.Idconta = c.Idconta 
          LEFT JOIN Categoria cat ON n.Idcategoria = cat.Idcategoria";

$whereClause = "";
if ($filtro === 'pendentes') {
    $whereClause = " WHERE n.status = 'pendente'";
} elseif ($filtro === 'publicadas') {
    $whereClause = " WHERE n.status = 'publicada'";
} elseif ($filtro === 'reprovadas') {
    $whereClause = " WHERE n.status = 'reprovada'";
}

// Contar total
$queryCount = "SELECT COUNT(*) FROM Noticia n" . $whereClause;
$stmtCount = $pdo->query($queryCount);
$totalNoticias = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalNoticias / $porPagina);

// Buscar notícias paginadas
$query .= $whereClause . " ORDER BY n.datapublicacao DESC, n.Idnoticia DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$noticias = $stmt->fetchAll();

$pageTitle = "Gerenciar Notícias - Admin";
include '../components/head.php';
include '../components/navbar.php';
?>

<!-- Gerenciar Notícias -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">
                    <i class="bi bi-newspaper text-primary me-3"></i>Gerenciar Notícias
                </h1>
                <p class="text-secondary mb-0">Revisar, aprovar e gerenciar todas as notícias</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Voltar
            </a>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <a href="?filtro=todas" class="text-decoration-none">
                    <div class="card border-0 shadow-lg rounded-4 <?= $filtro === 'todas' ? 'border-primary border-2' : '' ?>" style="background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3B82F6 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-secondary mb-1 small">Total</p>
                                    <h4 class="text-white fw-bold mb-0"><?= $stats['total'] ?? 0 ?></h4>
                                </div>
                                <i class="bi bi-newspaper text-primary fs-3"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="?filtro=pendentes" class="text-decoration-none">
                    <div class="card border-0 shadow-lg rounded-4 <?= $filtro === 'pendentes' ? 'border-warning border-2' : '' ?>" style="background: rgba(251, 191, 36, 0.1); border-left: 4px solid #FBBF24 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-secondary mb-1 small">Pendentes</p>
                                    <h4 class="text-white fw-bold mb-0"><?= $stats['pendentes'] ?? 0 ?></h4>
                                </div>
                                <i class="bi bi-clock-history text-warning fs-3"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="?filtro=publicadas" class="text-decoration-none">
                    <div class="card border-0 shadow-lg rounded-4 <?= $filtro === 'publicadas' ? 'border-success border-2' : '' ?>" style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22C55E !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-secondary mb-1 small">Publicadas</p>
                                    <h4 class="text-white fw-bold mb-0"><?= $stats['publicadas'] ?? 0 ?></h4>
                                </div>
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="?filtro=reprovadas" class="text-decoration-none">
                    <div class="card border-0 shadow-lg rounded-4 <?= $filtro === 'reprovadas' ? 'border-danger border-2' : '' ?>" style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #EF4444 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-secondary mb-1 small">Reprovadas</p>
                                    <h4 class="text-white fw-bold mb-0"><?= $stats['reprovadas'] ?? 0 ?></h4>
                                </div>
                                <i class="bi bi-x-circle text-danger fs-3"></i>
                            </div>
                        </div>
                    </div>
                </a>
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

        <!-- Lista de Notícias -->
        <div class="row g-4">
            <?php if (empty($noticias)): ?>
                <div class="col-12">
                    <div class="alert alert-info rounded-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <?php if ($filtro !== 'todas'): ?>
                            Nenhuma notícia encontrada com o filtro "<?= ucfirst($filtro) ?>".
                        <?php else: ?>
                            Nenhuma notícia encontrada.
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($noticias as $noticia): ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-lg rounded-4" style="background: rgba(30, 41, 59, 0.8);">
                            <div class="card-body p-4">
                                <div class="row">
                                    
                                    <!-- Imagem -->
                                    <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                                        <?php if (!empty($noticia['imagem'])): ?>
                                            <img src="/LibreNews/<?= htmlspecialchars($noticia['imagem']) ?>" 
                                                 alt="Capa" class="img-fluid rounded-3" style="height: 120px; width: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-3 d-flex align-items-center justify-content-center" style="height: 120px;">
                                                <i class="bi bi-image text-white fs-2"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Informações -->
                                    <div class="col-lg-7 col-md-5">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge 
                                                <?= $noticia['status'] === 'publicada' ? 'bg-success' : '' ?>
                                                <?= $noticia['status'] === 'pendente' ? 'bg-warning text-dark' : '' ?>
                                                <?= $noticia['status'] === 'reprovada' ? 'bg-danger' : '' ?>">
                                                <i class="bi <?= $noticia['status'] === 'publicada' ? 'bi-check-circle' : ($noticia['status'] === 'pendente' ? 'bi-clock-history' : 'bi-x-circle') ?> me-1"></i>
                                                <?= ucfirst($noticia['status']) ?>
                                            </span>
                                            <span class="badge bg-primary">
                                                <i class="bi bi-tag me-1"></i><?= htmlspecialchars($noticia['nomecategoria'] ?? 'Sem categoria') ?>
                                            </span>
                                        </div>
                                        
                                        <h5 class="text-white mb-2"><?= htmlspecialchars($noticia['titulo']) ?></h5>
                                        
                                        <p class="text-secondary mb-2 small">
                                            <?= limitarTexto(strip_tags($noticia['conteudo']), 150) ?>
                                        </p>
                                        
                                        <div class="d-flex gap-3 flex-wrap">
                                            <small class="text-secondary">
                                                <i class="bi bi-person me-1"></i><?= htmlspecialchars($noticia['autor'] ?? 'Desconhecido') ?>
                                            </small>
                                            <?php if (!empty($noticia['datapublicacao'])): ?>
                                            <small class="text-secondary">
                                                <i class="bi bi-calendar me-1"></i><?= date('d/m/Y H:i', strtotime($noticia['datapublicacao'])) ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Ações -->
                                    <div class="col-lg-3 col-md-4 mt-3 mt-md-0">
                                        <div class="d-flex flex-column gap-2">
                                            
                                            <!-- Botão Revisar/Visualizar -->
                                            <button type="button" class="btn btn-info btn-sm w-100" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalNoticia<?= $noticia['Idnoticia'] ?>">
                                                <i class="bi bi-eye me-1"></i>Revisar Conteúdo
                                            </button>
                                            
                                            <?php if ($noticia['status'] === 'pendente'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="acao" value="aprovar">
                                                    <input type="hidden" name="noticia_id" value="<?= $noticia['Idnoticia'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                        <i class="bi bi-check-circle me-1"></i>Aprovar e Publicar
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="acao" value="reprovar">
                                                    <input type="hidden" name="noticia_id" value="<?= $noticia['Idnoticia'] ?>">
                                                    <button type="submit" class="btn btn-warning btn-sm w-100">
                                                        <i class="bi bi-x-circle me-1"></i>Reprovar
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($noticia['status'] === 'publicada'): ?>
                                                <a href="/LibreNews/noticia.php?id=<?= $noticia['Idnoticia'] ?>" 
                                                   class="btn btn-outline-success btn-sm w-100" target="_blank">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>Ver no Site
                                                </a>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Deseja despublicar esta notícia?');">
                                                    <input type="hidden" name="acao" value="despublicar">
                                                    <input type="hidden" name="noticia_id" value="<?= $noticia['Idnoticia'] ?>">
                                                    <button type="submit" class="btn btn-warning btn-sm w-100">
                                                        <i class="bi bi-eye-slash me-1"></i>Despublicar
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($noticia['status'] === 'reprovada'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="acao" value="aprovar">
                                                    <input type="hidden" name="noticia_id" value="<?= $noticia['Idnoticia'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                                        <i class="bi bi-check-circle me-1"></i>Aprovar
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form method="POST" class="d-inline" onsubmit="return confirm('ATENÇÃO: Esta ação é irreversível! Deseja excluir permanentemente esta notícia?');">
                                                <input type="hidden" name="acao" value="excluir">
                                                <input type="hidden" name="noticia_id" value="<?= $noticia['Idnoticia'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="bi bi-trash me-1"></i>Excluir
                                                </button>
                                            </form>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal para Revisar Notícia -->
                    <div class="modal fade" id="modalNoticia<?= $noticia['Idnoticia'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content" style="background: #1E293B;">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title text-white">
                                        <i class="bi bi-eye me-2 text-primary"></i>Revisar Notícia
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Status -->
                                    <div class="mb-3">
                                        <span class="badge 
                                            <?= $noticia['status'] === 'publicada' ? 'bg-success' : '' ?>
                                            <?= $noticia['status'] === 'pendente' ? 'bg-warning text-dark' : '' ?>
                                            <?= $noticia['status'] === 'reprovada' ? 'bg-danger' : '' ?> fs-6">
                                            Status: <?= ucfirst($noticia['status']) ?>
                                        </span>
                                        <span class="badge bg-primary fs-6 ms-2">
                                            <?= htmlspecialchars($noticia['nomecategoria'] ?? 'Sem categoria') ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Título -->
                                    <h3 class="text-white mb-3"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                                    
                                    <!-- Metadados -->
                                    <div class="d-flex gap-4 mb-4 text-secondary">
                                        <span><i class="bi bi-person me-1"></i> <?= htmlspecialchars($noticia['autor'] ?? 'Desconhecido') ?></span>
                                        <?php if (!empty($noticia['datapublicacao'])): ?>
                                        <span><i class="bi bi-calendar me-1"></i> <?= date('d/m/Y H:i', strtotime($noticia['datapublicacao'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Imagem -->
                                    <?php if (!empty($noticia['imagem'])): ?>
                                    <div class="mb-4">
                                        <img src="/LibreNews/<?= htmlspecialchars($noticia['imagem']) ?>" 
                                             alt="Imagem de capa" class="img-fluid rounded-3" style="max-height: 400px;">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Conteúdo -->
                                    <div class="bg-dark rounded-3 p-4">
                                        <h6 class="text-white mb-3"><i class="bi bi-file-text me-2"></i>Conteúdo da Notícia:</h6>
                                        <div class="text-secondary" style="white-space: pre-wrap; line-height: 1.8;">
                                            <?= nl2br(htmlspecialchars($noticia['conteudo'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <?php if ($noticia['status'] === 'pendente'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="acao" value="aprovar">
                                            <input type="hidden" name="noticia_id" value="<?= $noticia['Idnoticia'] ?>">
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-circle me-1"></i>Aprovar e Publicar
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="acao" value="reprovar">
                                            <input type="hidden" name="noticia_id" value="<?= $noticia['Idnoticia'] ?>">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="bi bi-x-circle me-1"></i>Reprovar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
        <nav aria-label="Paginação de notícias" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($pagina > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?filtro=<?= $filtro ?>&pagina=<?= $pagina - 1 ?>">
                        <i class="bi bi-chevron-left"></i> Anterior
                    </a>
                </li>
                <?php endif; ?>
                
                <?php
                $inicio = max(1, $pagina - 2);
                $fim = min($totalPaginas, $pagina + 2);
                
                for ($i = $inicio; $i <= $fim; $i++):
                ?>
                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="?filtro=<?= $filtro ?>&pagina=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($pagina < $totalPaginas): ?>
                <li class="page-item">
                    <a class="page-link" href="?filtro=<?= $filtro ?>&pagina=<?= $pagina + 1 ?>">
                        Próxima <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

    </div>
</div>

<?php include '../components/footer.php'; ?>
