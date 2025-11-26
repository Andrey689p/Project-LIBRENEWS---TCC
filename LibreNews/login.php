<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: escritor/dashboard.php');
    }
    exit();
}

$erro = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } else {
        // Buscar usuário
        $stmt = $pdo->prepare("SELECT * FROM Conta WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && $senha === $usuario['senha']) {
            // Verificar tipo de usuário
            $stmtAdmin = $pdo->prepare("SELECT * FROM Administrador WHERE Idconta = ?");
            $stmtAdmin->execute([$usuario['Idconta']]);
            $isAdminUser = $stmtAdmin->fetch();
            
            if ($isAdminUser) {
                login($usuario['Idconta'], 'admin', $usuario['nomeusuario']);
                header('Location: admin/dashboard.php');
            } else {
                login($usuario['Idconta'], 'escritor', $usuario['nomeusuario']);
                header('Location: escritor/dashboard.php');
            }
            exit();
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    }
}

$pageTitle = "Login - LibreNews";
include 'components/head.php';
?>

<!-- Conteúdo: Página de Login -->
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #020617 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                
                <!-- Card de Login -->
                <div class="card border-0 shadow-lg rounded-4" style="background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px);">
                    <div class="card-body p-5">
                        
                        <!-- Logo e Título -->
                        <div class="text-center mb-4">
                            <div class="logo-icon-modern mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background: linear-gradient(135deg, #3B82F6, #2563EB);">
                                <i class="bi bi-lightning-charge-fill text-white" style="font-size: 35px;"></i>
                            </div>
                            <h3 class="fw-bold mb-2 text-white">LibreNews</h3>
                            <p class="text-secondary">Acesse sua conta</p>
                        </div>

                        <!-- Mensagem de Erro -->
                        <?php if ($erro): ?>
                            <div class="alert alert-danger rounded-3 text-center mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i><?= $erro ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulário de Login -->
                        <form method="POST" action="login.php">
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label text-white">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background: rgba(51, 65, 85, 0.5);">
                                        <i class="bi bi-envelope text-primary"></i>
                                    </span>
                                    <input type="email" class="form-control border-0 ps-2" id="email" name="email" 
                                           placeholder="seu@email.com" required 
                                           style="background: rgba(51, 65, 85, 0.5); color: #E2E8F0;"
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Senha -->
                            <div class="mb-4">
                                <label for="senha" class="form-label text-white">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background: rgba(51, 65, 85, 0.5);">
                                        <i class="bi bi-lock text-primary"></i>
                                    </span>
                                    <input type="password" class="form-control border-0 ps-2" id="senha" name="senha" 
                                           placeholder="••••••••" required
                                           style="background: rgba(51, 65, 85, 0.5); color: #E2E8F0;">
                                </div>
                            </div>

                            <!-- Botão Entrar -->
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 mb-3 fw-bold">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                            </button>

                            <!-- Link Voltar -->
                            <div class="text-center">
                                <a href="index.php" class="text-decoration-none" style="color: #94A3B8;">
                                    <i class="bi bi-arrow-left me-2"></i>Voltar para o site
                                </a>
                            </div>

                        </form>

                        <!-- Informação de Teste -->
                        <div class="mt-4 p-3 rounded-3" style="background: rgba(51, 65, 85, 0.3); border: 1px solid rgba(148, 163, 184, 0.2);">
                            <small class="d-block mb-2 text-white"><strong>Contas de teste:</strong></small>
                            <small class="d-block" style="color: #94A3B8;">📧 admin@librenews.com.br | 🔑 admin123</small>
                            <small class="d-block" style="color: #94A3B8;">📧 escritor@librenews.com.br | 🔑 escritor123</small>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>
