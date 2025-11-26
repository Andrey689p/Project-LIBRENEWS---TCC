<?php 
$pageTitle = "Privacidade e Termos de Uso - LibreNews";
include 'components/head.php'; 
include 'components/navbar.php';
?>

<!-- ========================================
     PÁGINA DE PRIVACIDADE E TERMOS DE USO
     ======================================== -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #020617 100%);">
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="logo-icon-modern mx-auto mb-4 rounded-circle" style="width: 80px; height: 80px; font-size: 40px;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h1 class="display-5 fw-bold mb-3 text-white">
                    Privacidade e Termos de Uso
                </h1>
                <p class="lead text-secondary mb-4">
                    Transparência e segurança nas suas informações
                </p>
                <p class="text-secondary small">
                    <i class="bi bi-calendar3 me-2"></i>Última atualização: <?php echo date('d/m/Y'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- ========================================
     CONTEÚDO PRINCIPAL
     ======================================== -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Navegação Rápida -->
            <div class="card border-0 bg-light mb-5 rounded-4 card-no-animation">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-list-ul text-primary me-2"></i>Navegação Rápida</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="#privacidade" class="text-decoration-none text-secondary d-block py-2">
                                <i class="bi bi-arrow-right text-primary me-2"></i>Política de Privacidade
                            </a>
                            <a href="#coleta" class="text-decoration-none text-secondary d-block py-2">
                                <i class="bi bi-arrow-right text-primary me-2"></i>Coleta de Dados
                            </a>
                            <a href="#cookies" class="text-decoration-none text-secondary d-block py-2">
                                <i class="bi bi-arrow-right text-primary me-2"></i>Cookies
                            </a>
                            <a href="#direitos" class="text-decoration-none text-secondary d-block py-2">
                                <i class="bi bi-arrow-right text-primary me-2"></i>Seus Direitos
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#termos" class="text-decoration-none text-secondary d-block py-2">
                                <i class="bi bi-arrow-right text-primary me-2"></i>Termos de Uso
                            </a>
                            <a href="#responsabilidade" class="text-decoration-none text-secondary d-block py-2">
                                <i class="bi bi-arrow-right text-primary me-2"></i>Limitação de Responsabilidade
                            </a>
                            <a href="#modificacoes" class="text-decoration-none text-secondary d-block py-2">
                                <i class="bi bi-arrow-right text-primary me-2"></i>Alterações nos Termos
                            </a>
                            <a href="#contato" class="text-decoration-none text-secondary d-block py-2">
                                <i class="bi bi-arrow-right text-primary me-2"></i>Fale Conosco
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================
                 POLÍTICA DE PRIVACIDADE
                 ======================================== -->
            <div id="privacidade" class="mb-5 pb-5">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-shield-lock text-primary fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Política de Privacidade</h2>
                        <small class="text-secondary">Como tratamos seus dados pessoais</small>
                    </div>
                </div>

                <div class="content-section">
                    <h4 class="mb-3">Introdução</h4>
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        O LibreNews valoriza e respeita a privacidade de todos os seus usuários. Esta Política de Privacidade tem como objetivo esclarecer de forma transparente e objetiva como coletamos, utilizamos, armazenamos, compartilhamos e protegemos as informações pessoais dos visitantes e usuários do nosso portal de notícias. Estamos comprometidos em cumprir integralmente as disposições da Lei Geral de Proteção de Dados Pessoais (LGPD - Lei nº 13.709/2018) e demais legislações aplicáveis sobre proteção de dados e privacidade.
                    </p>

                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Ao utilizar o LibreNews, você declara ter lido, compreendido e concordado com os termos desta Política de Privacidade. Caso não concorde com qualquer aspecto desta política, recomendamos que não utilize nossos serviços. Esta política aplica-se a todos os usuários do LibreNews, incluindo visitantes, leitores cadastrados, escritores e qualquer pessoa que interaja com nossa plataforma.
                    </p>
                </div>
            </div>

            <!-- ========================================
                 COLETA DE DADOS
                 ======================================== -->
            <div id="coleta" class="mb-5 pb-5">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-database text-primary fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Coleta de Dados</h2>
                        <small class="text-secondary">Quais informações coletamos</small>
                    </div>
                </div>

                <div class="content-section">
                    <h4 class="mb-3">Dados Fornecidos por Você</h4>
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Coletamos informações pessoais que você nos fornece voluntariamente ao utilizar determinadas funcionalidades do LibreNews. Isso inclui, mas não se limita a: nome completo, endereço de e-mail, informações profissionais e dados fornecidos em formulários de contato ou candidatura. Quando você se inscreve em nossa newsletter, fornecemos seu endereço de e-mail para que possamos enviar atualizações semanais sobre as principais notícias de tecnologia. Ao se candidatar para fazer parte da equipe de escritores, coletamos informações adicionais como experiência profissional, habilidades técnicas, portfólio e links para redes sociais profissionais.
                    </p>

                    <h4 class="mb-3 mt-5">Dados Coletados Automaticamente</h4>
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Quando você acessa o LibreNews, coletamos automaticamente certas informações técnicas sobre seu dispositivo e navegação. Isso inclui endereço IP, tipo de navegador, sistema operacional, páginas visitadas, tempo de permanência em cada página, origem do acesso, data e hora de acesso, e identificadores únicos de dispositivo. Essas informações são coletadas por meio de cookies, web beacons e tecnologias similares, e são utilizadas principalmente para melhorar a experiência do usuário, realizar análises estatísticas e detectar atividades suspeitas ou fraudulentas.
                    </p>

                    <h4 class="mb-3 mt-5">Finalidades da Coleta</h4>
                    <p class="text-secondary mb-3" style="text-align: justify; line-height: 1.8;">
                        Os dados pessoais coletados são utilizados para as seguintes finalidades específicas:
                    </p>
                    <ul class="text-secondary mb-4" style="line-height: 1.8;">
                        <li class="mb-2">Fornecer, manter e melhorar nossos serviços de portal de notícias</li>
                        <li class="mb-2">Enviar newsletters e comunicações sobre conteúdos relevantes</li>
                        <li class="mb-2">Processar candidaturas de escritores e gerenciar a equipe editorial</li>
                        <li class="mb-2">Responder a solicitações, perguntas e comentários dos usuários</li>
                        <li class="mb-2">Realizar análises estatísticas e métricas de audiência</li>
                        <li class="mb-2">Personalizar conteúdo e experiência do usuário</li>
                        <li class="mb-2">Prevenir fraudes, abusos e violações dos Termos de Uso</li>
                        <li class="mb-2">Cumprir obrigações legais e regulatórias</li>
                    </ul>
                </div>
            </div>

            <!-- ========================================
                 COOKIES
                 ======================================== -->
            <div id="cookies" class="mb-5 pb-5">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-gear text-primary fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Uso de Cookies</h2>
                        <small class="text-secondary">Tecnologias de rastreamento</small>
                    </div>
                </div>

                <div class="content-section">
                    <h4 class="mb-3">O que são Cookies</h4>
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Cookies são pequenos arquivos de texto armazenados no seu navegador quando você visita um site. Eles permitem que o site reconheça seu dispositivo e armazene informações sobre suas preferências e atividades.
                    </p>

                    <h4 class="mb-3 mt-5">Gerenciamento de Cookies</h4>
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Você pode gerenciar ou desativar cookies através das configurações do seu navegador. No entanto, observe que a desativação de cookies essenciais pode afetar a funcionalidade do site. Para desativar cookies analíticos e de funcionalidade, acesse as configurações de privacidade do seu navegador. Lembre-se de que suas preferências de cookies precisam ser configuradas em cada navegador e dispositivo que você utiliza.
                    </p>
                </div>
            </div>

            <!-- ========================================
                 DIREITOS DOS USUÁRIOS
                 ======================================== -->
            <div id="direitos" class="mb-5 pb-5">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-person-check text-primary fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Seus Direitos</h2>
                        <small class="text-secondary">Garantidos pela LGPD</small>
                    </div>
                </div>

                <div class="content-section">
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        De acordo com a Lei Geral de Proteção de Dados (LGPD), você possui os seguintes direitos em relação aos seus dados pessoais:
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 rounded-4 card-no-animation">
                                <div class="card-body p-4">
                                    <h6 class="mb-2"><i class="bi bi-eye text-primary me-2"></i>Direito de Acesso</h6>
                                    <small class="text-secondary">Confirmar se tratamos seus dados e solicitar uma cópia das informações que mantemos sobre você.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 rounded-4 card-no-animation">
                                <div class="card-body p-4">
                                    <h6 class="mb-2"><i class="bi bi-pencil text-primary me-2"></i>Direito de Correção</h6>
                                    <small class="text-secondary">Solicitar a correção de dados incompletos, inexatos ou desatualizados.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 rounded-4 card-no-animation">
                                <div class="card-body p-4">
                                    <h6 class="mb-2"><i class="bi bi-trash text-primary me-2"></i>Direito de Eliminação</h6>
                                    <small class="text-secondary">Solicitar a exclusão de dados tratados com seu consentimento.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 rounded-4 card-no-animation">
                                <div class="card-body p-4">
                                    <h6 class="mb-2"><i class="bi bi-x-circle text-primary me-2"></i>Direito de Oposição</h6>
                                    <small class="text-secondary">Opor-se ao tratamento de dados realizado sem seu consentimento.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 rounded-4 card-no-animation">
                                <div class="card-body p-4">
                                    <h6 class="mb-2"><i class="bi bi-download text-primary me-2"></i>Direito de Portabilidade</h6>
                                    <small class="text-secondary">Solicitar a portabilidade dos seus dados para outro fornecedor de serviço.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 rounded-4 card-no-animation">
                                <div class="card-body p-4">
                                    <h6 class="mb-2"><i class="bi bi-hand-thumbs-down text-primary me-2"></i>Revogação do Consentimento</h6>
                                    <small class="text-secondary">Revogar seu consentimento a qualquer momento.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Para exercer qualquer um desses direitos, entre em contato conosco através do e-mail contato@librenews.com.br. Responderemos sua solicitação em até 15 dias úteis, conforme estabelecido pela legislação vigente.
                    </p>
                </div>
            </div>

            <!-- ========================================
                 TERMOS DE USO
                 ======================================== -->
            <div id="termos" class="mb-5 pb-5">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-file-text text-primary fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Termos de Uso</h2>
                        <small class="text-secondary">Regras de utilização da plataforma</small>
                    </div>
                </div>

                <div class="content-section">
                    <h4 class="mb-3">Aceitação dos Termos</h4>
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Ao acessar e utilizar o LibreNews, você concorda em cumprir e estar vinculado a estes Termos de Uso. Se você não concorda com qualquer parte destes termos, não deve utilizar nosso portal. O LibreNews é um portal de notícias dedicado à comunidade de profissionais de Tecnologia da Informação, oferecendo conteúdo técnico especializado, análises de tendências tecnológicas e informações atualizadas sobre o mercado de TI. O uso dos nossos serviços está condicionado à aceitação destes termos.
                    </p>

                    <h4 class="mb-3 mt-5">Uso Adequado da Plataforma</h4>
                    <p class="text-secondary mb-3" style="text-align: justify; line-height: 1.8;">
                        Ao utilizar o LibreNews, você concorda em NÃO:
                    </p>
                    <ul class="text-secondary mb-4" style="line-height: 1.8;">
                        <li class="mb-2">Publicar, transmitir ou distribuir conteúdo ilegal, ofensivo, difamatório, obsceno ou que viole direitos de terceiros</li>
                        <li class="mb-2">Utilizar o portal para fins comerciais não autorizados ou atividades de spam</li>
                        <li class="mb-2">Tentar obter acesso não autorizado a sistemas, contas ou redes conectadas ao LibreNews</li>
                        <li class="mb-2">Interferir no funcionamento adequado da plataforma ou servidores</li>
                        <li class="mb-2">Coletar informações de outros usuários sem consentimento</li>
                        <li class="mb-2">Reproduzir, duplicar, copiar ou revender qualquer parte do LibreNews sem autorização expressa</li>
                        <li class="mb-2">Utilizar mecanismos automatizados para acessar o portal sem nossa permissão</li>
                        <li class="mb-2">Fazer-se passar por outra pessoa ou entidade</li>
                    </ul>

                    <h4 class="mb-3 mt-5">Conteúdo do Usuário</h4>
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Se você é um escritor aprovado e publica conteúdo no LibreNews, você garante que possui todos os direitos necessários sobre o conteúdo submetido e que este não viola direitos de terceiros. Você concede ao LibreNews uma licença mundial, não exclusiva, transferível e isenta de royalties para usar, reproduzir, modificar, adaptar, publicar e distribuir o conteúdo submetido. O LibreNews reserva-se o direito de remover qualquer conteúdo que viole estes Termos de Uso ou que seja considerado inadequado, a nosso exclusivo critério.
                    </p>
                </div>
            </div>

            <!-- ========================================
                 LIMITAÇÃO DE RESPONSABILIDADE
                 ======================================== -->
            <div id="responsabilidade" class="mb-5 pb-5">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-exclamation-triangle text-primary fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Limitação de Responsabilidade</h2>
                        <small class="text-secondary">Isenções e limitações</small>
                    </div>
                </div>

                <div class="content-section">
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        O LibreNews fornece o portal e todo o conteúdo "como está" e "conforme disponível", sem garantias de qualquer tipo, expressas ou implícitas. Não garantimos que o portal estará sempre disponível, livre de erros ou livre de vírus ou outros componentes prejudiciais. Embora nos esforcemos para fornecer informações precisas e atualizadas, não garantimos a exatidão, completude ou atualidade do conteúdo publicado.
                    </p>

                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        O LibreNews não será responsável por quaisquer danos diretos, indiretos, incidentais, consequenciais ou punitivos decorrentes do seu acesso ou uso (ou incapacidade de acessar ou usar) o portal. Isso inclui, mas não se limita a, danos por perda de lucros, uso, dados ou outras perdas intangíveis, mesmo que tenhamos sido avisados da possibilidade de tais danos.
                    </p>

                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        O LibreNews pode conter links para sites de terceiros que não são de nossa propriedade ou controle. Não temos controle sobre, e não assumimos responsabilidade pelo conteúdo, políticas de privacidade ou práticas de quaisquer sites de terceiros. Você reconhece e concorda que o LibreNews não será responsável, direta ou indiretamente, por qualquer dano ou perda causados ou alegadamente causados por ou em conexão com o uso ou confiança em tal conteúdo, bens ou serviços disponíveis em ou através de tais sites.
                    </p>
                </div>
            </div>

            <!-- ========================================
                 MODIFICAÇÕES
                 ======================================== -->
            <div id="modificacoes" class="mb-5 pb-5">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-arrow-repeat text-primary fs-3"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Alterações nos Termos</h2>
                        <small class="text-secondary">Atualizações e notificações</small>
                    </div>
                </div>

                <div class="content-section">
                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        O LibreNews reserva-se o direito de modificar esta Política de Privacidade e os Termos de Uso a qualquer momento. Quaisquer alterações entrarão em vigor imediatamente após a publicação da versão atualizada nesta página. Recomendamos que você revise periodicamente esta página para se manter informado sobre como estamos protegendo suas informações e quais são suas obrigações ao usar nossos serviços.
                    </p>

                    <p class="text-secondary mb-4" style="text-align: justify; line-height: 1.8;">
                        Em caso de alterações substanciais que afetem significativamente seus direitos ou a forma como tratamos seus dados, notificaremos você por e-mail (se você forneceu seu endereço de e-mail) ou através de um aviso proeminente em nosso portal, antes que a alteração entre em vigor. Seu uso continuado do LibreNews após a publicação de alterações constitui sua aceitação dessas alterações.
                    </p>
                </div>
            </div>

            <!-- ========================================
                 CONTATO
                 ======================================== -->
            <div id="contato" class="mb-5">
                <div class="card border-0 rounded-4 card-no-animation" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #020617 100%);">
                    <div class="card-body p-5 text-center">
                        <div class="logo-icon-modern mx-auto mb-4 rounded-circle" style="width: 70px; height: 70px; font-size: 35px;">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h3 class="mb-3 text-white">Fale Conosco</h3>
                        <p class="text-secondary mb-4">
                            Tem dúvidas sobre privacidade ou termos de uso? Entre em contato conosco.
                        </p>
                        <div class="d-flex flex-column align-items-center gap-3">
                            <div>
                                <i class="bi bi-envelope text-primary me-2"></i>
                                <a href="mailto:contato@librenews.com.br" class="text-primary text-decoration-none">
                                    contato@librenews.com.br
                                </a>
                            </div>
                            <div>
                                <i class="bi bi-shield-check text-primary me-2"></i>
                                <span class="text-secondary">Encarregado de Dados (DPO): privacidade@librenews.com.br</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- FIM PÁGINA DE PRIVACIDADE -->

<style>
/* Remover todas as animações e hovers dos cards nesta página */
.card-no-animation,
.card-no-animation:hover {
    transition: none !important;
    transform: none !important;
    box-shadow: none !important;
}
</style>

<?php include 'components/footer.php'; ?>