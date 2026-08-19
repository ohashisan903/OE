<?php
// Directory containing the photos (relative or absolute path)
$directory = "../pics/TeamMemberPictures"; // Location of employee photos

// Image file extensions
$extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Scan $directory for employee image files
$files = array_filter(scandir($directory), function($file) use ($directory, $extensions) {
    $path = "$directory/$file";
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return is_file($path) && in_array($ext, $extensions);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="refresh" content="10">
<title>TCI Employees</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f8f9fa;
        margin: 0;
        padding: 20px;
    }
    h1 {
        text-align: center;
        margin-bottom: 20px;
    }
    .gallery {
        display: grid;
        grid-template-columns: repeat(10, 1fr); /* 10 columns */
        gap: 8px;
        justify-items: center;
    }
    .gallery-item {
        text-align: center;
    }
    .gallery img {
        width: 100%;
        height: auto;
        max-width: 120px;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .gallery img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .name {
        margin-top: 6px;
        font-size: 14px;
        color: #333;
        word-break: break-word;
    }
    @media (max-width: 1200px) {
        .gallery { grid-template-columns: repeat(5, 1fr); }
    }
    @media (max-width: 768px) {
        .gallery { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 480px) {
        .gallery { grid-template-columns: repeat(2, 1fr); }
    }
</style>
</head>
<body>
    <img src="../pics/TransCableLogo.png" alt="TCI Logo">
    <h1>TCI Employees</h1>
    <div class="gallery">
        <?php foreach ($files as $file): 
            $name = pathinfo($file, PATHINFO_FILENAME); // get filename without extension
        ?>
            <div class="gallery-item">
                <img src="<?= htmlspecialchars("$directory/$file") ?>" alt="<?= htmlspecialchars($name) ?>">
                <div class="name"><?= htmlspecialchars($name) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
