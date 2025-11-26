<?php 
$pageTitle = "Sobre - LibreNews";
include 'components/head.php'; 
include 'components/navbar.php';
?>

<!-- ========================================
     HERO SECTION - APRESENTAÇÃO
     ======================================== -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #020617 100%);">
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="logo-icon-modern mx-auto mb-4 rounded-circle" style="width: 80px; height: 80px; font-size: 40px;">
                    <i class="fa fa-newspaper"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3 text-gradient">
                    Sobre o LibreNews
                </h1>
                <p class="lead text-secondary mb-4">
                    Portal de notícias especializado em Tecnologia da Informação, desenvolvido para democratizar o conhecimento técnico e promover perspectivas no cenário tecnológico.
                </p>
            </div>
        </div>
    </div>
</div>
<!-- FIM HERO SECTION -->

<!-- ========================================
     NOSSA MISSÃO
     ======================================== -->
<div class="container py-5">
    <div class="row g-5 align-items-center">
        <!-- Imagem da Missão -->
        <div class="col-lg-6">
            <div class="position-relative">
                <img src="img/tech-mission.jpg" class="img-fluid rounded-4" alt="Missão LibreNews" style="box-shadow: 0 10px 40px rgba(79, 195, 247, 0.2);">
                <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4" style="background: linear-gradient(135deg, rgba(79, 195, 247, 0.1), rgba(59, 130, 246, 0.1));"></div>
            </div>
        </div>
        
        <!-- Texto da Missão -->
        <div class="col-lg-6">
            <div class="badge bg-primary text-dark px-3 py-2 rounded-pill mb-3">
                <i class="fa fa-bullseye me-2"></i>Nossa Missão
            </div>
            <h2 class="mb-4">Democratizando o Conhecimento Tecnológico</h2>
            <p class="text-secondary mb-3">
                O LibreNews foi idealizado com o intuito de desenvolver um portal de notícias que atenda especificamente às necessidades das comunidades da área de Tecnologia da Informação.
            </p>
            <p class="text-secondary mb-4">
                Nossa proposta é proporcionar aos profissionais do setor um ambiente digital focado em informações técnicas atualizadas, priorizando a qualidade editorial e promovendo múltiplas perspectivas no cenário tecnológico.
            </p>
            
            <!-- Destaques da Missão -->
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa fa-check fa-lg"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Qualidade Editorial</h6>
                            <small class="text-secondary">Conteúdo verificado</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa fa-users fa-lg"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Comunidade Ativa</h6>
                            <small class="text-secondary">Profissionais de TI</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM NOSSA MISSÃO -->

<!-- ========================================
     NOSSOS VALORES
     ======================================== -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <div class="badge bg-primary text-dark px-3 py-2 rounded-pill mb-3">
                <i class="fa fa-heart me-2"></i>Nossos Valores
            </div>
            <h2 class="mb-3">O Que Nos Move</h2>
            <p class="text-secondary">Princípios fundamentais que guiam o LibreNews</p>
        </div>
        
        <div class="row g-4">
            <!-- Valor 1: Transparência -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm no-animation rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="logo-icon-modern rounded-circle" style="width: 60px; height: 60px; font-size: 28px;">
                                <i class="fa fa-shield-alt"></i>
                            </div>
                            <h5 class="ms-3 mb-0">Transparência</h5>
                        </div>
                        <p class="text-secondary mb-0">
                            Respeitamos rigorosamente os princípios de transparência, veracidade e ética jornalística em todas as nossas publicações.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Valor 2: Liberdade -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm no-animation rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="logo-icon-modern rounded-circle" style="width: 60px; height: 60px; font-size: 28px;">
                                <i class="fa fa-unlock"></i>
                            </div>
                            <h5 class="ms-3 mb-0">Liberdade</h5>
                        </div>
                        <p class="text-secondary mb-0">
                            Estabelecemos um espaço onde escritores especializados publicam conteúdos com liberdade editorial, respeitando os valores éticos.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Valor 3: Conhecimento -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm no-animation rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="logo-icon-modern rounded-circle" style="width: 60px; height: 60px; font-size: 28px;">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                            <h5 class="ms-3 mb-0">Conhecimento</h5>
                        </div>
                        <p class="text-secondary mb-0">
                            Democratizamos o acesso ao conhecimento técnico especializado, ampliando oportunidades de crescimento profissional.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Valor 4: Diversidade -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm no-animation rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="logo-icon-modern rounded-circle" style="width: 60px; height: 60px; font-size: 28px;">
                                <i class="fa fa-comments"></i>
                            </div>
                            <h5 class="ms-3 mb-0">Diversidade</h5>
                        </div>
                        <p class="text-secondary mb-0">
                            Valorizamos múltiplas perspectivas e promovemos a diversidade de vozes no cenário tecnológico contemporâneo.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Valor 5: Inovação -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm no-animation rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="logo-icon-modern rounded-circle" style="width: 60px; height: 60px; font-size: 28px;">
                                <i class="fa fa-rocket"></i>
                            </div>
                            <h5 class="ms-3 mb-0">Inovação</h5>
                        </div>
                        <p class="text-secondary mb-0">
                            Utilizamos tecnologias modernas para criar experiências inovadoras e acompanhar o ritmo acelerado da evolução tecnológica.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Valor 6: Comunidade -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm no-animation rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="logo-icon-modern rounded-circle" style="width: 60px; height: 60px; font-size: 28px;">
                                <i class="fa fa-handshake"></i>
                            </div>
                            <h5 class="ms-3 mb-0">Comunidade</h5>
                        </div>
                        <p class="text-secondary mb-0">
                            Fortalecemos os vínculos profissionais e criamos um espaço de troca qualificada de conhecimentos técnicos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM NOSSOS VALORES -->

<!-- ========================================
     CONTEXTO E PROPÓSITO
     ======================================== -->
<div class="container py-5">
    <div class="row g-4">
        <!-- Por Que Existe -->
        <div class="col-lg-6">
            <div class="card border-0 bg-light h-100 no-animation rounded-4">
                <div class="card-body p-4">
                    <h4 class="mb-4"><i class="fa fa-lightbulb text-primary me-2"></i>Por Que o LibreNews Existe?</h4>
                    <p class="text-secondary mb-3">
                        O avanço tecnológico nas últimas décadas tem provocado transformações estruturais profundas, impactando diretamente os paradigmas sociais, econômicos e organizacionais em escala global. O setor de Tecnologia da Informação desempenha um papel estratégico nesse contexto de mudanças.
                    </p>
                    <p class="text-secondary mb-3">
                        Entretanto, apesar da proliferação de plataformas digitais de comunicação, a comunidade de TI ainda enfrenta obstáculos significativos na efetivação de processos de troca de informações, experiências técnicas e conhecimentos especializados.
                    </p>
                    <p class="text-secondary mb-0">
                        Observa-se uma carência de espaços virtuais que promovam simultaneamente a interatividade entre especialistas, a liberdade de expressão editorial e a disseminação eficaz de conteúdo técnico de alta qualidade.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Nossa Proposta -->
        <div class="col-lg-6">
            <div class="card border-0 bg-light h-100 no-animation rounded-4">
                <div class="card-body p-4">
                    <h4 class="mb-4"><i class="fa fa-flag text-primary me-2"></i>Nossa Proposta</h4>
                    <p class="text-secondary mb-3">
                        O LibreNews surge estrategicamente para preencher essa lacuna identificada no ecossistema digital, posicionando-se como um portal de notícias especializado e dedicado exclusivamente à comunidade de profissionais de TI.
                    </p>
                    <p class="text-secondary mb-3">
                        Diferentemente de portais generalistas ou plataformas comerciais, adotamos uma abordagem editorial rigorosa, permitindo que redatores especializados e previamente selecionados publiquem notícias e análises sobre inovações da área de TI.
                    </p>
                    <p class="text-secondary mb-0">
                        A arquitetura informacional do LibreNews integra funcionalidades de curadoria de conteúdo, sistemas de feedback e mecanismos de verificação de informações, criando um ecossistema virtual que prioriza a confiabilidade e a relevância das publicações.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM CONTEXTO E PROPÓSITO -->

<!-- ========================================
     O QUE OFERECEMOS
     ======================================== -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="mb-3">O Que Oferecemos</h3>
            <p class="text-secondary">Funcionalidades e diferenciais do portal</p>
        </div>
        
        <div class="row g-4">
            <!-- Funcionalidade 1: Curadoria Editorial -->
            <div class="col-md-6">
                <div class="card border-0 h-100 no-animation rounded-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="fa fa-newspaper text-primary me-2"></i>Curadoria Editorial</h5>
                        <p class="text-secondary mb-0">
                            Sistema de seleção criteriosa de redatores especializados que garante qualidade técnica e credibilidade das publicações, estabelecendo padrões elevados de jornalismo tecnológico.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Funcionalidade 2: Conteúdo Especializado -->
            <div class="col-md-6">
                <div class="card border-0 h-100 no-animation rounded-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="fa fa-filter text-primary me-2"></i>Conteúdo Especializado</h5>
                        <p class="text-secondary mb-0">
                            Portal focado exclusivamente em Tecnologia da Informação, oferecendo conteúdo altamente segmentado e relevante para profissionais de desenvolvimento de software e engenheiros de sistemas.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Funcionalidade 3: Verificação de Informações -->
            <div class="col-md-6">
                <div class="card border-0 h-100 no-animation rounded-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="fa fa-check-circle text-primary me-2"></i>Verificação de Informações</h5>
                        <p class="text-secondary mb-0">
                            Mecanismos de verificação e validação que asseguram a veracidade das publicações, combatendo a disseminação de informações incorretas ou enganosas.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Funcionalidade 4: Comunidade Ativa -->
            <div class="col-md-6">
                <div class="card border-0 h-100 no-animation rounded-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="fa fa-users text-primary me-2"></i>Comunidade Ativa</h5>
                        <p class="text-secondary mb-0">
                            Espaço para a troca de conhecimento entre profissionais de TI, promovendo o fortalecimento da comunidade tecnológica no geral.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM O QUE OFERECEMOS -->

<!-- ========================================
     EQUIPE DESENVOLVEDORA
     ======================================== -->
<div class="container py-5">
    <div class="text-center mb-5">
        <p class="text-dark mb-2">Desenvolvido por</p>
        <h5 class="fw-bold text-dark mb-0">Estudantes de Desenvolvimento de Sistemas - ETEC Taquaritinga</h5>
    </div>
    
    <div class="row g-4 justify-content-center">
        <!-- Desenvolvedor 1 -->
        <div class="col-md-3 col-sm-6">
            <div class="text-center">
                <p class="mb-0 text-secondary fw-medium">Daniel Sydinei Sampaio Garcia</p>
            </div>
        </div>
        
        <!-- Desenvolvedor 2 -->
        <div class="col-md-3 col-sm-6">
            <div class="text-center">
                <p class="mb-0 text-secondary fw-medium">Estevão Gabriel Feitosa Brito</p>
            </div>
        </div>
        
        <!-- Desenvolvedor 3 -->
        <div class="col-md-3 col-sm-6">
            <div class="text-center">
                <p class="mb-0 text-secondary fw-medium">Gabriel Lisboa de Almeida</p>
            </div>
        </div>
        
        <!-- Desenvolvedor 4 -->
        <div class="col-md-3 col-sm-6">
            <div class="text-center">
                <p class="mb-0 text-secondary fw-medium">Giovanna da Silva Barbosa</p>
            </div>
        </div>
    </div>
</div>
<!-- FIM EQUIPE DESENVOLVEDORA -->

<!-- ========================================
     CALL TO ACTION - FAÇA PARTE
     ======================================== -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #020617 100%);">
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-4 text-white">Faça Parte da Comunidade LibreNews</h2>
                <p class="text-secondary mb-5">
                    Fique no topo das tendências com a comunidade que escolheu o LibreNews
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="equipe.php" class="btn btn-primary btn-lg rounded-pill px-5">
                        <i class="fa fa-users me-2"></i>Entre na Equipe
                    </a>
                    <a href="index.php" class="btn btn-outline-light btn-lg rounded-pill px-5">
                        <i class="fa fa-home me-2"></i>Voltar ao Início
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM CALL TO ACTION -->

<style>
/* Gradiente do título sem warning no VSCode */
.text-gradient {
    background: linear-gradient(135deg, var(--bs-primary), #ffffff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Remove 100% das animações e sombras dos cards */
.card,
.card.no-animation,
.card.no-animation:hover,
.card:hover {
    transition: none !important;
    transform: none !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
}

/* Garante que nenhum card faça nada no hover */
.card {
    transition: none !important;
}

.card:hover {
    transform: none !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
}

.badge {
    font-weight: 600;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }
    
    .lead {
        font-size: 1rem;
    }
}
</style>

<?php include 'components/footer.php'; ?>