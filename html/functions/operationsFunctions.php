<?php

function authenticationLogTable($conn)
{
    $sql = "
	SELECT
    		user_name,
    		session_id,
    		login_time,
    		logout_time,
    		ip_address
	FROM authentication_log
	WHERE login_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
	ORDER BY user_name ASC, login_time DESC
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result)
    {
        return "<p>Query failed: " . mysqli_error($conn) . "</p>";
    }

    $output = "
        <table style='border-collapse: collapse; width: auto;'>
            <tr style='background-color: lightgrey;'>
                <th>User Name</th>
                <th>Session ID</th>
                <th>Login Time</th>
                <th>Logout Time</th>
                <th>IP Address</th>
            </tr>
    ";

    while ($row = mysqli_fetch_assoc($result))
    {
        $output .= "
            <tr>
                <td>{$row['user_name']}</td>
                <td>{$row['login_time']}</td>
                <td>{$row['logout_time']}</td>
                <td>{$row['ip_address']}</td>
                <td>{$row['session_id']}</td>
            </tr>
        ";
    }

    $output .= "</table>";

    return $output;
}

?>
