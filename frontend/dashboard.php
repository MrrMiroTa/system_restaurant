<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
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
    <title>Admin Dashboard - Restaurant System</title>

    <link rel="icon" href="./image/U.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<style>
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

    .restaurant-report-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px #eee;
        overflow: hidden;
        margin-top: 8px;
        font-size: 1em;
    }

    .restaurant-report-table th,
    .restaurant-report-table td {
        border: 1px solid #eee;
        text-align: left;
        padding: 8px 10px;
    }

    .restaurant-report-table th {
        background: #f5f5f5;
        color: #007bff;
        font-weight: 600;
    }

    .restaurant-report-table td {
        vertical-align: middle;
    }

    .paid-orders-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px #eee;
        overflow: hidden;
    }

    .paid-orders-table th,
    .paid-orders-table td {
        border: 1px solid #eee;
        padding: 8px 10px;
        text-align: left;
        font-size: 1em;
    }

    .paid-orders-table th {
        background: #f5f5f5;
        color: #007bff;
        font-weight: 600;
    }

    .paid-orders-table td {
        vertical-align: middle;
    }

    .most-ordered-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px #eee;
        overflow: hidden;
        margin-top: 0;
        margin-bottom: 0.5em;
        font-size: 1em;
    }

    .most-ordered-table th,
    .most-ordered-table td {
        border: 1px solid #eee;
        padding: 10px 12px;
        text-align: left;
    }

    .most-ordered-table th {
        background: #f5f5f5;
        color: #007bff;
        font-weight: 600;
    }

    .most-ordered-table td {
        vertical-align: middle;
    }

    .most-ordered-table tr:nth-child(even) {
        background: #fafbfc;
    }

    .most-ordered-table tr:hover {
        background: #f0f8ff;
    }

    @media (max-width: 600px) {

        .paid-orders-table th,
        .paid-orders-table td,
        .restaurant-report-table th,
        .restaurant-report-table td,
        .most-ordered-table th,
        .most-ordered-table td {
            font-size: 0.98em;
            padding: 6px 4px;
        }
    }

    @media (max-width: 400px) {

        .paid-orders-table th,
        .paid-orders-table td,
        .restaurant-report-table th,
        .restaurant-report-table td,
        .most-ordered-table th,
        .most-ordered-table td {
            font-size: 0.9em;
            padding: 4px 2px;
        }
    }
</style>

<body>
    <?php include 'header.php'; ?>
    <?php include 'navmenu.php'; ?>
    <div class="main-content">
        <h1>Dashboard</h1>
        <div style="margin-bottom:16px;font-size:1.1em;color:#007bff;">
            Welcome, <b><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></b>!
        </div>
        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Sales Today</h3>
                <div id="sales-today">0</div>
            </div>
            <div class="card">
                <h3>Total Sales This Month</h3>
                <div id="sales-month">0</div>
            </div>
        </div>
        <div class="card" style="margin-bottom:24px;">
            <h3>Sales Overview (Last 7 Days)</h3>
            <canvas id="salesChart" height="80"></canvas>
        </div>
        <div class="card" id="restaurant-report-card" style="margin-bottom:24px;">
            <h3>Restaurant Daily Report</h3>
            <div id="restaurant-report" style="background:#f8f8f8;padding:18px 16px 12px 16px;border-radius:10px;box-shadow:0 2px 8px #e0e0e0;">
                <div style="color:#888;">Loading report...</div>
            </div>
        </div>
        <div class="card" id="most-ordered-today-card" style="margin-bottom:24px;">
            <h3>Most Ordered Today</h3>
            <div id="most-ordered-today" style="min-height:32px;"></div>
        </div>
        <div class="card" id="paid-orders-card" style="margin-bottom:24px;">
            <h3>Paid Orders (Today)</h3>
            <div id="paid-orders-list" style="overflow-x:auto;" class="order-table"></div>
        </div>
    </div>
    <script src="dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Render most ordered today directly here for robustness
        function renderMostOrderedToday(items) {
            const mostOrderedToday = document.getElementById('most-ordered-today');
            if (!mostOrderedToday) return;
            if (Array.isArray(items) && items.length > 0) {
                let html = `<table class='most-ordered-table'>`;
                html += `<thead><tr><th style='width:48px;'>No.</th><th>Name </th><th style='width:80px;'>Qty</th></tr></thead><tbody>`;
                items.forEach((item, idx) => {
                    html += `<tr style='font-family:Khmer OS Siemreap,Arial,sans-serif;'>`;
                    html += `<td style='text-align:center;'>${idx + 1}</td>`;
                    html += `<td>${item.name}</td>`;
                    html += `<td style='text-align:center;font-weight:bold;color:#007bff;'>${item.total_qty}</td>`;
                    html += `</tr>`;
                });
                html += `</tbody></table>`;
                mostOrderedToday.innerHTML = html;
            } else {
                mostOrderedToday.innerHTML = '<span style="color:#888;">No orders today.</span>';
            }
        }
        // Patch fetchDashboardData to always call renderMostOrderedToday
        const origFetchDashboardData = window.fetchDashboardData;
        window.fetchDashboardData = function() {
            fetch("../backend/dashboard_data.php")
                .then(res => res.json())
                .then(data => {
                    // ...existing code...
                    if (data && 'most_ordered_today' in data) {
                        renderMostOrderedToday(data.most_ordered_today);
                    }
                    // Call original logic if needed
                    if (typeof origFetchDashboardData === 'function') {
                        try {
                            origFetchDashboardData();
                        } catch (e) {}
                    }
                })
                .catch(err => {
                    renderMostOrderedToday([]);
                    console.error('Dashboard data fetch error:', err);
                });
        };
        // Call patched fetchDashboardData on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            window.fetchDashboardData();
        });
    </script>
    <script>
        // Low stock/zero stock alert for dashboard
        function checkLowStockAlert() {
            fetch('../backend/stock.php?action=list')
                .then(res => res.json())
                .then(data => {
                    if (Array.isArray(data)) {
                        const zeroStock = data.filter(item => Number(item.qty) < 5);
                        if (zeroStock.length > 0) {
                            alert('Warning: Some items are out of stock! Please check the stock list.');
                        }
                    }
                });
        }

        function fetchPaidOrders() {
            fetch('../backend/order.php?action=paid_today')
                .then(res => res.json())
                .then(data => {
                    const paidOrdersList = document.getElementById('paid-orders-list');
                    if (data && data.length > 0) {
                        let html = `<table class='paid-orders-table'>`;
                        html += `<thead><tr><th style='padding: 0 20px 0 20px;'>OrderID</th><th style='padding: 0 30px 0 30px;'>User</th><th>Items</th><th>Total</th><th>Date</th></tr></thead><tbody>`;
                        html += data.map(order =>
                            `<tr style='font-family:Khmer OS Siemreap,Arial,sans-serif;'><td><b>${order.id}</b></td><td>${order.username}</td><td>${order.items && order.items.length ? order.items.join(', ') : '-'}</td><td  style='color:blue;'>$${parseFloat(order.total_price).toFixed(2)}</td><td>${order.date_created}</td></tr>`
                        ).join('');
                        html += `</tbody></table>`;
                        paidOrdersList.innerHTML = html;
                    } else {
                        paidOrdersList.innerHTML = '<div style="color:#888;padding:12px;">No paid orders today.</div>';
                    }
                });
        }

        function fetchRestaurantReport() {
            fetch('../backend/order.php?action=report_today')
                .then(res => res.json())
                .then(data => {
                    const reportDiv = document.getElementById('restaurant-report');
                    if (!data || !data.orders || !Array.isArray(data.orders)) {
                        reportDiv.innerHTML = '<div style="color:#888;">No report data for today.</div>';
                        return;
                    }
                    let html = `<div style='font-size:1.1em;margin-bottom:10px;'><b>Total Sales Today:</b> <span style='color:#28a745;font-weight:bold;'>$${parseFloat(data.total_sales).toFixed(2)}</span></div>`;
                    html += `<div style='font-size:1.08em;margin-bottom:8px;'><b>All Orders Today:</b></div>`;
                    html += `<table class='restaurant-report-table'>`;
                    html += `<thead><tr><th>Order ID</th><th>User</th><th>Status</th><th>Total</th><th>Time</th></tr></thead><tbody>`;
                    html += data.orders.map(order =>
                        `<tr><td style='padding:6px 8px;'>${order.id}</td><td style='padding:6px 8px;'>${order.username}</td><td style='padding:6px 8px;color:green;'>${order.status ? order.status : '-'}</td><td style='padding:6px 8px;'>$${parseFloat(order.total_price).toFixed(2)}</td><td style='padding:6px 8px;'>${order.date_created.substr(11,5)}</td></tr>`
                    ).join('');
                    html += `</tbody></table>`;
                    reportDiv.innerHTML = html;
                })
                .catch(() => {
                    document.getElementById('restaurant-report').innerHTML = '<div style="color:#888;">Failed to load report.</div>';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchDashboardData();
            checkLowStockAlert();
            fetchPaidOrders();
            fetchSalesChart();
            fetchRestaurantReport();
        });

        function fetchSalesChart() {
            fetch('../backend/dashboard_data.php?action=sales_chart')
                .then(res => res.json())
                .then((data) => {
                    if (!data || !Array.isArray(data)) return;
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    const labels = data.map(d => d.date);
                    const sales = data.map(d => Number(d.total));
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total Sales',
                                data: sales,
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0,123,255,0.1)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: '#007bff',
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Date'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Sales ($)'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
        }
    </script>
</body>

</html>
