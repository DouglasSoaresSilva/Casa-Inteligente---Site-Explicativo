<?php
/**
 * index.php — Casa Inteligente
 * Projeto acadêmico de Sistemas Embarcados (ESP32-S3 + Blynk)
 *
 * Lógica PHP:
 * - Processa o formulário de contato/feedback via POST (validação simples server-side).
 * - Usa um array associativo para centralizar os dados de hardware, evitando repetição de HTML.
 */

// ---------------------------------------------------------------
// 1. Processamento do formulário de feedback
// ---------------------------------------------------------------
$feedback_enviado = false;
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_feedback'])) {
    $nome = trim(htmlspecialchars($_POST['nome'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $mensagem = trim(htmlspecialchars($_POST['mensagem'] ?? ''));

    if (empty($nome) || empty($email) || empty($mensagem)) {
        $erro = 'Preencha todos os campos antes de enviar.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } else {
        // Em produção, aqui entraria o envio por e-mail (mail()/PHPMailer) ou gravação em banco.
        // Para o protótipo acadêmico, apenas confirmamos o recebimento.
        $feedback_enviado = true;
    }
}

// ---------------------------------------------------------------
// 2. Dados do hardware centralizados (evita repetir HTML na seção de componentes)
// ---------------------------------------------------------------
$componentes = [
    [
        'nome' => 'ESP32-S3 (N16R8)',
        'funcao' => 'Microcontrolador principal',
        'detalhe' => '16MB Flash / 8MB PSRAM, Wi-Fi integrado para comunicação com o Blynk IoT.'
    ],
    [
        'nome' => 'Módulo RFID RC522',
        'funcao' => 'Leitor de aproximação',
        'detalhe' => 'Protocolo SPI. Único meio físico de desarmar o alarme por tag cadastrada.'
    ],
    [
        'nome' => 'Sensor PIR HC-SR501',
        'funcao' => 'Detector de presença',
        'detalhe' => 'Dispara o alarme por movimento somente quando o sistema está armado.'
    ],
    [
        'nome' => 'Fotoresistor (LDR)',
        'funcao' => 'Sensor de luminosidade',
        'detalhe' => 'Aciona automaticamente o poste externo quando o ambiente escurece.'
    ],
    [
        'nome' => 'Buzzer',
        'funcao' => 'Atuador sonoro',
        'detalhe' => 'Emite o alerta sonoro do alarme quando o PIR é disparado com o sistema armado.'
    ],
    [
        'nome' => 'LEDs por cômodo',
        'funcao' => 'Atuadores de iluminação',
        'detalhe' => 'Sala, Quarto 1, Quarto 2, Banheiro e Cozinha — controlados individualmente via app.'
    ],
];

// ---------------------------------------------------------------
// 3. Pinos virtuais do Blynk centralizados
// ---------------------------------------------------------------
$pinos_blynk = [
    ['pino' => 'V0', 'funcao' => 'Armar / desarmar o alarme (somente leitura pelo app — desarme físico apenas)'],
    ['pino' => 'V2', 'funcao' => 'Senha para desarme alternativo ao RFID'],
    ['pino' => 'V3', 'funcao' => 'Slider de brilho geral (PWM)'],
    ['pino' => 'V4', 'funcao' => 'LED — Sala'],
    ['pino' => 'V5', 'funcao' => 'LED — Quarto 1'],
    ['pino' => 'V6', 'funcao' => 'LED — Quarto 2'],
    ['pino' => 'V7', 'funcao' => 'LED — Banheiro'],
    ['pino' => 'V8', 'funcao' => 'LED — Cozinha'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Casa Inteligente — Projeto de Sistemas Embarcados</title>
<meta name="description" content="Protótipo acadêmico de automação e segurança residencial com ESP32-S3, Blynk, RFID e PIR.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ===================== HEADER / NAV ===================== -->
<header class="header" id="header">
    <nav class="navbar">
        <a href="#hero" class="logo">
            <span class="logo-icon">⌁</span> Casa<span class="accent">Inteligente</span>
        </a>

        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-links" id="navLinks">
            <li><a href="#hero" class="nav-link">Início</a></li>
            <li><a href="#diagrama" class="nav-link">Diagrama</a></li>
            <li><a href="#funcionalidades" class="nav-link">Funcionalidades</a></li>
            <li><a href="#hardware" class="nav-link">Hardware</a></li>
            <li><a href="#seguranca" class="nav-link">Segurança</a></li>
            <li><a href="#contato" class="nav-link">Contato</a></li>
        </ul>

        <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
            <span id="themeIcon">☾</span>
        </button>
    </nav>
</header>

<main>

<!-- ===================== HERO ===================== -->
<section class="hero" id="hero">
    <div class="hero-grid-bg" aria-hidden="true"></div>
    <div class="container hero-content">
        <span class="badge">Sistemas Embarcados · ESP32-S3</span>
        <h1 class="hero-title">
            Casa <span class="accent">Inteligente</span>
        </h1>
        <p class="hero-subtitle">
            Protótipo de automação e segurança residencial controlado via ESP32-S3 e aplicativo Blynk.
            Iluminação por cômodos, brilho ajustável, poste automático por luminosidade e alarme com
            desarme exclusivo por RFID ou senha.
        </p>
        <div class="hero-actions">
            <a href="#funcionalidades" class="btn btn-primary">Ver funcionalidades</a>
            <a href="#hardware" class="btn btn-ghost">Ver hardware</a>
        </div>

        <div class="hero-stats">
            <div class="stat">
                <span class="stat-number">5</span>
                <span class="stat-label">Cômodos controlados</span>
            </div>
            <div class="stat">
                <span class="stat-number">8</span>
                <span class="stat-label">Pinos virtuais Blynk</span>
            </div>
            <div class="stat">
                <span class="stat-number">100%</span>
                <span class="stat-label">Desarme físico seguro</span>
            </div>
        </div>
    </div>
</section>

<!-- ===================== DIAGRAMA ELETRÔNICO ===================== -->
<section class="section" id="diagrama">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Montagem física</span>
            <h2>Diagrama eletrônico</h2>
        </div>

        <figure class="diagram-figure">
            <div class="diagram-frame">
                <img src="diagrama-eletronico.png" alt="Diagrama de ligações do circuito: ESP32-S3, módulo RFID RC522, sensor PIR, buzzer e bateria 9V montados em protoboard" loading="lazy">
            </div>
            <figcaption>
                Esquema de ligação do protótipo: ESP32-S3, leitor RFID RC522, sensor PIR, buzzer e fonte de 9V montados em protoboard.
            </figcaption>
        </figure>
    </div>
</section>

<!-- ===================== FUNCIONALIDADES ===================== -->
<section class="section section-alt" id="funcionalidades">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">O que o sistema faz</span>
            <h2>Funcionalidades</h2>
        </div>

        <div class="cards-grid">
            <article class="card">
                <div class="card-icon">💡</div>
                <h3>Iluminação por cômodo</h3>
                <p>Controle individual dos LEDs de Sala, Quarto 1, Quarto 2, Banheiro e Cozinha diretamente pelo app Blynk.</p>
            </article>

            <article class="card">
                <div class="card-icon">🎚️</div>
                <h3>Brilho geral (PWM)</h3>
                <p>Slider no app ajusta o brilho de todos os LEDs via sinal PWM gerado pelo ESP32-S3.</p>
            </article>

            <article class="card">
                <div class="card-icon">🌙</div>
                <h3>Poste automático</h3>
                <p>Fotoresistor (LDR) detecta a queda de luminosidade e aciona o poste externo automaticamente, sem intervenção manual.</p>
            </article>

            <article class="card">
                <div class="card-icon">🛡️</div>
                <h3>Alarme armado por app</h3>
                <p>O sistema de alarme é armado remotamente pelo Blynk, ativando o sensor PIR para detecção de movimento.</p>
            </article>

            <article class="card">
                <div class="card-icon">🔑</div>
                <h3>Desarme por RFID</h3>
                <p>Aproximar uma tag cadastrada no leitor RC522 é uma das duas únicas formas de desarmar o alarme.</p>
            </article>

            <article class="card">
                <div class="card-icon">🔢</div>
                <h3>Desarme por senha</h3>
                <p>Alternativa física ao RFID: uma senha cadastrada no pino V2 também desarma o sistema localmente.</p>
            </article>
        </div>
    </div>
</section>

<!-- ===================== HARDWARE ===================== -->
<section class="section" id="hardware">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Arquitetura física</span>
            <h2>Hardware utilizado</h2>
        </div>

        <div class="table-wrapper">
            <table class="hw-table">
                <thead>
                    <tr>
                        <th>Componente</th>
                        <th>Função</th>
                        <th>Detalhe técnico</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($componentes as $item): ?>
                    <tr>
                        <td data-label="Componente"><strong><?= $item['nome'] ?></strong></td>
                        <td data-label="Função"><?= $item['funcao'] ?></td>
                        <td data-label="Detalhe"><?= $item['detalhe'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3 class="subsection-title">Pinos virtuais do Blynk</h3>
        <div class="table-wrapper">
            <table class="hw-table pins-table">
                <thead>
                    <tr>
                        <th>Pino virtual</th>
                        <th>Função</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pinos_blynk as $pino): ?>
                    <tr>
                        <td data-label="Pino"><span class="pin-tag"><?= $pino['pino'] ?></span></td>
                        <td data-label="Função"><?= $pino['funcao'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ===================== LÓGICA / SEGURANÇA ===================== -->
<section class="section" id="seguranca">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Como funciona</span>
            <h2>Lógica de funcionamento e segurança</h2>
        </div>

        <div class="accordion" id="accordion">
            <div class="accordion-item open">
                <button class="accordion-header">
                    <span>1. Armar o sistema</span>
                    <span class="accordion-arrow">⌄</span>
                </button>
                <div class="accordion-panel">
                    <p>O usuário arma o alarme remotamente pelo app Blynk, através do pino virtual <span class="pin-tag">V0</span>. A partir desse momento, o sensor PIR HC-SR501 passa a monitorar movimento no ambiente.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-header">
                    <span>2. Detecção de movimento</span>
                    <span class="accordion-arrow">⌄</span>
                </button>
                <div class="accordion-panel">
                    <p>Se o PIR detectar movimento com o sistema armado, o ESP32-S3 aciona o buzzer imediatamente, disparando o alerta sonoro do alarme.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-header">
                    <span>3. Por que o app não desarma</span>
                    <span class="accordion-arrow">⌄</span>
                </button>
                <div class="accordion-panel">
                    <p>Por projeto, o alarme <strong>não pode ser desarmado pelo aplicativo</strong>. Essa decisão evita que alguém com acesso ao app (ou a um celular destravado) desative a segurança remotamente. O desarme exige presença física no local.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-header">
                    <span>4. Desarme físico</span>
                    <span class="accordion-arrow">⌄</span>
                </button>
                <div class="accordion-panel">
                    <p>O desarme só ocorre de duas formas: aproximando uma tag RFID cadastrada do leitor RC522, ou digitando a senha correspondente ao pino <span class="pin-tag">V2</span>. Qualquer uma das duas interrompe o buzzer e reseta o estado do alarme.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-header">
                    <span>5. Automação de iluminação</span>
                    <span class="accordion-arrow">⌄</span>
                </button>
                <div class="accordion-panel">
                    <p>Em paralelo à segurança, o LDR monitora a luminosidade ambiente e aciona o poste externo automaticamente quando escurece, independente do estado do alarme.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== CONTATO ===================== -->
<section class="section section-alt" id="contato">
    <div class="container contato-grid">
        <div class="contato-info">
            <span class="eyebrow">Créditos</span>
            <h2>Sobre o projeto</h2>
            <p>
                Projeto desenvolvido para a disciplina de <strong>Sistemas Embarcados</strong>,
                sob orientação do professor <strong>Carlos Alberto</strong>, como parte do curso
                de Desenvolvimento de Sistemas.
            </p>
            <p>
                Apresentado em feira tecnológica como demonstração prática de integração entre
                microcontroladores, sensores, atuadores e plataformas IoT.
            </p>
        </div>

        <div class="contato-form-wrapper">
            <h3>Deixe seu feedback</h3>

            <?php if ($feedback_enviado): ?>
                <div class="alert alert-success">Feedback enviado com sucesso. Obrigado!</div>
            <?php elseif ($erro): ?>
                <div class="alert alert-error"><?= $erro ?></div>
            <?php endif; ?>

            <form class="contato-form" method="POST" action="#contato">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Seu nome" value="<?= $nome ?? '' ?>">
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="seu@email.com" value="<?= $email ?? '' ?>">
                </div>
                <div class="form-group">
                    <label for="mensagem">Mensagem</label>
                    <textarea id="mensagem" name="mensagem" rows="4" placeholder="O que achou do projeto?"><?= $mensagem ?? '' ?></textarea>
                </div>
                <button type="submit" name="enviar_feedback" class="btn btn-primary btn-full">Enviar</button>
            </form>
        </div>
    </div>
</section>

</main>

<!-- ===================== FOOTER ===================== -->
<footer class="footer">
    <div class="container footer-content">
        <p>&copy; <?= date('Y') ?> Casa Inteligente — Projeto acadêmico de Sistemas Embarcados.</p>
        <p class="footer-sub">ESP32-S3 · Blynk · RFID RC522 · PIR HC-SR501</p>
    </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="Voltar ao topo">↑</button>

<script src="script.js"></script>
</body>
</html>
