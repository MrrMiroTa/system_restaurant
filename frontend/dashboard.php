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
        <div class="card" id="most-ordered-today-card" style="margin-bottom:24px;">
            <h3>Most Ordered Today</h3>
            <div id="most-ordered-today">Coming Soon...!</div>
        </div>
        <div class="card" id="paid-orders-card" style="margin-bottom:24px;">
            <h3>Paid Orders (Today)</h3>
            <div id="paid-orders-list" style="overflow-x:auto;" class="order-table"></div>
        </div>
    </div>
    <script src="dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Low stock/zero stock alert for dashboard
        function checkLowStockAlert() {
            fetch('../backend/stock.php?action=list')
                .then(res => res.json())
                .then(data => {
                    if (Array.isArray(data)) {
                        const zeroStock = data.filter(item => Number(item.qty) === 0);
                        if (zeroStock.length > 0) {
                            alert('Warning: Some items are out of stock!');
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
                        let html = `<table class='paid-orders-table '>`;
                        html += `<thead><tr><th>Order #</th><th>User</th><th>Items</th><th>Total</th><th>Date</th></tr></thead><tbody>`;
                        html += data.map(order =>
                            `<tr><td ><b>${order.id}</b></td><td>${order.username}</td><td>${order.items && order.items.length ? order.items.join(', ') : '-'}</td><td>$${parseFloat(order.total_price).toFixed(2)}</td><td>${order.date_created}</td></tr>`
                        ).join('');
                        html += `</tbody></table>`;
                        paidOrdersList.innerHTML = html;
                    } else {
                        paidOrdersList.innerHTML = '<div style="color:#888;padding:12px;">No paid orders today.</div>';
                    }
                });
        }
        document.addEventListener('DOMContentLoaded', function() {
            fetchDashboardData();
            checkLowStockAlert();
            fetchPaidOrders();
            fetchSalesChart();
        });

        function fetchSalesChart() {
            fetch('../backend/dashboard_data.php?action=sales_chart')
                .then(res => res.json())
                .then(data => {
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
    <style>
        /* ...existing styles... */
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

        @media (max-width: 600px) {

            .paid-orders-table th,
            .paid-orders-table td {
                font-size: 0.98em;
                padding: 6px 4px;
            }
        }
    </style>
</body>

</html>