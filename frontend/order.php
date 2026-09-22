<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// if ($_SESSION['role'] !== 'admin') {
//     header('Location: customer_menu.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders - Restaurant System</title>
    <link rel="icon" href="./image/U.png">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        h1 {
            text-align: center;
            margin-bottom: 32px;
            color: #007bff;
            font-size: 2.2em;
            letter-spacing: 1px;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px #eee;
            overflow: hidden;
        }

        .order-table th,
        .order-table td {
            border: 1px solid #eee;
            padding: 10px;
            text-align: left;
        }

        .order-table th {
            background: #f5f5f5;
        }

        .order-table td {
            vertical-align: middle;
        }

        .delete-btn {
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #b52a37;
        }

        .print-receipt-btn {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .main-content {
                padding: 16px 4vw;
            }
        }

        @media (max-width: 600px) {
            .main-content {
                padding: 8px 2vw;
            }

            .order-table th,
            .order-table td {
                font-size: 0.9em;
                padding: 6px 4px;
            }
        }

        @media (max-width: 400px) {
            .order-table th,
            .order-table td {
                font-size: 0.8em;
                padding: 4px 2px;
            }
        }

        /* Responsive receipt items table: show as flex rows/cards */
        #receipt-content table,
        #receipt-content thead,
        #receipt-content tbody,
        #receipt-content tr {
            display: block;
            width: 100%;
        }

        #receipt-content thead {
            display: none;
        }

        #receipt-content tr {
            margin-bottom: 10px;
            background: #f9f9f9;
            border-radius: 6px;
            box-shadow: 0 1px 4px #eee;
            padding: 8px 4px;
        }

        #receipt-content td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 4px;
            border: none !important;
            font-size: 1em;
        }

        #receipt-content td:before {
            content: attr(data-label);
            font-weight: bold;
            color: #007bff;
            flex: 1 1 50%;
            min-width: 90px;
            margin-right: 8px;
        }
    </style>
</head>

<body>
    <?php include 'navmenu.php'; ?>
    <?php include 'header.php'; ?>

    <div class="main-content">
        <h1>All Orders</h1>
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:18px;gap:10px;">
            <button id="export-pdf-btn"
                style="background:#007bff;color:#fff;border:none;border-radius:4px;padding:8px 18px;cursor:pointer;">Export/Print Orders</button>
            <select id="order-date-filter" style="padding:8px 12px;border:1px solid #ccc;border-radius:4px;min-width:140px;">
                <option value="all">All</option>
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="last7">Last 7 Days</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>
            <input id="order-search" type="text" placeholder="Search date..." style="flex:1;min-width:220px;max-width:320px;padding:8px 12px;border:1px solid #ccc;border-radius:4px;">
        </div>
        <div style="overflow-x:auto;font-family:Khmer OS Siemreap,Arial,sans-serif;">
            <table id="order-list" class="order-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>User</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="toast" style="display:none;position:fixed;bottom:32px;right:32px;z-index:9999;background:#28a745;color:#fff;padding:14px 28px;border-radius:8px;box-shadow:0 2px 12px #0002;font-size:1.1em;">Order deleted!</div>
    </div>
    <script>
        // Pass PHP session role to JS
        const USER_ROLE = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';
    </script>
    <script src="dashboard.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchOrders();
            document.getElementById('export-pdf-btn').onclick = function() {
                exportOrdersToPDF();
            };
            document.getElementById('order-date-filter').addEventListener('change', filterOrders);
        });

        let allOrders = [];

        function fetchOrders() {
            fetch('../backend/order.php?action=list')
                .then(res => res.json())
                .then(data => {
                    allOrders = data;
                    renderOrders(data);
                });
        }

        function renderOrders(data) {
            const tbody = document.querySelector('#order-list tbody');
            tbody.innerHTML = data.map(order => `
                <tr>
                    <td><b>${order.id}</b></td>
                    <td>${order.username ? order.username + ' (' + order.role + ')' : order.user_id}</td>
                    <td>${order.items && order.items.length ? order.items.join(', ') : '-'}</td>
                    <td>$${parseFloat(order.total_price).toFixed(2)}</td>
                    <td>${renderStatusBadge(order.status)}</td>
                    <td>${order.date_created}</td>
                    <td>
                        ${USER_ROLE === 'admin' ? `<button class="delete-btn" data-id="${order.id}" title="Delete this order"><span class="delete-text">Delete</span><span class="delete-loading" style="display:none;">...</span></button>` : ''}
                        <button class="print-receipt-btn" data-id="${order.id}" title="Print Receipt" style="margin-left:6px;">Print Receipt</button>
                    </td>
                </tr>
            `).join('');
        }

        function renderStatusBadge(status) {
            let color = 'rgb(0, 207, 38)';
            if (status === 'Paid') color = ' #00c6ff';
            else if (status === 'Pending') color = '#ffc107';
            else if (status === 'Cancelled') color = '#dc3545';
            return `<span style="display:inline-block;padding:4px 12px;border-radius:12px;background:${color};color:#fff;font-size:0.98em;min-width:70px;text-align:center;">${status}</span>`;
        }

        function filterOrders() {
            const q = document.getElementById('order-search').value.trim().toLowerCase();
            const filter = document.getElementById('order-date-filter').value;
            let datePattern = /^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/;
            let dateMatch = q.match(datePattern);
            const now = new Date();
            renderOrders(allOrders.filter(order => {
                // Date filter logic
                if (filter !== 'all' && order.date_created) {
                    const orderDate = new Date(order.date_created);
                    if (filter === 'today') {
                        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                        if (orderDate < today || orderDate >= new Date(today.getTime() + 24 * 60 * 60 * 1000)) return false;
                    } else if (filter === 'yesterday') {
                        const yesterday = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
                        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                        if (orderDate < yesterday || orderDate >= today) return false;
                    } else if (filter === 'last7') {
                        const sevenDaysAgo = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 6);
                        const tomorrow = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
                        if (orderDate < sevenDaysAgo || orderDate >= tomorrow) return false;
                    } else if (filter === 'month') {
                        if (orderDate.getFullYear() !== now.getFullYear() || orderDate.getMonth() !== now.getMonth()) return false;
                    } else if (filter === 'year') {
                        if (orderDate.getFullYear() !== now.getFullYear()) return false;
                    }
                }
                // Search box logic
                if (!q) return true;
                if (order.id && String(order.id).toLowerCase().includes(q)) return true;
                if (order.username && order.username.toLowerCase().includes(q)) return true;
                if (order.role && order.role.toLowerCase().includes(q)) return true;
                if (order.status && order.status.toLowerCase().includes(q)) return true;
                if (order.date_created && order.date_created.toLowerCase().includes(q)) return true;
                if (dateMatch && order.date_created) {
                    let d = dateMatch;
                    let year = d[3].length === 2 ? ('20' + d[3]) : d[3];
                    let month = d[2].padStart(2, '0');
                    let day = d[1].padStart(2, '0');
                    let ymd = `${year}-${month}-${day}`;
                    if (order.date_created.substr(0, 10) === ymd) return true;
                }
                return false;
            }));
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-btn')) {
                if (USER_ROLE !== 'admin') {
                    alert('Only admins can delete orders.');
                    return;
                }
                const btn = e.target;
                const id = btn.getAttribute('data-id');
                if (confirm('Delete this order?')) {
                    btn.querySelector('.delete-text').style.display = 'none';
                    btn.querySelector('.delete-loading').style.display = 'inline';
                    fetch(`../backend/order.php?action=delete&id=${id}`, {
                            method: 'POST'
                        })
                        .then(res => res.json())
                        .then(data => {
                            btn.querySelector('.delete-text').style.display = '';
                            btn.querySelector('.delete-loading').style.display = 'none';
                            if (data.success) {
                                showToast('Order deleted!');
                                setTimeout(() => location.reload(), 800);
                            } else alert('Delete failed');
                        });
                }
            }
        });

        // Add modal for receipt
        if (!document.getElementById('receipt-modal')) {
            const modal = document.createElement('div');
            modal.id = 'receipt-modal';
            modal.style = 'display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;';
            modal.innerHTML = `<div id='receipt-content' style='background:#fff;padding:32px 24px;border-radius:8px;max-width:400px;width:100%;margin:auto;box-shadow:0 2px 16px #888;'></div>`;
            document.body.appendChild(modal);
        }
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('print-receipt-btn')) {
                const orderId = e.target.getAttribute('data-id');
                fetch(`../backend/order.php?action=get&id=${orderId}`)
                    .then(res => res.json())
                    .then(order => {
                        showReceipt(order);
                    });
            }
        });

        function showReceipt(order) {
            if (!order || order.error) {
                let html = `<h2 style='text-align:center;color:#dc3545;'>Receipt Error</h2>`;
                html += `<div style='color:#dc3545;text-align:center;margin:16px;'>Could not load order details.<br>${order && order.error ? order.error : ''}</div>`;
                html += `<button onclick='document.getElementById("receipt-modal").style.display="none"' style='background:#ccc;padding:8px 18px;border:none;border-radius:4px;display:block;margin:0 auto;'>Close</button>`;
                document.getElementById('receipt-content').innerHTML = html;
                document.getElementById('receipt-modal').style.display = 'flex';
                console.error('Order receipt error:', order);
                return;
            }

            let html = `<div style='position:relative;max-height:90vh; display:flex; flex-direction:column; overflow-y:auto; padding-right:8px; scrollbar-width: none; '>`;
            html += `<span id='close-receipt-x' title='Close Receipt' style='position:absolute;top:0;right:0;font-size:1.6em;color:#dc3545;cursor:pointer;font-weight:bold;z-index:10;'>&times;</span>`;
            html += `<h2 style='text-align:center;'>Receipt</h2>`;
            html += `<table style='width:100%;border-collapse:collapse;margin-bottom:10px;'>`;
            html += `<tr><td><b>Order ID:</b></td><td>${order.id}</td></tr>`;
            html += `<tr><td><b>Date/Time:</b></td><td>${order.date_created}</td></tr>`;
            if (order.username) html += `<tr><td><b>Order By:</b></td><td>${order.username}</td></tr>`;
            if (order.note && order.note.trim()) html += `<tr><td><b>Note:</b></td><td>${order.note.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</td></tr>`;
            html += `</table>`;

            html += `<div style='margin-bottom:6px;'><b>Items:</b></div>`;
            html += `<table style='width:100%;border-collapse:collapse;font-size:0.98em;margin-bottom:10px;'>`;
            html += `<thead><tr style='background:#f5f5f5;'><th style='padding:4px 6px;'>Name</th><th style='padding:4px 6px;'>Qty</th><th style='padding:4px 6px;'>Price</th><th style='padding:4px 6px;'>Discount</th><th style='padding:4px 6px;'>Total</th></tr></thead><tbody>`;

            let subtotal = 0,
                totalDiscount = 0,
                total = 0;

            if (order.items && Array.isArray(order.items)) {
                order.items.forEach(i => {
                    if (typeof i === 'object' && i !== null) {
                        const qty = i.qty ? i.qty : 1;
                        const price = i.price ? parseFloat(i.price) : 0;
                        const discount = i.discount ? parseFloat(i.discount) : 0;
                        const itemSubtotal = price * qty;
                        const itemDiscount = itemSubtotal * (discount / 100);
                        subtotal += itemSubtotal;
                        totalDiscount += itemDiscount;
                        total += itemSubtotal - itemDiscount;
                        html += `<tr>`;
                        html += `<td data-label='Name' style='padding:4px 6px;font-family:Khmer OS Siemreap,Arial,sans-serif;'>${i.name ? i.name : ''}</td>`;
                        html += `<td data-label='Qty' style='padding:4px 6px;text-align:center;'>${qty}</td>`;
                        html += `<td data-label='Price' style='padding:4px 6px;text-align:right;'>$${price.toFixed(2)}</td>`;
                        html += `<td data-label='Discount' style='padding:4px 6px;text-align:center;'>${discount > 0 ? '-' + discount + '%' : ''}</td>`;
                        html += `<td data-label='Total' style='padding:4px 6px;text-align:right;'>$${(itemSubtotal - itemDiscount).toFixed(2)}</td>`;
                        html += `</tr>`;
                    }
                });
            }

            const discountPercent = subtotal > 0 ? (totalDiscount / subtotal) * 100 : 0;

            html += `</tbody></table>`;
            html += `<table style='width:100%;border-collapse:collapse;font-size:1em;'>`;
            html += `<tr><td><b>Subtotal:</b></td><td style='text-align:right;'>$${subtotal.toFixed(2)}</td></tr>`;
            if (totalDiscount > 0) {
                html += `<tr><td><b>Discount:</b></td><td style='text-align:right;'>-$${totalDiscount.toFixed(2)} (${discountPercent.toFixed(2)}%) <span style='color:red;font-size:0.97em;'>(-${(totalDiscount*4000).toLocaleString('en-US')} ៛)</span></td></tr>`;
            }
            html += `<tr><td><b>Total:</b></td><td style='text-align:right;font-weight:bold;color:blue;'>$${total.toFixed(2)} <span style='color:red;font-size:0.97em;'>(${(total*4000).toLocaleString('en-US')} ៛)</span></td></tr>`;
            html += `</table>`;

            html += `<div id='qrcode' style='text-align:center;margin:16px 0;'></div>`;
            html += `<div style='text-align:center;margin:12px 0;color:#28a745;font-weight:bold;font-family:Khmer OS Siemreap,Arial,sans-serif;'>សូមអរគុណសម្រាប់ការកម្ម៉ង់🙏!<br>Thank you for order at Friend's Meet🙏!<br>Design By <bold>Mrr. Phors</div>`;
            html += `<button id='print-receipt-btn' onclick='window.print()' style='background:#007bff;color:#fff;padding:8px 18px;border:none;border-radius:4px;margin-right:8px;font-family:Khmer OS Siemreap,Arial,sans-serif;'>Print</button>`;
            html += `<button onclick='document.getElementById("receipt-modal").style.display="none";location.reload();' style='background:#ccc;padding:8px 18px;border:none;border-radius:4px;font-family:Khmer OS Siemreap,Arial,sans-serif;'>Close</button>`;
            html += `</div>`;

            document.getElementById('receipt-content').innerHTML = html;
            document.getElementById('receipt-modal').style.display = 'flex';

            setTimeout(() => {
                window.print();
            }, 400);

            // Generate QR code (e.g., for customer menu)
            generateQRCode(order.id);
        }


        function generateQRCode(orderId) {
            // Show only your image in the QR code area
            const qrDiv = document.getElementById('qrcode');
            qrDiv.innerHTML = `<img src="./image/image.png" alt="Logo" style="width:140px;height:140px;display:inline-block;">`;
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 2000);
        }

        function exportOrdersToPDF() {
            const orderTable = document.getElementById('order-list');
            html2canvas(orderTable).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new window.jspdf.jsPDF('l', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const imgWidth = pageWidth - 40;
                const imgHeight = canvas.height * imgWidth / canvas.width;
                pdf.addImage(imgData, 'PNG', 20, 20, imgWidth, imgHeight);
                pdf.save('orders.pdf');
            });
        }

        // Handle close receipt X icon
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'close-receipt-x') {
                document.getElementById('receipt-modal').style.display = 'none';
                location.reload();
            }
        });
    </script>
</body>

</html>
