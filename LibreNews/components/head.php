<!-- ========================================
     LibreNews - HEAD SECTION
     Meta Tags, Fontes e Estilos
     ======================================== -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Meta Tags Básicas -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LibreNews - Seu portal de notícias sobre tecnologia, programação e inovação">
    <meta name="keywords" content="tecnologia, programação, notícias, desenvolvimento, software">
    <meta name="author" content="LibreNews">
    
    <!-- Título da Página -->
    <title><?php echo $pageTitle ?? 'LibreNews - Portal de Notícias Tech'; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@100;600;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Stylesheet Principal -->
    <link rel="stylesheet" href="/LibreNews/assets/css/style.css">

</head>
<body>
    <!-- ========================================
         SPINNER DE CARREGAMENTO
         ======================================== -->
    <div id="spinner" class="show w-100 vh-100 bg-dark position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- FIM SPINNER -->