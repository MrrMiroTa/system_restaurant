function toggleMenu() {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("active");
}

document.addEventListener("DOMContentLoaded", function () {
  fetchDashboardData();
  fetchMenu();
  fetchStock();

  // Menu form submit
  const menuForm = document.getElementById("menu-form");
  if (menuForm) {
    menuForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(menuForm);
      fetch("../backend/menu.php?action=add", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            menuForm.reset();
            fetchMenu();
            alert("Menu item added!");
          } else {
            alert("Error: " + (data.error || "Failed to add menu"));
          }
        });
    });
  }

  // Stock form submit
  const stockForm = document.getElementById("stock-form");
  if (stockForm) {
    stockForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(stockForm);
      fetch("../backend/stock.php?action=add", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            stockForm.reset();
            fetchStock();
            alert("Stock item added!");
          } else {
            alert("Error: " + (data.error || "Failed to add stock"));
          }
        });
    });
  }
});

function fetchDashboardData() {
  fetch("../backend/dashboard_data.php")
    .then((res) => res.json())
    .then((data) => {
      // Defensive: check if elements exist before setting innerHTML/textContent
      const salesTodayDiv = document.getElementById("sales-today");
      if (salesTodayDiv) {
        salesTodayDiv.textContent = `${data.qty_today} / $${parseFloat(
          data.sales_today
        ).toFixed(2)}`;
      }
      const salesMonthDiv = document.getElementById("sales-month");
      if (salesMonthDiv) {
        salesMonthDiv.textContent = `${data.qty_month} / $${parseFloat(
          data.sales_month
        ).toFixed(2)}`;
      }
      const topMenuList = document.getElementById("top-menu-list");
      if (topMenuList && data.top_menu) {
        topMenuList.innerHTML = data.top_menu
          .map((item) => `<div>${item.name} (${item.total_qty})</div>`)
          .join("");
      }
      const alertBox = document.getElementById("stock-alert");
      if (alertBox) {
        if (data.low_stock && data.low_stock.length > 0) {
          alertBox.style.display = "block";
          alertBox.textContent =
            "Low stock: " +
            data.low_stock.map((s) => `${s.name} (qty: ${s.qty})`).join(", ");
        } else {
          alertBox.style.display = "none";
        }
      }
      // Most ordered today
      const mostOrderedToday = document.getElementById("most-ordered-today");
      console.log("most_ordered_today:", data.most_ordered_today);
      if (mostOrderedToday) {
        if (
          Array.isArray(data.most_ordered_today) &&
          data.most_ordered_today.length > 0
        ) {
          mostOrderedToday.innerHTML = data.most_ordered_today
            .map(
              (item, idx) =>
                `<div>${idx + 1}. ${item.name} <span style='color:#007bff;'>(x${
                  item.total_qty
                })</span></div>`
            )
            .join("");
        } else {
          mostOrderedToday.innerHTML = "No orders today.";
        }
      }
    })
    .catch((err) => {
      console.error("Dashboard data fetch error:", err);
    });
}

function fetchMenu() {
  fetch("../backend/menu.php?action=list")
    .then((res) => res.json())
    .then((data) => {
      const menuList = document.getElementById("menu-list");
      if (!menuList) return;
      menuList.innerHTML = data
        .map(
          (item) => `
        <div class="menu-item">
          <img src="${
            item.picture || ""
          }" alt="" style="max-width:60px;max-height:60px;object-fit:cover;">
          <div><b>${item.name}</b> (${item.category})<br>${
            item.description
          }<br>Qty: ${item.qty} | $${parseFloat(item.price).toFixed(2)}</div>
        </div>
      `
        )
        .join("");
    });
}

function fetchStock() {
  fetch("../backend/stock.php?action=list")
    .then((res) => res.json())
    .then((data) => {
      const stockList = document.getElementById("stock-list");
      if (!stockList) return;
      stockList.innerHTML = data
        .map(
          (item) => `
        <div class="stock-item">
          <img src="${
            item.picture || ""
          }" alt="" style="max-width:60px;max-height:60px;object-fit:cover;">
          <div><b>${item.name}</b> (${item.category})<br>${
            item.description
          }<br>Qty: ${item.qty} | $${parseFloat(item.price).toFixed(2)}</div>
        </div>
      `
        )
        .join("");
    });
}
