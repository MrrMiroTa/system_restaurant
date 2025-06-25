<?php
// cleanup_orders.php
require_once __DIR__ . '/../backend/db.php';
// Delete order_items older than 1 month
$conn->query("DELETE oi FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.date_created < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
// Delete orders older than 1 month
$conn->query("DELETE FROM orders WHERE date_created < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
echo "Old orders and order items deleted.";
