<?php
// Buscar últimas notícias para o rodapé (se conexão existir)
$ultimasNoticiasFooter = [];
$categoriasFooter = [];

if (isset($pdo)) {
    try {
        // Buscar 3 últimas notícias publicadas
        $stmtUltimas = $pdo->query("
            SELECT n.Idnoticia, n.titulo, n.datapublicacao, cat.nomecategoria
            FROM Noticia n 
            LEFT JOIN Categoria cat ON n.Idcategoria = cat.Idcategoria
            WHERE n.status = 'publicada'
            ORDER BY n.datapublicacao DESC
            LIMIT 3
        ");
        $ultimasNoticiasFooter = $stmtUltimas->fetchAll(PDO::FETCH_ASSOC);
        
        // Buscar categorias com contagem
        $stmtCats = $pdo->query("
            SELECT c.Idcategoria, c.nomecategoria, COUNT(n.Idnoticia) as total
            FROM Categoria c
            LEFT JOIN Noticia n ON c.Idcategoria = n.Idcategoria AND n.status = 'publicada'
            GROUP BY c.Idcategoria, c.nomecategoria
            ORDER BY c.nomecategoria ASC
            LIMIT 5
        ");
        $categoriasFooter = $stmtCats->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Silenciar erros - usar dados estáticos se falhar
    }
}

// Função para obter ícone da categoria
function getIconeCategoriaFooter($categoria) {
    $icones = [
        'Desenvolvimento' => 'bi-code-slash',
        'Programação' => 'bi-terminal',
        'Tecnologia' => 'bi-cpu',
        'Sistemas' => 'bi-hdd-network',
        'Mercado' => 'bi-graph-up-arrow',
        'Inteligência Artificial' => 'bi-robot',
        'Segurança' => 'bi-shield-check'
    ];
    return $icones[$categoria] ?? 'bi-tag';
}

// Cores para os ícones de notícias
$coresNoticias = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
?>

<!-- ========================================
     FOOTER - RODAPÉ DO SITE
     ======================================== -->
<div class="container-fluid bg-dark footer py-5">
    <div class="container py-5">
        
        <!-- Logo e Descrição Centralizada -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <a href="/LibreNews/index.php" class="navbar-brand brand-logo-modern d-inline-flex mb-4">
                    <div class="logo-icon-modern rounded-circle">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <div class="brand-text-modern">
                        <span class="brand-name-modern">LibreNews</span>
                    </div>
                </a>
                <p class="text-secondary mb-0 mt-3">Seu portal de notícias sobre desenvolvimento e tecnologia</p>
            </div>
        </div>

        <!-- Colunas de Conteúdo -->
        <div class="row g-5 justify-content-center" style="row-gap: 4rem;">
            
            <!-- Coluna 1: Categorias -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-item">
                    <h5 class="mb-4 text-white">
                        <i class="bi bi-grid-3x3-gap text-primary me-2"></i>Categorias
                    </h5>
                    <div class="d-flex flex-column gap-3">
                        <?php if (!empty($categoriasFooter)): ?>
                            <?php foreach ($categoriasFooter as $cat): ?>
                            <a class="text-secondary text-decoration-none d-flex align-items-start" 
                               href="/LibreNews/index.php?categoria=<?= $cat['Idcategoria'] ?>">
                                <i class="bi <?= getIconeCategoriaFooter($cat['nomecategoria']) ?> text-primary me-3 fs-4 mt-1"></i>
                                <div>
                                    <div><?= htmlspecialchars($cat['nomecategoria']) ?></div>
                                    <small style="color: rgba(255,255,255,0.6);"><?= $cat['total'] ?> artigo<?= $cat['total'] != 1 ? 's' : '' ?></small>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback estático -->
                            <a class="text-secondary text-decoration-none d-flex align-items-start" href="/LibreNews/index.php">
                                <i class="bi bi-code-slash text-primary me-3 fs-4 mt-1"></i>
                                <div>
                                    <div>Desenvolvimento</div>
                                    <small style="color: rgba(255,255,255,0.6);">Web, Mobile e mais</small>
                                </div>
                            </a>
                            <a class="text-secondary text-decoration-none d-flex align-items-start" href="/LibreNews/index.php">
                                <i class="bi bi-terminal text-primary me-3 fs-4 mt-1"></i>
                                <div>
                                    <div>Programação</div>
                                    <small style="color: rgba(255,255,255,0.6);">Linguagens e frameworks</small>
                                </div>
                            </a>
                            <a class="text-secondary text-decoration-none d-flex align-items-start" href="/LibreNews/index.php">
                                <i class="bi bi-cpu text-primary me-3 fs-4 mt-1"></i>
                                <div>
                                    <div>Tecnologia</div>
                                    <small style="color: rgba(255,255,255,0.6);">Hardware e inovação</small>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Coluna 2: Últimas Notícias -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-item">
                    <h5 class="mb-4 text-white">
                        <i class="bi bi-clock-history text-primary me-2"></i>Últimas Notícias
                    </h5>
                    
                    <?php if (!empty($ultimasNoticiasFooter)): ?>
                        <?php foreach ($ultimasNoticiasFooter as $index => $noticia): ?>
                        <div class="d-flex <?= $index < count($ultimasNoticiasFooter) - 1 ? 'mb-4 pb-4 border-bottom border-white border-opacity-10' : '' ?> gap-3">
                            <div class="<?= $coresNoticias[$index % count($coresNoticias)] ?> rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 68px; height: 68px;">
                                <i class="bi <?= getIconeCategoriaFooter($noticia['nomecategoria'] ?? '') ?> text-white fs-3"></i>
                            </div>
                            <div>
                                <a href="/LibreNews/noticia.php?id=<?= $noticia['Idnoticia'] ?>" class="text-white text-decoration-none d-block mb-1">
                                    <h6 class="mb-0"><?= htmlspecialchars(mb_strimwidth($noticia['titulo'], 0, 45, '...')) ?></h6>
                                </a>
                                <small class="text-secondary">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?php if (!empty($noticia['datapublicacao'])): ?>
                                        <?= date('d M, Y', strtotime($noticia['datapublicacao'])) ?>
                                    <?php else: ?>
                                        Recente
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback quando não há notícias -->
                        <div class="text-center py-4">
                            <i class="bi bi-newspaper text-secondary fs-1 mb-3 d-block"></i>
                            <p class="text-secondary mb-0">Nenhuma notícia publicada ainda.</p>
                            <a href="/LibreNews/equipe.php" class="text-primary">Seja um escritor!</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Coluna 3: Links Úteis -->
            <div class="col-lg-3 col-md-6 offset-lg-1">
                <div class="footer-item">
                    <h5 class="mb-4 text-white">
                        <i class="bi bi-link-45deg text-primary me-2"></i>Links Úteis
                    </h5>
                    <div class="d-flex flex-column gap-3">
                        <!-- Link: Sobre Nós -->
                        <a class="text-secondary text-decoration-none d-flex align-items-center" href="/LibreNews/sobre.php">
                            <i class="bi bi-info-circle text-primary me-3 fs-5"></i>
                            <span>Sobre Nós</span>
                        </a>
                        
                        <!-- Link: Faça parte da Equipe -->
                        <a class="text-secondary text-decoration-none d-flex align-items-center" href="/LibreNews/equipe.php">
                            <i class="bi bi-people text-primary me-3 fs-5"></i>
                            <span>Faça parte da Equipe</span>
                        </a>
                        
                        <!-- Link: Buscar Notícias -->
                        <a class="text-secondary text-decoration-none d-flex align-items-center" href="/LibreNews/busca.php">
                            <i class="bi bi-search text-primary me-3 fs-5"></i>
                            <span>Buscar Notícias</span>
                        </a>
                        
                        <!-- Link: Política de Privacidade -->
                        <a class="text-secondary text-decoration-none d-flex align-items-center" href="/LibreNews/privacidade.php#privacidade">
                            <i class="bi bi-shield-check text-primary me-3 fs-5"></i>
                            <span>Política de Privacidade</span>
                        </a>
                        
                        <!-- Link: Termos de Uso -->
                        <a class="text-secondary text-decoration-none d-flex align-items-center" href="/LibreNews/privacidade.php#termos">
                            <i class="bi bi-file-text text-primary me-3 fs-5"></i>
                            <span>Termos de Uso</span>
                        </a>
                        
                        <!-- Link: Etec -->
                        <a class="text-secondary text-decoration-none d-flex align-items-center" href="https://etecdans.cps.sp.gov.br/" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-building text-primary me-3 fs-5"></i>
                            <span>Etec</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Redes Sociais (Opcional) -->
        <div class="row mt-5 pt-4 border-top border-white border-opacity-10">
            <div class="col-12 text-center mb-4">
                <h6 class="text-white mb-3">Siga-nos</h6>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-outline-info rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="#" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    <a href="#" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-github"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="row mt-4 pt-4 border-top border-white border-opacity-10">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="text-secondary mb-0">
                    <i class="bi bi-c-circle me-1"></i><?= date('Y') ?> LibreNews. Todos os direitos reservados.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="text-secondary mb-0">
                    Desenvolvido com <i class="bi bi-heart-fill text-danger"></i> para a comunidade tech
                </p>
            </div>
        </div>
    </div>
</div>
<!-- FIM FOOTER -->

<!-- ========================================
     BOTÃO VOLTAR AO TOPO
     ======================================== -->
<a href="#" class="back-to-top" aria-label="Voltar ao topo">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="back-to-top-icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 19V5M5 12l7-7 7 7" />
    </svg>
</a>

<!-- ========================================
     SCRIPTS - BIBLIOTECAS JAVASCRIPT
     ======================================== -->
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Principal -->
<script src="/LibreNews/assets/js/main.js"></script>

</body>
</html>
