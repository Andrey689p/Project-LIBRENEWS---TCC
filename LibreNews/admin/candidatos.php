<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Verificar se é admin
requireAdmin();

$userData = getUserData();
$mensagem = '';
$erro = '';
$contaCriada = null;

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $candidatoId = intval($_POST['candidato_id'] ?? 0);
    
    if ($acao === 'aprovar' && $candidatoId > 0) {
        // Buscar dados do candidato
        $stmtCand = $pdo->prepare("SELECT * FROM Candidato WHERE Idcandidato = ?");
        $stmtCand->execute([$candidatoId]);
        $candidato = $stmtCand->fetch();
        
        if ($candidato) {
            $senha = $_POST['senha'] ?? '';
            
            if (empty($senha) || strlen($senha) < 6) {
                $erro = 'A senha deve ter pelo menos 6 caracteres.';
            } else {
                // Verificar se o email já existe na tabela Conta
                $stmtCheck = $pdo->prepare("SELECT Idconta FROM Conta WHERE email = ?");
                $stmtCheck->execute([$candidato['email']]);
                
                if ($stmtCheck->fetch()) {
                    $erro = 'Este email já possui uma conta no sistema.';
                } else {
                    try {
                        $pdo->beginTransaction();
                        
                        // Criar conta
                        $stmtConta = $pdo->prepare("INSERT INTO Conta (nomeusuario, email, senha) VALUES (?, ?, ?)");
                        $stmtConta->execute([$candidato['nome'], $candidato['email'], $senha]);
                        $idConta = $pdo->lastInsertId();
                        
                        // Criar escritor
                        $stmtEscritor = $pdo->prepare("INSERT INTO Escritor (Idconta) VALUES (?)");
                        $stmtEscritor->execute([$idConta]);
                        
                        // Atualizar candidato como aprovado
                        $stmtUpdate = $pdo->prepare("UPDATE Candidato SET status = 'aprovado' WHERE Idcandidato = ?");
                        $stmtUpdate->execute([$candidatoId]);
                        
                        $pdo->commit();
                        
                        // Guardar dados para exibir
                        $contaCriada = [
                            'nome' => $candidato['nome'],
                            'email' => $candidato['email'],
                            'senha' => $senha
                        ];
                        
                        $mensagem = 'Candidato aprovado! Conta de escritor criada com sucesso.';
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $erro = 'Erro ao criar conta: ' . $e->getMessage();
                    }
                }
            }
        }
    } elseif ($acao === 'rejeitar' && $candidatoId > 0) {
        $stmt = $pdo->prepare("UPDATE Candidato SET status = 'rejeitado' WHERE Idcandidato = ?");
        $stmt->execute([$candidatoId]);
        $mensagem = 'Candidato rejeitado.';
    }
}

// Buscar candidatos pendentes
$stmt = $pdo->query("SELECT * FROM Candidato WHERE status = 'pendente' ORDER BY datacandidatura DESC");
$candidatos = $stmt->fetchAll();

// Contar estatísticas
$stmtStats = $pdo->query("
    SELECT 
        SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
        SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
        SUM(CASE WHEN status = 'rejeitado' THEN 1 ELSE 0 END) as rejeitados
    FROM Candidato
");
$stats = $stmtStats->fetch();

$pageTitle = "Avaliar Candidatos - Admin";
include '../components/head.php';
include '../components/navbar.php';
?>

<!-- Avaliar Candidatos -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">
                    <i class="bi bi-person-plus text-primary me-3"></i>Avaliar Candidatos
                </h1>
                <p class="text-secondary mb-0">Aprovar ou rejeitar candidaturas para escritores</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Voltar
            </a>
        </div>

        <!-- Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 rounded-4" style="background: rgba(251, 191, 36, 0.1); border-left: 4px solid #FBBF24 !important;">
                    <div class="card-body p-3 text-center">
                        <h3 class="text-warning fw-bold mb-0"><?= $stats['pendentes'] ?? 0 ?></h3>
                        <small class="text-secondary">Pendentes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 rounded-4" style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22C55E !important;">
                    <div class="card-body p-3 text-center">
                        <h3 class="text-success fw-bold mb-0"><?= $stats['aprovados'] ?? 0 ?></h3>
                        <small class="text-secondary">Aprovados</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 rounded-4" style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #EF4444 !important;">
                    <div class="card-body p-3 text-center">
                        <h3 class="text-danger fw-bold mb-0"><?= $stats['rejeitados'] ?? 0 ?></h3>
                        <small class="text-secondary">Rejeitados</small>
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

        <!-- Modal de Conta Criada -->
        <?php if ($contaCriada): ?>
        <div class="card border-0 rounded-4 mb-4" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(59, 130, 246, 0.2)); border: 2px solid #22C55E !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-3 me-3" style="background: rgba(34, 197, 94, 0.3);">
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    </div>
                    <div>
                        <h4 class="text-white mb-0">Conta Criada com Sucesso!</h4>
                        <small class="text-secondary">Envie esses dados por email para o novo escritor</small>
                    </div>
                </div>
                
                <div class="bg-dark rounded-3 p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="text-secondary small">Nome</label>
                            <p class="text-white mb-0 fw-bold"><?= htmlspecialchars($contaCriada['nome']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-secondary small">Email (Login)</label>
                            <p class="text-primary mb-0 fw-bold"><?= htmlspecialchars($contaCriada['email']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-secondary small">Senha</label>
                            <p class="text-warning mb-0 fw-bold"><?= htmlspecialchars($contaCriada['senha']) ?></p>
                        </div>
                    </div>
                    
                    <hr class="border-secondary my-3">
                    
                    <div class="text-secondary small">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Instruções para o email:</strong><br>
                        "Sua candidatura foi aprovada! Acesse <strong>librenews.com/login.php</strong> com:<br>
                        Email: <strong><?= htmlspecialchars($contaCriada['email']) ?></strong><br>
                        Senha: <strong><?= htmlspecialchars($contaCriada['senha']) ?></strong><br>
                        Recomendamos alterar sua senha no primeiro acesso."
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Lista de Candidatos Pendentes -->
        <div class="row g-4">
            <?php if (empty($candidatos)): ?>
                <div class="col-12">
                    <div class="alert alert-info rounded-3">
                        <i class="bi bi-info-circle me-2"></i>Não há candidatos pendentes no momento.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($candidatos as $candidato): ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-lg rounded-4" style="background: rgba(30, 41, 59, 0.8);">
                            <div class="card-body p-4">
                                <div class="row">
                                    <!-- Informações do Candidato -->
                                    <div class="col-lg-8">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="rounded-circle p-3 me-3" style="background: rgba(251, 191, 36, 0.2);">
                                                <i class="bi bi-person-badge text-warning fs-3"></i>
                                            </div>
                                            <div>
                                                <h5 class="text-white mb-1"><?= htmlspecialchars($candidato['nome']) ?></h5>
                                                <p class="text-primary mb-0">
                                                    <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($candidato['email']) ?>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="text-secondary small">Categoria/Perfil</label>
                                                <p class="text-white mb-0"><?= htmlspecialchars($candidato['telefone'] ?? 'Não informado') ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-secondary small">Data da Candidatura</label>
                                                <p class="text-white mb-0">
                                                    <?= $candidato['datacandidatura'] ? date('d/m/Y H:i', strtotime($candidato['datacandidatura'])) : 'Não informada' ?>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($candidato['experiencia'])): ?>
                                        <div class="mb-3">
                                            <label class="text-secondary small">Experiência e Motivação</label>
                                            <div class="bg-dark rounded-3 p-3" style="max-height: 200px; overflow-y: auto;">
                                                <p class="text-secondary mb-0 small"><?= nl2br(htmlspecialchars($candidato['experiencia'])) ?></p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($candidato['portfolio'])): ?>
                                        <div class="mb-3">
                                            <label class="text-secondary small">Links/Portfólio</label>
                                            <div class="bg-dark rounded-3 p-3">
                                                <p class="text-info mb-0 small"><?= nl2br(htmlspecialchars($candidato['portfolio'])) ?></p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Ações -->
                                    <div class="col-lg-4">
                                        <div class="card border-0 rounded-3 h-100" style="background: rgba(59, 130, 246, 0.1);">
                                            <div class="card-body p-3">
                                                <h6 class="text-white mb-3"><i class="bi bi-gear me-2"></i>Ações</h6>
                                                
                                                <!-- Formulário de Aprovação -->
                                                <form method="POST" class="mb-3">
                                                    <input type="hidden" name="candidato_id" value="<?= $candidato['Idcandidato'] ?>">
                                                    <input type="hidden" name="acao" value="aprovar">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label text-white small">
                                                            <i class="bi bi-key me-1"></i>Definir Senha *
                                                        </label>
                                                        <input type="text" class="form-control form-control-sm" name="senha" 
                                                               placeholder="Mínimo 6 caracteres" required minlength="6"
                                                               value="<?= 'senha' . rand(100, 999) ?>">
                                                        <small class="text-secondary">Esta senha será exibida para você enviar ao candidato</small>
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-success w-100 mb-2">
                                                        <i class="bi bi-check-circle me-1"></i>Aprovar e Criar Conta
                                                    </button>
                                                </form>
                                                
                                                <!-- Botão Rejeitar -->
                                                <form method="POST" onsubmit="return confirm('Tem certeza que deseja rejeitar este candidato?')">
                                                    <input type="hidden" name="candidato_id" value="<?= $candidato['Idcandidato'] ?>">
                                                    <input type="hidden" name="acao" value="rejeitar">
                                                    <button type="submit" class="btn btn-outline-danger w-100">
                                                        <i class="bi bi-x-circle me-1"></i>Rejeitar
                                                    </button>
                                                </form>
                                            </div>
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
