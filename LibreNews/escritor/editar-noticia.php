<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Verificar se é escritor ou admin
requireEscritor();

$userData = getUserData();
$mensagem = '';
$erro = '';

// Obter ID da notícia
$noticiaId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($noticiaId <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Buscar Idescritor do usuário logado
$idescritor = getIdEscritor($pdo, $userData['id']);

if (!$idescritor) {
    header('Location: dashboard.php');
    exit();
}

// Buscar notícia do banco com categoria
$stmt = $pdo->prepare("
    SELECT n.*, c.nomecategoria 
    FROM Noticia n 
    LEFT JOIN Categoria c ON n.Idcategoria = c.Idcategoria 
    WHERE n.Idnoticia = ? AND n.Idescritor = ?
");
$stmt->execute([$noticiaId, $idescritor]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não encontrou ou não pertence ao escritor
if (!$noticia) {
    header('Location: dashboard.php');
    exit();
}

// Pode editar qualquer notícia (publicada volta para revisão ao editar)
$podeEditar = true;

// --- PROCESSAR FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $podeEditar) {
    $titulo = sanitize($_POST['titulo'] ?? '');
    $conteudo = $_POST['conteudo'] ?? '';
    $categoria = intval($_POST['categoria'] ?? 0);

    // Validar campos obrigatórios
    if (!$titulo || !$conteudo || !$categoria) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        // Upload da imagem, se houver nova
        $imagemCapa = $noticia['imagem']; // Manter imagem atual por padrão
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImagemNoticia($_FILES['imagem']);
            if ($uploadResult['success']) {
                $imagemCapa = $uploadResult['filename'];
            } else {
                $erro = $uploadResult['message'];
            }
        }

        if (!$erro) {
            // SEMPRE envia para revisão ao salvar (status pendente)
            $novoStatus = 'pendente';
            
            // Atualizar notícia
            $stmt = $pdo->prepare("UPDATE Noticia SET Idcategoria = ?, titulo = ?, conteudo = ?, imagem = ?, status = ? WHERE Idnoticia = ? AND Idescritor = ?");
            $stmt->execute([$categoria, $titulo, $conteudo, $imagemCapa, $novoStatus, $noticiaId, $idescritor]);
            
            $mensagem = 'Notícia atualizada e enviada para revisão do administrador!';
            
            // Recarregar dados da notícia
            $stmt = $pdo->prepare("SELECT n.*, c.nomecategoria FROM Noticia n LEFT JOIN Categoria c ON n.Idcategoria = c.Idcategoria WHERE n.Idnoticia = ?");
            $stmt->execute([$noticiaId]);
            $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Manter flag de edição (sempre true)
            $podeEditar = true;
        }
    }
}

// --- BUSCAR CATEGORIAS DO BANCO ---
$stmt = $pdo->query("SELECT Idcategoria, nomecategoria FROM Categoria ORDER BY nomecategoria ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Editar Notícia - Escritor";
include '../components/head.php';
include '../components/navbar.php';
?>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="text-white fw-bold mb-0">
                <i class="bi bi-pencil-square text-primary me-2"></i>Editar Notícia
            </h1>
            <a href="dashboard.php" class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Voltar ao Dashboard
            </a>
        </div>

        <!-- Status da Notícia -->
        <div class="alert <?= $noticia['status'] === 'publicada' ? 'alert-success' : ($noticia['status'] === 'reprovada' ? 'alert-danger' : 'alert-warning') ?> rounded-3 d-flex align-items-center gap-3">
            <i class="bi <?= $noticia['status'] === 'publicada' ? 'bi-check-circle' : ($noticia['status'] === 'reprovada' ? 'bi-x-circle' : 'bi-clock-history') ?> fs-4"></i>
            <div>
                <strong>Status Atual:</strong> <?= ucfirst($noticia['status']) ?>
                <?php if ($noticia['status'] === 'reprovada'): ?>
                    <br><small class="text-white-50">Esta notícia foi reprovada. Edite e salve para enviar uma nova versão para revisão.</small>
                <?php elseif ($noticia['status'] === 'pendente'): ?>
                    <br><small class="text-dark">Esta notícia está aguardando aprovação. Você ainda pode editá-la.</small>
                <?php elseif ($noticia['status'] === 'publicada'): ?>
                    <br><small>Ao editar esta notícia publicada, ela será despublicada e enviada para nova revisão do admin.</small>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-success rounded-3 d-flex align-items-center gap-3" role="alert">
                <i class="bi bi-check-circle fs-4"></i>
                <div>
                    <strong>Sucesso!</strong><br>
                    <?= $mensagem ?>
                </div>
            </div>
            <div class="text-center mb-4">
                <a href="dashboard.php" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Voltar ao Dashboard
                </a>
            </div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $erro ?>
            </div>
        <?php endif; ?>

        <?php if ($podeEditar): ?>
            <div class="card border-0 rounded-4 shadow-lg" style="background: rgba(30, 41, 59, 0.9);">
                <div class="card-header border-0 py-3" style="background: rgba(59, 130, 246, 0.1);">
                    <h5 class="text-white mb-0">
                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>Formulário de Edição
                    </h5>
                    <small class="text-secondary">Ao salvar, a notícia será enviada para revisão do administrador</small>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data" novalidate>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-4">
                                    <label for="titulo" class="form-label text-white fw-semibold">
                                        <i class="bi bi-type me-2"></i>Título da Notícia *
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="titulo" name="titulo" 
                                           value="<?= htmlspecialchars($_POST['titulo'] ?? $noticia['titulo']) ?>" 
                                           placeholder="Digite um título atrativo..."
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="conteudo" class="form-label text-white fw-semibold">
                                        <i class="bi bi-text-paragraph me-2"></i>Conteúdo *
                                    </label>
                                    <textarea class="form-control" id="conteudo" name="conteudo" rows="15" 
                                              placeholder="Escreva o conteúdo completo da notícia..."
                                              required><?= htmlspecialchars($_POST['conteudo'] ?? $noticia['conteudo']) ?></textarea>
                                    <small class="text-secondary">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Dica: Separe parágrafos com linhas em branco para melhor legibilidade.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label for="categoria" class="form-label text-white fw-semibold">
                                        <i class="bi bi-tag me-2"></i>Categoria *
                                    </label>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="">Selecione a categoria</option>
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?= $cat['Idcategoria'] ?>" 
                                                    <?= ($cat['Idcategoria'] == ($_POST['categoria'] ?? $noticia['Idcategoria'])) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['nomecategoria']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Imagem Atual -->
                                <div class="mb-4">
                                    <label class="form-label text-white fw-semibold">
                                        <i class="bi bi-image me-2"></i>Imagem de Capa
                                    </label>
                                    <?php if (!empty($noticia['imagem'])): ?>
                                    <div class="bg-dark rounded-3 p-3 mb-2">
                                        <img src="/LibreNews/<?= htmlspecialchars($noticia['imagem']) ?>" 
                                             alt="Imagem atual" class="img-fluid rounded" style="max-height: 180px; width: 100%; object-fit: cover;">
                                        <small class="d-block text-secondary mt-2">Imagem atual</small>
                                    </div>
                                    <?php else: ?>
                                    <div class="bg-dark rounded-3 p-4 mb-2 text-center">
                                        <i class="bi bi-image text-secondary fs-1"></i>
                                        <p class="text-secondary small mb-0 mt-2">Nenhuma imagem</p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <input type="file" class="form-control" id="imagem" name="imagem" accept=".jpg,.jpeg,.png,.gif,.webp">
                                    <small class="text-secondary">
                                        <?= !empty($noticia['imagem']) ? 'Envie uma nova imagem para substituir a atual' : 'Adicione uma imagem de capa' ?>
                                    </small>
                                </div>

                                <!-- Informações -->
                                <div class="card border-0 rounded-3" style="background: rgba(59, 130, 246, 0.1);">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Informações</h6>
                                        <ul class="list-unstyled text-secondary small mb-0">
                                            <li class="mb-2">
                                                <i class="bi bi-check text-success me-2"></i>
                                                Ao salvar, a notícia será enviada para revisão
                                            </li>
                                            <li class="mb-2">
                                                <i class="bi bi-check text-success me-2"></i>
                                                O admin receberá sua atualização
                                            </li>
                                            <li>
                                                <i class="bi bi-check text-success me-2"></i>
                                                Você será notificado após a revisão
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-secondary my-4">

                        <div class="d-flex gap-3 flex-wrap">
                            <button type="submit" class="btn btn-success rounded-pill py-3 px-5 fw-bold">
                                <i class="bi bi-send me-2"></i>Salvar e Enviar para Revisão
                            </button>
                            
                            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill py-3 px-5 fw-bold">
                                <i class="bi bi-x-lg me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Visualização somente leitura para notícias publicadas -->
            <div class="card border-0 rounded-4 shadow-lg" style="background: rgba(30, 41, 59, 0.9);">
                <div class="card-header border-0 py-3" style="background: rgba(34, 197, 94, 0.1);">
                    <h5 class="text-white mb-0">
                        <i class="bi bi-eye me-2 text-success"></i>Visualização da Notícia (Somente Leitura)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label text-white fw-semibold">Título</label>
                        <p class="text-secondary fs-5"><?= htmlspecialchars($noticia['titulo']) ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-white fw-semibold">Categoria</label>
                        <p><span class="badge bg-primary"><?= htmlspecialchars($noticia['nomecategoria']) ?></span></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-white fw-semibold">Conteúdo</label>
                        <div class="bg-dark rounded-3 p-4" style="max-height: 400px; overflow-y: auto;">
                            <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($noticia['conteudo'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($noticia['imagem'])): ?>
                    <div class="mb-4">
                        <label class="form-label text-white fw-semibold">Imagem de Capa</label>
                        <div>
                            <img src="/LibreNews/<?= htmlspecialchars($noticia['imagem']) ?>" 
                                 alt="Imagem" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info rounded-3 mt-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Para solicitar alterações em notícias publicadas, entre em contato com o administrador.
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include '../components/footer.php'; ?>
