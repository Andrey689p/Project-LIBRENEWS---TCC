<?php 
require_once 'config/database.php';
require_once 'includes/functions.php';

// Obter ID da notícia
$noticiaId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($noticiaId <= 0) {
    header('Location: index.php');
    exit();
}

// Buscar notícia do banco de dados
$stmt = $pdo->prepare("
    SELECT n.*, c.nomeusuario as autor, c.email as autor_email, cat.nomecategoria, cat.Idcategoria
    FROM Noticia n 
    LEFT JOIN Escritor e ON n.Idescritor = e.Idescritor 
    LEFT JOIN Conta c ON e.Idconta = c.Idconta 
    LEFT JOIN Categoria cat ON n.Idcategoria = cat.Idcategoria
    WHERE n.Idnoticia = ? AND n.status = 'publicada'
");
$stmt->execute([$noticiaId]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não encontrou a notícia, redirecionar
if (!$noticia) {
    header('Location: index.php');
    exit();
}

// Função para obter ícone da categoria
function getIconeCategoria($categoria) {
    $icones = [
        'Desenvolvimento' => 'bi-code-slash',
        'Programação' => 'bi-terminal',
        'Tecnologia' => 'bi-cpu',
        'Sistemas' => 'bi-hdd-network',
        'Mercado' => 'bi-graph-up-arrow',
        'Inteligência Artificial' => 'bi-robot',
        'Segurança' => 'bi-shield-check',
        'Mobile' => 'bi-phone',
        'Cloud' => 'bi-cloud',
        'DevOps' => 'bi-gear'
    ];
    return $icones[$categoria] ?? 'bi-newspaper';
}

// Função para obter imagem
function getImagemNoticia($noticia) {
    if (!empty($noticia['imagem'])) {
        return '/LibreNews/' . $noticia['imagem'];
    }
    if (!empty($noticia['imagemcapa'])) {
        return '/LibreNews/' . $noticia['imagemcapa'];
    }
    return '/LibreNews/assets/img/news-placeholder.jpg';
}

// Calcular tempo de leitura
function calcularTempoLeitura($conteudo) {
    $palavras = str_word_count(strip_tags($conteudo));
    $minutos = ceil($palavras / 200);
    return max(1, $minutos);
}

// Contar palavras
function contarPalavras($conteudo) {
    return str_word_count(strip_tags($conteudo));
}

// Buscar notícias relacionadas (mesma categoria)
$stmtRelacionadas = $pdo->prepare("
    SELECT n.*, c.nomeusuario as autor, cat.nomecategoria
    FROM Noticia n 
    LEFT JOIN Escritor e ON n.Idescritor = e.Idescritor 
    LEFT JOIN Conta c ON e.Idconta = c.Idconta 
    LEFT JOIN Categoria cat ON n.Idcategoria = cat.Idcategoria
    WHERE n.Idcategoria = ? AND n.Idnoticia != ? AND n.status = 'publicada'
    ORDER BY n.datapublicacao DESC
    LIMIT 4
");
$stmtRelacionadas->execute([$noticia['Idcategoria'], $noticiaId]);
$noticiasRelacionadas = $stmtRelacionadas->fetchAll(PDO::FETCH_ASSOC);

// Buscar notícia anterior e próxima
$stmtAnterior = $pdo->prepare("
    SELECT Idnoticia, titulo FROM Noticia 
    WHERE Idnoticia < ? AND status = 'publicada'
    ORDER BY Idnoticia DESC LIMIT 1
");
$stmtAnterior->execute([$noticiaId]);
$noticiaAnterior = $stmtAnterior->fetch(PDO::FETCH_ASSOC);

$stmtProxima = $pdo->prepare("
    SELECT Idnoticia, titulo FROM Noticia 
    WHERE Idnoticia > ? AND status = 'publicada'
    ORDER BY Idnoticia ASC LIMIT 1
");
$stmtProxima->execute([$noticiaId]);
$noticiaProxima = $stmtProxima->fetch(PDO::FETCH_ASSOC);

// Contar total de notícias do autor
$stmtTotalAutor = $pdo->prepare("
    SELECT COUNT(*) FROM Noticia n 
    JOIN Escritor e ON n.Idescritor = e.Idescritor 
    JOIN Conta c ON e.Idconta = c.Idconta 
    WHERE c.nomeusuario = ? AND n.status = 'publicada'
");
$stmtTotalAutor->execute([$noticia['autor']]);
$totalNoticiasAutor = $stmtTotalAutor->fetchColumn();

// URL atual para compartilhamento
$urlAtual = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$tituloEncoded = urlencode($noticia['titulo']);

$pageTitle = htmlspecialchars($noticia['titulo']) . " - LibreNews";
include 'components/head.php'; 
include 'components/navbar.php';
?>

<style>
/* Estilos específicos para a página de notícia */
.article-hero {
    background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
    padding: 80px 0 40px;
}

.article-content {
    font-size: 1.1rem;
    line-height: 1.9;
    color: #CBD5E1;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.share-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.share-btn:hover {
    transform: translateY(-3px);
}

.share-btn.facebook { background: #1877F2; color: white; }
.share-btn.twitter { background: #1DA1F2; color: white; }
.share-btn.linkedin { background: #0A66C2; color: white; }
.share-btn.whatsapp { background: #25D366; color: white; }
.share-btn.copy { background: #6B7280; color: white; }

.author-card {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.nav-article {
    background: rgba(30, 41, 59, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.nav-article:hover {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.related-card {
    transition: all 0.3s ease;
}

.related-card:hover {
    transform: translateY(-5px);
}

.category-badge {
    background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
}

.reading-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 4px;
    background: linear-gradient(90deg, #3B82F6, #8B5CF6);
    z-index: 9999;
    transition: width 0.1s ease;
}
</style>

<!-- Barra de progresso de leitura -->
<div class="reading-progress" id="readingProgress"></div>

<!-- Hero Section -->
<div class="article-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-secondary text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php?categoria=<?= $noticia['Idcategoria'] ?>" class="text-secondary text-decoration-none"><?= htmlspecialchars($noticia['nomecategoria']) ?></a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Artigo</li>
                    </ol>
                </nav>
                
                <!-- Badge de Categoria -->
                <div class="mb-4">
                    <a href="index.php?categoria=<?= $noticia['Idcategoria'] ?>" class="badge category-badge px-4 py-2 rounded-pill text-decoration-none text-white fs-6">
                        <i class="bi <?= getIconeCategoria($noticia['nomecategoria']) ?> me-2"></i><?= htmlspecialchars($noticia['nomecategoria']) ?>
                    </a>
                </div>

                <!-- Título -->
                <h1 class="display-4 fw-bold text-white mb-4 lh-sm">
                    <?= htmlspecialchars($noticia['titulo']) ?>
                </h1>

                <!-- Metadados -->
                <div class="d-flex flex-wrap gap-4 mb-4 text-secondary align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-person-fill text-white"></i>
                        </div>
                        <div>
                            <span class="text-white d-block"><?= htmlspecialchars($noticia['autor']) ?></span>
                            <small class="text-secondary"><?= $totalNoticiasAutor ?> artigo<?= $totalNoticiasAutor > 1 ? 's' : '' ?> publicado<?= $totalNoticiasAutor > 1 ? 's' : '' ?></small>
                        </div>
                    </div>
                    
                    <?php if (!empty($noticia['datapublicacao'])): ?>
                    <div>
                        <i class="bi bi-calendar3 me-2"></i>
                        <?= date('d \d\e F \d\e Y', strtotime($noticia['datapublicacao'])) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div>
                        <i class="bi bi-clock me-2"></i>
                        <?= calcularTempoLeitura($noticia['conteudo']) ?> min de leitura
                    </div>
                    
                    <div>
                        <i class="bi bi-file-text me-2"></i>
                        <?= number_format(contarPalavras($noticia['conteudo']), 0, ',', '.') ?> palavras
                    </div>
                </div>

                <!-- Botões de Compartilhamento -->
                <div class="d-flex gap-2 flex-wrap align-items-center mb-4">
                    <span class="text-secondary me-2"><i class="bi bi-share me-1"></i> Compartilhar:</span>
                    
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($urlAtual) ?>" 
                       target="_blank" class="share-btn facebook" title="Compartilhar no Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($urlAtual) ?>&text=<?= $tituloEncoded ?>" 
                       target="_blank" class="share-btn twitter" title="Compartilhar no Twitter">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($urlAtual) ?>&title=<?= $tituloEncoded ?>" 
                       target="_blank" class="share-btn linkedin" title="Compartilhar no LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    
                    <a href="https://wa.me/?text=<?= $tituloEncoded ?>%20<?= urlencode($urlAtual) ?>" 
                       target="_blank" class="share-btn whatsapp" title="Compartilhar no WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    
                    <button type="button" class="share-btn copy" onclick="copyToClipboard('<?= $urlAtual ?>')" title="Copiar link">
                        <i class="bi bi-link-45deg"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Conteúdo Principal -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            <!-- Imagem de Destaque -->
            <?php $imagem = getImagemNoticia($noticia); ?>
            <?php if ($imagem && $imagem !== '/LibreNews/assets/img/news-placeholder.jpg'): ?>
            <div class="mb-5">
                <img src="<?= htmlspecialchars($imagem) ?>" 
                     class="img-fluid w-100 rounded-4" 
                     alt="<?= htmlspecialchars($noticia['titulo']) ?>"
                     style="box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-height: 500px; object-fit: cover;">
            </div>
            <?php endif; ?>

            <!-- Conteúdo do Artigo -->
            <article class="article-content mb-5">
                <?= nl2br(htmlspecialchars($noticia['conteudo'], ENT_QUOTES, 'UTF-8')) ?>
            </article>

            <!-- Tags/Categoria -->
            <div class="mb-5 pb-5 border-bottom border-secondary">
                <span class="text-secondary me-2"><i class="bi bi-tags me-1"></i> Categoria:</span>
                <a href="index.php?categoria=<?= $noticia['Idcategoria'] ?>" class="badge bg-primary text-decoration-none">
                    <?= htmlspecialchars($noticia['nomecategoria']) ?>
                </a>
            </div>

            <!-- Card do Autor -->
            <div class="author-card rounded-4 p-4 mb-5">
                <div class="d-flex gap-4 align-items-start">
                    <div class="rounded-circle bg-gradient d-flex align-items-center justify-content-center flex-shrink-0" 
                         style="width: 80px; height: 80px; background: linear-gradient(135deg, #3B82F6, #8B5CF6);">
                        <i class="bi bi-person-fill text-white fs-2"></i>
                    </div>
                    <div>
                        <h5 class="text-white mb-1">Escrito por <?= htmlspecialchars($noticia['autor']) ?></h5>
                        <p class="text-secondary mb-2">
                            <i class="bi bi-newspaper me-1"></i> <?= $totalNoticiasAutor ?> artigo<?= $totalNoticiasAutor > 1 ? 's' : '' ?> publicado<?= $totalNoticiasAutor > 1 ? 's' : '' ?> no LibreNews
                        </p>
                        <p class="text-secondary mb-0 small">
                            Contribuidor do LibreNews, compartilhando conhecimento e novidades sobre <?= htmlspecialchars($noticia['nomecategoria']) ?> e tecnologia.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navegação entre artigos -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <?php if ($noticiaAnterior): ?>
                    <a href="noticia.php?id=<?= $noticiaAnterior['Idnoticia'] ?>" class="nav-article d-block p-4 rounded-4 text-decoration-none h-100">
                        <div class="text-secondary small mb-2">
                            <i class="bi bi-arrow-left me-1"></i> Artigo Anterior
                        </div>
                        <h6 class="text-white mb-0"><?= htmlspecialchars(limitarTexto($noticiaAnterior['titulo'], 60)) ?></h6>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-md-end">
                    <?php if ($noticiaProxima): ?>
                    <a href="noticia.php?id=<?= $noticiaProxima['Idnoticia'] ?>" class="nav-article d-block p-4 rounded-4 text-decoration-none h-100">
                        <div class="text-secondary small mb-2">
                            Próximo Artigo <i class="bi bi-arrow-right ms-1"></i>
                        </div>
                        <h6 class="text-white mb-0"><?= htmlspecialchars(limitarTexto($noticiaProxima['titulo'], 60)) ?></h6>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Artigos Relacionados -->
            <?php if (!empty($noticiasRelacionadas)): ?>
            <div class="mt-5 pt-5 border-top border-secondary">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-white fw-bold mb-0">
                        <i class="bi bi-collection text-primary me-2"></i>Mais em <?= htmlspecialchars($noticia['nomecategoria']) ?>
                    </h3>
                    <a href="index.php?categoria=<?= $noticia['Idcategoria'] ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                        Ver todos <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                
                <div class="row g-4">
                    <?php foreach ($noticiasRelacionadas as $relacionada): ?>
                    <div class="col-md-6">
                        <a href="noticia.php?id=<?= $relacionada['Idnoticia'] ?>" class="text-decoration-none">
                            <div class="related-card card h-100 border-0 rounded-4" style="background: rgba(30, 41, 59, 0.8);">
                                <div class="position-relative overflow-hidden rounded-top-4">
                                    <img src="<?= getImagemNoticia($relacionada) ?>" 
                                         class="card-img-top" 
                                         style="height: 180px; object-fit: cover;"
                                         alt="<?= htmlspecialchars($relacionada['titulo']) ?>">
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge bg-primary rounded-pill">
                                            <i class="bi <?= getIconeCategoria($relacionada['nomecategoria']) ?> me-1"></i>
                                            <?= htmlspecialchars($relacionada['nomecategoria']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="text-white mb-3"><?= htmlspecialchars($relacionada['titulo']) ?></h5>
                                    <p class="text-secondary small mb-3">
                                        <?= limitarTexto(strip_tags($relacionada['conteudo']), 100) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                                        <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($relacionada['autor']) ?></span>
                                        <span><i class="bi bi-clock me-1"></i><?= calcularTempoLeitura($relacionada['conteudo']) ?> min</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
// Barra de progresso de leitura
window.addEventListener('scroll', function() {
    const article = document.querySelector('.article-content');
    if (article) {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = (scrollTop / docHeight) * 100;
        document.getElementById('readingProgress').style.width = Math.min(progress, 100) + '%';
    }
});

// Copiar link para clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link copiado para a área de transferência!');
    }).catch(function(err) {
        // Fallback para navegadores mais antigos
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Link copiado para a área de transferência!');
    });
}
</script>

<?php include 'components/footer.php'; ?>
