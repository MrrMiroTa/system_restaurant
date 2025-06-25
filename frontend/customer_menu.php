<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
// if ($_SESSION['role'] !== 'user') {
//     header('Location: dashboard.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Order Food</title>
    <link rel="icon" href="./image/U.png">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        @font-face {
            font-family: 'Khmer OS Siemreap';
            src: url('./fonts/KhmerOSSiemreap.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        html,
        body,
        * {
            font-family: 'Khmer OS Siemreap', 'Segoe UI', 'Arial', 'sans-serif' !important;
        }

        body,
        .main-content,
        .customer-menu-list,
        .customer-menu-item,
        .sidebar,
        .category-btn,
        #cart-modal,
        #receipt-modal,
        #cart-total,
        #cart-items,
        #cart-btn,
        .cart-badge {
            font-family: 'Khmer OS Siemreap', 'Segoe UI', 'Arial', 'sans-serif';
        }

        .customer-menu-list {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            justify-content: center;
        }

        @media (max-width: 1200px) {
            .customer-menu-list {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .customer-menu-list {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .customer-menu-list {
                grid-template-columns: 1fr;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 10px;
            }

            .sidebar {
                width: 220px;
                left: -220px;
                transition: left 0.3s;
            }

            .sidebar.active {
                left: 0;
            }
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100vh;
            background: #222;
            color: #fff;
            z-index: 1200;
            transition: left 0.3s;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar .logo {
            font-size: 1.5em;
            font-weight: bold;
            padding: 24px 20px 12px 20px;
        }

        .sidebar .menu-toggle,
        .sidebar .menu-close {
            display: block;
            background: none;
            border: none;
            color: #fff;
            font-size: 2em;
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1300;
        }

        @media (min-width: 901px) {
            .sidebar {
                left: 0;
            }

            .sidebar .menu-toggle,
            .sidebar .menu-close {
                display: none;
            }
        }

        .customer-menu-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px #ccc;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
        }

        .customer-menu-item img {
            max-width: 120px;
            max-height: 120px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .customer-menu-item button {
            margin-top: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            cursor: pointer;
        }

        .customer-menu-item button:hover {
            background: #0056b3;
        }

        h1 {
            text-align: center;
            color: rgb(0, 39, 81);
            font-size: 2.2em;
            margin-bottom: 24px;
        }

        .main-content {
            margin-left: 230px;
            padding: 10px 32px 32px 32px;
            background: rgba(184, 221, 253, 0.95);
            border-radius: 18px;
            box-shadow: 0 4px 32px #b6c6e0aa;
            max-width: 1100px;
            margin-top: 36px;
            margin-bottom: 36px;
            transition: margin-left 0.3s;
        }

        @media (max-width: 600px) {
            .main-content {
                margin-left: 0 !important;
                padding: 10px;
            }
        }

        .category-btn {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .category-btn:hover {
            background: #0056b3;
        }

        .category-btn.active {
            background: #0056b3;
        }

        #cart-modal {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 350px;
            height: 100vh;
            background: #fff;
            box-shadow: -2px 0 8px #ccc;
            z-index: 1000;
            padding: 24px;
            overflow-y: auto;
        }

        #cart-modal h2 {
            margin-top: 0;
        }

        #cart-items {
            margin-bottom: 16px;
        }

        #cart-total {
            margin: 16px 0;
            font-weight: bold;
        }

        #checkout-btn {
            background: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #checkout-btn:hover {
            background: #218838;
        }

        .floating-cart-btn {
            position: fixed;
            bottom: 32px;
            right: 32px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 28px;
            z-index: 999;
            box-shadow: 0 2px 8px #aaa;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            z-index: 1001;
        }

        #receipt-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        #receipt-content {
            background: #fff;
            padding: 32px 24px;
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            margin: auto;
            box-shadow: 0 2px 16px #888;
        }
    </style>
</head>

<body style="background:#f8f8f8;">
    <?php include 'navmenu.php'; ?>
    <?php include 'header.php'; ?>

    <div class="main-content">
        <h1 style="text-align:center;">Menu</h1>
        <div id="category-filter-bar" style="display:flex;gap:12px;justify-content:center;margin-bottom:24px;"></div>
        <div id="customer-menu-list" class="customer-menu-list"></div>
    </div>
    <div id="cart-modal" style="display:none;position:fixed;top:0;right:0;width:350px;height:100vh;background:#fff;box-shadow:-2px 0 8px #ccc;z-index:1000;padding:24px;overflow-y:auto;">
        <h2>🛒 Your Cart</h2>
        <div id="cart-items"></div>
        <div id="cart-total" style="margin:16px 0;font-weight:bold;"></div>
        <button id="checkout-btn" style="background:#28a745;color:#fff;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;">Checkout</button>
        <button onclick="toggleCart(false)" style="margin-left:10px;background:#ccc;">Close</button>
    </div>
    <div id="receipt-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;">
        <div id="receipt-content" style="background:#fff;padding:32px 24px;border-radius:8px;max-width:400px;width:100%;margin:auto;box-shadow:0 2px 16px #888;">
            <!-- Receipt will be rendered here -->
        </div>
    </div>
    <button onclick="toggleCart(true)" class="floating-cart-btn" id="cart-btn" style="position:fixed;bottom:32px;right:32px;">
        🛒
        <span class="cart-badge" id="cart-badge" style="display:none;">0</span>
    </button>
    <script src="dashboard.js"></script>
    <script>
        const ADMIN_NAME = '<?php echo isset($_SESSION['username']) ? addslashes($_SESSION['username']) : ''; ?>';

        let allMenuItems = [];
        let currentCategory = 'All';
        let cart = [];

        // Load cart from localStorage if exists
        function loadCart() {
            try {
                const saved = localStorage.getItem('customer_cart');
                if (saved) cart = JSON.parse(saved);
            } catch (e) {
                cart = [];
            }
        }
        // Save cart to localStorage
        function saveCart() {
            localStorage.setItem('customer_cart', JSON.stringify(cart));
        }

        // Call loadCart on page load
        loadCart();

        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth <= 900) {
                document.getElementById('sidebar').classList.remove('active');
            }
        }

        function toggleCart(show) {
            document.getElementById('cart-modal').style.display = show ? 'block' : 'none';
            if (show) renderCart();
        }

        function addToCart(itemId) {
            const item = allMenuItems.find(i => i.id == itemId);
            if (!item) return;
            const found = cart.find(i => i.id == itemId);
            if (found) {
                if (found.qty < item.qty) found.qty++;
            } else {
                cart.push({
                    ...item,
                    qty: 1
                });
            }
            saveCart();
            renderCart();
            updateCartBadge();
            showAddToCartToast();
        }

        function updateCartQty(itemId, delta) {
            const idx = cart.findIndex(i => i.id == itemId);
            if (idx > -1) {
                cart[idx].qty += delta;
                if (cart[idx].qty <= 0) cart.splice(idx, 1);
                saveCart();
                renderCart();
                updateCartBadge();
            }
        }

        const cartTotalDiv = document.getElementById('cart-total');
        if (cartTotalDiv && !document.getElementById('discount-row')) {
            const discountRow = document.createElement('div');
            discountRow.id = 'discount-row';
            discountRow.style = 'margin:8px 0;';
            discountRow.innerHTML = `
        <label style="font-size:0.98em;">Discount (%): <input type="number" id="discount-input" min="0" max="100" value="0" style="width:60px;margin-left:6px;"> </label>
    `;
            cartTotalDiv.parentNode.insertBefore(discountRow, cartTotalDiv.nextSibling);
        }

        let discountPercent = 0;

        function getDiscountPercent() {
            const input = document.getElementById('discount-input');
            let val = parseFloat(input ? input.value : 0);
            if (isNaN(val) || val < 0) val = 0;
            if (val > 100) val = 100;
            return val;
        }

        // Add per-item discount to cart items
        function renderCart() {
            const cartItems = document.getElementById('cart-items');
            if (!cart.length) {
                cartItems.innerHTML = '<div style="color:#888;text-align:center;padding:32px 0;">🛒 Cart is empty.</div>';
                document.getElementById('cart-total').textContent = '';
                if (document.getElementById('discount-row')) document.getElementById('discount-row').style.display = 'none';
                return;
            }
            if (document.getElementById('discount-row')) document.getElementById('discount-row').style.display = 'none'; // Hide global discount
            cartItems.innerHTML = cart.map((item, idx) => {
                if (typeof item.discount !== 'number') item.discount = 0;
                return `
                    <div style="display:flex;align-items:center;gap:12px;background:#f6f6f6;border-radius:8px;padding:12px 10px;margin-bottom:14px;box-shadow:0 1px 4px #eee;">
                        <img src="${item.picture || ''}" alt="${item.name}" style="width:54px;height:54px;border-radius:6px;object-fit:cover;border:1px solid #e0e0e0;">
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:1.08em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.name}</div>
                            <div style="color:#888;font-size:0.98em;">${item.category}</div>
                            <div style="margin-top:4px;font-size:0.97em;">
                                <label>Discount: <input type="number" min="0" max="100" value="${item.discount}" data-cart-idx="${idx}" class="item-discount-input" style="width:48px;"> %</label>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:4px;">
                            <button onclick="updateCartQty(${item.id},-1)" style="width:28px;height:28px;background:#eee;border:none;border-radius:4px;font-size:1.1em;font-weight:bold;color:#007bff;cursor:pointer;">-</button>
                            <span style="min-width:22px;text-align:center;font-weight:500;">${item.qty}</span>
                            <button onclick="updateCartQty(${item.id},1)" style="width:28px;height:28px;background:#eee;border:none;border-radius:4px;font-size:1.1em;font-weight:bold;color:#007bff;cursor:pointer;">+</button>
                        </div>
                        <div style="font-weight:600;color:#222;min-width:60px;text-align:right;">$${((item.price * (1 - item.discount/100)) * item.qty).toFixed(2)}</div>
                    </div>
                `;
            }).join('');

            // Bind per-item discount input events
            document.querySelectorAll('.item-discount-input').forEach(input => {
                input.addEventListener('input', function() {
                    let idx = parseInt(this.getAttribute('data-cart-idx'));
                    let val = parseFloat(this.value);
                    if (isNaN(val) || val < 0) val = 0;
                    if (val > 100) val = 100;
                    cart[idx].discount = val;
                    saveCart();
                    renderCart();
                    updateCartBadge();
                });
            });
            // Calculate totals
            let subtotal = 0,
                totalDiscount = 0,
                total = 0;
            cart.forEach(item => {
                const itemSubtotal = item.price * item.qty;
                const itemDiscount = itemSubtotal * (item.discount / 100);
                subtotal += itemSubtotal;
                totalDiscount += itemDiscount;
                total += itemSubtotal - itemDiscount;
            });
            // Add note input above total
            let noteValue = localStorage.getItem('customer_note') || '';
            document.getElementById('cart-total').innerHTML = `
                <div style="margin-bottom:8px;">
                    <label for="customer-note" style="color:#007bff;font-size:1.02em;font-weight:500;">Note for Admin/Shop (optional):</label><br>
                    <textarea id="customer-note" rows="2" style="width:100%;margin-top:4px;resize:vertical;">${noteValue}</textarea>
                </div>
                <div style="margin-bottom:8px;color:#007bff;font-size:1.02em;font-weight:500;">សម្គាល់: សូមពិនិត្យមើលចំនួន និងបញ្ចុះតម្លៃមុនបញ្ជាទិញ។<br>Note: Please review your items and discounts before checkout.</div>
                <span style="font-size:1.1em;">Total:</span> <span style="font-weight:bold;font-size:1.15em;color:#28a745;">$${total.toFixed(2)}</span>${totalDiscount > 0 ? ` <span style="color:#888;font-size:0.98em;">(Discount: -$${totalDiscount.toFixed(2)})</span>` : ''}
            `;
            // Save note to localStorage on change
            const noteInput = document.getElementById('customer-note');
            if (noteInput) {
                noteInput.addEventListener('input', function() {
                    localStorage.setItem('customer_note', this.value);
                });
            }
        }

        function renderMenuItems(items) {
            const menuList = document.getElementById('customer-menu-list');
            if (!items.length) {
                menuList.innerHTML = '<div style="grid-column:1/-1;text-align:center;">No items found.</div>';
                return;
            }
            menuList.innerHTML = items.map(item => `
                <div class="customer-menu-item">
                    <img src="${item.picture || ''}" alt="${item.name}">
                    <div><b>${item.name}</b></div>
                    <div>${item.category}</div>
                    <div>${item.description}</div>
                    <div>Price: $${parseFloat(item.price).toFixed(2)}</div>
                    <div>Available: ${item.qty}</div>
                    <button onclick="addToCart(${item.id})">Order</button>
                </div>
            `).join('');
        }

        function renderCategoryBar(categories) {
            const bar = document.getElementById('category-filter-bar');
            bar.innerHTML = '';
            const allBtn = document.createElement('button');
            allBtn.textContent = 'All';
            allBtn.className = 'category-btn' + (currentCategory === 'All' ? ' active' : '');
            allBtn.onclick = () => selectCategory('All');
            bar.appendChild(allBtn);
            categories.forEach(cat => {
                const btn = document.createElement('button');
                btn.textContent = cat;
                btn.className = 'category-btn' + (currentCategory === cat ? ' active' : '');
                btn.onclick = () => selectCategory(cat);
                bar.appendChild(btn);
            });
        }

        function selectCategory(cat) {
            currentCategory = cat;
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.toggle('active', btn.textContent === cat);
            });
            if (cat === 'All') {
                renderMenuItems(allMenuItems);
            } else {
                renderMenuItems(allMenuItems.filter(item => item.category === cat));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            Promise.all([
                fetch('../backend/menu.php?action=categories').then(res => res.json()),
                fetch('../backend/menu.php?action=list').then(res => res.json())
            ]).then(([categories, menuItems]) => {
                allMenuItems = menuItems;
                renderCategoryBar(categories);
                renderMenuItems(menuItems);
            });
        });

        document.getElementById('checkout-btn').onclick = function() {
            if (!cart.length) {
                return;
            }
            // Check for any item with qty = 0
            let outOfStock = cart.find(i => {
                const menu = allMenuItems.find(m => m.id == i.id);
                return !menu || menu.qty === 0 || i.qty > menu.qty;
            });
            if (outOfStock) {
                alert('Cannot place order: One or more items are out of stock! Please inform admin.');
                return;
            }
            // Prepare items with per-item discount
            const items = cart.map(i => ({
                menu_id: i.id,
                qty: i.qty,
                price: i.price,
                discount: typeof i.discount === 'number' ? i.discount : 0
            }));
            let subtotal = 0,
                totalDiscount = 0,
                total = 0;
            items.forEach(item => {
                const itemSubtotal = item.price * item.qty;
                const itemDiscount = itemSubtotal * (item.discount / 100);
                subtotal += itemSubtotal;
                totalDiscount += itemDiscount;
                total += itemSubtotal - itemDiscount;
            });
            // Get note value
            const note = document.getElementById('customer-note') ? document.getElementById('customer-note').value : '';
            fetch('../backend/order.php?action=add', {
                    method: 'POST',
                    headers: {},
                    body: new URLSearchParams({
                        items: JSON.stringify(items),
                        total_price: total,
                        discount_amount: totalDiscount,
                        note: note
                    })
                })
                .then(async res => {
                    if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
                    const text = await res.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        // Log raw response for debugging
                        console.error('Raw backend response:', text);
                        alert('Order error: Backend did not return valid JSON. See console for details.');
                        throw e;
                    }
                })
                .then(data => {
                    console.log('Order response:', data); // Debug log
                    if (data.success) {
                        alert('Order placed successfully!');
                        showReceipt(data.order_id, items, subtotal, totalDiscount, total, note);
                        cart = [];
                        saveCart();
                        localStorage.removeItem('customer_note');
                        renderCart();
                        updateCartBadge();
                        toggleCart(false);
                    } else {
                        alert('Order failed! ' + (data.message || 'Unknown error.'));
                    }
                })
                .catch(err => {
                    alert('Order error: ' + err);
                    console.error('Order fetch error:', err);
                });
        };

        let receiptModalOpen = false;

        // Update showReceipt to show discount and note
        function showReceipt(orderId, items, subtotal, discount, total, note) {
            try {
                receiptModalOpen = false;
                const now = new Date();
                // Get admin name from PHP session (injected into JS)
                let adminName = typeof ADMIN_NAME !== 'undefined' ? ADMIN_NAME : '';
                let html = `<h2 style='text-align:center;position:relative;'>Receipt` +
                    `<span onclick='closeReceiptAndBack()' title='Close Receipt' style='position:absolute;top:0;right:0;font-size:1.3em;color:#dc3545;cursor:pointer;padding:0 8px;'>&#10006;</span></h2>`;
                html += `<div><b>Order ID:</b> ${orderId}</div>`;
                html += `<div><b>Date/Time:</b> ${now.toLocaleString()}</div>`;
                if (adminName) html += `<div><b>Admin:</b> ${adminName}</div>`;
                if (note && note.trim()) html += `<div style='margin:8px 0;'><b>Note:</b> ${note.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>`;
                html += `<hr>`;
                html += `<div><b>Items:</b></div>`;
                html += `<ul style='padding-left:18px;'>`;
                items.forEach(i => {
                    const menu = allMenuItems.find(m => m.id == i.menu_id);
                    const itemSubtotal = i.price * i.qty;
                    const itemDiscount = itemSubtotal * (i.discount / 100);
                    html += `<li>${menu ? menu.name : 'Item'} x ${i.qty} - $${(itemSubtotal - itemDiscount).toFixed(2)}`;
                    if (i.discount > 0) html += ` <span style='color:#888;font-size:0.97em;'>(-${i.discount}%: -$${itemDiscount.toFixed(2)})</span>`;
                    html += `</li>`;
                });
                html += `</ul>`;
                html += `<div style='margin:8px 0;'><b>Subtotal:</b> $${subtotal.toFixed(2)}</div>`;
                if (discount > 0) html += `<div style='margin:8px 0;'><b>Discount:</b> -$${discount.toFixed(2)}</div>`;
                html += `<div style='margin:8px 0;'><b>Total:</b> $${total.toFixed(2)}</div>`;
                html += `<div id='qrcode' style='text-align:center;margin:16px 0;'></div>`;
                html += `<div style='text-align:center;margin:12px 0;color:#28a745;font-weight:bold;'>Thank you for your order!</div>`;
                html += `<div style='margin:10px 0;'><b>Feedback:</b><br><textarea style='width:100%;height:60px;'></textarea></div>`;
                html += `<button onclick='window.print()' style='background:#007bff;color:#fff;padding:8px 18px;border:none;border-radius:4px;margin-right:8px;'>Print</button>`;
                html += `<button onclick='closeReceipt()' style='background:#ccc;padding:8px 18px;border:none;border-radius:4px;'>Close</button>`;
                const receiptContent = document.getElementById('receipt-content');
                if (receiptContent) receiptContent.innerHTML = html;
                const modal = document.getElementById('receipt-modal');
                if (modal) modal.style.display = 'flex';
                generateQRCode(orderId);
            } catch (err) {
                alert('Error displaying receipt: ' + err);
                console.error('Receipt modal error:', err);
            }
        }

        // New function for X button: just close and reload, do not delete order
        function closeReceiptAndBack() {
            document.getElementById('receipt-modal').style.display = 'none';
            location.reload();
        }

        function closeReceipt() {
            receiptModalOpen = false;
            document.getElementById('receipt-modal').style.display = 'none';
            location.reload(); // RELOAD ONLY WHEN USER CLOSES RECEIPT
        }

        // Prevent accidental modal close if receipt is open
        window.addEventListener('keydown', function(e) {
            if (receiptModalOpen && (e.key === 'Escape' || e.keyCode === 27)) {
                e.preventDefault();
            }
        });

        // Prevent clicking outside the modal from closing it
        const receiptModal = document.getElementById('receipt-modal');
        if (receiptModal) {
            receiptModal.addEventListener('click', function(e) {
                if (receiptModalOpen && e.target === receiptModal) {
                    // Do nothing, require explicit close
                    e.stopPropagation();
                }
            });
        }

        // Ensure correct button visibility on load and resize
        function updateSidebarShowBtn() {
            const sidebar = document.getElementById('sidebar');
            const showBtn = document.getElementById('sidebar-show-btn');
            if (window.innerWidth <= 900 && !sidebar.classList.contains('active')) {
                showBtn.style.display = 'flex';
            } else {
                showBtn.style.display = 'none';
            }
        }
        window.addEventListener('resize', updateSidebarShowBtn);
        document.addEventListener('DOMContentLoaded', updateSidebarShowBtn);

        function updateCartBadge() {
            const badge = document.getElementById('cart-badge');
            const count = cart.reduce((sum, i) => sum + i.qty, 0);
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        // Add toast notification for add to cart
        function showAddToCartToast() {
            let toast = document.getElementById('add-to-cart-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'add-to-cart-toast';
                toast.style.position = 'fixed';
                toast.style.bottom = '90px';
                toast.style.right = '40px';
                toast.style.background = '#222';
                toast.style.color = '#fff';
                toast.style.padding = '14px 28px';
                toast.style.borderRadius = '8px';
                toast.style.fontSize = '1.08em';
                toast.style.boxShadow = '0 2px 12px #888';
                toast.style.zIndex = '3000';
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                document.body.appendChild(toast);
            }
            toast.textContent = 'Added to cart!';
            toast.style.opacity = '1';
            setTimeout(() => {
                toast.style.opacity = '0';
            }, 1200);
        }

        // Listen for discount input changes
        setTimeout(() => {
            const discountInput = document.getElementById('discount-input');
            if (discountInput) {
                discountInput.addEventListener('input', function() {
                    discountPercent = getDiscountPercent();
                    renderCart();
                });
            }
        }, 500);

        function generateQRCode(orderId) {
            // Show only your image in the QR code area
            const qrDiv = document.getElementById('qrcode');
            qrDiv.innerHTML = `<img src="./image/image.png" alt="Logo" style="width:140px;height:130px;display:inline-block;">`;
        }

        // Remove or comment out the old deleteOrderAndBack function if not used elsewhere.
    </script>
</body>

</html>