<?php
/* ========================================
   LIBRENEWS - CONFIGURAÇÃO DO BANCO DE DADOS
   ======================================== */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'librenews');
define('DB_USER', 'root');
define('DB_PASS', ''); // Vazio no XAMPP

// Criar conexão com PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
?>
