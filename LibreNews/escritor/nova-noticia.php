<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php'; // Para uploadImagemNoticia

// Verificar se é escritor (NÃO admin)
requireLogin();
if (!isEscritor()) {
    header('Location: /LibreNews/index.php');
    exit();
}

$userData = getUserData();
$mensagem = '';
$erro = '';

// --- PROCESSAR FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitize($_POST['titulo'] ?? '');
    $conteudo = $_POST['conteudo'] ?? '';
    $categoria = intval($_POST['categoria'] ?? 0);

    // Validar campos obrigatórios
    if (!$titulo || !$conteudo || !$categoria) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        // Upload da imagem, se houver
        $imagemCapa = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImagemNoticia($_FILES['imagem']);
            if ($uploadResult['success']) {
                $imagemCapa = $uploadResult['filename'];
            } else {
                $erro = $uploadResult['message'];
            }
        }

        if (!$erro) {
            // Buscar Idescritor a partir do Idconta
            $idescritor = getIdEscritor($pdo, $userData['id']);
            
            if ($idescritor) {
                // Inserir notícia no banco (status pendente)
                $stmt = $pdo->prepare("INSERT INTO Noticia (Idcategoria, Idescritor, titulo, conteudo, imagem, status) VALUES (?, ?, ?, ?, ?, 'pendente')");
                $stmt->execute([$categoria, $idescritor, $titulo, $conteudo, $imagemCapa]);
                $mensagem = 'Notícia enviada para revisão com sucesso!';
            } else {
                $erro = 'Erro: Escritor não encontrado. Entre em contato com o administrador.';
            }
        }
    }
}

// --- BUSCAR CATEGORIAS DO BANCO ---
$stmt = $pdo->query("SELECT Idcategoria, nomecategoria FROM Categoria ORDER BY nomecategoria ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Nova Notícia - Escritor";
include '../components/head.php';
include '../components/navbar.php';
?>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); min-height: 100vh;">
    <div class="container">

        <h1 class="text-white fw-bold mb-4"><i class="bi bi-plus-circle text-primary me-2"></i>Nova Notícia</h1>

        <?php if ($mensagem): ?>
            <div class="alert alert-success rounded-3" role="alert"><?= $mensagem ?></div>
            <a href="dashboard.php" class="btn btn-secondary rounded-pill mt-3"><i class="bi bi-arrow-left me-2"></i>Voltar ao Dashboard</a>
        <?php else: ?>
            <?php if ($erro): ?>
                <div class="alert alert-danger rounded-3" role="alert"><?= $erro ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="mb-3">
                    <label for="titulo" class="form-label text-white">Título *</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="conteudo" class="form-label text-white">Conteúdo *</label>
                    <textarea class="form-control" id="conteudo" name="conteudo" rows="10" required><?= htmlspecialchars($_POST['conteudo'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="categoria" class="form-label text-white">Categoria *</label>
                    <select class="form-select" id="categoria" name="categoria" required>
                        <option value="">Selecione a categoria</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['Idcategoria'] ?>" <?= (isset($_POST['categoria']) && $_POST['categoria'] == $cat['Idcategoria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nomecategoria']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="imagem" class="form-label text-white">Imagem de Capa (opcional)</label>
                    <input type="file" class="form-control" id="imagem" name="imagem" accept=".jpg,.jpeg,.png,.gif">
                </div>

                <button type="submit" class="btn btn-primary rounded-pill py-3 px-5 fw-bold">Enviar para Revisão</button>
                <a href="dashboard.php" class="btn btn-secondary rounded-pill py-3 px-5 fw-bold ms-3">Cancelar</a>
            </form>
        <?php endif; ?>

    </div>
</div>

<?php include '../components/footer.php'; ?>
