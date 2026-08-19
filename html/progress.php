<!DOCTYPE html>
<html>
<head>
<title>Live Progress</title>
<style>
.progress-container {
    width: 100%;
    background-color: #ddd;
    border-radius: 25px;
    padding: 3px;
}
.progress-bar {
    height: 30px;
    width: 0%;
    background-color: #4CAF50;
    border-radius: 25px;
    text-align: center;
    color: white;
    font-weight: bold;
    line-height: 30px;
    transition: width 0.5s ease-in-out;
}
</style>

    <title>TOEE</title>
	<!-- Load the chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- A pointer to CSS that makes it pretty. -->
	<link rel="stylesheet" href="newstyle.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="60">
</head>
<body>

<h2>Data Progress</h2>
<table width=80% style="border: 1 px solid black;">
    <tr>
        <td>
<div class="progress-container">
    <div class="progress-bar">0%</div>
</div>

<script>
function updateProgress() {
    fetch('progress.php')
        .then(res => res.json())
        .then(data => {
            if (data.progress !== undefined) {
                const bar = document.querySelector('.progress-bar');
                bar.style.width = data.progress + '%';
                bar.textContent = data.progress + '%';
            }
        })
        .catch(err => console.error('Error:', err));
}

// Refresh every 5 seconds
setInterval(updateProgress, 5000);
updateProgress(); // Initial load
</script>
        </td>
    </tr>
</table>
</body>
</html>