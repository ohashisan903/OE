<?php
// =============================================================================

function progresBar($conn) {
    header('Content-Type: application/json');

    // Example: calculate % complete from your table
    $sql = "SELECT (SUM(completed_quantity) / SUM(total_quantity)) * 100 AS progress FROM my_table";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $progress = round($row['progress'], 2); // 2 decimal places
    } else {
        $progress = 0;
    }

    $conn->close();

    echo json_encode(["progress" => $progress]);
}


/*
function workCenterLaborTracking($conn) {
    $sql = "
        SELECT
        	actual_work_center, employee_name, pwo_number, total_hours
        FROM
        	v_pwo_labor
        WHERE
        	start_date >= CURDATE()
        AND
        	work_center = 'ZIND'
    ";

    $result = $conn->query($sql);
    $whichShift = 0;

    if (!$result) {
        return "<tr><td colspan='2'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
    }

    // If no results
    if ($result->num_rows === 0) {
        return "<tr><td colspan='2'>No data found.</td></tr>";
    }

    $rows = '';
    $bgcount = 0;

    while ($row = $result->fetch_assoc()) {
        if ($bgcount % 2 != 0) {
            # Odd
            $backgroundColor = '';
            $bgcount++;
        } else {
            # Even
            $backgroundColor = 'background-color: lightblue';
            $bgcount++;
        }

        $rows .= "<tr style=\"{$backgroundColor}\">
            <td>{$row['actual_work_center']}</td>
            <td>{$row['employee_name']}</td>
            <td>{$row['pwo_number']}</td>
            <td>{$row['total_hours']}</td>
        </tr>";
    }
*/

function displayQuarterlyTransactionTotalsDev($conn) {
    $sql = "
    SELECT
      user_name,
      FORMAT(total_qty, 2) AS total_quantity
    FROM (
      SELECT
        user_name,
        COALESCE(SUM(transaction_quantity), 0) AS total_qty
      FROM item_lrb_transactions
      WHERE type = 'Manufacturing Receipt'
        AND QUARTER(created_date_time) = QUARTER(CURDATE())
        AND YEAR(created_date_time) = YEAR(CURDATE())
      GROUP BY user_name

      UNION ALL

      SELECT
        'TOTAL' AS user_name,
        COALESCE(SUM(transaction_quantity), 0) AS total_qty
      FROM item_lrb_transactions
      WHERE type = 'Manufacturing Receipt'
        AND QUARTER(created_date_time) = QUARTER(CURDATE())
        AND YEAR(created_date_time) = YEAR(CURDATE())) AS combined
    ORDER BY
      CASE WHEN user_name = 'TOTAL' THEN 2 ELSE 1 END,
      total_qty DESC,
      user_name ASC;
    ";

    $currentMonth = date('n');
    $whichQuarter = ceil($currentMonth / 3);

    $result = $conn->query($sql);

    if (!$result) {
        echo "Error executing query: " . $conn->error;
        return;
    }

    echo '<table border="0" cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 50%;">';
    echo '<thead style="background-color: #ddd; text-align: left;">';
    echo '<tr><th>Operator</th><th>Total (Q' . $whichQuarter . ')</th></tr>';
    echo '</thead><tbody>';

    $rowIndex = 0;  // to track user rows only (skip TOTAL row)

    while ($row = $result->fetch_assoc()) {
        $isTotal = ($row['user_name'] === 'TOTAL');

        // Alternate row color only for user rows
        if ($isTotal) {
            $style = 'font-weight: bold; background-color: #f0f0f0;';
        } else {
            $style = ($rowIndex % 2 === 0) ? 'background-color: #ddecf0;' : '';  // light blue on even rows
            $rowIndex++;
        }

        echo '<tr style="' . $style . '">';
        echo '<td><a href="operators/user.php?user=' . htmlspecialchars($row['user_name'])
        . '&dbUser=" style="text-decoration: none; color: black;">' . htmlspecialchars($row['user_name']) . '</a></td>';
        echo '<td style="text-align: right;">' . $row['total_quantity'] . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}

function indirectLaborWorkCentersDailyDev($conn) {
    $sql = "
    SELECT
        actual_work_center,
        employee_name,
        REGEXP_REPLACE(pwo_number, '^[A-Z]+ [0-9]{4} ', '') AS pwo_number,
        DATE_FORMAT(start_date, '%H:%i') AS start_time,
        DATE_FORMAT(stop_date, '%H:%i')  AS stop_time,
        total_hours
    FROM
        v_pwo_labor
    WHERE
        start_date >= CURDATE()
        AND work_center = 'ZIND'
    ORDER BY actual_work_center
    ";

    $result = $conn->query($sql);

    if (!$result) {
        return "<tr><td colspan='2'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
    }

    if ($result->num_rows === 0) {
        return "<tr><td colspan='2'>No data found.</td></tr>";
    }

    $rows = '<tr><td colspan=6 style="background: lightgrey; font-weight: bold; text-align: center;">
        <a href="indirectlaborworkcenter.php" style="text-decoration: none; color: #000;">'
        . date('l m/d') . '</a></td></tr>
        <tr>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Work Center</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Operator</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">PWO</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Start</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Stop</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center;">Time</td>
        </tr>';

    $bgcount = 0;

    while ($row = $result->fetch_assoc()) {
        $backgroundColor = ($bgcount % 2 == 0) ? 'background-color: #ddecf0' : '';
        $bgcount++;

        $rows .= "<tr style=\"{$backgroundColor}\">
            <td>" . htmlspecialchars(substr($row['actual_work_center'], 0, 11)) . "</td>
            <td>{$row['employee_name']}</td>
            <td>{$row['pwo_number']}</td>
            <td>{$row['start_time']}</td>
            <td>{$row['stop_time']}</td>
            <td>{$row['total_hours']}</td>
        </tr>";
    }

    return $rows;
}


?>