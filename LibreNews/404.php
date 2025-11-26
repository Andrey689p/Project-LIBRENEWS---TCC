<?php 
$pageTitle = "Página não encontrada - LibreNews";
include 'components/head.php'; 
include 'components/navbar.php';
?>

<!-- ========================================
     PÁGINA 404 - NÃO ENCONTRADA
     ======================================== -->
<div class="container-fluid py-5" style="min-height: 85vh;">
    <div class="container py-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">

                <!-- Número 404 com Gradiente -->
                <h1 class="fw-bold mb-3"
                    style="
                        font-size: 8rem;
                        background: linear-gradient(135deg, #4FC3F7, #3B82F6);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                        line-height: 1;
                    ">
                    404
                </h1>

                <!-- Título -->
                <h2 class="mb-4 fw-bold">Página Não Encontrada</h2>

                <!-- Descrição -->
                <p class="text-secondary mb-5 fs-5">
                    Ops! A página que você está procurando não existe ou foi movida.<br>
                    Não se preocupe, temos muito conteúdo interessante esperando por você!
                </p>

                <!-- Caixa de Busca Simples -->
                <div class="mb-5">
                    <form class="d-flex justify-content-center gap-2" action="busca.php" method="GET">
                        <input 
                            type="text" 
                            name="q"
                            class="form-control form-control-lg rounded-pill"
                            placeholder="Buscar notícias..."
                            style="max-width: 500px; background-color: #1E293B; border-color: #4A5568; color: #E2E8F0;"
                        >
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Botões de Ação -->
                <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
                    <!-- Botão: Voltar para Home -->
                    <a href="index.php" class="btn btn-primary btn-lg rounded-pill px-5">
                        <i class="bi bi-house-door me-2"></i>Voltar para Home
                    </a>

                    <!-- Botão: Página Anterior -->
                    <button onclick="history.back()" class="btn btn-outline-primary btn-lg rounded-pill px-5">
                        <i class="bi bi-arrow-left me-2"></i>Página Anterior
                    </button>
                </div>

                <!-- Links Úteis -->
                <div class="mt-5">
                    <p class="text-secondary mb-3">Ou navegue por:</p>
                    <div class="d-flex justify-content-center gap-4 flex-wrap">
                        <a href="index.php" class="text-primary text-decoration-none">
                            <i class="bi bi-newspaper me-1"></i>Últimas Notícias
                        </a>
                        <a href="sobre.php" class="text-primary text-decoration-none">
                            <i class="bi bi-info-circle me-1"></i>Sobre Nós
                        </a>
                        <a href="equipe.php" class="text-primary text-decoration-none">
                            <i class="bi bi-people me-1"></i>Faça Parte
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- FIM PÁGINA 404 -->

<?php include 'components/footer.php'; ?>