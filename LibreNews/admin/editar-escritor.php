<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Verificar se é admin
requireAdmin();

$userData = getUserData();
$mensagem = '';
$erro = '';

// Obter ID do escritor
$escritorId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($escritorId <= 0) {
    header('Location: escritores.php');
    exit();
}

// Buscar dados do escritor
$stmt = $pdo->prepare("
    SELECT e.*, c.Idconta, c.nomeusuario, c.email, c.senha
    FROM Escritor e
    JOIN Conta c ON e.Idconta = c.Idconta
    WHERE e.Idescritor = ?
");
$stmt->execute([$escritorId]);
$escritor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$escritor) {
    header('Location: escritores.php');
    exit();
}

// Estatísticas do escritor
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'publicada' THEN 1 ELSE 0 END) as publicadas,
        SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
        SUM(CASE WHEN status = 'reprovada' THEN 1 ELSE 0 END) as reprovadas
    FROM Noticia WHERE Idescritor = ?
");
$stmtStats->execute([$escritorId]);
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'atualizar_perfil') {
        $nome = sanitize($_POST['nome'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        
        if (empty($nome) || empty($email)) {
            $erro = 'Nome e email são obrigatórios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Email inválido.';
        } else {
            // Verificar se email já existe (exceto o próprio)
            $stmtCheck = $pdo->prepare("SELECT Idconta FROM Conta WHERE email = ? AND Idconta != ?");
            $stmtCheck->execute([$email, $escritor['Idconta']]);
            
            if ($stmtCheck->fetch()) {
                $erro = 'Este email já está em uso por outro usuário.';
            } else {
                $stmt = $pdo->prepare("UPDATE Conta SET nomeusuario = ?, email = ? WHERE Idconta = ?");
                $stmt->execute([$nome, $email, $escritor['Idconta']]);
                
                $mensagem = 'Perfil atualizado com sucesso!';
                
                // Recarregar dados
                $stmt = $pdo->prepare("SELECT e.*, c.Idconta, c.nomeusuario, c.email, c.senha FROM Escritor e JOIN Conta c ON e.Idconta = c.Idconta WHERE e.Idescritor = ?");
                $stmt->execute([$escritorId]);
                $escritor = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    } elseif ($acao === 'alterar_senha') {
        $novaSenha = $_POST['nova_senha'] ?? '';
        
        if (empty($novaSenha) || strlen($novaSenha) < 6) {
            $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
        } else {
            $stmt = $pdo->prepare("UPDATE Conta SET senha = ? WHERE Idconta = ?");
            $stmt->execute([$novaSenha, $escritor['Idconta']]);
            
            $mensagem = 'Senha alterada com sucesso! Nova senha: ' . $novaSenha;
            
            // Recarregar dados
            $stmt = $pdo->prepare("SELECT e.*, c.Idconta, c.nomeusuario, c.email, c.senha FROM Escritor e JOIN Conta c ON e.Idconta = c.Idconta WHERE e.Idescritor = ?");
            $stmt->execute([$escritorId]);
            $escritor = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } elseif ($acao === 'remover') {
        try {
            $pdo->beginTransaction();
            
            // Remover notícias do escritor
            $stmt = $pdo->prepare("DELETE FROM Noticia WHERE Idescritor = ?");
            $stmt->execute([$escritorId]);
            
            // Remover escritor
            $stmt = $pdo->prepare("DELETE FROM Escritor WHERE Idescritor = ?");
            $stmt->execute([$escritorId]);
            
            // Remover conta
            $stmt = $pdo->prepare("DELETE FROM Conta WHERE Idconta = ?");
            $stmt->execute([$escritor['Idconta']]);
            
            $pdo->commit();
            
            header('Location: escritores.php?msg=removido');
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = 'Erro ao remover escritor: ' . $e->getMessage();
        }
    }
}

$pageTitle = "Editar Escritor - Admin";
include '../components/head.php';
include '../components/navbar.php';
?>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">
                    <i class="bi bi-person-gear text-primary me-3"></i>Editar Escritor
                </h1>
                <p class="text-secondary mb-0">Gerenciar dados de <?= htmlspecialchars($escritor['nomeusuario']) ?></p>
            </div>
            <a href="escritores.php" class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Voltar
            </a>
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

        <div class="row g-4">
            <!-- Coluna Esquerda: Card de Perfil -->
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-lg text-center" style="background: rgba(30, 41, 59, 0.9);">
                    <div class="card-body p-4">
                        <!-- Avatar -->
                        <div class="rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; background: linear-gradient(135deg, #22C55E, #16A34A);">
                            <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                        
                        <!-- Nome -->
                        <h4 class="text-white mb-1"><?= htmlspecialchars($escritor['nomeusuario']) ?></h4>
                        <span class="badge bg-success mb-3">Escritor</span>
                        
                        <!-- Email -->
                        <p class="text-secondary mb-2">
                            <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($escritor['email']) ?>
                        </p>
                        
                        <!-- Senha Atual -->
                        <p class="text-secondary mb-4">
                            <i class="bi bi-key me-2"></i>Senha: <code class="text-warning"><?= htmlspecialchars($escritor['senha']) ?></code>
                        </p>
                        
                        <!-- Estatísticas -->
                        <div class="row g-2 text-center">
                            <div class="col-6">
                                <div class="bg-dark rounded-3 p-2">
                                    <h5 class="text-primary mb-0"><?= $stats['total'] ?? 0 ?></h5>
                                    <small class="text-secondary" style="font-size: 0.7rem;">Total</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-dark rounded-3 p-2">
                                    <h5 class="text-success mb-0"><?= $stats['publicadas'] ?? 0 ?></h5>
                                    <small class="text-secondary" style="font-size: 0.7rem;">Publicadas</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-dark rounded-3 p-2">
                                    <h5 class="text-warning mb-0"><?= $stats['pendentes'] ?? 0 ?></h5>
                                    <small class="text-secondary" style="font-size: 0.7rem;">Pendentes</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-dark rounded-3 p-2">
                                    <h5 class="text-danger mb-0"><?= $stats['reprovadas'] ?? 0 ?></h5>
                                    <small class="text-secondary" style="font-size: 0.7rem;">Reprovadas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botão Remover -->
                <div class="card border-0 rounded-4 shadow-lg mt-4" style="background: rgba(239, 68, 68, 0.1);">
                    <div class="card-body p-4">
                        <h6 class="text-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i>Zona de Perigo</h6>
                        <p class="text-secondary small mb-3">Remover este escritor irá excluir permanentemente todas as suas notícias.</p>
                        <form method="POST" onsubmit="return confirm('ATENÇÃO: Isso irá remover o escritor e TODAS as suas notícias. Esta ação NÃO pode ser desfeita. Continuar?')">
                            <input type="hidden" name="acao" value="remover">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash me-2"></i>Remover Escritor
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Coluna Direita: Formulários -->
            <div class="col-lg-8">
                <!-- Informações Pessoais -->
                <div class="card border-0 rounded-4 shadow-lg mb-4" style="background: rgba(30, 41, 59, 0.9);">
                    <div class="card-header border-0 py-3" style="background: rgba(59, 130, 246, 0.1);">
                        <h5 class="text-white mb-0">
                            <i class="bi bi-person-lines-fill text-primary me-2"></i>Informações Pessoais
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <input type="hidden" name="acao" value="atualizar_perfil">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-white">Nome Completo</label>
                                    <input type="text" class="form-control" name="nome" 
                                           value="<?= htmlspecialchars($escritor['nomeusuario']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= htmlspecialchars($escritor['email']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <i class="bi bi-check-lg me-2"></i>Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Alterar Senha -->
                <div class="card border-0 rounded-4 shadow-lg" style="background: rgba(30, 41, 59, 0.9);">
                    <div class="card-header border-0 py-3" style="background: rgba(251, 191, 36, 0.1);">
                        <h5 class="text-white mb-0">
                            <i class="bi bi-key text-warning me-2"></i>Redefinir Senha
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info rounded-3 mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Senha atual:</strong> <code><?= htmlspecialchars($escritor['senha']) ?></code>
                            <br><small>Você pode enviar esta senha para o escritor se ele esqueceu.</small>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="acao" value="alterar_senha">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-white">Nova Senha</label>
                                    <input type="text" class="form-control" name="nova_senha" 
                                           minlength="6" required placeholder="Mínimo 6 caracteres"
                                           value="<?= 'nova' . rand(100, 999) ?>">
                                    <small class="text-secondary">A nova senha será exibida para você informar ao escritor</small>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-warning rounded-pill px-4">
                                    <i class="bi bi-key me-2"></i>Redefinir Senha
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../components/footer.php'; ?>

