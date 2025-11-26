<?php 
require_once 'config/database.php';
require_once 'includes/functions.php';

// Filtro por categoria (se houver)
$categoriaFiltro = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;

// Paginação para seção "Últimas Notícias"
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 6;
$offsetUltimas = ($pagina - 1) * $porPagina;

// Buscar categorias para filtros
$stmtCategorias = $pdo->query("SELECT Idcategoria, nomecategoria FROM Categoria ORDER BY nomecategoria ASC");
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Buscar notícias publicadas (aleatórias) para carousel e destaques
$queryNoticias = "SELECT n.*, c.nomeusuario as autor, cat.nomecategoria, cat.Idcategoria
                  FROM Noticia n 
                  LEFT JOIN Escritor e ON n.Idescritor = e.Idescritor 
                  LEFT JOIN Conta c ON e.Idconta = c.Idconta 
                  LEFT JOIN Categoria cat ON n.Idcategoria = cat.Idcategoria
                  WHERE n.status = 'publicada'";

// Aplicar filtro de categoria se houver
if ($categoriaFiltro > 0) {
    $queryNoticias .= " AND n.Idcategoria = :categoria";
    $stmtNoticias = $pdo->prepare($queryNoticias . " ORDER BY RAND() LIMIT 18");
    $stmtNoticias->execute([':categoria' => $categoriaFiltro]);
} else {
    $stmtNoticias = $pdo->query($queryNoticias . " ORDER BY RAND() LIMIT 18");
}

$todasNoticias = $stmtNoticias->fetchAll(PDO::FETCH_ASSOC);

// Separar notícias para diferentes seções
$noticiasCarousel = array_slice($todasNoticias, 0, 12); // 12 notícias para o carousel (4 por slide)
$noticiaDestaque = !empty($todasNoticias) ? $todasNoticias[0] : null;
$noticiasEmAlta = array_slice($todasNoticias, 1, 5); // 5 notícias para "Em Alta"

// Buscar notícias paginadas para "Últimas Notícias"
$queryUltimas = "SELECT n.*, c.nomeusuario as autor, cat.nomecategoria, cat.Idcategoria
                  FROM Noticia n 
                  LEFT JOIN Escritor e ON n.Idescritor = e.Idescritor 
                  LEFT JOIN Conta c ON e.Idconta = c.Idconta 
                  LEFT JOIN Categoria cat ON n.Idcategoria = cat.Idcategoria
                  WHERE n.status = 'publicada'";

if ($categoriaFiltro > 0) {
    $queryUltimas .= " AND n.Idcategoria = :categoria";
}

$queryUltimas .= " ORDER BY n.datapublicacao DESC LIMIT :limit OFFSET :offset";

// Contar total para paginação
$queryCount = "SELECT COUNT(*) FROM Noticia n WHERE n.status = 'publicada'";
if ($categoriaFiltro > 0) {
    $queryCount .= " AND n.Idcategoria = :categoria";
    $stmtCount = $pdo->prepare($queryCount);
    $stmtCount->execute([':categoria' => $categoriaFiltro]);
} else {
    $stmtCount = $pdo->query($queryCount);
}
$totalUltimas = $stmtCount->fetchColumn();
$totalPaginasUltimas = ceil($totalUltimas / $porPagina);

// Buscar últimas notícias paginadas
if ($categoriaFiltro > 0) {
    $stmtUltimas = $pdo->prepare($queryUltimas);
    $stmtUltimas->bindValue(':categoria', $categoriaFiltro, PDO::PARAM_INT);
    $stmtUltimas->bindValue(':limit', $porPagina, PDO::PARAM_INT);
    $stmtUltimas->bindValue(':offset', $offsetUltimas, PDO::PARAM_INT);
    $stmtUltimas->execute();
} else {
    $stmtUltimas = $pdo->prepare($queryUltimas);
    $stmtUltimas->bindValue(':limit', $porPagina, PDO::PARAM_INT);
    $stmtUltimas->bindValue(':offset', $offsetUltimas, PDO::PARAM_INT);
    $stmtUltimas->execute();
}
$ultimasNoticias = $stmtUltimas->fetchAll(PDO::FETCH_ASSOC);

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

// Função para obter imagem padrão se não houver
function getImagemNoticia($noticia) {
    // Verificar campo imagem primeiro
    if (!empty($noticia['imagem'])) {
        // Se já começar com http ou /, retornar como está
        if (preg_match('/^(https?:\/\/|\/)/', $noticia['imagem'])) {
            return $noticia['imagem'];
        }
        // Caso contrário, retornar o caminho relativo
        return $noticia['imagem'];
    }
    // Verificar campo imagemcapa
    if (!empty($noticia['imagemcapa'])) {
        if (preg_match('/^(https?:\/\/|\/)/', $noticia['imagemcapa'])) {
            return $noticia['imagemcapa'];
        }
        return $noticia['imagemcapa'];
    }
    // Imagens padrão aleatórias se não houver imagem
    $imagensPadrao = [
        'assets/img/news-1.jpg',
        'assets/img/news-2.jpg',
        'assets/img/news-3.jpg',
        'assets/img/news-4.jpg',
        'assets/img/news-5.jpg',
        'assets/img/news-6.jpg',
        'assets/img/news-7.jpg',
        'assets/img/news-8.jpg'
    ];
    return $imagensPadrao[array_rand($imagensPadrao)];
}

// Função para criar resumo do conteúdo
function criarResumo($conteudo, $limite = 150) {
    $texto = strip_tags($conteudo);
    $texto = html_entity_decode($texto);
    if (strlen($texto) <= $limite) {
        return $texto;
    }
    return substr($texto, 0, $limite) . '...';
}

// Contar notícias por categoria
function contarNoticiasPorCategoria($pdo, $categoriaId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Noticia WHERE Idcategoria = ? AND status = 'publicada'");
    $stmt->execute([$categoriaId]);
    return $stmt->fetchColumn();
}

$pageTitle = "Home - LibreNews";
include 'components/head.php'; 
?>

<?php include 'components/navbar.php'; ?>

<!-- ========================================
     CAROUSEL PRINCIPAL - NOTÍCIAS EM DESTAQUE
     ======================================== -->
<?php if (!empty($noticiasCarousel)): ?>
<div id="carouselExampleIndicators" class="carousel slide position-relative" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php 
        $slides = array_chunk($noticiasCarousel, 4); // 4 notícias por slide
        foreach ($slides as $index => $slideNoticias): 
        ?>
        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <div class="container py-4">
                <div class="row g-3">
                    <?php if (isset($slideNoticias[0])): ?>
                    <div class="col-md-6">
                        <a href="noticia.php?id=<?= $slideNoticias[0]['Idnoticia'] ?>" class="text-decoration-none">
                            <div class="carousel-box box-1 rounded-4" style="background-image: url('<?= getImagemNoticia($slideNoticias[0]) ?>'); background-size: cover; background-position: center;">
                                <div class="carousel-text p-4">
                                    <small class="text-uppercase"><?= htmlspecialchars($slideNoticias[0]['nomecategoria']) ?></small>
                                    <h5 class="fw-bold"><?= htmlspecialchars($slideNoticias[0]['titulo']) ?></h5>
                                    <p class="mb-0">Por <?= htmlspecialchars($slideNoticias[0]['autor']) ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6">
                        <div class="row g-3">
                            <?php for ($i = 1; $i < 4; $i++): ?>
                                <?php if (isset($slideNoticias[$i])): ?>
                                <div class="<?= $i === 3 ? 'col-12' : 'col-6' ?>">
                                    <a href="noticia.php?id=<?= $slideNoticias[$i]['Idnoticia'] ?>" class="text-decoration-none">
                                        <div class="carousel-box box-<?= $i + 1 ?> rounded-4" style="background-image: url('<?= getImagemNoticia($slideNoticias[$i]) ?>'); background-size: cover; background-position: center;">
                                            <div class="carousel-text p-3">
                                                <small class="text-uppercase"><?= htmlspecialchars($slideNoticias[$i]['nomecategoria']) ?></small>
                                                <h6 class="mb-0"><?= htmlspecialchars($slideNoticias[$i]['titulo']) ?></h6>
                                                <small>Por <?= htmlspecialchars($slideNoticias[$i]['autor']) ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Indicadores do Carousel -->
    <?php if (count($slides) > 1): ?>
    <div class="carousel-indicators mb-0">
        <?php for ($i = 0; $i < count($slides); $i++): ?>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $i + 1 ?>"></button>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <!-- Controles do Carousel -->
    <?php if (count($slides) > 1): ?>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="bi bi-chevron-left fs-1" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="bi bi-chevron-right fs-1" aria-hidden="true"></span>
        <span class="visually-hidden">Próximo</span>
    </button>
    <?php endif; ?>
</div>
<?php endif; ?>
<!-- FIM CAROUSEL PRINCIPAL -->

<!-- ========================================
     MATÉRIA PRINCIPAL + EM ALTA
     ======================================== -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-5">
            <!-- Matéria em Destaque (Lado Esquerdo) -->
            <div class="col-lg-8">
                <?php if ($noticiaDestaque): ?>
                <div class="position-relative overflow-hidden rounded-4">
                    <a href="noticia.php?id=<?= $noticiaDestaque['Idnoticia'] ?>">
                        <img src="<?= getImagemNoticia($noticiaDestaque) ?>" class="img-fluid rounded-4 img-zoomin w-100" 
                             alt="<?= htmlspecialchars($noticiaDestaque['titulo']) ?>" 
                             style="height: 460px; object-fit: cover;">
                    </a>
                    <div class="position-absolute top-0 start-0 m-4">
                        <span class="badge bg-primary rounded-pill d-flex align-items-center gap-2 px-4 py-2">
                            <i class="bi bi-star-fill"></i> Destaque do Dia
                        </span>
                    </div>
                </div>

                <!-- Informações da Matéria -->
                <div class="mt-4">
                    <div class="d-flex align-items-center flex-wrap gap-3 mb-3">
                        <span class="badge bg-secondary rounded-pill d-inline-flex align-items-center gap-1 px-3 py-2">
                            <i class="bi <?= getIconeCategoria($noticiaDestaque['nomecategoria']) ?>"></i> <?= htmlspecialchars($noticiaDestaque['nomecategoria']) ?>
                        </span>
                        <?php 
                        $dataPub = $noticiaDestaque['datapublicacao'] ?? null;
                        if ($dataPub): 
                        ?>
                        <small class="text-secondary"><i class="bi bi-calendar3 me-2"></i><?= formatarDataSimples($dataPub) ?></small>
                        <?php endif; ?>
                        <small class="text-secondary"><i class="bi bi-person me-2"></i><?= htmlspecialchars($noticiaDestaque['autor']) ?></small>
                    </div>

                    <a href="noticia.php?id=<?= $noticiaDestaque['Idnoticia'] ?>" class="display-5 text-white mb-3 d-block link-hover fw-bold">
                        <?= htmlspecialchars($noticiaDestaque['titulo']) ?>
                    </a>

                    <p class="text-secondary fs-5 mb-4 lh-lg">
                        <?= criarResumo($noticiaDestaque['conteudo'], 200) ?>
                    </p>

                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <a href="noticia.php?id=<?= $noticiaDestaque['Idnoticia'] ?>" class="btn btn-primary rounded-pill px-5 py-3 d-inline-flex align-items-center gap-2">
                            <i class="bi bi-book-half"></i> Ler Matéria Completa
                        </a>
                        <div class="d-flex align-items-center gap-4 text-secondary">
                            <span><i class="bi bi-clock me-2"></i><?= ceil(strlen(strip_tags($noticiaDestaque['conteudo'])) / 1000) ?> min de leitura</span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-info rounded-4">
                    <i class="bi bi-info-circle me-2"></i>Nenhuma notícia em destaque no momento.
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar "Em Alta" (Lado Direito) -->
            <div class="col-lg-4">
                <div class="bg-light rounded-4 p-4 h-100">
                    <h3 class="mb-4 text-white">
                        <i class="bi bi-fire text-primary me-2"></i> Em Alta
                    </h3>

                    <div class="d-flex flex-column gap-4">
                        <?php if (!empty($noticiasEmAlta)): ?>
                            <?php foreach ($noticiasEmAlta as $index => $noticia): ?>
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge <?= $index < 3 ? 'bg-primary' : 'rounded-pill text-white' ?>" 
                                      style="<?= $index >= 3 ? 'background: linear-gradient(135deg, #81C784, #4FC3F7);' : '' ?> width: 44px; height: 44px; font-size: 1.4rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                    <?= $index + 1 ?>
                                </span>
                                <div class="w-100">
                                    <span class="badge bg-secondary rounded-pill d-inline-flex align-items-center gap-1 small mb-2">
                                        <i class="bi <?= getIconeCategoria($noticia['nomecategoria']) ?>"></i> <?= htmlspecialchars($noticia['nomecategoria']) ?>
                                    </span>
                                    <a href="noticia.php?id=<?= $noticia['Idnoticia'] ?>" class="h6 text-white link-hover d-block mb-1"><?= htmlspecialchars($noticia['titulo']) ?></a>
                                    <div class="text-secondary small">
                                        <i class="bi bi-clock me-1"></i><?= ceil(strlen(strip_tags($noticia['conteudo'])) / 1000) ?> min • 
                                        <i class="bi bi-person ms-2 me-1"></i><?= htmlspecialchars($noticia['autor']) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-secondary">Nenhuma notícia em alta no momento.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM MATÉRIA PRINCIPAL + EM ALTA -->

<!-- ========================================
     ÚLTIMAS NOTÍCIAS
     ======================================== -->
<div class="container-fluid py-5 bg-darker">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
            <h2 class="text-white mb-0">
                <i class="bi bi-newspaper me-2"></i>Últimas Notícias
            </h2>
            
            <!-- Filtro por Categoria -->
            <div class="d-flex align-items-center gap-3">
                <form method="GET" class="d-flex gap-2">
                    <select name="categoria" class="form-select form-select-sm" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="0" <?= $categoriaFiltro == 0 ? 'selected' : '' ?>>Todas as categorias</option>
                        <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['Idcategoria'] ?>" <?= $categoriaFiltro == $cat['Idcategoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nomecategoria']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($ultimasNoticias)): ?>
                <?php foreach ($ultimasNoticias as $noticia): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 rounded-4">
                        <div class="position-relative overflow-hidden rounded-top-4">
                            <a href="noticia.php?id=<?= $noticia['Idnoticia'] ?>">
                                <img src="<?= getImagemNoticia($noticia) ?>" class="card-img-top img-zoomin" alt="<?= htmlspecialchars($noticia['titulo']) ?>" style="height: 180px; object-fit: cover;">
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
                            <a href="noticia.php?id=<?= $noticia['Idnoticia'] ?>" class="h5 text-white link-hover d-block mb-2"><?= htmlspecialchars($noticia['titulo']) ?></a>
                            <p class="text-secondary mb-3 small"><?= criarResumo($noticia['conteudo'], 120) ?></p>
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
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info rounded-4 text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        <?= $categoriaFiltro > 0 ? 'Nenhuma notícia encontrada nesta categoria.' : 'Nenhuma notícia publicada ainda.' ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPaginasUltimas > 1): ?>
        <nav aria-label="Paginação de notícias" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php if ($pagina > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= $categoriaFiltro > 0 ? 'categoria=' . $categoriaFiltro . '&' : '' ?>pagina=<?= $pagina - 1 ?>">
                        <i class="bi bi-chevron-left"></i> Anterior
                    </a>
                </li>
                <?php endif; ?>
                
                <?php
                $inicio = max(1, $pagina - 2);
                $fim = min($totalPaginasUltimas, $pagina + 2);
                
                for ($i = $inicio; $i <= $fim; $i++):
                ?>
                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= $categoriaFiltro > 0 ? 'categoria=' . $categoriaFiltro . '&' : '' ?>pagina=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($pagina < $totalPaginasUltimas): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= $categoriaFiltro > 0 ? 'categoria=' . $categoriaFiltro . '&' : '' ?>pagina=<?= $pagina + 1 ?>">
                        Próxima <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>
<!-- FIM ÚLTIMAS NOTÍCIAS -->

<!-- ========================================
     EXPLORE POR CATEGORIA (BANNER FULL-WIDTH)
     ======================================== -->
<div class="container-fluid py-5">
    <div class="px-3 px-md-4">
        <div class="banner-section">
            <h2 class="text-white mb-5 text-center">
                <i class="bi bi-grid-3x3-gap me-2"></i>Explore por Categoria
            </h2>
            <div class="row g-3 justify-content-center mx-auto" style="max-width: 1400px;">
                <?php 
                $coresCategorias = [
                    'Desenvolvimento' => 'linear-gradient(135deg, #4FC3F7 0%, #3B82F6 100%)',
                    'Programação' => 'linear-gradient(135deg, #81C784 0%, #66BB6A 100%)',
                    'Tecnologia' => 'linear-gradient(135deg, #BA68C8 0%, #9C27B0 100%)',
                    'Sistemas' => 'linear-gradient(135deg, #FF7043 0%, #F4511E 100%)',
                    'Mercado' => 'linear-gradient(135deg, #FFA726 0%, #FB8C00 100%)'
                ];
                
                foreach ($categorias as $cat): 
                    $total = contarNoticiasPorCategoria($pdo, $cat['Idcategoria']);
                    $cor = $coresCategorias[$cat['nomecategoria']] ?? 'linear-gradient(135deg, #4FC3F7 0%, #3B82F6 100%)';
                ?>
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="card border-0 text-center h-100 rounded-4" style="background: <?= $cor ?>;">
                        <div class="card-body d-flex flex-column justify-content-center py-3 px-2">
                            <i class="bi <?= getIconeCategoria($cat['nomecategoria']) ?> fs-1 text-white mb-2"></i>
                            <h5 class="text-white mb-1"><?= htmlspecialchars($cat['nomecategoria']) ?></h5>
                            <p class="text-white mb-2 fw-bold small"><?= $total ?> <?= $total == 1 ? 'artigo' : 'artigos' ?></p>
                            <a href="index.php?categoria=<?= $cat['Idcategoria'] ?>" class="btn btn-light btn-sm mt-auto rounded-pill">
                                Explorar <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!-- FIM EXPLORE POR CATEGORIA -->

<!-- ========================================
     SITES RECOMENDADOS PELO LIBRENEWS
     ======================================== -->
<div class="container-fluid py-5 bg-darker">
    <div class="container py-5">
        <div class="text-center mb-5">
            <span class="badge bg-primary mb-3 px-4 py-2 rounded-pill">
                <i class="bi bi-bookmark-star-fill me-2"></i>RECOMENDAÇÃO LIBRENEWS
            </span>
            <h2 class="text-white mb-3">Sites Essenciais para Desenvolvedores</h2>
            <p class="text-secondary">Curadoria especial com os melhores recursos da web selecionados pelo LibreNews</p>
        </div>
        
        <div class="row g-4">
            <!-- Site 1: Stack Overflow -->
            <div class="col-lg-4 col-md-6">
                <a href="https://stackoverflow.com" target="_blank" class="text-decoration-none">
                    <div class="card border-0 h-100 rounded-4 bg-light">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-stack fs-3 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="text-white mb-1">Stack Overflow</h5>
                                    <span class="badge bg-primary rounded-pill small">Q&A</span>
                                </div>
                            </div>
                            <p class="text-secondary mb-0">A maior comunidade de perguntas e respostas para programadores do mundo</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Site 2: GitHub -->
            <div class="col-lg-4 col-md-6">
                <a href="https://github.com" target="_blank" class="text-decoration-none">
                    <div class="card border-0 h-100 rounded-4 bg-light">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-github fs-3 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="text-white mb-1">GitHub</h5>
                                    <span class="badge bg-primary rounded-pill small">Repositórios</span>
                                </div>
                            </div>
                            <p class="text-secondary mb-0">Hospede, revise código e gerencie projetos colaborativamente</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Site 3: MDN Web Docs -->
            <div class="col-lg-4 col-md-6">
                <a href="https://developer.mozilla.org" target="_blank" class="text-decoration-none">
                    <div class="card border-0 h-100 rounded-4 bg-light">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-book fs-3 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="text-white mb-1">MDN Web Docs</h5>
                                    <span class="badge bg-primary rounded-pill small">Documentação</span>
                                </div>
                            </div>
                            <p class="text-secondary mb-0">Documentação completa sobre tecnologias web: HTML, CSS e JavaScript</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Site 4: freeCodeCamp -->
            <div class="col-lg-4 col-md-6">
                <a href="https://www.freecodecamp.org" target="_blank" class="text-decoration-none">
                    <div class="card border-0 h-100 rounded-4 bg-light">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-mortarboard fs-3 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="text-white mb-1">freeCodeCamp</h5>
                                    <span class="badge bg-primary rounded-pill small">Cursos Gratuitos</span>
                                </div>
                            </div>
                            <p class="text-secondary mb-0">Aprenda programação gratuitamente com certificações reconhecidas</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Site 5: LeetCode -->
            <div class="col-lg-4 col-md-6">
                <a href="https://leetcode.com" target="_blank" class="text-decoration-none">
                    <div class="card border-0 h-100 rounded-4 bg-light">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-puzzle fs-3 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="text-white mb-1">LeetCode</h5>
                                    <span class="badge bg-primary rounded-pill small">Desafios</span>
                                </div>
                            </div>
                            <p class="text-secondary mb-0">Pratique algoritmos e estruturas de dados para entrevistas técnicas</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Site 6: DevDocs -->
            <div class="col-lg-4 col-md-6">
                <a href="https://devdocs.io" target="_blank" class="text-decoration-none">
                    <div class="card border-0 h-100 rounded-4 bg-light">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-file-earmark-code fs-3 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="text-white mb-1">DevDocs</h5>
                                    <span class="badge bg-primary rounded-pill small">API Reference</span>
                                </div>
                            </div>
                            <p class="text-secondary mb-0">Documentação unificada de APIs e frameworks em um só lugar</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- FIM SITES RECOMENDADOS -->

<?php include 'components/footer.php'; ?>

