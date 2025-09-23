<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Search</title>
    <link rel="stylesheet" href="assets/css/searchbar.css">
</head>

<body>
    <h1>Find Events</h1>

    <div id="search-bar-container"></div>
    <ul id="event-list">
        <?php
        $events = [
            ['title' => 'Opening Keynote', 'time' => '9:00 AM'],
            ['title' => 'React Deep Dive', 'time' => '11:00 AM'],
            ['title' => 'Networking Lunch', 'time' => '1:00 PM'],
        ];
        foreach ($events as $e) {
            echo "<li><strong>{$e['title']}</strong> — {$e['time']}</li>";
        }
        ?>
    </ul>

    <script src="assets/js/searchbar.js"></script>
    <script>
        initSearchBar({
            containerId: 'search-bar-container',
            placeholder: 'Search events...',
            onChange: function(query) {
                const items = document.querySelectorAll('#event-list li');
                items.forEach(li => {
                    const text = li.textContent.toLowerCase();
                    li.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
                });
            }
        });
    </script>
</body>

</html>