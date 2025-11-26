<?php 
require_once 'config/database.php';
require_once 'includes/functions.php';

// Obter termo de busca
$termo = isset($_GET['q']) ? trim($_GET['q']) : '';

// Paginação
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 12;
$offset = ($pagina - 1) * $porPagina;

$noticias = [];
$totalNoticias = 0;
$totalPaginas = 0;

if (!empty($termo)) {
    // Buscar notícias publicadas que contenham o termo
    $termoBusca = '%' . $termo . '%';
    
    // Contar total de resultados
    $stmtCount = $pdo->prepare("
        SELECT COUNT(*) 
        FROM Noticia n 
        LEFT JOIN Escritor e ON n.Idescritor = e.Idescritor 
        LEFT JOIN Conta c ON e.Idconta = c.Idconta 
        LEFT JOIN Categoria cat ON n.Idcategoria = cat.Idcategoria
        WHERE n.status = 'publicada' 
        AND (n.titulo LIKE ? OR n.conteudo LIKE ? OR c.nomeusuario LIKE ? OR cat.nomecategoria LIKE ?)
    ");
    $stmtCount->execute([$termoBusca, $termoBusca, $termoBusca, $termoBusca]);
    $totalNoticias = $stmtCount->fetchColumn();
    $totalPaginas = ceil($totalNoticias / $porPagina);
    
    // Buscar notícias com paginação
    $stmt = $pdo->prepare("
        SELECT n.*, c.nomeusuario as autor, cat.nomecategoria, cat.Idcategoria
        FROM Noticia n 
        LEFT JOIN Escritor e ON n.Idescritor = e.Idescritor 
        LEFT JOIN Conta c ON e.Idconta = c.Idconta 
        LEFT JOIN Categoria cat ON n.Idcategoria = cat.Idcategoria
        WHERE n.status = 'publicada' 
        AND (n.titulo LIKE ? OR n.conteudo LIKE ? OR c.nomeusuario LIKE ? OR cat.nomecategoria LIKE ?)
        ORDER BY n.datapublicacao DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$termoBusca, $termoBusca, $termoBusca, $termoBusca, $porPagina, $offset]);
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para destacar termo de busca no texto
function destacarBusca($texto, $termo) {
    if (empty($termo)) return $texto;
    return preg_replace('/(' . preg_quote($termo, '/') . ')/i', '<mark>$1</mark>', $texto);
}

// Função para obter ícone da categoria
function getIconeCategoria($categoria) {
    $icones = [
        'Desenvolvimento' => 'bi-code-slash',
        'Programação' => 'bi-terminal',
        'Tecnologia' => 'bi-cpu',
        'Sistemas' => 'bi-hdd-network',
        'Mercado' => 'bi-graph-up-arrow'
    ];
    return $icones[$categoria] ?? 'bi-newspaper';
}

// Função para obter imagem
function getImagemNoticia($noticia) {
    if (!empty($noticia['imagem'])) {
        return $noticia['imagem'];
    }
    if (!empty($noticia['imagemcapa'])) {
        return $noticia['imagemcapa'];
    }
    return 'assets/img/news-1.jpg';
}

$pageTitle = !empty($termo) ? "Busca: " . htmlspecialchars($termo) . " - LibreNews" : "Buscar Notícias - LibreNews";
include 'components/head.php'; 
include 'components/navbar.php';
?>

<!-- ========================================
     PÁGINA DE BUSCA
     ======================================== -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container py-5">
        
        <!-- Cabeçalho -->
        <div class="row mb-5">
            <div class="col-12">
                <h1 class="text-white fw-bold mb-3">
                    <i class="bi bi-search text-primary me-2"></i>Buscar Notícias
                </h1>
                
                <!-- Formulário de Busca -->
                <form method="GET" action="busca.php" class="mb-4">
                    <div class="input-group input-group-lg">
                        <input 
                            type="search" 
                            name="q" 
                            class="form-control rounded-start-pill py-3" 
                            placeholder="Digite sua busca..." 
                            value="<?= htmlspecialchars($termo) ?>"
                            required
                            autofocus
                        >
                        <button type="submit" class="btn btn-primary rounded-end-pill px-5">
                            <i class="bi bi-search me-2"></i>Buscar
                        </button>
                    </div>
                </form>
                
                <?php if (!empty($termo)): ?>
                    <p class="text-secondary mb-0">
                        <?php if ($totalNoticias > 0): ?>
                            Encontrados <strong class="text-white"><?= $totalNoticias ?></strong> resultado(s) para "<strong class="text-white"><?= htmlspecialchars($termo) ?></strong>"
                        <?php else: ?>
                            Nenhum resultado encontrado para "<strong class="text-white"><?= htmlspecialchars($termo) ?></strong>"
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Resultados -->
        <?php if (!empty($termo)): ?>
            <?php if (!empty($noticias)): ?>
                <div class="row g-4">
                    <?php foreach ($noticias as $noticia): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 rounded-4">
                            <div class="position-relative overflow-hidden rounded-top-4">
                                <a href="noticia.php?id=<?= $noticia['Idnoticia'] ?>">
                                    <img src="<?= getImagemNoticia($noticia) ?>" 
                                         class="card-img-top img-zoomin" 
                                         alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                                         style="height: 200px; object-fit: cover;">
                                </a>
                                <span class="position-absolute top-0 end-0 m-3 badge bg-primary px-4 py-2 rounded-pill fs-6">
                                    <i class="bi <?= getIconeCategoria($noticia['nomecategoria']) ?> me-1"></i><?= htmlspecialchars($noticia['nomecategoria']) ?>
                                </span>
                            </div>
                            <div class="card-body py-3 px-3">
                                <?php 
                                $dataPub = $noticia['datapublicacao'] ?? null;
                                if ($dataPub): 
                                ?>
                                <small class="text-secondary d-block mb-2">
                                    <i class="bi bi-calendar3 me-1"></i> <?= formatarDataSimples($dataPub) ?>
                                </small>
                                <?php endif; ?>
                                <a href="noticia.php?id=<?= $noticia['Idnoticia'] ?>" class="h5 text-white link-hover d-block mb-2">
                                    <?= destacarBusca(htmlspecialchars($noticia['titulo']), $termo) ?>
                                </a>
                                <p class="text-secondary mb-3 small">
                                    <?= destacarBusca(limitarTexto(strip_tags($noticia['conteudo']), 120), $termo) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-secondary">
                                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($noticia['autor']) ?>
                                    </small>
                                    <div class="text-secondary small">
                                        <i class="bi bi-clock me-1"></i><?= ceil(strlen(strip_tags($noticia['conteudo'])) / 1000) ?> min
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Paginação -->
                <?php if ($totalPaginas > 1): ?>
                <nav aria-label="Paginação de resultados" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagina > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?q=<?= urlencode($termo) ?>&pagina=<?= $pagina - 1 ?>">
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
                            <a class="page-link" href="?q=<?= urlencode($termo) ?>&pagina=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagina < $totalPaginas): ?>
                        <li class="page-item">
                            <a class="page-link" href="?q=<?= urlencode($termo) ?>&pagina=<?= $pagina + 1 ?>">
                                Próxima <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="alert alert-info rounded-4 text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    Nenhum resultado encontrado. Tente usar outros termos de busca.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-secondary rounded-4 text-center">
                <i class="bi bi-search me-2"></i>
                Digite um termo de busca no campo acima para encontrar notícias.
            </div>
        <?php endif; ?>
        
    </div>
</div>
<!-- FIM PÁGINA DE BUSCA -->

<?php include 'components/footer.php'; ?>

