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
        .main-content {
            margin-left: 260px;
            max-width: 900px;
            margin-top: 40px;
            margin-bottom: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px #e0e0e0;
            padding: 32px 24px;
        }

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

        @media (max-width: 900px) {
            .main-content {
                margin-left: 0;
                padding: 16px 4vw;
            }
        }

        @media (max-width: 600px) {
            .main-content {
                padding: 8px 2vw;
            }

            .order-item {
                font-size: 0.98em;
                padding: 12px 8px;
            }
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
            <input id="order-search" type="text" placeholder="Search by user, status, or date..." style="flex:1;min-width:220px;max-width:320px;padding:8px 12px;border:1px solid #ccc;border-radius:4px;">
        </div>
        <div style="overflow-x:auto;">
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
            document.getElementById('order-search').addEventListener('input', filterOrders);
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
                        <button class="print-receipt-btn" data-id="${order.id}" title="Print Receipt" style="background:#007bff;color:#fff;border:none;border-radius:4px;padding:6px 12px;margin-left:6px;cursor:pointer;">Print Receipt</button>
                    </td>
                </tr>
            `).join('');
        }

        function renderStatusBadge(status) {
            let color = '#6c757d';
            if (status === 'Paid') color = '#08f811';
            else if (status === 'Pending') color = '#ffc107';
            else if (status === 'Cancelled') color = '#dc3545';
            return `<span style="display:inline-block;padding:4px 12px;border-radius:12px;background:${color};color:#fff;font-size:0.98em;min-width:70px;text-align:center;">${status}</span>`;
        }

        function filterOrders() {
            const q = document.getElementById('order-search').value.toLowerCase();
            renderOrders(allOrders.filter(order => {
                return (
                    (order.username && order.username.toLowerCase().includes(q)) ||
                    (order.role && order.role.toLowerCase().includes(q)) ||
                    (order.status && order.status.toLowerCase().includes(q)) ||
                    (order.date_created && order.date_created.toLowerCase().includes(q)) ||
                    (order.id && String(order.id).includes(q))
                );
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
                                setTimeout(() => location.reload(), 800); // Only reload, do not call fetchOrders() before reload
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
            let html = `<div style='position:relative;'>`;
            // X icon: just close and refresh, do not delete order
            html += `<span id='close-receipt-x' title='Close Receipt' style='position:absolute;top:0;right:0;font-size:1.6em;color:#dc3545;cursor:pointer;font-weight:bold;z-index:10;'>&times;</span>`;
            html += `<h2 style='text-align:center;'>Receipt</h2>`;
            html += `<div><b>Order ID:</b> ${order.id}</div>`;
            html += `<div><b>Date/Time:</b> ${order.date_created}</div>`;
            // Show admin name if available
            if (order.username) html += `<div><b>Admin:</b> ${order.username}</div>`;
            if (order.note && order.note.trim()) html += `<div style='margin:8px 0;'><b>Note:</b> ${order.note.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>`;
            html += `<hr>`;
            html += `<div><b>Items:</b></div>`;
            html += `<ul style='padding-left:18px;'>`;
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
                        html += `<li>${i.name ? i.name : ''} x ${qty} - $${(itemSubtotal - itemDiscount).toFixed(2)}`;
                        if (discount > 0) {
                            html += ` <span style='color:#888;font-size:0.97em;'>(-${discount}%: -$${itemDiscount.toFixed(2)})</span>`;
                        }
                        html += `</li>`;
                    } else {
                        html += `<li>${i}</li>`;
                    }
                });
            }
            html += `</ul>`;
            html += `<div style='margin:8px 0;'><b>Subtotal:</b> $${subtotal.toFixed(2)}</div>`;
            if (totalDiscount > 0) html += `<div style='margin:8px 0;'><b>Discount:</b> -$${totalDiscount.toFixed(2)}</div>`;
            html += `<div style='margin:8px 0;'><b>Total:</b> $${(subtotal - totalDiscount).toFixed(2)}</div>`;
            // Show logo image in receipt (like QR code area)
            html += `<div id='qrcode' style='text-align:center;margin:16px 0;'><img src="./image/image.png" alt="Logo" style="width:140px;height:130px;display:inline-block;"></div>`;
            html += `<div style='text-align:center;margin:12px 0;color:#28a745;font-weight:bold;'>Thank you for your order!</div>`;
            html += `<button id='auto-print-btn' onclick='window.print()' style='background:#007bff;color:#fff;padding:8px 18px;border:none;border-radius:4px;margin-right:8px;'>Print</button>`;
            html += `<button onclick='document.getElementById("receipt-modal").style.display="none"' style='background:#ccc;padding:8px 18px;border:none;border-radius:4px;'>Close</button>`;
            html += `</div>`;
            document.getElementById('receipt-content').innerHTML = html;
            document.getElementById('receipt-modal').style.display = 'flex';

            // Auto trigger print dialog after rendering
            setTimeout(() => {
                const printBtn = document.getElementById('auto-print-btn');
                if (printBtn) printBtn.click();
            }, 300);

            // Add X icon handler: just close and refresh
            setTimeout(() => {
                const xBtn = document.getElementById('close-receipt-x');
                if (xBtn) {
                    xBtn.onclick = function() {
                        document.getElementById('receipt-modal').style.display = 'none';
                        location.reload();
                    };
                }
            }, 100);
        }

        function generateQRCode(orderId) {
            // Show only your image in the QR code area
            const qrDiv = document.getElementById('qrcode');
            qrDiv.innerHTML = `<img src="./image/image.png" alt="Logo" style="width:140px;height:130px;display:inline-block;">`;
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
    </script>
</body>

</html>