<?php
/* ========================================
   LIBRENEWS - FUNÇÕES AUXILIARES
   ======================================== */

require_once __DIR__ . '/../config/database.php';

// Sanitizar entrada de dados
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Formatar data brasileira
function formatarData($data) {
    $timestamp = strtotime($data);
    return date('d/m/Y H:i', $timestamp);
}

// Formatar data simples (sem hora)
function formatarDataSimples($data) {
    $timestamp = strtotime($data);
    return date('d/m/Y', $timestamp);
}

// Upload de imagem de notícia
function uploadImagemNoticia($file) {
    $uploadDir = __DIR__ . '/../assets/uploads/noticias/';
    
    // Verificar se é imagem
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido. Use JPG, PNG ou GIF.'];
    }
    
    // Verificar tamanho (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Arquivo muito grande. Tamanho máximo: 5MB.'];
    }
    
    // Gerar nome único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = uniqid('noticia_') . '.' . $extension;
    $uploadPath = $uploadDir . $newName;
    
    // Fazer upload
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'filename' => 'assets/uploads/noticias/' . $newName];
    } else {
        return ['success' => false, 'message' => 'Erro ao fazer upload da imagem.'];
    }
}

// Limitar caracteres e adicionar reticências
function limitarTexto($texto, $limite = 150) {
    if (strlen($texto) <= $limite) {
        return $texto;
    }
    return substr($texto, 0, $limite) . '...';
}

// Remover tags HTML do conteúdo
function limparHTML($html) {
    return strip_tags($html);
}

// Gerar slug de URL amigável
function gerarSlug($texto) {
    $texto = strtolower($texto);
    $texto = preg_replace('/[^a-z0-9\s-]/', '', $texto);
    $texto = preg_replace('/[\s-]+/', '-', $texto);
    $texto = trim($texto, '-');
    return $texto;
}

// Verificar se email é válido
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Contar notícias pendentes (para admin)
function contarNoticiasPendentes() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM Noticia WHERE status = 'pendente'");
    return $stmt->fetchColumn();
}

// Contar candidatos pendentes (para admin)
function contarCandidatosPendentes() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM Candidato WHERE status = 'pendente'");
    return $stmt->fetchColumn();
}
?>
