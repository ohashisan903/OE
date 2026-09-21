<?php
/*
Paul Ohashi
oeeFunctions.php - Function for connecting to, and pulling data from Rubicon's MariaDG:
*/
function connectRubiconTci() {
    $host = '';
    $user = '';
    $pass = '';
    $db   = '';

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
} /* End of connectRubiconTci() */

/* This function gets a list of current TCI employees */
function activeOperators($conn) {
	$table_rows = '';

    $getActiveEmployeesSQL = "
        SELECT name FROM tci.employees
        WHERE termination_date IS NULL
	ORDER BY name
    ";

    $result = $conn->query($getActiveEmployeesSQL);

    $backgroundColor = 'background-color: #ddecf0';
    $bgcount = 0;
    $currentMonth = date('n');
    $whichQuarter = ceil($currentMonth / 3);

	# $count = 0;  # Used to count the number of operators, but I don't think this is necessary?
    if ($result) {
        $table_rows .= "<tr style='background-color: lightgrey; text-align: center;'>
                        <td colspan=2 style='text-align: center;'>
                        <strong>TCI Employees</strong>
                        </td></tr>";
        while ($row = $result->fetch_assoc()) {

        if ($bgcount % 2 != 0) {
            # Odd
            $backgroundColor = '';
            $bgcount++;
        } else {
            # Even
            $backgroundColor = 'background-color: #ddecf0';
            $bgcount++;
        }

			#$count++;
            $name[] = $row['name'];
            $dbUser = $row['name'];
            /* Rationalize the name to match Active Directory standards (first.last) and add it to the
               $table_rows array with an anchor (a href) tag, so that names can be clicked to launch a dynamic
               page about the operator's stats (operators\firstName.lastName.php)
            */
            $fullAdName = explode(',', $row['name']);
            if (count($fullAdName) == 2) {
                $lastName = trim($fullAdName[0]);
                $firstName = trim($fullAdName[1]);
                $fullAdName = "$firstName $lastName";
            }

			$table_rows .=  "<tr style=\"{$backgroundColor}\"><td style=\"text-align: left;\">"
                        .   "<a href=\"operators/user.php?user={$fullAdName}&dbUser={$dbUser}\""
                        .   " style=\"text-decoration: none; color: black;\">{$row['name']}</a></td>"
                        .   "<td>Quarterly</td></tr>";
        }
        echo "<table>$table_rows</table>";
    } else {
        die("Query failed: " . $conn->error);
    }
} /* End of getActiveEmployees() - oee.php */

function openSalesOrders($conn, $statuses, $counts) {
    $sql = "
        SELECT status, COUNT(*) AS count
        FROM v_sos
        WHERE open = '1'
        GROUP BY status
    ";
    $result = $conn->query($sql);

    $statuses = [];
    $counts = [];

    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row['status'];
        $counts[] = $row['count'];
    }
    $conn->close();
}

/* getUserLaborHistoryThisQuarter() - Pull labor history from item_lrb_transactions
by user for the past 24 hours (user.php) */
function getUserLaborHistoryThisQuarter($conn, $username) {
    $sql = "
        SELECT lrb_number, user_name, received_work_center, created_date_time, transaction_quantity
        FROM v_lrb_transactions
        WHERE QUARTER(created_date_time) = QUARTER(CURDATE())
          AND YEAR(created_date_time) = YEAR(CURDATE())
          AND user_name = '" . $conn->real_escape_string($username) . "'
          AND received_work_center != ''
          AND transaction_quantity > 0
        ORDER BY created_date_time DESC
    ";

    $result = $conn->query($sql);
    $table_rows = '';
    $count = 1;
    $totalQuantity = 0;

    while ($row = $result->fetch_assoc()) {
        $backgroundColor = ($count % 2 == 0) ? 'background-color: #ddecf0;' : '';
        $count++;

        // Add to total as a float
        $totalQuantity += (float)$row['transaction_quantity'];

        // If/Else to format $formattedQuantity for display. $ = test, ? = if true, : = else
        $formattedQuantity = is_numeric($row['transaction_quantity'])
        ? number_format($row['transaction_quantity'], 2)
        : $row['transaction_quantity'];

        $table_rows .= "<tr style=\"{$backgroundColor}\">"
                     . "<td>{$row['lrb_number']}</td>"
                     . "<td>{$row['user_name']}</td>"
                     . "<td>{$row['received_work_center']}</td>"
                     . "<td>{$row['created_date_time']}</td>"
                     . "<td>{$formattedQuantity}</td>"
                     . "</tr>";
    }

    $conn->close();

    // Return both table rows and total quantity
    return [
        'table_rows' => $table_rows,
        'totalQuantity' => $totalQuantity
    ];
}



/* getUserPwoLaborHistoryLast7Days() - Pull PWO labor history by user for the past 7 days (user.php) */
function getUserLaborHistoryLast7Days($conn, $username) {
    $sql = "
        SELECT pwo_number, employee_name, item_description, start_date, stop_date, total_hours, completed_quantity
        FROM v_pwo_labor
        WHERE start_date >= NOW() - INTERVAL 7 DAY AND employee_name = '$username'
        ORDER BY pwo_number
    ";

    $result = $conn->query($sql);

    $pwoNumber = [];
    $employeeName = [];
    $itemDescription = [];
    $startDate = [];
    $stopDate = [];
    $totalHours = [];
    $completedQuantity = [];
    $totalQuantity = 0;
    $formattedTotal;
    $table_rows = '';
    $count = 1;

    while ($row = $result->fetch_assoc()) {
        if ($count % 2 != 0) {
            # Odd
            $backgroundColor = '';
            $count++;
        } else {
            # Even
            $backgroundColor = 'background-color: #ddecf0';
            $count++;
        }
        $pwoNumber[] = $row['pwo_number'];
        $employeeName[] = $row['employee_name'];
        $itemDescription[] = $row['item_description'];
        $startDate[] = $row['start_date'];
        $stopDate[] = $row['stop_date'];
        $totalHours[] = $row['total_hours'];
        $totalQuantity += $row['completed_quantity'];
        $formattedTotal = number_format($totalQuantity, 2);

        $table_rows .= "<tr style=\"{$backgroundColor}\"><td>{$row['pwo_number']}</td><td>{$row['employee_name']}</td>"
                    . "<td>{$row['item_description']}</td><td>{$row['start_date']}</td>"
                    . "<td>{$row['stop_date']}</td><td>{$row['total_hours']}</td>"
                    . "<td>{$row['completed_quantity']}</td><td>{$formattedTotal}</td>";
        ;
    }
    $conn->close();
    return $table_rows;
}

/*  Main page, OE work center output. Discussion with Jim about using this view instead of
    v_pwo_labor, because the tci.v_pwo_labor.completed_quantity doesn't have any safety
    rails around operator input, and any number can be input into this field. Whereas
    tci.v_lrb_transactions.transaction_quantity has built-in triggers to prevent over stating
    the amount of product completed. */
function workCenterOutput($conn) {
    // Determine which day's 1st shift to display
$now = new DateTime();

// TEST: Change '08:00' and  $shift1Start to something in the near future and keep an eye
//	 on the dashboard (1st shift column) to watch the footage zero out when the new
//	 time occurs.
if ($now->format('H:i') < '08:00') {

    // Before 11:30 AM, show yesterday's 1st shift
    $shift1Start = date('Y-m-d 08:00:00', strtotime('-1 day'));
    $shift1End   = date('Y-m-d 16:00:00', strtotime('-1 day'));

} else {

    // 11:30 AM or later, show today's 1st shift
    $shift1Start = date('Y-m-d 08:00:00');
    $shift1End   = date('Y-m-d 16:00:00');
}


    // Define queries for each shift
    $queries = [
        'shift1' => "SELECT received_work_center, CAST(ROUND(SUM(transaction_quantity)) AS UNSIGNED) AS qty
                     FROM v_lrb_transactions
                     WHERE transaction_type = 'Manufacturing Receipt'
                       AND created_date_time >= '$shift1Start'
                       AND created_date_time <  '$shift1End'
                     GROUP BY received_work_center",

        'shift2' => "SELECT received_work_center, CAST(ROUND(SUM(transaction_quantity)) AS UNSIGNED) AS qty
                     FROM v_lrb_transactions
                     WHERE transaction_type = 'Manufacturing Receipt'
                       AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR
                       AND created_date_time <  CURDATE()
                     GROUP BY received_work_center",

        'shift3' => "SELECT received_work_center, CAST(ROUND(SUM(transaction_quantity)) AS UNSIGNED) AS qty
                     FROM v_lrb_transactions
                     WHERE transaction_type = 'Manufacturing Receipt'
                       AND created_date_time >= CURDATE()
                       AND created_date_time <  CURDATE() + INTERVAL 8 HOUR
                     GROUP BY received_work_center",
    ];

    // Store all results in a combined array
    $data = [];
    $percent = 0;
    foreach ($queries as $shift => $sql) {
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $wc = $row['received_work_center'];
                $qty = (float)$row['qty'];
                if (!isset($data[$wc])) {
                    $data[$wc] = ['shift1' => 0, 'shift2' => 0, 'shift3' => 0];
                }
                $data[$wc][$shift] = $qty;
            }
        }
    }

    if (empty($data)) {
        return "<tr><td colspan='4'>4 No data found.</td></tr>";
    }

    // Sort alphabetically by work center
    ksort($data);

    // Build table rows
    $rows = '';
    $bgcount = 0;

    foreach ($data as $workCenter => $shifts) {
    $backgroundColor = ($bgcount++ % 2 == 0) ? 'background-color: #ddecf0;' : '';

    // Determine max capacity based on Jimmy's numbers
    switch (true) {
        case (strpos($workCenter, 'CABLER 2') === 0):
            $maxCapacity = 160000; break;
        case (strpos($workCenter, 'CABLER 3') === 0):
            $maxCapacity = 31000; break;
        case (strpos($workCenter, 'CABLER 4') === 0):
            $maxCapacity = 30000; break;
        case (strpos($workCenter, 'COILER') === 0):
            $maxCapacity = 105000; break;
        case (strpos($workCenter, 'EXTRUDER-01') === 0):
            $maxCapacity = 630000; break;
        case (strpos($workCenter, 'EXTRUDER-02') === 0):
            $maxCapacity = 170000; break;
        case (strpos($workCenter, 'EXTRUDER-03') === 0):
            $maxCapacity = 367500; break;
        case (strpos($workCenter, 'EXTRUDER-04') === 0):
            $maxCapacity = 105000; break;
        case (strpos($workCenter, 'EXTRUDER-05') === 0):
            $maxCapacity = 'Unknown'; break;
        case (strpos($workCenter, 'EXTRUDER-06') === 0):
            $maxCapacity = 20000; break;
        case (strpos($workCenter, 'EXTRUDER-07') === 0):
            $maxCapacity = 20000; break;
        case (strpos($workCenter, 'FIBER CUTDO') === 0):
            $maxCapacity = 'Unknown'; break;
        case preg_match('/^SPOOLER/', $workCenter):
            $maxCapacity = 105000; break;
        case preg_match('/^TWINNER-(0[1-9]|1[0-9])/', $workCenter):
            $maxCapacity = 43200; break;
        default:
            $maxCapacity = 'Unknown'; break;
    }

    // Work center cell with max capacity
    $rows .= "<tr style='{$backgroundColor}'>";
    $rows .= "<td width='40%'>"
           . htmlspecialchars(substr($workCenter, 0, 11))
           . " (" . (is_numeric($maxCapacity) ? number_format($maxCapacity) : $maxCapacity) . ")</td>";

    foreach (['shift1', 'shift2', 'shift3'] as $shift) {
        $qty = $shifts[$shift];
        $percent = 0;
        $style = '';

        if (is_numeric($maxCapacity) && $maxCapacity > 0) {
            $percent = ($qty / $maxCapacity) * 100;
            $percent = round($percent);
            if ($percent >= 100) {
                $style = "style='color: green; font-weight: ;'";
            } elseif ($percent < 100 && $percent >= 75) {
                $style = "style='color: #d19c47; font-weight: ;'";
            } elseif ($percent < 75) {
                $style = "style='color: red; font-weight: ;'";
            }
        }

        $rows .= "<td width='20%' {$style}>" . number_format($qty) . " (" . round($percent) . "%)</td>";
    }

    $rows .= "</tr>";
}


    return $rows;
} // END OF - workCenterOutput($conn)
// =============================================================================

function displayClock() {
    return '
        <h3 style="text-align: left;">
            <div id="clock" style="font-weight: bold; font-size: 0.8em; color: #000;"></div>
            <script>
                function updateClock() {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" , second: "2-digit"});
                    const dateString = now.toLocaleDateString([], {
                        year: "numeric",
                        month: "short",
                        day: "numeric"
                    });
                    document.getElementById("clock").innerHTML = `${dateString}<br>${timeString}`;
                }
                setInterval(updateClock, 1000);
                updateClock();
            </script>
        </h3>
    ';
}


/* Order status functions */
function getSalesOrdersFromV_SOS($conn, $reportType) {

    $sql = "
        SELECT sales_order_number, customer, order_date, status, salesperson1
        FROM v_sos
        WHERE status = '{$reportType}' and open = '1'
        ORDER BY sales_order_number
    ";

    $result = $conn->query($sql);

    $countSql = "
    SELECT COUNT(*) AS total
    FROM v_sos
    WHERE status = '{$reportType}' AND open = '1'
    ";

    $countResult = $conn->query($countSql);
    $rowCount = 0;
    if ($countResult && $countData = $countResult->fetch_assoc()) {
        $rowCount = $countData['total'];
    }

    $backgroundColor = 'background-color: #ddecf0';
    $salesOrderNumber = [];
    $customer = [];
    $orderDate = [];
    $status = [];
    $salesPerson1 = [];
    $table_rows = '';

    $count = 1;

    while ($row = $result->fetch_assoc()) {
        if ($count % 2 != 0) {
            # Odd
            $backgroundColor = '';
            $count++;
        } else {
            # Even
            $backgroundColor = 'background-color: #ddecf0';
            $count++;
        }
        $salesOrderNumber[] = $row['sales_order_number'];
        $customer[] = $row['customer'];
        $orderDate[] = $row['order_date'];
        $status[] = $row['status'];
        $salesPerson1[] = $row['salesperson1'];
        $table_rows .= "<tr style=\"{$backgroundColor}\";><td>{$row['sales_order_number']}</td><td>{$row['customer']}</td>"
                    . "<td>{$row['order_date']}</td><td>{$row['status']}</td><td>{$row['salesperson1']}</td>";
        ;
    }
    $conn->close();
    return [$table_rows, $rowCount];
}

/* oee.php - Total output last 24 hours */
function total_output_today($conn) {
    $getCompletedQuantityCompleted = "
        SELECT SUM(transaction_quantity) AS total_quantity
        FROM
            v_lrb_transactions
        WHERE
            transaction_type = 'Manufacturing Receipt'
        AND
            transaction_date >= CURDATE()
        AND
            transaction_date <= NOW()
    ";

    $result = $conn->query($getCompletedQuantityCompleted);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['total_quantity'];
        // Did the SQL work? Is there any output?
        if (is_numeric($total)) {
            echo number_format($total, 2);  // formats to 2 decimal places
        } else {
            echo "0.00";
        }
    } else {
        echo "Nothing for today, weekend?";
    }

    $conn->close();
}

/* oee.php - Total output last 7 days */
function total_output_seven_days($conn) {
    $getCompletedQuantityCompleted = "
        SELECT SUM(transaction_quantity) AS total_quantity
        FROM
            v_lrb_transactions
        WHERE
            transaction_type = 'Manufacturing Receipt'
        AND
            transaction_date >= (CURDATE() - INTERVAL 7 DAY)
        AND
            transaction_date <= NOW()
    ";

    $result = $conn->query($getCompletedQuantityCompleted);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['total_quantity'];
        // Did the SQL work? Is there any output?
        if (is_numeric($total)) {
            echo number_format($total, 2);  // formats to 2 decimal places
        } else {
            echo "0.00";
        }
    } else {
        echo "Nothing in the past 7 days?";
    }

    $conn->close();
}

/* oee.php - Total output last 30 days */
function total_output_thirty_days($conn) {
    $getCompletedQuantityCompleted = "
        SELECT SUM(transaction_quantity) AS total_quantity
        FROM
            v_lrb_transactions
        WHERE
            transaction_type = 'Manufacturing Receipt'
        AND
            transaction_date >= (CURDATE() - INTERVAL 30 DAY)
        AND
            transaction_date <= NOW()
    ";
    $result = $conn->query($getCompletedQuantityCompleted);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['total_quantity'];
        // Did the SQL work? Is there any output?
        if (is_numeric($total)) {
            echo number_format($total, 2);  // formats to 2 decimal places
        } else {
            echo "0.00";
        }
    } else {
        echo "Nothing in the past 30 days?";
    }
    $conn->close();
}

function total_output_this_month($conn) {
    $getMonth = "
        SELECT SUM(transaction_quantity) AS total_quantity
            FROM
	            v_lrb_transactions
            WHERE
	            transaction_type = 'Manufacturing Receipt'
            AND transaction_date >= DATE_FORMAT(NOW(), '%Y-%m-01')
            AND transaction_date <= NOW()
    ";

    $result = $conn->query($getMonth);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['total_quantity'];
        // Did the SQL work? Is there any output?
        if (is_numeric($total)) {
            echo number_format($total, 2);  // formats to 2 decimal places
        } else {
            echo "0.00";
        }
    } else {
        echo "Nothing in the past 30 days?";
    }
    $conn->close();
}

function poweredBy() {
    return '<table><tr style="color: #ffffff;"><td colspan=3 style="text-align: right;">
        Powered by Ohashisan</td></tr></table>';
}

function thirdShiftTotalManufacturingReceiptQuantity($conn) {
    $thirdShiftNumber = "
        SELECT SUM(transaction_quantity) AS Quantity
        FROM
	        item_lrb_transactions
        WHERE
	        type = 'Manufacturing Receipt'
	        AND created_date_time >= CURDATE()                      # Midnight last night
	        AND created_date_time <= CURDATE() + INTERVAL 8 HOUR    # 8am today
    ";

    $result = $conn->query($thirdShiftNumber);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['Quantity'];

        if (is_numeric($total)) {
            echo number_format($total, 2);
        } else {
            echo "0.00";
        }
    }
}

function secondShiftTotalManufacturingReceiptQuantity($conn) {
    $secondShiftNumber = "
        SELECT SUM(transaction_quantity) AS Quantity
        FROM
	        item_lrb_transactions
        WHERE
	        type = 'Manufacturing Receipt'
	        AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR    # 4pm yesterday
	        AND created_date_time <= CURDATE()                                          # Midnight last night
    ";

    $result = $conn->query($secondShiftNumber);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['Quantity'];

        if (is_numeric($total)) {
            echo number_format($total, 2);
        } else {
            echo "0.00";
        }
    }
}

// Total quantity of feet from LRBs in a 'Manufacturing Receipt' status, today between 8am and 4pm.
function firstShiftTotalManufacturingReceiptQuantity($conn) {
    $firstShiftNumber = "
        SELECT SUM(transaction_quantity) AS Quantity
        FROM
	        item_lrb_transactions
        WHERE
	        type = 'Manufacturing Receipt'
	        AND created_date_time >= CURDATE() + INTERVAL 8 HOUR
	        AND created_date_time <= CURDATE() + INTERVAL 16 HOUR
    ";

    $result = $conn->query($firstShiftNumber);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['Quantity'];

        if (is_numeric($total)) {
            echo number_format($total, 2);
        } else {
            echo "0.00";
        }
    }
}

// Operator monthly stats
function displayMonthlyTransactionTotals($conn) {
    /* PO: SQL for quarter totals in case we change the bonus plan back to quarterly
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
    "; */

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
    		AND MONTH(created_date_time) = MONTH(CURDATE())
    		AND YEAR(created_date_time) = YEAR(CURDATE())
  	GROUP BY user_name

  	UNION ALL

  	SELECT
    		'TOTAL' AS user_name,
    		COALESCE(SUM(transaction_quantity), 0) AS total_qty
  	FROM item_lrb_transactions
  	WHERE type = 'Manufacturing Receipt'
    		AND MONTH(created_date_time) = MONTH(CURDATE())
    		AND YEAR(created_date_time) = YEAR(CURDATE())
		) AS combined
	ORDER BY
  	CASE WHEN user_name = 'TOTAL' THEN 2 ELSE 1 END,
  		total_qty DESC,
  		user_name ASC;
	";

    /* PO: for quarterly results
    $currentMonth = date('n');
    $whichQuarter = ceil($currentMonth / 3);
    */

    $currentMonth = date('F'); // August, September, etc.
    $result = $conn->query($sql);

    if (!$result) {
        echo "Error executing query: " . $conn->error;
        return;
    }

    echo '<table border="0" cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 50%;">';
    echo '<thead style="background-color: #ddd; text-align: left;">';
    echo '<tr><td colspan=2 style="text-algin: center;"><center><strong>Monthly Production KPI</strong></center></td></tr>';
    echo '<tr><th>Operator</th><th>Total (' . $currentMonth . ')</th></tr>';
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

	// Shadow: Horizontal offset, Vertical offset, Blur radius, rgba(0,0,0,0.4) = Shadow color and opacity
        echo '<td><a href="operators/user.php?user=' . htmlspecialchars($row['user_name'])
        	. '&dbUser=" style="text-decoration: none; color: #36A2EB;
		text-shadow: 1px 1px 0px rgba(0,0,0,0.5);">'
		. htmlspecialchars($row['user_name']) . '</a></td>';

        echo '<td style="text-align: right;">' . $row['total_quantity'] . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
} // End displayQuarterlyTransactionTotals()

// Operator efficiency report - This information is displayed by drilling down into one of the
// shift links under the navigation links on the main dashboard...
// File: oeeoperatorefficiency.php

function operatorEfficiencyReportFirst($conn) {
    // SQL query (summed up by user)
    $sql = "
        SELECT
            user_name,
            FORMAT(SUM(transaction_quantity), 2) AS total_quantity
        FROM v_lrb_transactions
        WHERE transaction_type = 'Manufacturing Receipt'
        AND created_date_time >= CURDATE() + INTERVAL 8 HOUR
        AND created_date_time <= CURDATE() + INTERVAL 16 HOUR
        GROUP BY user_name
        ORDER BY user_name;
    ";

    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        return "<tr><td colspan='2' style=\"background: lightgrey; font-weight: bold;\">No results found!</td></tr>";
    }


    // Build date range for header
    $startDate = date('m-d', strtotime('today 8:00'));
    $endDate   = date('m-d', strtotime('today 16:00'));
    $dateRange = "$startDate to $endDate";

        // Define the shift scheduled
        // Build date + shift label for header
        $startHour = 8;   // from SQL filter
        $endHour   = 16;  // from SQL filter
        $shiftName = '1st';

        if ($startHour == 8 && $endHour == 16) {
            $shiftName = '1st shift';
        } elseif ($startHour == 16 && $endHour == 24) {
            $shiftName = '2nd shift';
        } elseif ($startHour == 0 && $endHour == 8) {
            $shiftName = '3rd shift';
        }

        $reportDate = date('m-d');  // today’s date
        $dateRange = "$reportDate $shiftName";

    // Add header row
    $rows = "<tr>
            <td colspan='6' style='background: lightgrey; font-weight: bold; text-align: center;'>
                <a href=\"oeeoperatorefficiencydetailed.php\" style=\"text-decoration: none; color: #000;\">{$dateRange}</a>
            </td>
         </tr>";

    // Alternate row colors
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $backgroundColor = ($count % 2 == 0) ? "background-color: #ddecf0" : "";
        $count++;

        $rows .= "<tr style=\"$backgroundColor\">
                    <td>{$row['user_name']}</td>
                    <td>{$row['total_quantity']}</td>
                 </tr>";
    }

    return $rows;
} // END OF - Operator efficiency 1st shift report

function operatorEfficiencyReportSecond($conn) {
    // SQL query (summed up by user)
    $sql = "
        SELECT
            user_name,
            SUM(transaction_quantity) AS total_quantity
        FROM v_lrb_transactions
        WHERE transaction_type = 'Manufacturing Receipt'
          AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR
          AND created_date_time <= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 24 HOUR
        GROUP BY user_name
        ORDER BY user_name
    ";

    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        return "<tr><td colspan='2' style=\"background: lightgrey; font-weight: bold;\">No results found</td></tr>";
    }


    // Build date range for header
    $startDate = date('m-d', strtotime('today 16:00'));
    $endDate   = date('m-d', strtotime('today 00:00'));
    $dateRange = "$startDate to $endDate";

        // Define the shift scheduled
        // Build date + shift label for header
        $startHour = 16;   // from SQL filter
        $endHour   = 24;  // from SQL filter
        $shiftName = '2nd';

        if ($startHour == 8 && $endHour == 16) {
            $shiftName = '1st shift';
        } elseif ($startHour == 16 && $endHour == 24) {
            $shiftName = '2nd shift';
        } elseif ($startHour == 0 && $endHour == 8) {
            $shiftName = '3rd shift';
        }

        $reportDate = date('m-d', strtotime('yesterday'));  // yesterday’s date
        $dateRange = "$reportDate $shiftName";

    // Add header row
    $rows = "<tr>
            <td colspan='6' style='background: lightgrey; font-weight: bold; text-align: center;'>
                <a href=\"oeeoperatorefficiencydetailed.php\" style=\"text-decoration: none; color: #000;\">{$dateRange}</a>
            </td>
         </tr>";

    // Alternate row colors
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $backgroundColor = ($count % 2 == 0) ? "background-color: #ddecf0" : "";
        $count++;

        $rows .= "<tr style=\"$backgroundColor\">
                    <td>{$row['user_name']}</td>
                    <td>{$row['total_quantity']}</td>
                 </tr>";
    }

    return $rows;
} // END OF - Operator efficiency 2nd shift report

function operatorEfficiencyReportThird($conn) {
    // SQL query (summed up by user)
    $sql = "
        SELECT
            user_name,
            SUM(transaction_quantity) AS total_quantity
        FROM v_lrb_transactions
        WHERE transaction_type = 'Manufacturing Receipt'
          AND created_date_time >= CURDATE()
          AND created_date_time <= CURDATE() + INTERVAL 8 HOUR
        GROUP BY user_name
        ORDER BY user_name
    ";

    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        return "<tr><td colspan='6'>No results found</td></tr>";
    }


    // Build date range for header
    $startDate = date('m-d', strtotime('today 00:00'));
    $endDate   = date('m-d', strtotime('today 08:00'));
    $dateRange = "$startDate to $endDate";

        // Define the shift scheduled
        // Build date + shift label for header
        $startHour = 24;   // from SQL filter
        $endHour   = 8;  // from SQL filter
        $shiftName = '3rd';

        if ($startHour == 8 && $endHour == 16) {
            $shiftName = '1st shift';
        } elseif ($startHour == 16 && $endHour == 24) {
            $shiftName = '2nd shift';
        } elseif ($startHour == 24 && $endHour == 8) {
            $shiftName = '3rd shift';
        }

        $reportDate = date('m-d');  // today’s date
        $dateRange = "$reportDate $shiftName";

    $rows = "<tr>
            <td colspan='6' style='background: lightgrey; font-weight: bold; text-align: center;'>
                <a href=\"oeeoperatorefficiencydetailed.php\" style=\"text-decoration: none; color: #000;\">{$dateRange}</a>
            </td>
         </tr>";

    // Alternate row colors
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $backgroundColor = ($count % 2 == 0) ? "background-color: #ddecf0" : "";
        $count++;

        $rows .= "<tr style=\"$backgroundColor\">
                    <td>{$row['user_name']}</td>
                    <td>{$row['total_quantity']}</td>
                 </tr>";
    }

    return $rows;
} // END OF - Operator efficiency 3rd shift report


function comingSoon() {
    echo "Coming soon...";
}


// Information on LRB Manufacturing Receipts from the timeframe clicked (Today, 7 Day, 30 Day and current month)
// Table displayed at the top center of the oee.php page.
function lrbManufacturingReceipt($conn, $when) {
    switch ($when) {
        case 'thisMonth':
            $sql = "
                SELECT lrb_number, created_date_time, received_work_center, user_name, transaction_quantity
                FROM v_lrb_transactions
                WHERE created_date_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                AND created_date_time < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
                AND transaction_type = 'Manufacturing Receipt'
                ORDER BY received_work_center ASC
            ";
            break;

        case 'today':
            $sql = "
                SELECT lrb_number, created_date_time, received_work_center, user_name, transaction_quantity
                FROM v_lrb_transactions
                WHERE created_date_time >= CURDATE()
                AND created_date_time < CURDATE() + INTERVAL 1 DAY
                AND transaction_type = 'Manufacturing Receipt'
                ORDER BY received_work_center ASC
            ";
            break;

        case '7day':
            $sql = "
                SELECT lrb_number, created_date_time, received_work_center, user_name, transaction_quantity
                FROM v_lrb_transactions
                WHERE created_date_time >= CURDATE() - INTERVAL 7 DAY
                AND created_date_time < CURDATE() + INTERVAL 1 DAY
                AND transaction_type = 'Manufacturing Receipt'
                ORDER BY received_work_center ASC
            ";
            break;

        case '30day':
            $sql = "
                SELECT lrb_number, created_date_time, received_work_center, user_name, transaction_quantity
                FROM v_lrb_transactions
                WHERE created_date_time >= CURDATE() - INTERVAL 30 DAY
                AND created_date_time < CURDATE() + INTERVAL 1 DAY
                AND transaction_type = 'Manufacturing Receipt'
                ORDER BY received_work_center ASC
            ";
            break;

        default:
            return "<tr><td colspan='6'>Invalid filter</td></tr>";
    }

    // Run the query
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        return "<tr><td colspan='6'>No results found</td></tr>";
    }

    $backgroundColor = '';
    $count = 0;
    $rows = "";
    $numberOfLRBs = 0;

    $rows .= "<tr style=\"background: lightgrey; font-weight: bold;\">
                <td>#</td>
                <td>LRB #</td>
                <td>Date/Time</td>
                <td>Work Center</td>
                <td>Operator</td>
                <td>Quantity</td>
              </tr>";

    while ($row = $result->fetch_assoc()) {
        $numberOfLRBs++;
        if ($count % 2 != 0) {
            # Odd
            $backgroundColor = '';
            $count++;
        } else {
            # Even
            $backgroundColor = 'background-color: #ddecf0';
            $count++;
        }

        $rows .= "<tr style=\"{$backgroundColor}\";>
            <td>$numberOfLRBs</td>
            <td>{$row['lrb_number']}</td>
            <td>{$row['created_date_time']}</td>
            <td>{$row['received_work_center']}</td>
            <td>{$row['user_name']}</td>
            <td>{$row['transaction_quantity']}</td>
        </tr>";
    }

    return [$rows, $numberOfLRBs];
    #return $rows;
}

//Work Center Daily
function workCenterDaily($conn, $workCenter) {
    $sql = "SELECT lrb_number,
                   user_name,
                   received_work_center,
                   created_date_time,
                   transaction_quantity
            FROM v_lrb_transactions
            WHERE transaction_type = 'Manufacturing Receipt'
              AND created_date_time >= CURDATE()
              AND received_work_center LIKE '{$workCenter}%'
            ORDER BY received_work_center, created_date_time DESC";

    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        return "<tr><td colspan='6'>No results found</td></tr>";
    }

    $backgroundColor = '';
    $count = 1;
    $rows = "";

    while ($row = $result->fetch_assoc()) {
        if ($count % 2 != 0) {
            $backgroundColor = '';
        } else {
            $backgroundColor = 'background-color: #ddecf0';
        }
        $count++;

        $rows .= "<tr style=\"{$backgroundColor}\">
            <td>{$row['lrb_number']}</td>
            <td>{$row['user_name']}</td>
            <td>{$row['received_work_center']}</td>
            <td>{$row['created_date_time']}</td>
            <td>{$row['transaction_quantity']}</td>
        </tr>";
    }

    return $rows;
}


function quarterProgress($conn) {
    $today = new DateTime();
    $year = $today->format("Y");

    // Determine current quarter
    $month = (int)$today->format("n");
    $quarter = ceil($month / 3);

    // Quarter start date
    $quarterStart = new DateTime("$year-" . ((($quarter - 1) * 3) + 1) . "-01 00:00:00");
    // Quarter end date
    $quarterEnd = clone $quarterStart;
    $quarterEnd->modify("+3 months")->modify("-1 second");

    // Days in quarter
    $totalDays = $quarterStart->diff($quarterEnd)->days + 1;
    // Days elapsed
    $elapsedDays = $quarterStart->diff($today)->days + 1;

    // Percentage complete
    $percent = ($elapsedDays / $totalDays) * 100;

    return round($percent, 2);
}

function indirectLaborWorkCenters($conn) {
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
        return "<tr><td colspan='2'>1 No data found.</td></tr>";
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
            $backgroundColor = 'background-color: #ddecf0';
            $bgcount++;
        }

        $rows .= "<tr style=\"{$backgroundColor}\">
            <td>" . htmlspecialchars(substr($row['actual_work_center'], 0, 15)) ."</td>
            <td>{$row['employee_name']}</td>
            <td>{$row['pwo_number']}</td>
            <td>{$row['total_hours']}</td>
        </tr>";
    }

    return $rows;
}

function indirectLaborWorkCentersDailyShort($conn) {
    $sql = "
    SELECT
        actual_work_center,
        REGEXP_REPLACE(pwo_number, '^[0-9]{2}/[0-9]{4} ', '') AS pwo_number,
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
        return "<tr><td colspan='3'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
    }

    if ($result->num_rows === 0) {
        return "<tr><td colspan='3' style=\"text-align: center; font-weight: bold;\">Work centers operational</td></tr>";
    }

    $rows = '
	<tr><td colspan="3" style="background: lightgrey; font-weight: bold; text-align: center;">
	Work Center Down Time
	</tr>
	<tr><td colspan="3" style="background: lightgrey; font-weight: bold; text-align: center;">
        <a href="indirectlaborworkcenter.php" style="text-decoration: none; color: blue;">'
        . date('l m/d') . '</a></td></tr>
        <tr>
            <td style="background: lightgrey; font-weight: bold; text-align: center; padding:10; margin:10;">Work Center</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center; padding:10; margin:10;">PWO</td>
            <td style="background: lightgrey; font-weight: bold; text-align: center; padding:10; margin:10;">Down Time</td>
        </tr>';

    $bgcount = 0;

    while ($row = $result->fetch_assoc()) {
        $backgroundColor = ($bgcount % 2 == 0) ? 'background-color: #ddecf0' : '';
        $bgcount++;

        // numeric compare: treat missing/NULL as 0
        $hoursVal = isset($row['total_hours']) ? floatval($row['total_hours']) : 0.0;

        // Exact string match to "0.000" use: ($row['total_hours'] === '0.000')
        $timeStyle = ($hoursVal == 0.0) ? 'color: red; font-weight: bold;' : '';
	$rowTextStyle = ($hoursVal == 0.0) ? 'color: red; font-weight: bold;' : '';

        $rows .= "<tr style=\"{$backgroundColor}; {$rowTextStyle} padding:0; margin:0;\">
            <td style=\"padding:5; margin:0;\">" . htmlspecialchars(substr($row['actual_work_center'], 0, 11)) . "</td>
            <td style=\"padding:5; margin:0;\">" . htmlspecialchars($row['pwo_number']) . "</td>
            <td style=\"padding:5; margin:0; {$timeStyle}\">" . htmlspecialchars($row['total_hours']) . "</td>
        </tr>";
    }

    return $rows;
}



function indirectLaborWorkCentersDaily($conn) {
    $sql = "
    SELECT
        actual_work_center,
        employee_name,
        REGEXP_REPLACE(pwo_number, '^[A-Z]+ [0-9]{4} ', '') AS pwo_number,
        DATE_FORMAT(start_date, '%c/%e/%y %H:%i') AS start_time,
        DATE_FORMAT(stop_date, '%c/%e/%y %H:%i')  AS stop_time,
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
        return "<tr><td colspan='2'>Indirect Labor Work Center</td></tr>";
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


function indirectLaborWorkCentersWeekly($conn) {
    $sql = "
    SELECT
        actual_work_center,
        employee_name,
        REGEXP_REPLACE(pwo_number, '^[A-Z]+ [0-9]{4} ', '') AS pwo_number,
        DATE_FORMAT(start_date, '%c/%e/%y %H:%i') AS start_time,
        DATE_FORMAT(stop_date, '%c/%e/%y %H:%i')  AS stop_time,
        total_hours
    FROM
        v_pwo_labor
    WHERE
        start_date >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
        AND start_date < DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY)
        AND work_center = 'ZIND'
        ORDER BY actual_work_center
    ";

    $result = $conn->query($sql);
    $whichShift = 0;

    if (!$result) {
        return "<tr><td colspan='2'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
    }

    // If no results
    if ($result->num_rows === 0) {
        return "<tr><td colspan='2'>3No data found.</td></tr>";
    }

    // Get Monday of the current week
    $monday = date('M d', strtotime('monday this week'));

    // Get Sunday of the current week
    $sunday = date('M d', strtotime('sunday this week'));

    // Build your row
    $rows = '<tr><td colspan=6 style="background: lightgrey; font-weight: bold; text-align: center;">Week of '
       . $monday . ' - ' . $sunday . '</td></tr>';
    $rows .= '
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
        if ($bgcount % 2 != 0) {
            # Odd
            $backgroundColor = '';
            $bgcount++;
        } else {
            # Even
            $backgroundColor = 'background-color: #ddecf0';
            $bgcount++;
        }

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

// Web page emoji's...
function getEmoji($category, $size = "3em") {
    $emojiMap = [
        // Alerts
        "alert"     => "🚨",
        "warning"   => "⚠️",
        "stop"      => "🛑",
        "error"     => "❌",
        "important" => "❗",

        // Manufacturing
        "factory"   => "🏭",
        "tools"     => "🛠",
        "gear"      => "⚙️",
        "wrench"    => "🔧",
        "bolt"      => "🔩",
        "box"       => "📦",
        "truck"     => "🚚",

        // Production & Performance
        "chart"     => "📊",
        "growth"    => "📈",
        "drop"      => "📉",
        "trophy"    => "🏆",
        "target"    => "🎯",
        "perfect"   => "💯",
        "time"      => "⏱",
        "medal"     => "🏅",

        // People & Shifts
        "worker"    => "👷",
        "team"      => "👥",
        "night"     => "🌙",
        "day"       => "☀️",

        // Safety
        "safety"    => "🦺",
        "helmet"    => "🪖",
        "firetruck" => "🚒",
        "firstaid"  => "🚑",
        "extinguisher" => "🧯",

        // Recognition
        "star"      => "🌟",
        "celebrate" => "🎉",
        "confetti"  => "🎊",
        "thumbsup"  => "👍",
        "success"   => "🙌",
        "clap"      => "👏",
        "strong"    => "💪",

        // Energy / Ops
        "power"     => "⚡",
        "battery"   => "🔋",
        "plug"      => "🔌",
        "idea"      => "💡",
        "rocket"    => "🚀",
        "global"    => "🌐",
    ];

    $emoji = $emojiMap[$category] ?? "";
    return $emoji ? "<span style='font-size: {$size};'>{$emoji}</span>" : "";
}




?>
