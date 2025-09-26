<?php
function fetchTechWeekData()
{
    $url = 'https://zermoh.github.io/restapi/techweek_schedule.json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $json = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && $json !== false) {
        return json_decode($json, true);
    }

    $json = @file_get_contents($url);
    if ($json !== false) {
        return json_decode($json, true);
    }

    return null;
}

$data = fetchTechWeekData();
$eventDetails = $data['event']['event_detail'] ?? [];

// improve fetched json file to retrieve not only day but also month and year
// so we can remove the need for the datepicker
$dayNames = [
    'monday' => ['day' => 'MON', 'num' => '29', 'month' => 'SEP'],
    'tuesday' => ['day' => 'TUE', 'num' => '30', 'month' => 'SEP'],
    'wednesday' => ['day' => 'WED', 'num' => '01', 'month' => 'OCT'],
    'thursday' => ['day' => 'THU', 'num' => '02', 'month' => 'OCT'],
    'friday' => ['day' => 'FRI', 'num' => '03', 'month' => 'OCT'],
    'saturday' => ['day' => 'SAT', 'num' => '04', 'month' => 'OCT'],
    'sunday' => ['day' => 'SUN', 'num' => '05', 'month' => 'OCT']
];

function countEventsForDay($dayData)
{
    if (!is_array($dayData)) return 0;
    $count = 0;
    foreach ($dayData as $key => $value) {
        if (is_numeric($key)) $count++;
    }
    return $count;
}

function formatTime($time)
{
    if (empty($time)) return '';
    $time = trim($time);

    $time = str_replace(':', '', $time);
    $time = str_replace(' ', '', $time);
    $time = str_replace('-', '', $time);
    $time = str_pad($time, 8, '0', STR_PAD_LEFT);

    $groups = str_split($time, 4);
    $formatted = implode(' - ', array_map(function ($group) {
        return date('H:i', strtotime($group . '00')) . ' ';
    }, $groups));

    return substr($formatted, 0, -1); // remove trailing space
}

function getThemeEmoji($theme)
{
    $themes = [
        'Generative AI' => '🤖',
        'Animation' => '🎬',
        'Défis spatiaux' || 'Défis spetiaux' => '🚀',
        'Entrepreneuriat & l\'IA' => '💡',
        'Défis technologiques du futur' => '⚡'
    ];
    return $themes[$theme] ?? '📅';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechWeek 2025 - LaSalle College Montreal</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <header class="header">
        <div class="header-content">
            <div class="logo-container">
                <img src="logo.png" alt="Logo" class="logo">
            </div>
            <div>
                <h1>TechWeek 2025</h1>
                <p>LaSalle College Montreal</p>
            </div>

            <div class="header-brand">
                <div class="brand-left">
                    <!-- ...existing logo + títulos (copiar o conteúdo atual aqui) ... -->
                    <!-- Exemplo de placeholder (remova/replace pelo seu markup atual): -->
                    <!-- <img src="assets/elite-logo.png" alt="Elite Team logo" class="site-logo"> -->
                    <!-- <div class="site-titles"><h1>TechWeek 2025</h1><p class="subtitle">Evento X</p></div> -->
                </div>

                <div class="brand-right">
                    <!-- Pill de datas (inglês - versão curta aparece no mobile) -->
                    <div class="tw-dates"
                         role="note"
                         aria-label="TechWeek dates: September 29 to October 5, 2025"
                         tabindex="0">
                      <span class="tw-dates__full">Sep 29 – Oct 05, 2025</span>
                      <span class="tw-dates__short" aria-hidden="true">Sep 29 – Oct 5</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="search-container">
            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Search events, locations, categories..." id="searchInput">
                <button class="cancel-btn" id="cancelBtn" style="display: none;">×</button>
            </div>
        </div>
        <div class="calendar-nav">
            <?php foreach ($dayNames as $dayKey => $dayInfo): ?>
                <?php
                $dayData = $eventDetails[$dayKey] ?? [];
                $eventCount = countEventsForDay($dayData);
                $isActive = $dayKey === 'monday' ? 'active-day' : '';
                ?>
                <div class="day-column" data-day="<?php echo $dayKey; ?>">
                    <div class="day-header"><?php echo $dayInfo['day']; ?></div>
                    <div class="day-number <?php echo $isActive; ?>"><?php echo $dayInfo['num']; ?></div>
                    <div class="month"><?php echo $dayInfo['month']; ?></div>
                    <div class="event-count"><?php echo $eventCount; ?> event<?php echo $eventCount != 1 ? 's' : ''; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="events-list">
            <?php if ($data && !empty($eventDetails)): ?>
                <?php foreach ($eventDetails as $dayKey => $dayData): ?>
                    <?php if (is_array($dayData)): ?>
                        <?php
                        $dayDisplay = ucfirst($dayKey);
                        $dayNum = $dayNames[$dayKey]['num'] ?? '';
                        $theme = $dayData['theme'] ?? '';
                        $themeEmoji = getThemeEmoji($theme);
                        ?>

                        <?php foreach ($dayData as $eventKey => $event): ?>
                            <?php if (is_numeric($eventKey) && is_array($event)): ?>
                                <div class="event-card" data-day="<?php echo $dayKey; ?>">
                                    <div class="event-time">
                                        <div class="day-label"><?php echo $dayNum; ?> <?php echo $dayDisplay; ?></div>
                                        <div class="time"><?php echo formatTime($event['time'] ?? ''); ?></div>
                                    </div>
                                    <div class="event-details">
                                        <div class="event-header">
                                            <h3 class="event-title">
                                                <?php echo $themeEmoji; ?>
                                                <?php echo htmlspecialchars($event['title'] ?? 'Untitled Event'); ?>
                                            </h3>
                                            <div class="event-actions">
                                                <button class="share-btn">↗</button>
                                            </div>
                                        </div>

                                        <?php if (!empty($event['location'])): ?>
                                            <div class="location">📍 <?php echo htmlspecialchars($event['location']); ?></div>
                                        <?php endif; ?>

                                        <?php if (!empty($event['presenter']) && $event['presenter'] !== '========' && $event['presenter'] !== 'Speaker: '): ?>
                                            <div class="presenter">👤
                                                <?php if (!empty($event['biography'])): ?>
                                                    <button class="button-presenter" data-biography="<?php echo htmlspecialchars($event['biography']); ?>">
                                                        <?php echo htmlspecialchars($event['presenter']); ?>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($event['language'])): ?>
                                            <div class="language">🌐 <?php echo htmlspecialchars($event['language']); ?></div>
                                        <?php endif; ?>

                                        <?php if (!empty($event['description'])): ?>
                                            <div class="event-description">
                                                <?php echo htmlspecialchars($event['description']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>


                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="error-message">
                    <h3>Unable to load event data</h3>
                    <p>Please check your internet connection and try again later.</p>
                </div>
            <?php endif; ?>
        </div>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span id="closeModal" class="close-button">&times;</span>
                <p id="modalContent"></p>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Elite Team . All rights reserved.</p>
    </footer>
    <script src="script.js"></script>
</body>

</html>

<style>
/* .header-brand: agrupa branding + pill de datas (flex, wrap) */
.header-brand{
  display: flex;
  align-items: center;
  justify-content: space-between; /* mantém brand-left e pill alinhados em linha */
  gap: 12px;
  flex-wrap: wrap; /* permite quebra no mobile */
  /* largura e alinhamento do grupo ficam a cargo do header pai; 
     se quiser centralizar/limitar o bloco global, ajuste o container do header. */
  min-width: 0; /* importante para evitar overflow de filhos com texto longo */
}

/* esquerda: logo + títulos (preservar markup atual dentro deste bloco) */
.brand-left{
  min-width: 0; /* permite que títulos quebrem corretamente */
  word-break: normal; /* permite quebra suave se o título for muito longo */
}

/* direita: container para alinhar o pill junto ao branding */
.brand-right{
  display: flex;
  align-items: center;
  /* não usamos margin-inline-start:auto no pill; layout controlado pelo .header-brand */
}

/* pill de datas */
.tw-dates{
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding-block: 10px;    /* padding vertical */
  padding-inline: 12px;   /* padding horizontal */
  border-radius: 8px;
  background: #0B2A5B;
  color: #FFF;
  font-weight: 600;
  line-height: 1;
  border: 1px solid rgba(255,255,255,.15);
  box-shadow: inset 0 1px 0 rgba(255,255,255,.02);
  white-space: nowrap;
  max-width: 100%;
  -webkit-font-smoothing:antialiased;
  /* garante que o elemento tenha foco visível e acessível */
  transition: background .12s ease, box-shadow .12s ease, transform .06s ease;
}

/* ocultar versão curta por padrão (desktop/tablet) */
.tw-dates__short{ display: none; }

/* hover e foco — foco visível usando :focus-visible */
.tw-dates:hover,
.tw-dates:focus,
.tw-dates:focus-visible{
  background: #0E3A70; /* ligeiro claro ao hover/focus */
  box-shadow: 0 0 0 3px rgba(11,42,91,.18);
  outline: none;
  transform: translateY(0);
}

/* pequenas melhorias de acessibilidade visual: focus ring apenas quando for foco por teclado */
.tw-dates:focus:not(:focus-visible){
  box-shadow: inset 0 1px 0 rgba(255,255,255,.02); /* mantém aparência quando clicado com mouse */
}

/* Tipografia / breakpoints */

/* Desktop (>=992px) */
@media (min-width: 992px){
  .tw-dates{ font-size: 16px; padding-block:10px; padding-inline:14px; }
}

/* Tablet (>=576px && <=991px) */
@media (min-width: 576px) and (max-width: 991px){
  .tw-dates{ font-size: 15px; padding-block:9px; padding-inline:12px; }
}

/* Mobile (<=575.98px) — pill quebra para linha de baixo, centraliza e mostra versão curta */
@media (max-width: 575.98px){
  .header-brand{ gap: 8px; }

  /* forçamos a ordem para que o brand-left apareça primeiro e o pill quebre abaixo */
  .brand-left{ order: 1; width: 100%; min-width: 0; }
  .brand-right{ order: 2; width: 100%; display:flex; justify-content:center; }

  .tw-dates{
    font-size: 14px;
    padding-inline: 10px;
    padding-block: 8px;
    white-space: nowrap; /* mantém o pill compacto; se preferir permitir quebra, remova */
    max-width: calc(100% - 24px); /* garante margem visual com padding do header */
    box-sizing: border-box;
  }

  /* no mobile, esconder a versão longa e exibir a curta */
  .tw-dates__full{ display: none; }
  .tw-dates__short{ display: inline; }
}

/* foco claro para navegadores */
.tw-dates:focus-visible{
  outline: none;
  box-shadow: 0 0 0 4px rgba(11,42,91,.18);
  border-color: rgba(255,255,255,.22);
}

/* caso o título fique muito longo, permitir quebra controlada (não cortar palavras) */
.brand-left h1, .brand-left .site-titles {
  word-break: normal;
  overflow-wrap: break-word;
  hyphens: auto;
}
</style>