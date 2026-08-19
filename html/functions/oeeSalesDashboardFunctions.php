<?php
/*
Paul Ohashi
oeeSalesDashboardFunctions.php
*/

function salesDashboardOpenOrders1($conn) {
    $sql = "
        SELECT pwo_number, sales_order_number, name, line_number, item_number, status, ship_date, order_quantity, on_hand_quantity
        FROM v_so_lines
        WHERE open = '1'
        AND line_number = '1'
        ORDER BY sales_order_number;
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return "<p style='color:red;'>SQL Error: " . mysqli_error($conn) . "</p>";
    }

    if (mysqli_num_rows($result) === 0) {
        return "<p>No open sales orders found.</p>";
    }

    // Start table container (scrollable)
    $html = "
        <div style='
            max-height: 700px;
            overflow-y: auto;
            overflow-x: hidden;
            border: 0px solid #ccc;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        '>
        <table border='1' cellspacing='0' cellpadding='0'
            style='
                border-collapse: collapse;
                width: 1000px;
                font-family: Arial, sans-serif;
                font-size: 14px;
            '>
            <thead style='background-color: #0077ff; color: black; position: sticky; top: 0; z-index: 2;'>
                <tr>
    ";

    // Add the new first column header for row number
    $html .= "<th style='padding: 8px; text-align: center; background-color: lightgrey;'>#</th>";

    // Add column headers from query result
    $fields = mysqli_fetch_fields($result);
    foreach ($fields as $field) {
        $html .= "<th style='padding: 8px; text-align: center; background-color: lightgrey;'>" . htmlspecialchars($field->name) . "</th>";
    }

    $html .= "</tr></thead><tbody><center>";

    // Alternate row colors and add row counter
    $rowNum = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $bgColor = ($rowNum % 2 === 0) ? "#ffffff" : "#e6f3ff"; // white / light blue
        $html .= "<tr style='background-color: {$bgColor};'>";
        
        // Add row number as first column
        $html .= "<td style='padding: 0px; text-align: center; font-weight: bold; background-color: #f9f9f9;'>" . $rowNum . "</td>";

        // Add the rest of the columns
        foreach ($row as $value) {
            $html .= "<td style='padding: 0px; text-align: center;'><center>" . htmlspecialchars($value ?? '') . "</td>";
        }

        $html .= "</tr>";
        $rowNum++;
    }

    $html .= "</tbody></table></div>";

    mysqli_free_result($result);

    return $html;
}
?>

