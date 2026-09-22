<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stock Item - Restaurant System</title>
    <link rel="icon" href="./image/U.png">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'navmenu.php'; ?>
    <div class="main-content">
        <h1>Add Stock Item</h1>
        <form id="stock-form" enctype="multipart/form-data" style="max-width:400px;margin:0 auto;background:#f9f9f9;border-radius:8px;box-shadow:0 2px 8px #eee;padding:16px;display:flex;flex-direction:column;gap:10px;">
            <input type="text" name="name" placeholder="Name" required>
            <input type="file" name="picture" accept="image/*">
            <select name="category" required>
                <option value="ingredient">Ingredient</option>
                <option value="meat">Meat</option>
                <option value="vegetable">Vegetable</option>
                <option value="drink">Drink</option>
            </select>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="number" name="qty" placeholder="Quantity" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <button type="submit">Add Stock</button>
        </form>
    </div>
    <script src="dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stockForm = document.getElementById('stock-form');
            if (stockForm) {
                stockForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(stockForm);
                    fetch('../backend/stock.php?action=add', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                stockForm.reset();
                                alert('Stock item added!');
                            } else {
                                alert('Error: ' + (data.error || 'Failed to add stock'));
                            }
                        });
                }
            }
        });
    </script>
</body>

</html>
