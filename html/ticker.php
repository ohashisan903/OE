<?php
require_once './functions/oeeFunctions.php';
header('Content-Type: text/html; charset=UTF-8'); // we output <span> for colors

// Make sure this matches your factory local time
date_default_timezone_set('America/Chicago');

$conn = connectRubiconTci();
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);

// ---- CONFIG: choose how capacity should grow across shifts ----
// true  -> 1× (00–08), 2× (08–16), 3× (16–24)
// false -> 1× (00–08), 2× (08–24)
$TRIPLE_THIRD_SHIFT = false;

// Static ticker messages
$tickerMessages = [
    "🚨 Safety meeting every Friday at 7:55am",
    "🌟🌟🌟 Trans Cable International 🌟🌟🌟"
];

// Base capacities (per 8-hour shift)
$workCenterCapacities = [
    'CABLER 2'    => 160000,
    'CABLER 3'    => 31000,
    'CABLER 4'    => 30000,
    'COILER'      => 105000,
    'EXTRUDER-01' => 630000,
    'EXTRUDER-02' => 170000,
    'EXTRUDER-03' => 367500,
    'EXTRUDER-04' => 105000,
    'EXTRUDER-05' => 'Unknown',
    'EXTRUDER-06' => 20000,
    'EXTRUDER-07' => 20000,
    'FIBER CUTDO' => 'Unknown',
    'SPOOLER'     => 105000,
    'TWINNER'     => 43200
];

// Figure out current shift & multiplier
$currentHour = (int)date('G'); // 0..23

if ($currentHour >= 0 && $currentHour < 8) {
    $shiftIndex = 0;   // 3rd shift
    $multiplier = 1;
} elseif ($currentHour >= 8 && $currentHour < 16) {
    $shiftIndex = 1;   // 1st shift
    $multiplier = 2;
} else {
    $shiftIndex = 2;   // 2nd shift
    $multiplier = 3;
}

// Query daily totals
$sql = "
    SELECT received_work_center, SUM(transaction_quantity) AS total
    FROM v_lrb_transactions
    WHERE transaction_type = 'Manufacturing Receipt'
      AND transaction_date >= CURDATE()
    GROUP BY received_work_center
    ORDER BY received_work_center
";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $workCenter = preg_replace('/\s+/', ' ', strip_tags($row['received_work_center']));
        $total = (float)$row['total'];

        // Find base capacity by prefix match
        $baseCapacity = 'Unknown';
        foreach ($workCenterCapacities as $prefix => $cap) {
            if (strpos($workCenter, $prefix) === 0) {
                $baseCapacity = $cap;
                break;
            }
        }

        // Scale by current shift multiplier
        $maxCapacity = is_numeric($baseCapacity) ? $baseCapacity * $multiplier : $baseCapacity;

        // Color thresholds
        $color = 'white';
        if (is_numeric($maxCapacity)) {
            if ($total >= $maxCapacity) {
                $color = 'lightgreen';
            } elseif ($total >= 0.75 * $maxCapacity) {
                $color = 'yellow';
            } else {
                $color = '#EE4B2B';
            }
        }

        $formattedTotal = "<span style='color: {$color}; font-weight:bold;'>" . number_format($total) . "</span>";
        $maxStr = is_numeric($maxCapacity) ? number_format($maxCapacity) : $maxCapacity;

        $tickerMessages[] = "📊 {$workCenter} produced {$formattedTotal} (100%: {$maxStr})";
    }
}

$conn->close();

// Keep HTML intact; only collapse excessive whitespace
$tickerMessages = array_map(fn($m) => preg_replace('/\s+/', ' ', $m), $tickerMessages);

// Output + tiny debug comment (check View Source)
echo implode(' | ', $tickerMessages) . "<!-- hr={$currentHour} shift={$shiftIndex} mult={$multiplier} -->";
?>
