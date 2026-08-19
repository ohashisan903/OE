<!DOCTYPE html>
<html lang=”eng”>
<head>
    <meta charset=”UTF-8”>
    <meta name=”viewport” content=”width=device-width, initial-scale=1.0”>
    <title>Grid with Image</title>
    <link rel=”stylesheet” href=”gridStyles.css”>
	<style>
	Body {
    Margin: 0;
    Display: flex;
    Justify-content: center;
    Align-items: center;
    Height: 100vh;
    Background-color: #f0f0f0;
}

.grid-container {
    Display: grid;
    Grid-template-columns: repeat(100, 10px);
    Grid-template-rows: repeat(100, 10px);
    Gap: 1px;
    Background-color: #ddd;
}

.cell {
    Width: 10px;
    Height: 10px;
    Background-color: #fff;
}

.image-cell {
    Background: url(‘image.jpg’) no-repeat center center / cover;
}
	</style>
	
</head>
<body>
    <div class=”grid-container”>
        <!—Generate 100x100 cells
        <!—Image will be added dynamically to the 7th row, 5th cell
		Document.addEventListener(“DOMContentLoaded”, function () {
    Const gridContainer = document.querySelector(‘.grid-container’);

    // Create 10,000 cells
    For (let I = 0; I < 100 * 100; i++) {
        Const cell = document.createElement(‘div’);
        Cell.classList.add(‘cell’);
        gridContainer.appendChild(cell);
    }

    // Add image to the 7th row, 5th cell
    Const imageCellIndex = 6 * 100 + 4; // 7th row and 5th column (zero-based index)
    Const imageCell = gridContainer.children[imageCellIndex];
    imageCell.classList.add(‘image-cell’);
});

    </div>
</body>
</html>
