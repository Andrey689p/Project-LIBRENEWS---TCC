<?php 
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Equipe - LibreNews";
$mensagem = '';
$erro = '';

// Processar formulário de candidatura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coletar dados do formulário
    $nome = sanitize($_POST['nome'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $categoria = sanitize($_POST['categoria'] ?? '');
    $area_atuacao = sanitize($_POST['area_atuacao'] ?? '');
    $tecnologias = isset($_POST['tecnologias']) ? implode(', ', $_POST['tecnologias']) : '';
    $tecnologias_outras = sanitize($_POST['tecnologias_outras'] ?? '');
    $github = sanitize($_POST['github'] ?? '');
    $linkedin = sanitize($_POST['linkedin'] ?? '');
    $portfolio = sanitize($_POST['portfolio'] ?? '');
    $instagram = sanitize($_POST['instagram'] ?? '');
    $experiencia_escrita = sanitize($_POST['experiencia_escrita'] ?? '');
    $artigos_anteriores = sanitize($_POST['artigos_anteriores'] ?? '');
    $motivacao = sanitize($_POST['motivacao'] ?? '');
    $proposta_artigo = sanitize($_POST['proposta_artigo'] ?? '');
    
    // Validar campos obrigatórios
    if (empty($nome) || empty($email) || empty($categoria) || empty($area_atuacao) || 
        empty($experiencia_escrita) || empty($motivacao) || empty($proposta_artigo)) {
        $erro = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Por favor, insira um email válido.';
    } else {
        // Verificar se o email já foi cadastrado
        $stmtCheck = $pdo->prepare("SELECT Idcandidato FROM Candidato WHERE email = ?");
        $stmtCheck->execute([$email]);
        
        if ($stmtCheck->fetch()) {
            $erro = 'Este email já possui uma candidatura registrada. Aguarde o resultado da análise.';
        } else {
            // Montar experiência completa
            $experiencia = "Categoria: $categoria\n";
            $experiencia .= "Área de Interesse: $area_atuacao\n";
            $experiencia .= "Tecnologias: $tecnologias" . ($tecnologias_outras ? ", $tecnologias_outras" : "") . "\n";
            $experiencia .= "Experiência com escrita: $experiencia_escrita\n";
            if ($artigos_anteriores) {
                $experiencia .= "Artigos anteriores:\n$artigos_anteriores\n";
            }
            $experiencia .= "\nMotivação:\n$motivacao\n";
            $experiencia .= "\nProposta de primeiro artigo:\n$proposta_artigo";
            
            // Links
            $portfolioCompleto = '';
            if ($github) $portfolioCompleto .= "GitHub: $github\n";
            if ($linkedin) $portfolioCompleto .= "LinkedIn: $linkedin\n";
            if ($portfolio) $portfolioCompleto .= "Portfolio: $portfolio\n";
            if ($instagram) $portfolioCompleto .= "Instagram: $instagram";
            
            try {
                // Inserir candidatura no banco
                $stmt = $pdo->prepare("
                    INSERT INTO Candidato (nome, email, telefone, experiencia, portfolio, status, datacandidatura) 
                    VALUES (?, ?, ?, ?, ?, 'pendente', NOW())
                ");
                $stmt->execute([$nome, $email, $categoria, $experiencia, $portfolioCompleto]);
                
                $mensagem = 'Candidatura enviada com sucesso! Você receberá uma resposta em até 48 horas no email informado.';
                
                // Limpar dados do formulário
                $_POST = [];
            } catch (PDOException $e) {
                $erro = 'Erro ao enviar candidatura. Por favor, tente novamente. Erro: ' . $e->getMessage();
            }
        }
    }
}

include 'components/head.php'; 
include 'components/navbar.php';
?>

<!-- ========================================
     PÁGINA DE CANDIDATURA - FAÇA PARTE DA EQUIPE
     ======================================== -->
<div class="container-fluid py-5">
    <div class="container py-5">

        <!-- ========================================
             HERO SECTION - APRESENTAÇÃO
             ======================================== -->
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="badge px-4 py-2 rounded-pill mb-3" style="background-color: #81C784;">
                    <i class="bi bi-star-fill me-2"></i>Faça Parte da Equipe
                </span>
                <h1 class="display-5 fw-bold mb-3">
                    Torne-se um Escritor <span class="text-primary">LibreNews</span>
                </h1>
                <p class="text-secondary fs-5 mb-0">
                    Compartilhe conhecimento técnico com profissionais de TI e contribua para uma comunidade especializada em tecnologia e desenvolvimento.
                </p>
            </div>
        </div>
        <!-- FIM HERO SECTION -->

        <!-- Mensagens de sucesso/erro -->
        <?php if ($mensagem): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Sucesso!</strong> <?= htmlspecialchars($mensagem) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Erro!</strong> <?= htmlspecialchars($erro) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ========================================
             BENEFÍCIOS DE SER UM ESCRITOR
             ======================================== -->
        <div class="row g-4 justify-content-center mb-5">
            <!-- Benefício 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 text-center p-3 rounded-4 shadow-sm h-100" style="background-color: #2D3748;">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-pencil-square text-primary fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Publique Conteúdo Técnico</h5>
                        <p class="text-secondary small mb-0">Escreva sobre temas que você domina e compartilhe sua expertise</p>
                    </div>
                </div>
            </div>
            
            <!-- Benefício 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 text-center p-3 rounded-4 shadow-sm h-100" style="background-color: #2D3748;">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-graph-up-arrow text-primary fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Desenvolva Habilidades</h5>
                        <p class="text-secondary small mb-0">Aprimore sua escrita técnica e comunicação</p>
                    </div>
                </div>
            </div>
            
            <!-- Benefício 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 text-center p-3 rounded-4 shadow-sm h-100" style="background-color: #2D3748;">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-people-fill text-primary fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Conecte-se com Profissionais</h5>
                        <p class="text-secondary small mb-0">Faça parte da comunidade tech</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM BENEFÍCIOS -->

        <!-- ========================================
             FORMULÁRIO DE CANDIDATURA
             ======================================== -->
        <?php if (!$mensagem): ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4">
                    <!-- Cabeçalho do Formulário -->
                    <div class="card-header text-white text-center py-4 border-0 rounded-top-4" style="background-color: #2549abff !important;">
                        <h2 class="h3 fw-bold mb-1">Candidate-se Agora</h2>
                        <p class="mb-0 small">Preencha o formulário e aguarde a análise em até 48 horas</p>
                    </div>

                    <div class="card-body p-4">
                        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" id="formCandidatura">

                            <!-- Seção 1: Informações Pessoais -->
                            <div class="mb-5 pb-4">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom border-primary">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-2">
                                        <i class="bi bi-person-circle text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Informações Pessoais</h5>
                                        <small class="text-secondary">Seus dados básicos</small>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-person me-1"></i>Nome Completo *
                                        </label>
                                        <input type="text" class="form-control rounded-pill" name="nome" placeholder="Digite seu nome completo" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-envelope me-1"></i>E-mail *
                                        </label>
                                        <input type="email" class="form-control rounded-pill" name="email" placeholder="seu@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 2: Perfil Profissional -->
                            <div class="mb-5 pb-4">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom border-primary">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-2">
                                        <i class="bi bi-briefcase-fill text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Perfil Profissional</h5>
                                        <small class="text-secondary">Sua atuação e interesses</small>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-tag me-1"></i>Categoria *
                                        </label>
                                        <select class="form-select rounded-pill" name="categoria" required style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                            <option value="">Selecione...</option>
                                            <option value="estudante" <?= ($_POST['categoria'] ?? '') === 'estudante' ? 'selected' : '' ?>>Estudante</option>
                                            <option value="professor" <?= ($_POST['categoria'] ?? '') === 'professor' ? 'selected' : '' ?>>Professor/Educador</option>
                                            <option value="profissional" <?= ($_POST['categoria'] ?? '') === 'profissional' ? 'selected' : '' ?>>Profissional da Área</option>
                                            <option value="pesquisador" <?= ($_POST['categoria'] ?? '') === 'pesquisador' ? 'selected' : '' ?>>Pesquisador</option>
                                            <option value="freelancer" <?= ($_POST['categoria'] ?? '') === 'freelancer' ? 'selected' : '' ?>>Freelancer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-lightbulb me-1"></i>Área de Interesse *
                                        </label>
                                        <select class="form-select rounded-pill" name="area_atuacao" required style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                            <option value="">Selecione...</option>
                                            <option value="desenvolvimento" <?= ($_POST['area_atuacao'] ?? '') === 'desenvolvimento' ? 'selected' : '' ?>>Desenvolvimento</option>
                                            <option value="programacao" <?= ($_POST['area_atuacao'] ?? '') === 'programacao' ? 'selected' : '' ?>>Programação</option>
                                            <option value="tecnologia" <?= ($_POST['area_atuacao'] ?? '') === 'tecnologia' ? 'selected' : '' ?>>Tecnologia</option>
                                            <option value="sistemas" <?= ($_POST['area_atuacao'] ?? '') === 'sistemas' ? 'selected' : '' ?>>Sistemas</option>
                                            <option value="mercado" <?= ($_POST['area_atuacao'] ?? '') === 'mercado' ? 'selected' : '' ?>>Mercado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 3: Habilidades Técnicas -->
                            <div class="mb-5 pb-4">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom border-primary">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-2">
                                        <i class="bi bi-code-slash text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Habilidades Técnicas</h5>
                                        <small class="text-secondary">Tecnologias que você domina</small>
                                    </div>
                                </div>

                                <!-- Checkboxes de Tecnologias -->
                                <div class="row g-3 mb-3">
                                    <?php 
                                    $techs = [
                                        'JavaScript' => 'javascript',
                                        'Python' => 'python',
                                        'Java' => 'java',
                                        'PHP' => 'php',
                                        'C#' => 'csharp',
                                        'C++' => 'cpp',
                                        'React' => 'react',
                                        'Node.js' => 'nodejs',
                                        'MySQL' => 'mysql',
                                        'MongoDB' => 'mongodb',
                                        'Docker' => 'docker',
                                        'Git' => 'git'
                                    ];
                                    $selectedTechs = $_POST['tecnologias'] ?? [];
                                    foreach($techs as $label => $val):
                                    ?>
                                    <div class="col-md-3 col-sm-6">
                                        <input class="btn-check" type="checkbox" name="tecnologias[]" value="<?=$val?>" id="tech_<?=$val?>" <?= in_array($val, $selectedTechs) ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-primary w-100 rounded-pill py-2" for="tech_<?=$val?>">
                                            <i class="bi bi-code-square me-1"></i><?=$label?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-plus-circle me-1"></i>Outras Tecnologias
                                        </label>
                                        <input type="text" class="form-control rounded-pill" name="tecnologias_outras" placeholder="Ex: TypeScript, Rust... (separadas por vírgula)" value="<?= htmlspecialchars($_POST['tecnologias_outras'] ?? '') ?>" style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 4: Links & Portfólio -->
                            <div class="mb-5 pb-4">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom border-primary">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-2">
                                        <i class="bi bi-link-45deg text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Links & Portfólio</h5>
                                        <small class="text-secondary">Suas redes e trabalhos (opcional)</small>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-github me-1"></i>GitHub
                                        </label>
                                        <input type="url" class="form-control rounded-pill" name="github" placeholder="https://github.com/..." value="<?= htmlspecialchars($_POST['github'] ?? '') ?>" style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-linkedin me-1"></i>LinkedIn
                                        </label>
                                        <input type="url" class="form-control rounded-pill" name="linkedin" placeholder="https://linkedin.com/in/..." value="<?= htmlspecialchars($_POST['linkedin'] ?? '') ?>" style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-globe me-1"></i>Portfólio/Site
                                        </label>
                                        <input type="url" class="form-control rounded-pill" name="portfolio" placeholder="https://..." value="<?= htmlspecialchars($_POST['portfolio'] ?? '') ?>" style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-instagram me-1"></i>Instagram
                                        </label>
                                        <input type="url" class="form-control rounded-pill" name="instagram" placeholder="https://instagram.com/..." value="<?= htmlspecialchars($_POST['instagram'] ?? '') ?>" style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 5: Experiência com Escrita -->
                            <div class="mb-5 pb-4">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom border-primary">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-2">
                                        <i class="bi bi-file-text-fill text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Experiência com Escrita</h5>
                                        <small class="text-secondary">Seu histórico de publicações</small>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-question-circle me-1"></i>Você já escreveu artigos técnicos? *
                                        </label>
                                        <select class="form-select rounded-pill" name="experiencia_escrita" required style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;">
                                            <option value="">Selecione...</option>
                                            <option value="sim_profissional" <?= ($_POST['experiencia_escrita'] ?? '') === 'sim_profissional' ? 'selected' : '' ?>>Sim, profissionalmente</option>
                                            <option value="sim_blog" <?= ($_POST['experiencia_escrita'] ?? '') === 'sim_blog' ? 'selected' : '' ?>>Sim, em blog pessoal</option>
                                            <option value="sim_plataforma" <?= ($_POST['experiencia_escrita'] ?? '') === 'sim_plataforma' ? 'selected' : '' ?>>Sim, em plataformas técnicas</option>
                                            <option value="nao_interesse" <?= ($_POST['experiencia_escrita'] ?? '') === 'nao_interesse' ? 'selected' : '' ?>>Não, mas tenho interesse</option>
                                            <option value="nao_iniciante" <?= ($_POST['experiencia_escrita'] ?? '') === 'nao_iniciante' ? 'selected' : '' ?>>Não, sou iniciante</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-collection me-1"></i>Links de Artigos Anteriores
                                        </label>
                                        <textarea class="form-control rounded-4" rows="3" name="artigos_anteriores" placeholder="Cole links de artigos (um por linha)" style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;"><?= htmlspecialchars($_POST['artigos_anteriores'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 6: Sobre Você -->
                            <div class="mb-5 pb-4">
                                <div class="d-flex align-items-center mb-3 pb-2 border-bottom border-primary">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-2">
                                        <i class="bi bi-chat-left-quote-fill text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Sobre Você</h5>
                                        <small class="text-secondary">Conte-nos suas motivações</small>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-heart me-1"></i>Por que escrever para a LibreNews? *
                                        </label>
                                        <textarea class="form-control rounded-4" rows="3" name="motivacao" placeholder="Suas motivações e interesse..." required style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;"><?= htmlspecialchars($_POST['motivacao'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-secondary mb-1">
                                            <i class="bi bi-newspaper me-1"></i>Proposta de Primeiro Artigo *
                                        </label>
                                        <textarea class="form-control rounded-4" rows="3" name="proposta_artigo" placeholder="Tema, objetivos e abordagem..." required style="background-color: #2D3748; border-color: #4A5568; color: #E2E8F0;"><?= htmlspecialchars($_POST['proposta_artigo'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Termos e Condições -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="termos" required>
                                        <label class="form-check-label small" for="termos">
                                            Concordo com os <a href="privacidade.php#termos" class="text-primary text-decoration-underline">Termos de Uso</a> e <a href="privacidade.php#privacidade" class="text-primary text-decoration-underline">Política de Privacidade</a> *
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="originalidade" required>
                                        <label class="form-check-label small" for="originalidade">
                                            Comprometo-me a produzir conteúdo original e de qualidade *
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-3">
                                        <button type="submit" class="btn btn-primary py-3 fw-semibold rounded-pill">
                                            <i class="bi bi-send-fill me-2"></i>Enviar Candidatura
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary py-2 fw-semibold rounded-pill">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Limpar Formulário
                                        </button>
                                    </div>
                                    
                                    <p class="text-secondary text-center small mt-4 mb-0">
                                        <i class="bi bi-clock-history me-2"></i>
                                        <strong>* Campos obrigatórios</strong> • Análise em até 48 horas • Você receberá um e-mail com a resposta
                                    </p>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Mensagem de sucesso com botão de voltar -->
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="card border-0 shadow-lg rounded-4 p-5" style="background: rgba(30, 41, 59, 0.9);">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="text-white mb-3">Candidatura Enviada!</h3>
                    <p class="text-secondary mb-4">
                        Sua candidatura foi recebida com sucesso. Nossa equipe irá analisar seu perfil 
                        e você receberá uma resposta no email informado em até 48 horas.
                    </p>
                    <a href="index.php" class="btn btn-primary rounded-pill px-5 py-3">
                        <i class="bi bi-house me-2"></i>Voltar para Home
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <!-- FIM FORMULÁRIO -->

        <!-- ========================================
             PERGUNTAS FREQUENTES (FAQ)
             ======================================== -->
        <div class="row justify-content-center mt-5 pt-5">
            <div class="col-lg-9">
                <div class="text-center mb-5">
                    <h3 class="fw-bold">Perguntas Frequentes</h3>
                    <p class="text-secondary">Tire suas dúvidas sobre o processo de candidatura</p>
                </div>
                
                <!-- Accordion de FAQs (Sem Setinhas) -->
                <div class="accordion" id="faqEquipe" style="--bs-accordion-btn-icon: none; --bs-accordion-btn-active-icon: none;">
                    
                    <!-- FAQ 1 -->
                    <div class="accordion-item border border-2 rounded-4 mb-3 shadow-sm" style="background-color: #2D3748;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-semibold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1" style="background-color: #2D3748; color: #E2E8F0;">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                Preciso ter experiência prévia com escrita?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqEquipe">
                            <div class="accordion-body rounded-bottom-4" style="background-color: #1E293B; color: #E2E8F0;">
                                Não é obrigatório ter experiência prévia com escrita técnica. O LibreNews valoriza principalmente seu conhecimento técnico na área de TI e sua capacidade de comunicar ideias de forma clara e objetiva. A plataforma foi desenvolvida como um ambiente de aprendizado e crescimento, onde escritores iniciantes podem desenvolver suas habilidades com o apoio da equipe editorial.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="accordion-item border border-2 rounded-4 mb-3 shadow-sm" style="background-color: #2D3748;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-semibold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2" style="background-color: #2D3748; color: #E2E8F0;">
                                <i class="bi bi-calendar-check text-primary me-2"></i>
                                Com que frequência preciso publicar artigos?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqEquipe">
                            <div class="accordion-body rounded-bottom-4" style="background-color: #1E293B; color: #E2E8F0;">
                                O LibreNews não estabelece uma frequência mínima obrigatória de publicações. A plataforma prioriza a qualidade do conteúdo em detrimento da quantidade, respeitando o tempo e a disponibilidade de cada escritor. Recomendamos pelo menos uma publicação por mês para manter o perfil ativo.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="accordion-item border border-2 rounded-4 mb-3 shadow-sm" style="background-color: #2D3748;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-semibold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3" style="background-color: #2D3748; color: #E2E8F0;">
                                <i class="bi bi-check-circle text-primary me-2"></i>
                                Os artigos precisam passar por aprovação antes de serem publicados?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqEquipe">
                            <div class="accordion-body rounded-bottom-4" style="background-color: #1E293B; color: #E2E8F0;">
                                Sim, todos os artigos submetidos passam por um processo de revisão editorial antes da publicação. Este procedimento é fundamental para garantir a qualidade, precisão técnica e conformidade com os padrões editoriais da plataforma. O processo de aprovação geralmente leva até 48 horas.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="accordion-item border border-2 rounded-4 mb-3 shadow-sm" style="background-color: #2D3748;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-semibold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4" style="background-color: #2D3748; color: #E2E8F0;">
                                <i class="bi bi-laptop text-primary me-2"></i>
                                Posso escrever sobre qualquer tema relacionado a tecnologia?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqEquipe">
                            <div class="accordion-body rounded-bottom-4" style="background-color: #1E293B; color: #E2E8F0;">
                                Sim, você pode escrever sobre qualquer tema que esteja relacionado às áreas de Tecnologia da Informação. O LibreNews aceita conteúdos nas categorias de Desenvolvimento, Programação, Tecnologia, Sistemas e Mercado.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="accordion-item border border-2 rounded-4 shadow-sm" style="background-color: #2D3748;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-4 fw-semibold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5" style="background-color: #2D3748; color: #E2E8F0;">
                                <i class="bi bi-key text-primary me-2"></i>
                                Como funciona o sistema de login após a aprovação?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqEquipe">
                            <div class="accordion-body rounded-bottom-4" style="background-color: #1E293B; color: #E2E8F0;">
                                Após a aprovação da sua candidatura pela equipe de administradores, você receberá um e-mail com suas credenciais de acesso à área de escritores. Este e-mail conterá seu login (geralmente seu endereço de e-mail) e uma senha temporária que deve ser alterada no primeiro acesso.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- FIM FAQ -->

    </div>
</div>
<!-- FIM PÁGINA DE CANDIDATURA -->

<style>
/* Remove animações e sombras dos cards */
.card,
.card:hover,
.card.shadow-lg,
.card.shadow-lg:hover {
    transition: none !important;
    transform: none !important;
}
</style>

<?php include 'components/footer.php'; ?>
