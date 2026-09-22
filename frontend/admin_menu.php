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
    <title>Admin Menu Management</title>
    <link rel="icon" href="./image/U.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./dashboard.css">
    <style>
        .crud-btn {
            margin: 0 4px;
            padding: 4px 10px;
            padding-bottom: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .crud-btn.edit {
            margin-bottom: 10px;
            background: #ffc107;
            color: #222;
        }

        .crud-btn.delete {
            background: #dc3545;
            color: #fff;
        }

        .crud-btn.save {
            background: #28a745;
            color: #fff;
        }

        .crud-btn.cancel {
            background: #6c757d;
            color: #fff;
        }

        .admin-menu-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }

        .admin-menu-table th,
        .admin-menu-table td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }

        .admin-menu-table th {
            background: #f5f5f5;
        }

        h1 {
            text-align: center;
            color: rgb(0, 39, 81);
            font-size: 2.2em;
            margin-bottom: 24px;
        }

        @media (max-width: 900px) {
            .admin-menu-table th,
            .admin-menu-table td {
                font-size: 0.85em;
                padding: 6px 4px;
            }

            .crud-btn {
                font-size: 0.8em;
                padding: 3px 8px;
            }
        }

        @media (max-width: 600px) {
            .admin-menu-table th,
            .admin-menu-table td {
                font-size: 0.75em;
                padding: 4px 2px;
            }

            .crud-btn {
                display: block;
                width: 100%;
                margin-bottom: 4px;
                font-size: 0.7em;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'navmenu.php'; ?>
    <div class="main-content">
        <h1>Menu Management</h1>
        <button id="show-menu-form-btn" class="add-btn" style="margin-bottom:16px;display:inline-block;">+ Add Menu Item
        </button>
        <form id="menu-form" enctype="multipart/form-data"
            style="display:none;max-width:400px;margin:0 auto 24px auto;background:#f9f9f9;border-radius:8px;box-shadow:0 2px 8px #eee;padding:16px;flex-direction:column;gap:10px;">
            <input type="text" name="name" placeholder="Name" required>
            <input type="file" name="picture" accept="image/*">
            <input type="text" name="category" placeholder="Category" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="number" name="qty" placeholder="Quantity" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <button type="submit">Add Menu Item</button>
            <button type="button" id="hide-menu-form-btn"
                style="margin-top:8px;background:#ccc;color:#222;border:none;border-radius:4px;padding:6px 0;">Cancel</button>
        </form>
        <div style="overflow-x:auto;">
            <table class="admin-menu-table" id="admin-menu-table">
                <thead>
                    <tr>
                        <th>Picture</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <script src="dashboard.js"></script>
    <script>
        function renderAdminMenuTable(data) {
            const tbody = document.querySelector('#admin-menu-table tbody');
            tbody.innerHTML = data.map(item => `
          <tr data-id="${item.id}">
            <td><img src="${item.picture || ''}" alt="" style="max-width:60px;max-height:60px;object-fit:cover;"></td>
            <td class="name">${item.name}</td>
            <td class="category">${item.category}</td>
            <td class="description">${item.description}</td>
            <td class="qty">${item.qty}</td>
            <td class="price">$${parseFloat(item.price).toFixed(2)}</td>
            <td class="created_by_name">${item.created_by_name || ''}</td>
            <td>
              <button class="crud-btn edit">Edit</button>
              <button class="crud-btn delete">Delete</button>
            </td>
          </tr>
        `).join('');
        }

        function fetchAdminMenu() {
            fetch('../backend/menu.php?action=list')
                .then(res => res.json())
                .then(data => renderAdminMenuTable(data));
        }
        document.addEventListener('DOMContentLoaded', function() {
            fetchAdminMenu();
            // Add menu form toggle
            const menuForm = document.getElementById('menu-form');
            const showMenuFormBtn = document.getElementById('show-menu-form-btn');
            const hideMenuFormBtn = document.getElementById('hide-menu-form-btn');
            if (showMenuFormBtn && menuForm) {
                showMenuFormBtn.style.display = 'inline-block';
                menuForm.style.display = 'none';
                showMenuFormBtn.onclick = function() {
                    menuForm.style.display = 'block';
                    showMenuFormBtn.style.display = 'none';
                    menuForm.reset();
                };
            }
            if (hideMenuFormBtn && menuForm && showMenuFormBtn) {
                hideMenuFormBtn.onclick = function() {
                    menuForm.style.display = 'none';
                    showMenuFormBtn.style.display = 'inline-block';
                };
            }
            document.querySelector('#admin-menu-table').addEventListener('click', function(e) {
                const tr = e.target.closest('tr');
                if (!tr) return;
                const id = tr.getAttribute('data-id');
                if (e.target.classList.contains('delete')) {
                    if (confirm('Delete this menu item?')) {
                        fetch(`../backend/menu.php?action=delete&id=${id}`, {
                                method: 'POST'
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) fetchAdminMenu();
                                else if (data.error) alert(data.error);
                                else alert('Delete failed');
                            });
                    }
                }
                if (e.target.classList.contains('edit')) {
                    // Inline edit for all fields, but allow partial update
                    const editableFields = ['name', 'category', 'description', 'qty', 'price'];
                    const originalValues = {};
                    editableFields.forEach(field => {
                        const td = tr.querySelector('.' + field);
                        if (td) {
                            originalValues[field] = td.textContent.replace('$', '').trim();
                            td.innerHTML = `<input value="${originalValues[field]}" style="width:90%">`;
                        }
                    });
                    e.target.style.display = 'none';
                    const saveBtn = document.createElement('button');
                    saveBtn.textContent = 'Save';
                    saveBtn.className = 'crud-btn save';
                    const cancelBtn = document.createElement('button');
                    cancelBtn.textContent = 'Cancel';
                    cancelBtn.className = 'crud-btn cancel';
                    e.target.parentNode.appendChild(saveBtn);
                    e.target.parentNode.appendChild(cancelBtn);
                    saveBtn.onclick = function(ev) {
                        ev.preventDefault();
                        const inputs = tr.querySelectorAll('input');
                        const updateData = {};
                        editableFields.forEach((field, idx) => {
                            const input = inputs[idx];
                            if (!input) return;
                            const newValue = input.value.trim();
                            if (newValue !== originalValues[field]) {
                                updateData[field] = newValue;
                            }
                        });
                        if (Object.keys(updateData).length === 0) {
                            fetchAdminMenu();
                            return;
                        }
                        fetch(`../backend/menu.php?action=update&id=${id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(updateData)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Update successfully');
                                    fetchAdminMenu();
                                } else alert('Update failed');
                            });
                    };
                    cancelBtn.onclick = function(ev) {
                        ev.preventDefault();
                        fetchAdminMenu();
                    };
                }
            });
        });
    </script>
</body>

</html>
