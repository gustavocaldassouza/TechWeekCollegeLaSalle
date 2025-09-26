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

    return substr($formatted, 0, -1);
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
        </div>
        <div class="header-brand">
            <div class="brand-right">
                <div class="tw-dates"
                    role="note"
                    aria-label="TechWeek dates: September 29 to October 5, 2025"
                    tabindex="0">
                    <span class="tw-dates__full">Sep 29 – Oct 05, 2025</span>
                    <span class="tw-dates__short" aria-hidden="true">Sep 29 – Oct 5</span>
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
                <div class="modal-content">
                    <p id="modalContent"></p>
                </div>
                <button id="closeModal" class="close-button" aria-label="Close">Close</button>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Elite Team . All rights reserved.</p>
    </footer>
    <script src="script.js"></script>
</body>

</html>