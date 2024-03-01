<?php
// Function to generate a calendar for a specific month and year
function generateCalendar($month, $year) {
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDay = mktime(0, 0, 0, $month, 1, $year);
    $firstDayOfWeek = date('N', $firstDay);

    // Array of holidays (format: 'month-day' => 'Holiday Name')
    $holidays = [
        '01-01' => 'New Year\'s Day',
        '02-09' => 'Lunar New Year Holiday',
        '02-10' => 'Lunar New Year\'s Day',
        '02-25' => 'People Power Anniversary',
        '03-11' => 'Ramadans Start',
        '03-28' => 'Maundy Thursday',
        '03-29' => 'Good Friday',
        '03-30' => 'Black Saturday',
        '03-31' => 'Easter Sunday',
        '04-09' => 'The Day of Valor',
        '05-01' => 'Labour Day',
        '06-12' => 'Independence Day',
        '08-21' => 'Ninoy Aquino Day',
        '08-26' => 'National Heroes Day',
        '11-01' => 'All Saint\'s Day',
        '11-02' => 'All Souls Day',
        '11-30' => 'Bonifacio Day',
        '12-08' => 'Feast of the Immaculate Concepcion',
        '12-24' => 'Christmas Eve',
        '12-25' => 'Christmas Day',
        '12-30' => 'Rizal Day',
        '12-31' => 'New Year\'s Eve'
        ];
        $events = [
            '01-01' => 'New Year\'s Day',
            '03-04' => 'Teacher\'s Day'
        ];

        // Start building the table with modern design
        echo '<div style="font-family: Arial, sans-serif;">';
        echo '<table style="width: 100%; border-collapse: collapse; margin: 20px; background-color: #f0f0f0; border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
        echo '<caption style="font-size: 1.5em; padding: 10px; text-align: center; background-color: #3498db; color: #fff;">' . date('F Y', $firstDay) . '</caption>';
        echo '<tr style="background-color: #3498db; color: #fff;">
                <th style="padding: 10px;">Mon</th>
                <th style="padding: 10px;">Tue</th>
                <th style="padding: 10px;">Wed</th>
                <th style="padding: 10px;">Thu</th>
                <th style="padding: 10px;">Fri</th>
                <th style="padding: 10px;">Sat</th>
                <th style="padding: 10px;">Sun</th>
            </tr>';

        // Add empty cells for days before the first day of the month
        echo '<tr>';
        for ($i = 1; $i < $firstDayOfWeek; $i++) {
            echo '<td></td>';
        }

        // Loop through the days of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = sprintf('%02d-%02d', $month, $day);

            // Check if the current date is a holiday
            $isHoliday = isset($holidays[$currentDate]);
            $cellStyle = $isHoliday ? 'background-color: #ffc0cb;' : '';

            $isEvent = isset($events[$currentDate]);
            $cellStyleEvent = $isEvent ? 'background-color: #c0ebcc;' : '';

            echo '<td style="padding: 10px; text-align: center; font-weight: ' . ($isHoliday ? 'bold' : 'normal') . ';' . $cellStyle . $cellStyleEvent .'">';
            echo '<div style="font-size: 1.2em;">' . $day . '</div>';

            if ($isHoliday) {
                echo '<div style="font-size: 0.8em; color: #e44d26;">' . $holidays[$currentDate] . '</div>';
            }
            if ($isEvent) {
                echo '<div style="font-size: 0.8em; color: green;">' . $events[$currentDate] . '</div>';
            }
            echo '</td>';

            // Start a new row every 7 days (a week)
            if (($day + $firstDayOfWeek - 1) % 7 == 0) {
                echo '</tr><tr>';
            }
        }

        // Add empty cells for remaining days in the last week
        for ($i = ($day + $firstDayOfWeek - 1) % 7; $i < 7; $i++) {
            echo '<td></td>';
        }

        echo '</tr></table>';
        echo '</div>';
    }

    // Process the form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedMonth = (int)$_POST['month'];
        $selectedYear = (int)$_POST['year'];
    } else {
        // Default to the current month and year
        $selectedMonth = date('n');
        $selectedYear = date('Y');
    }

// Display the form with a dropdown for month and year
echo '<form method="post" style="text-align: center; margin: 20px;">';
echo '  <label for="month" style="margin-right: 10px;">Select Month:</label>';
echo '  <select name="month" id="month" style="padding: 5px;">';
for ($i = 1; $i <= 12; $i++) {
    echo '    <option value="' . $i . '" ' . ($selectedMonth == $i ? 'selected' : '') . '>' . date('F', mktime(0, 0, 0, $i, 1)) . '</option>';
}
echo '  </select>';

echo '  <label for="year" style="margin-right: 10px;">Select Year:</label>';
echo '  <select name="year" id="year" style="padding: 5px;">';
for ($i = date('Y') - 10; $i <= date('Y') + 10; $i++) {
    echo '    <option value="' . $i . '" ' . ($selectedYear == $i ? 'selected' : '') . '>' . $i . '</option>';
}
echo '  </select>';

echo '  <input type="submit" value="Show Calendar" style="padding: 5px; background-color: #3498db; color: #fff; border: none; cursor: pointer;">';
echo '</form>';

// Display the calendar for the selected month and year
generateCalendar($selectedMonth, $selectedYear);
?>

<?php
// Function to get current month's holidays
function getCurrentMonthEvents() {
    // Array of holidays (format: 'month-day' => 'Holiday Name')
    $holidays = [
        '01-01' => 'New Year\'s Day',
        '02-09' => 'Lunar New Year Holiday',
        '02-10' => 'Lunar New Year\'s Day',
        '02-25' => 'People Power Anniversary',
        '03-11' => 'Ramadan Start',
        '03-28' => 'Maundy Thursday',
        '03-29' => 'Good Friday',
        '03-30' => 'Black Saturday',
        '03-31' => 'Easter Sunday',
        '04-09' => 'The Day of Valor',
        '05-01' => 'Labour Day',
        '06-12' => 'Independence Day',
        '08-21' => 'Ninoy Aquino Day',
        '08-26' => 'National Heroes Day',
        '11-01' => 'All Saint\'s Day',
        '11-02' => 'All Souls Day',
        '11-30' => 'Bonifacio Day',
        '12-08' => 'Feast of the Immaculate Conception',
        '12-24' => 'Christmas Eve',
        '12-25' => 'Christmas Day',
        '12-30' => 'Rizal Day',
        '12-31' => 'New Year\'s Eve'
    ];

    // Array of events (format: 'month-day' => 'Event Name')
    $events = [
        '01-01' => 'New Year\'s Day',
        '03-04' => 'Teacher\'s Day'
    ];

    // Get current month and year
    $currentMonth = date('n');
    $currentYear = date('Y');

    // Merge holidays and events
    $currentMonthEvents = array_merge($holidays, $events);

    // Filter events for the current month
    $currentMonthEvents = array_filter($currentMonthEvents, function ($key) use ($currentMonth) {
        return explode('-', $key)[0] == $currentMonth;
    }, ARRAY_FILTER_USE_KEY);

    return $currentMonthEvents;
}

// Get current month's holidays and events
$currentMonthEvents = getCurrentMonthEvents();

// Display holidays and events in a card
if (!empty($currentMonthEvents)) {
    echo '<div style="border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; margin: 20px; background-color: #fff; border-radius: 8px;">';
    echo '<h2 style="color: #3498db;">Upcoming Holidays and Events</h2>';
    echo '<ul>';
    foreach ($currentMonthEvents as $date => $event) {
        echo "<li>($date) $event</li>";
    }
    echo '</ul>';
    echo '</div>';
} else {
    echo '<p style="margin: 20px;">There are no holidays or events this month.</p>';
}
?>
