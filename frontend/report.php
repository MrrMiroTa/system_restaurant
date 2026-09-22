<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report - Restaurant System</title>
    <link rel="icon" href="./image/U.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <!-- Google Fonts: Noto Sans Khmer for Khmer support -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Add the Khmer font for jsPDF (make sure this file exists) -->
    <script src="fonts/NotoSansKhmer-Medium-normal.js"></script>
    <style>
        #report-form {
            margin-bottom: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        #report-form label {
            font-weight: bold;
        }

        #report-form input[type="date"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        #report-form button {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 18px;
            cursor: pointer;
        }

        #report-form button:hover {
            background: #0056b3;
        }

        #report-form span {
            font-size: 0.95em;
            color: #888;
            flex: 1 1 100%;
        }

        #pdf-report-content {
            background: #fff;
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 18px 18px 18px;
            box-shadow: 0 2px 8px #eee;
            border-radius: 10px;
            font-family: 'Noto Sans Khmer', Arial, sans-serif;
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
            font-family: 'Noto Sans Khmer', Khmer OS Siemreap, Arial, sans-serif;
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

            .most-ordered-table th,
            .most-ordered-table td {
                font-size: 0.98em;
                padding: 6px 4px;
            }

            #report-form {
                flex-direction: column;
                align-items: stretch;
            }

            #report-form input[type="date"] {
                width: 100%;
            }

            #report-form button {
                width: 100%;
            }
        }

        @media (max-width: 400px) {

            .most-ordered-table th,
            .most-ordered-table td {
                font-size: 0.9em;
                padding: 4px 2px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'navmenu.php'; ?>
    <div class="main-content">
        <form id="report-form">
            <label for="report-date"><b>Select or Input Date:</b></label>
            <input type="date" id="report-date" name="report-date" max="<?php echo date('Y-m-d'); ?>" required placeholder="YYYY-MM-DD" pattern="\\d{4}-\\d{2}-\\d{2}">
            <span>You can type or pick a date (format: YYYY-MM-DD)</span>
            <button type="submit">View Report</button>
        </form>
        <div id="pdf-report-content" style="background:#fff;max-width:900px;margin:0 auto;padding:24px 18px 18px 18px;box-shadow:0 2px 8px #eee;border-radius:10px; font-family: 'Noto Sans Khmer', Arial, sans-serif;">
            <div id="report-content-inner">
                <div id="report-title" style="font-size:2em;font-weight:bold;margin-bottom:8px;">Daily Report</div>
                <div id="report-date-label" style="font-size:1.1em;margin-bottom:12px;color:#007bff;"></div>
                <div id="report-section">
                    <div style="color:#888;">Please select a date to view the report.</div>
                </div>
            </div>
        </div>
        <button id="generate-pdf" style="margin-top:18px;display:none;">Generate PDF</button>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        const username = <?php echo json_encode($username); ?>;
        const pdfBtn = document.getElementById('generate-pdf');
        let lastReportData = null;

        document.getElementById('report-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const date = document.getElementById('report-date').value;
            if (!date) return;
            fetch(`../backend/order.php?action=report_by_date&date=${date}`)
                .then(async res => {
                    let text = await res.text();
                    try {
                        const data = JSON.parse(text);
                        lastReportData = data;
                        let reportHtml = '';
                        reportHtml += `<div id='report-title' style='font-size:2em;font-weight:bold;margin-bottom:8px;'>Daily Report</div>`;
                        reportHtml += `<div id='report-date-label' style='font-size:1.1em;margin-bottom:12px;color:#007bff;'><b>Date:</b> <span style='color:#333;'>${date}</span></div>`;
                        if (!data || !data.orders || !Array.isArray(data.orders) || data.orders.length === 0) {
                            reportHtml += '<div style="color:#888;">No report data for this date.</div>';
                            document.getElementById('report-content-inner').innerHTML = reportHtml;
                            pdfBtn.style.display = 'none';
                            return;
                        }
                        reportHtml += `<div style='font-size:1.1em;margin-bottom:10px;'><b>Total Sales:</b> <span style='color:#28a745;font-weight:bold;'>$${parseFloat(data.total_sales).toFixed(2)}</span></div>`;
                        reportHtml += `<div style='font-size:1.08em;margin-bottom:8px;'><b>Number of Receipts:</b> <span style='color:#007bff;'>${data.orders.length}</span></div>`;
                        reportHtml += `<div style='font-size:1.08em;margin-bottom:8px;'><b>Report Generated By:</b> <span style='color:#333;'>${username}</span></div>`;
                        reportHtml += `<div style='font-size:1.08em;margin-bottom:8px;'><b>Item Sales:</b></div>`;
                        reportHtml += `<div style='overflow-x:auto;'><table class='most-ordered-table'>`;
                        reportHtml += `<thead><tr><th style='width:48px;'>No.</th><th>Name</th><th style='width:80px;'>Qty</th><th style='width:100px;'>Total</th></tr></thead><tbody>`;
                        (data.items || []).forEach((item, idx) => {
                            reportHtml += `<tr style='font-family: \"Noto Sans Khmer\", Khmer OS Siemreap, Arial, sans-serif;'>`;
                            reportHtml += `<td style='text-align:center;'>${idx + 1}</td>`;
                            reportHtml += `<td>${item.name}</td>`;
                            reportHtml += `<td style='text-align:center;font-weight:bold;color:#007bff;'>${item.total_qty}</td>`;
                            reportHtml += `<td style='text-align:right;'>$${parseFloat(item.total_price).toFixed(2)}</td>`;
                            reportHtml += `</tr>`;
                        });
                        reportHtml += `</tbody></table></div>`;
                        document.getElementById('report-content-inner').innerHTML = reportHtml;
                        pdfBtn.style.display = '';
                    } catch (err) {
                        document.getElementById('report-content-inner').innerHTML = `<div style='color:red;white-space:pre-wrap;'>Backend error: ${text}</div>`;
                        pdfBtn.style.display = 'none';
                        console.error('Report fetch error:', text);
                    }
                })
                .catch((err) => {
                    document.getElementById('report-content-inner').innerHTML = `<div style='color:red;'>JS Fetch error: ${err}</div>`;
                    pdfBtn.style.display = 'none';
                    console.error('JS Fetch error:', err);
                });
        });

        pdfBtn.addEventListener('click', function() {
            if (!lastReportData || !lastReportData.orders) return;
            const date = document.getElementById('report-date').value;
            const element = document.getElementById('pdf-report-content');
            setTimeout(function() {
                html2pdf().set({
                    margin: 0.2,
                    filename: `report_${date}.pdf`,
                    html2canvas: {
                        scale: 2,
                        backgroundColor: '#fff'
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                }).from(element).save();
            }, 200);
        });
    </script>
</body>

</html>
