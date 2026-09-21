<?php

function displayMonthlyTransactionTotalsEmail($conn) {

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

    $currentMonth = date('F');
    $result = $conn->query($sql);

    if (!$result) {
        echo "Error executing query: " . $conn->error;
        return;
    }

    echo '
    <table
        border="0"
        cellpadding="0"
        cellspacing="0"
        style="
            width: 100px;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333333;
        ">
    ';

    echo '
        <thead>
            <tr>
                <td colspan="2"
                    style="
                        text-align: center;
                        font-size: 16px;
                        font-weight: bold;
                        padding: 8px;
                        background-color: #dddddd;
                        border: 1px solid #cccccc;
                    ">
                    Monthly Production KPI
                </td>
            </tr>

            <tr>
                <th
                    style="
                        text-align: left;
                        font-size: 12px;
                        font-weight: bold;
                        padding: 6px 10px;
                        background-color: #dddddd;
                        border: 1px solid #cccccc;
                        white-space: nowrap;
                    ">
                    Operator
                </th>

                <th
                    style="
                        text-align: right;
                        font-size: 12px;
                        font-weight: bold;
                        padding: 6px 10px;
                        background-color: #dddddd;
                        border: 1px solid #cccccc;
                        white-space: nowrap;
                    ">
                    Total (' . $currentMonth . ')
                </th>
            </tr>
        </thead>

        <tbody>
    ';

    $rowIndex = 0;

    while ($row = $result->fetch_assoc()) {

        $isTotal = ($row['user_name'] === 'TOTAL');

        if ($isTotal) {
            $style = '
                font-weight: bold;
                background-color: #f0f0f0;
            ';
        } else {
            $style = ($rowIndex % 2 === 0)
                ? 'background-color: #ddecf0;'
                : '';

            $rowIndex++;
        }

        echo '<tr style="' . $style . '">';

	echo '
    		<td
        		style="
            		padding: 5px 10px;
            		font-size: 12px;
            		border: 1px solid #cccccc;
            		white-space: nowrap;
        	">
        	' . htmlspecialchars($row['user_name']) . '
    		</td>
	';

        echo '</td>';

        echo '
            <td
                style="
                    text-align: right;
                    padding: 5px 10px;
                    font-size: 12px;
                    border: 1px solid #cccccc;
                    white-space: nowrap;
                ">
                ' . $row['total_quantity'] . '
            </td>
        ';

        echo '</tr>';
    }

    echo '
        </tbody>
    </table>
    ';
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














?>
