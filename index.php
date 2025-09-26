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
                        <!-- logo + titles (keep your existing markup here) -->
                        <!-- example placeholder — replace with your markup -->
                        <!-- <img src="assets/elite-logo.png" alt="Elite Team logo" class="site-logo"> -->
                        <!-- <div class="site-titles"><h1>TechWeek 2025</h1><p class="subtitle">Event X</p></div> -->
                    </div>

                    <div class="brand-right">
                    <!-- date pill (short version shows on mobile) -->
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
                                                    <button class="button-presenter"
                                                            data-biography="<?php echo htmlspecialchars($event['biography']); ?>"
                                                            data-subtitle="<?php echo htmlspecialchars($event['profession'] ?? ''); ?>"
                                                            data-photo="<?php echo htmlspecialchars($event['photo'] ?? ''); ?>"
                                                            data-linkedin="<?php echo htmlspecialchars($event['linkedin'] ?? ''); ?>">
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
        <div id="myModal" class="modal" aria-hidden="true" role="dialog" aria-labelledby="modalSpeakerName">
            <div class="modal-card" role="document">
                <button id="closeModal" class="close-button" aria-label="Close">&times;</button>

                <section class="modal-body modal-body-centered">
                    <div class="modal-avatar-large-wrap">
                        <img id="modalAvatar" src="" alt="Speaker photo" class="modal-avatar-large" />
                    </div>

                    <div class="modal-divider" aria-hidden="true"></div>

                    <!-- About content (formatted paragraphs) -->
                    <div id="modalContent" class="modal-about"></div>

                    <div class="modal-actions" style="margin-top: 18px;">
                        <a id="modalLinkedIn" class="linkedin-btn" href="#" target="_blank" rel="noopener" hidden>View on LinkedIn</a>
                    </div>
                </section>
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
/* Header branding + date pill layout (flex, wrap) */
.header-brand{
    display: flex;
    align-items: center;
    justify-content: space-between; /* keeps brand-left and pill aligned */
    gap: 12px;
    flex-wrap: wrap; /* allows wrapping on narrow screens */
    /* container sizing handled by header parent */
    min-width: 0;
}

/* left: logo + titles (keep your existing markup inside this block) */
.brand-left{
    min-width: 0; /* allows titles to wrap */
    word-break: normal; /* enable soft wrapping for long titles */
}

/* right: align the date pill with branding */
.brand-right{
    display: flex;
    align-items: center;
    /* do not use margin-inline-start:auto here; .header-brand handles layout */
}

/* date pill */
.tw-dates{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding-block: 10px;    /* vertical padding */
    padding-inline: 12px;   /* horizontal padding */
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
    /* ensure visible focus for accessibility */
    transition: background .12s ease, box-shadow .12s ease, transform .06s ease;
}

/* hide short version by default (desktop/tablet) */
.tw-dates__short{ display: none; }

/* hover & focus — use :focus-visible for keyboard focus */
.tw-dates:hover,
.tw-dates:focus,
.tw-dates:focus-visible{
    background: #0E3A70; /* subtle brighten on hover/focus */
    box-shadow: 0 0 0 3px rgba(11,42,91,.18);
    outline: none;
    transform: translateY(0);
}

/* small accessibility tweak: show focus ring only for keyboard focus */
.tw-dates:focus:not(:focus-visible){
    box-shadow: inset 0 1px 0 rgba(255,255,255,.02);
}

/* Breakpoints for the pill */
@media (min-width: 992px){
    .tw-dates{ font-size: 16px; padding-block:10px; padding-inline:14px; }
}

@media (min-width: 576px) and (max-width: 991px){
    .tw-dates{ font-size: 15px; padding-block:9px; padding-inline:12px; }
}

/* Mobile: pill wraps, centers, and shows short version */
@media (max-width: 575.98px){
    .header-brand{ gap: 8px; }

    /* force order so brand-left appears first and pill wraps below */
    .brand-left{ order: 1; width: 100%; min-width: 0; }
    .brand-right{ order: 2; width: 100%; display:flex; justify-content:center; }

    .tw-dates{
        font-size: 14px;
        padding-inline: 10px;
        padding-block: 8px;
        white-space: nowrap; /* keep pill compact; remove to allow wrap */
        max-width: calc(100% - 24px);
        box-sizing: border-box;
    }

    /* hide long version on mobile and show short version */
    .tw-dates__full{ display: none; }
    .tw-dates__short{ display: inline; }
}

/* clear focus style for browsers */
.tw-dates:focus-visible{
    outline: none;
    box-shadow: 0 0 0 4px rgba(11,42,91,.18);
    border-color: rgba(255,255,255,.22);
}

/* allow graceful wrapping for long titles */
.brand-left h1, .brand-left .site-titles {
    word-break: normal;
    overflow-wrap: break-word;
    hyphens: auto;
}
</style>