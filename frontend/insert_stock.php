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
    <div class="sidebar" id="sidebar">
        <div class="logo">🍽️ Restaurant</div>
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        <ul class="menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="stock.php">Stock</a></li>
            <li><a href="order.php">Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Add Stock Item</h1>
        <form id="stock-form" enctype="multipart/form-data">
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
                });
            }
        });
    </script>
</body>

</html>