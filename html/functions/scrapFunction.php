<?php

function getTodayScrapTotals($conn) {

    $sql = "
        SELECT 
            SUM(scrap_lbs) AS total_lbs,
            SUM(scrap_quantity) AS total_qty,
            SUM(scrap_ext) AS total_ext
        FROM pwo_scraps
        WHERE DATE(created_date_time) = CURDATE();
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return "<tr><td colspan='4'>Query Error: " . mysqli_error($conn) . "</td></tr>";
    }

    $row = mysqli_fetch_assoc($result);

    // Format today’s date
    $today = date("Y-m-d");

    // Ensure null values show as 0
    $total_lbs = $row['total_lbs'] ?? 0;
    $total_qty = $row['total_qty'] ?? 0;
    $total_ext = $row['total_ext'] ?? 0;

    // Add comma formatting
    $total_lbs = number_format($total_lbs);
    $total_qty = number_format($total_qty);
    $total_ext = number_format($total_ext, 2);  // keep cents

    // Return a single table row
    return "
        <tr>
            <td width=25%>{$total_lbs} lbs.</td>
            <td width=25%>{$total_qty} ft.</td>
            <td width=25%>\${$total_ext}</td>
            <td width=25%>{$today}</td>
        </tr>
    ";
}


function getMonthToDateScrapTotals($conn) {

    $sql = "
        SELECT
            SUM(scrap_lbs) AS total_lbs,
            SUM(scrap_quantity) AS total_qty,
            SUM(scrap_ext) AS total_ext
        FROM pwo_scraps
        WHERE created_date_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
          AND created_date_time <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY);
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return "<tr><td colspan='4'>Query Error: " . mysqli_error($conn) . "</td></tr>";
    }

    $row = mysqli_fetch_assoc($result);

    // Ensure null values show as 0
    $total_lbs = $row['total_lbs'] ?? 0;
    $total_qty = $row['total_qty'] ?? 0;
    $total_ext = $row['total_ext'] ?? 0;

    // Add comma formatting
    $total_lbs = number_format($total_lbs);
    $total_qty = number_format($total_qty);
    $total_ext = number_format($total_ext, 2);  // keep cents

    // Label for the date column
    $label = date("F Y");

    // Return a single table row
    return "
        <tr>
            <td width=25%>{$total_lbs} lbs.</td>
            <td width=25%>{$total_qty} ft.</td>
            <td width=25%>\${$total_ext}</td>
            <td width=25%>{$label}</td>
        </tr>
    ";
}

function getQuartlyToDateScrapTotals($conn) {

    $sql = "
	SELECT
    		SUM(scrap_lbs) AS total_lbs,
    		SUM(scrap_quantity) AS total_qty,
    		SUM(scrap_ext) AS total_ext
	FROM pwo_scraps
	WHERE created_date_time >= MAKEDATE(YEAR(CURDATE()), 1)
      		+ INTERVAL QUARTER(CURDATE())*3 - 3 MONTH
  	AND created_date_time <  MAKEDATE(YEAR(CURDATE()), 1)
      		+ INTERVAL QUARTER(CURDATE())*3 MONTH;
	";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return "<tr><td colspan='4'>Query Error: " . mysqli_error($conn) . "</td></tr>";
    }

    $row = mysqli_fetch_assoc($result);

    // Ensure null values show as 0
    $total_lbs = $row['total_lbs'] ?? 0;
    $total_qty = $row['total_qty'] ?? 0;
    $total_ext = $row['total_ext'] ?? 0;

    // Add comma formatting
    $total_lbs = number_format($total_lbs);
    $total_qty = number_format($total_qty);
    $total_ext = number_format($total_ext, 2);  // keep cents

    // Label for the date column
    $quarter = ceil(date("n") / 3);   // n = month number
    $label = "Q" . $quarter;


    // Return a single table row
    return "
        <tr>
            <td width=25%>{$total_lbs} lbs.</td>
            <td width=25%>{$total_qty} ft.</td>
            <td width=25%>\${$total_ext}</td>
            <td width=25%>{$label}</td>
        </tr>
    ";
}

function getYearToDateScrapTotals($conn) {

    $sql = "
        SELECT
            SUM(scrap_lbs) AS total_lbs,
            SUM(scrap_quantity) AS total_qty,
            SUM(scrap_ext) AS total_ext
        FROM pwo_scraps
        WHERE created_date_time >= MAKEDATE(YEAR(CURDATE()), 1)
          AND created_date_time <  CURDATE() + INTERVAL 1 DAY;
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return "<tr><td colspan='4'>Query Error: " . mysqli_error($conn) . "</td></tr>";
    }

    $row = mysqli_fetch_assoc($result);

    // Ensure null values show as 0
    $total_lbs = $row['total_lbs'] ?? 0;
    $total_qty = $row['total_qty'] ?? 0;
    $total_ext = $row['total_ext'] ?? 0;

    // Add comma formatting
    $total_lbs = number_format($total_lbs);
    $total_qty = number_format($total_qty);
    $total_ext = number_format($total_ext, 2);

    // Label for the date column (example: "YTD 2025")
    $label = "YTD " . date("Y");

    // Return a single table row
    return "
        <tr>
            <td width=25%>{$total_lbs} lbs.</td>
            <td width=25%>{$total_qty} ft.</td>
            <td width=25%>\${$total_ext}</td>
            <td width=25%>{$label}</td>
        </tr>
    ";
}

function getMonthlyScrapTotals($conn) {
    $sql = "
        SELECT 
            MONTH(created_date_time) AS month_num,
            SUM(scrap_lbs) AS total_lbs,
            SUM(scrap_quantity) AS total_qty,
            SUM(scrap_ext) AS total_ext
        FROM pwo_scraps
        WHERE YEAR(created_date_time) = YEAR(CURDATE())
        GROUP BY MONTH(created_date_time)
        ORDER BY MONTH(created_date_time);
    ";

    $result = mysqli_query($conn, $sql);

    // Prepare arrays for all 12 months (initialize to 0)
    $months = array_fill(1, 12, 0);
    $lbs = array_fill(1, 12, 0);
    $qty = array_fill(1, 12, 0);
    $ext = array_fill(1, 12, 0);

    while ($row = mysqli_fetch_assoc($result)) {
        $m = (int)$row['month_num'];
        $lbs[$m] = (float)$row['total_lbs'];
        $qty[$m] = (float)$row['total_qty'];
        $ext[$m] = (float)$row['total_ext'];
    }

    return [
        "months" => $months, // 1–12
        "lbs"    => $lbs,
        "qty"    => $qty,
        "ext"    => $ext
    ];
}



?>
