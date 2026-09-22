<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header('Location: customer_menu.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Menu - Restaurant System</title>
    <link rel="icon" href="./image/U.png">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .menu-list {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            justify-content: center;
        }

        .menu-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px #ccc;
            padding: 16px;
            width: 260px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
        }

        .menu-item img {
            max-width: 120px;
            max-height: 120px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .add-btn {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 18px;
            font-size: 1em;
            cursor: pointer;
            margin-bottom: 8px;
        }

        .add-btn:hover {
            background: #0056b3;
        }

        #menu-form {
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 8px #eee;
            padding: 16px;
            max-width: 400px;
            margin: 0 auto 24px auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        #menu-form input,
        #menu-form textarea {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px;
            font-size: 1em;
        }

        #menu-form button {
            margin-top: 8px;
        }

        @media (max-width: 1000px) {
            .menu-list {
                gap: 16px;
            }

            .menu-item {
                width: 48vw;
                min-width: 180px;
                max-width: 320px;
            }
        }

        @media (max-width: 768px) {
            .menu-list {
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }

            .menu-item {
                width: 95vw;
                max-width: 350px;
            }

            #menu-form {
                max-width: 98vw;
            }
        }

        @media (max-width: 500px) {
            .menu-item {
                width: 98vw;
                max-width: 99vw;
                padding: 8px;
            }

            #menu-form {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'navmenu.php'; ?>
    <div class="main-content">
        <h1 style="text-align:center;">All Menu</h1>
        <button id="show-menu-form-btn" class="add-btn">+ Add Menu Item</button>
        <form id="menu-form" enctype="multipart/form-data" style="display:none;">
            <input type="text" name="name" placeholder="Name" required>
            <input type="file" name="picture" accept="image/*">
            <input type="text" name="category" placeholder="Category" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="number" name="qty" placeholder="Quantity" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <button type="submit">Add Menu Item</button>
        </form>
        <div id="menu-list" class="menu-list"></div>
    </div>
    <script src="dashboard.js"></script>
    <script>
        function renderMenuList(data) {
            const list = document.getElementById('menu-list');
            if (!data.length) {
                list.innerHTML = '<div style="color:#888;text-align:center;">No menu items found.</div>';
                return;
            }
            list.innerHTML = data.map(item => `
                <div class="menu-item">
                    <img src="${item.picture || ''}" alt="${item.name}">
                    <div><b>${item.name}</b></div>
                    <div>${item.category}</div>
                    <div>${item.description}</div>
                    <div>Qty: ${item.qty}</div>
                    <div>Price: $${parseFloat(item.price).toFixed(2)}</div>
                    <div style="color:#888;font-size:0.95em;">Added by: <b>${item.created_by_name ? item.created_by_name : (item.created_by || 'Unknown')}</b></div>
                </div>
            `).join('');
        }

        function fetchMenuList() {
            fetch('menu.php?action=list')
                .then(res => res.json())
                .then(data => renderMenuList(data));
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent double event binding
            const menuForm = document.getElementById('menu-form');
            const showMenuFormBtn = document.getElementById('show-menu-form-btn');
            if (showMenuFormBtn && menuForm) {
                showMenuFormBtn.onclick = function() {
                    menuForm.style.display = menuForm.style.display === 'none' ? 'block' : 'none';
                    if (menuForm.style.display === 'block') menuForm.reset();
                };
            }
            fetchMenuList();
        });
    </script>
</body>

</html>
