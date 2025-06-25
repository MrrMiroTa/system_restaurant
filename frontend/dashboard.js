function toggleMenu() {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("open");
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
      document.getElementById("sales-today").textContent = `${
        data.qty_today
      } / $${parseFloat(data.sales_today).toFixed(2)}`;
      document.getElementById("sales-month").textContent = `${
        data.qty_month
      } / $${parseFloat(data.sales_month).toFixed(2)}`;
      // Top menu
      const topMenuList = document.getElementById("top-menu-list");
      topMenuList.innerHTML = data.top_menu
        .map((item) => `<div>${item.name} (${item.total_qty})</div>`)
        .join("");
      // Low stock alert
      const alertBox = document.getElementById("stock-alert");
      if (data.low_stock && data.low_stock.length > 0) {
        alertBox.style.display = "block";
        alertBox.textContent =
          "Low stock: " +
          data.low_stock.map((s) => `${s.name} (qty: ${s.qty})`).join(", ");
      } else {
        alertBox.style.display = "none";
      }
      // Most ordered today
      const mostOrderedToday = document.getElementById("most-ordered-today");
      if (mostOrderedToday) {
        console.log('most_ordered_today:', data.most_ordered_today);
        if (data.most_ordered_today && data.most_ordered_today.name) {
          mostOrderedToday.innerHTML = `${data.most_ordered_today.name} (${data.most_ordered_today.total_qty})`;
        } else {
          mostOrderedToday.innerHTML = "No orders today.";
        }
      } else {
        console.warn('Element #most-ordered-today not found in DOM');
      }
    });
}

function fetchMenu() {
  fetch("../backend/menu.php?action=list")
    .then((res) => res.json())
    .then((data) => {
      const menuList = document.getElementById("menu-list");
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
