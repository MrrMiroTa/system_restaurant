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
    <title>All Stock - Restaurant System</title>
    <link rel="icon" href="./image/U.png">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
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

        #stock-form {
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

        #stock-form input,
        #stock-form textarea,
        #stock-form select {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px;
            font-size: 1em;
        }

        #stock-form button {
            margin-top: 8px;
        }

        .stock-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px #eee;
            overflow: hidden;
        }

        .stock-table th,
        .stock-table td {
            padding: 10px 6px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .stock-table th {
            background: #f5f5f5;
        }

        .stock-table td {
            vertical-align: middle;
        }

        /* Modal styles */
        #history-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            z-index: 3000;
            align-items: center;
            justify-content: center;
        }

        #history-content {
            background: #fff;
            padding: 32px 24px;
            border-radius: 8px;
            max-width: 500px;
            width: 96vw;
            margin: auto;
            box-shadow: 0 2px 16px #888;
            max-height: 90vh;
            overflow-y: auto;
        }

        #history-table {
            width: 100%;
            border-collapse: collapse;
        }

        #history-table th,
        #history-table td {
            padding: 10px 6px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        #history-table th {
            background: #f5f5f5;
        }

        @media (max-width: 900px) {
            #stock-table th,
            #stock-table td {
                font-size: 0.9em;
                padding: 6px 4px;
            }

            .stock-table td button {
                font-size: 0.8em;
                padding: 4px 8px;
            }
        }

        @media (max-width: 600px) {
            #stock-table {
                font-size: 0.85em;
            }

            #stock-table th,
            #stock-table td {
                padding: 4px 2px;
            }

            .stock-table td button {
                display: block;
                width: 100%;
                margin-bottom: 4px;
                font-size: 0.75em;
            }
        }

        @media (max-width: 400px) {
            #stock-table th,
            #stock-table td {
                font-size: 0.75em;
                padding: 3px 1px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'navmenu.php'; ?>
    <div class="main-content">
        <h1 style="text-align:center;">All Stock</h1>
        <button id="show-stock-form-btn" class="add-btn">+ Add Stock</button>
        <form id="stock-form" enctype="multipart/form-data" style="display:none;">
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
        <div id="stock-list"></div>
        <button id="export-history-btn" class="add-btn" style="background:#343a40;margin-bottom:18px;float:right;">Export & Cleanup Old History</button>
    </div>

    <!-- History Modal -->
    <div id="history-modal">
        <div id="history-content">
            <h2 style="margin-top:0;text-align:center;">Stock History</h2>
            <div id="history-table"></div>
            <button onclick="closeHistoryModal()" style="margin-top:18px;background:#007bff;color:#fff;padding:8px 18px;border:none;border-radius:4px;">Close</button>
        </div>
    </div>

    <script src="dashboard.js"></script>
    <script>
        document.getElementById('export-history-btn').onclick = function() {
            if (!confirm('Export and delete all stock history older than 7 days?')) return;
            fetch('../backend/export_stock_history.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.file) {
                        window.open('../backend/' + data.file, '_blank');
                        alert('Exported and cleaned up old history!');
                    } else {
                        alert(data.message || 'No old history to export.');
                    }
                });
        }

        function fetchStockList() {
            fetch('../backend/stock.php?action=list')
                .then(res => res.json())
                .then(function(data) {
                    renderStockList(data);
                });
        }

        function renderStockList(data) {
            const list = document.getElementById('stock-list');
            if (!data.length) {
                list.innerHTML = '<div style="color:#888;text-align:center;">No stock items found.</div>';
                return;
            }
            let html = `<table id="stock-table" class="stock-table" style="width:100%;border-collapse:collapse;box-shadow:0 2px 12px #eee;background:#fff;border-radius:8px;overflow:hidden;">
                <thead>
                    <tr style='background:#f5f5f5;'>
                        <th style="padding:10px 6px;">Picture</th>
                        <th style="padding:10px 6px;">Name</th>
                        <th style="padding:10px 6px;">Category</th>
                        <th style="padding:10px 6px;">Description</th>
                        <th style="padding:10px 6px;">Qty</th>
                        <th style="padding:10px 6px;">Price</th>
                        <th style="padding:10px 6px;">Total Price</th>
                        <th style="padding:10px 6px;">Actions</th>
                    </tr>
                </thead>
                <tbody>`;
            html += data.map(item => `
                <tr data-id="${item.id}" class="stock-row" style="border-bottom:1px solid #eee;">
                    <td style="text-align:center;"><img src="${item.picture || ''}" alt="${item.name}" style="width:44px;height:44px;object-fit:cover;border-radius:4px;"></td>
                    <td>${item.name}</td>
                    <td>${item.category}</td>
                    <td>${item.description}</td>
                    <td><span class="qty">${item.qty}</span></td>
                    <td>$${parseFloat(item.price).toFixed(2)}</td>
                    <td>$${(item.qty * item.price).toFixed(2)}</td>
                    <td style="width:180px;">
                        <button type="button" class="delete-btn add-btn" style="background:#dc3545;margin-bottom:0;">Delete</button>
                        <button type="button" class="stock-in-btn add-btn" style="background:#28a745;margin-bottom:0;">Stock In</button>
                        <button type="button" class="stock-out-btn add-btn" style="background:#007bff;margin-bottom:0;">Stock Out</button>
                        <button type="button" class="history-btn add-btn" style="background:#6c757d;margin-bottom:0;">History</button>
                    </td>
                </tr>
            `).join('');
            html += '</tbody></table>';
            list.innerHTML = html;
        }

        function stockInOut(id, type) {
            const qty = prompt(`Enter quantity to ${type === 'in' ? 'add' : 'remove'}:`);
            if (!qty || isNaN(qty) || qty <= 0) return alert('Invalid quantity!');
            fetch(`../backend/stock_in_out.php?action=${type}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `stock_id=${id}&qty=${qty}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) fetchStockList();
                    else alert(data.error || 'Operation failed');
                });
        }

        function deleteStock(id) {
            if (!confirm('Delete this stock item?')) return;
            fetch(`../backend/stock.php?action=delete&id=${id}`, {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) fetchStockList();
                    else alert('Delete failed');
                });
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent double event binding
            const stockForm = document.getElementById('stock-form');
            const showStockFormBtn = document.getElementById('show-stock-form-btn');
            if (showStockFormBtn && stockForm) {
                showStockFormBtn.onclick = function() {
                    stockForm.style.display = stockForm.style.display === 'none' ? 'block' : 'none';
                    if (stockForm.style.display === 'block') stockForm.reset();
                };
            }
            fetchStockList();
            // Use event delegation on the table itself for best reliability
            document.getElementById('stock-list').addEventListener('click', function(e) {
                let tr = e.target.closest('tr[data-id]');
                if (!tr) return;
                const id = tr.getAttribute('data-id');
                if (e.target.classList.contains('stock-in-btn')) {
                    stockInOut(id, 'in');
                    return;
                }
                if (e.target.classList.contains('stock-out-btn')) {
                    stockInOut(id, 'out');
                    return;
                }
                if (e.target.classList.contains('delete-btn')) {
                    deleteStock(id);
                    return;
                }
                if (e.target.classList.contains('history-btn')) {
                    showHistoryModal(id);
                    return;
                }
            }, false);

            function showHistoryModal(stockId) {
                fetch(`../backend/stock_in_out.php?action=history&stock_id=${stockId}`)
                    .then(res => res.json())
                    .then(function(data) {
                        let html = '';
                        if (data.length) {
                            let runningTotal = 0;
                            html += `<table style='width:100%;border-collapse:collapse;'>`;
                            html += `<thead><tr><th>Date</th><th>User</th><th>Type</th><th>Qty</th><th>Price</th><th>Value</th><th>Running Total</th></tr></thead><tbody>`;
                            data.forEach(h => {
                                const value = h.qty * h.price;
                                runningTotal += h.type === 'in' ? value : -value;
                                html += `<tr><td>${h.date}</td><td>${h.username}</td><td style='color:${h.type==='in'?'#28a745':'#dc3545'};font-weight:bold;'>${h.type.toUpperCase()}</td><td>${h.qty}</td><td>$${parseFloat(h.price).toFixed(2)}</td><td>$${value.toFixed(2)}</td><td>$${runningTotal.toFixed(2)}</td></tr>`;
                            });
                            html += `</tbody></table>`;
                        } else {
                            html = '<div style="color:#888;text-align:center;">No history found.</div>';
                        }
                        document.getElementById('history-table').innerHTML = html;
                        document.getElementById('history-modal').style.display = 'flex';
                    });
            }
        });
        // Fetch stock list immediately on script load (for robustness)
        fetchStockList();

        function closeHistoryModal() {
            document.getElementById('history-modal').style.display = 'none';
        }
    </script>
</body>

</html>
