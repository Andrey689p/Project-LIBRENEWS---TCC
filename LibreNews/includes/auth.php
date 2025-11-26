<?php
/* ========================================
   LIBRENEWS - FUNÇÕES DE AUTENTICAÇÃO
   ======================================== */

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Verificar se é administrador
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Verificar se é escritor
function isEscritor() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'escritor';
}

// Redirecionar se não estiver logado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /LibreNews/login.php');
        exit();
    }
}

// Redirecionar se não for admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /LibreNews/index.php');
        exit();
    }
}

// Redirecionar se não for escritor ou admin
function requireEscritor() {
    requireLogin();
    if (!isEscritor() && !isAdmin()) {
        header('Location: /LibreNews/index.php');
        exit();
    }
}

// Fazer login do usuário
function login($userId, $userType, $userName) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_type'] = $userType;
    $_SESSION['user_name'] = $userName;
    $_SESSION['logged_in'] = true;
}

// Fazer logout do usuário
function logout() {
    session_start();
    session_unset();
    session_destroy();
    header('Location: /LibreNews/index.php');
    exit();
}

// Obter dados do usuário logado
function getUserData() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'type' => $_SESSION['user_type'],
        'name' => $_SESSION['user_name']
    ];
}

// Obter Idescritor a partir do Idconta
function getIdEscritor($pdo, $idconta) {
    $stmt = $pdo->prepare("SELECT Idescritor FROM Escritor WHERE Idconta = ?");
    $stmt->execute([$idconta]);
    $escritor = $stmt->fetch();
    return $escritor ? $escritor['Idescritor'] : null;
}
?>
