<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Verificar se é escritor ou admin
requireEscritor();

$userData = getUserData();
$mensagem = '';
$erro = '';

// Buscar dados completos do usuário
$stmt = $pdo->prepare("SELECT * FROM Conta WHERE Idconta = ?");
$stmt->execute([$userData['id']]);
$conta = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar dados do escritor
$idescritor = getIdEscritor($pdo, $userData['id']);
$escritor = null;
if ($idescritor) {
    $stmt = $pdo->prepare("SELECT * FROM Escritor WHERE Idescritor = ?");
    $stmt->execute([$idescritor]);
    $escritor = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Estatísticas do escritor
$estatisticas = ['total' => 0, 'publicadas' => 0];
if ($idescritor) {
    $stmtStats = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'publicada' THEN 1 ELSE 0 END) as publicadas FROM Noticia WHERE Idescritor = ?");
    $stmtStats->execute([$idescritor]);
    $estatisticas = $stmtStats->fetch(PDO::FETCH_ASSOC);
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'atualizar_perfil') {
        $nome = sanitize($_POST['nome'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        
        // Validar campos
        if (empty($nome) || empty($email)) {
            $erro = 'Nome e email são obrigatórios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Email inválido.';
        } else {
            // Verificar se email já existe (exceto o próprio usuário)
            $stmtCheck = $pdo->prepare("SELECT Idconta FROM Conta WHERE email = ? AND Idconta != ?");
            $stmtCheck->execute([$email, $userData['id']]);
            if ($stmtCheck->fetch()) {
                $erro = 'Este email já está em uso por outro usuário.';
            } else {
                // Atualizar dados
                $stmt = $pdo->prepare("UPDATE Conta SET nomeusuario = ?, email = ? WHERE Idconta = ?");
                $stmt->execute([$nome, $email, $userData['id']]);
                
                // Atualizar sessão
                $_SESSION['user_name'] = $nome;
                
                $mensagem = 'Perfil atualizado com sucesso!';
                
                // Recarregar dados
                $stmt = $pdo->prepare("SELECT * FROM Conta WHERE Idconta = ?");
                $stmt->execute([$userData['id']]);
                $conta = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    } elseif ($acao === 'alterar_senha') {
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        
        // Validar campos
        if (empty($senhaAtual) || empty($novaSenha) || empty($confirmarSenha)) {
            $erro = 'Preencha todos os campos de senha.';
        } elseif ($novaSenha !== $confirmarSenha) {
            $erro = 'A nova senha e confirmação não coincidem.';
        } elseif (strlen($novaSenha) < 6) {
            $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
        } else {
            // Verificar senha atual (comparação direta - consistente com login.php)
            if ($senhaAtual === $conta['senha']) {
                // Atualizar senha (texto plano - consistente com o sistema)
                $stmt = $pdo->prepare("UPDATE Conta SET senha = ? WHERE Idconta = ?");
                $stmt->execute([$novaSenha, $userData['id']]);
                
                $mensagem = 'Senha alterada com sucesso!';
                
                // Recarregar dados da conta
                $stmt = $pdo->prepare("SELECT * FROM Conta WHERE Idconta = ?");
                $stmt->execute([$userData['id']]);
                $conta = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $erro = 'Senha atual incorreta.';
            }
        }
    }
}

$pageTitle = "Meu Perfil - Escritor";
include '../components/head.php';
include '../components/navbar.php';
?>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">
                    <i class="bi bi-person-circle text-primary me-3"></i>Meu Perfil
                </h1>
                <p class="text-secondary mb-0">Gerencie suas informações pessoais</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary rounded-pill">
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
                    <div class="card-body p-5">
                        <!-- Avatar -->
                        <div class="rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px; background: linear-gradient(135deg, #3B82F6, #8B5CF6);">
                            <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                        </div>
                        
                        <!-- Nome -->
                        <h4 class="text-white mb-1"><?= htmlspecialchars($conta['nomeusuario']) ?></h4>
                        <span class="badge bg-primary mb-3">Escritor</span>
                        
                        <!-- Email -->
                        <p class="text-secondary mb-4">
                            <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($conta['email']) ?>
                        </p>
                        
                        <!-- Estatísticas -->
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="bg-dark rounded-3 p-3">
                                    <h4 class="text-primary mb-0"><?= $estatisticas['total'] ?? 0 ?></h4>
                                    <small class="text-secondary">Notícias</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-dark rounded-3 p-3">
                                    <h4 class="text-success mb-0"><?= $estatisticas['publicadas'] ?? 0 ?></h4>
                                    <small class="text-secondary">Publicadas</small>
                                </div>
                            </div>
                        </div>
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
                                           value="<?= htmlspecialchars($conta['nomeusuario']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= htmlspecialchars($conta['email']) ?>" required>
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
                    <div class="card-header border-0 py-3" style="background: rgba(239, 68, 68, 0.1);">
                        <h5 class="text-white mb-0">
                            <i class="bi bi-shield-lock text-danger me-2"></i>Alterar Senha
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <input type="hidden" name="acao" value="alterar_senha">
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-white">Senha Atual</label>
                                    <input type="password" class="form-control" name="senha_atual" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white">Nova Senha</label>
                                    <input type="password" class="form-control" name="nova_senha" 
                                           minlength="6" required>
                                    <small class="text-secondary">Mínimo 6 caracteres</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white">Confirmar Nova Senha</label>
                                    <input type="password" class="form-control" name="confirmar_senha" required>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-danger rounded-pill px-4">
                                    <i class="bi bi-key me-2"></i>Alterar Senha
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

