<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        width: 220px;
        height: 100vh;
        background: #222;
        color: #fff;
        z-index: 1200;
        transition: left 0.3s;
        box-shadow: 2px 0 8px #0002;
        display: flex;
        flex-direction: column;
    }

    .sidebar.active {
        left: 0;
    }

    .sidebar .logo {
        height: 150px;
        background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%);
        color: #fff;
        font-size: 1.35em;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        min-height: 110px;
        box-shadow: 0 2px 12px #007bff22;
    }

    .sidebar .logo img {
        width: 100%;
        height: 80%;
        object-fit: contain;
        display: block;
        margin: 0 auto 8px auto;
        background: #fff;
        /* border-radius: 50%; */
        box-shadow: 0 2px 12px #007bff33;
        border: 3px solid #fff;
        transition: box-shadow 0.2s;
    }

    .sidebar .logo img:hover {
        box-shadow: 0 4px 18px #00c6ff55;
        transform: scale(1.05);

    }

    .sidebar .menu {
        flex: 1 1 auto;
        overflow-y: auto;
        margin-bottom: 0;
        padding-bottom: 80px;
        /* Space for the hide button */
    }

    .sidebar-hide-btn {
        position: relative;
        margin: 0 auto;
        background: #fff;
        color: #007bff;
        border: 2px solid #007bff;
        border-radius: 50%;
        width: 54px;
        height: 54px;
        font-size: 1.2em;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1303;
        box-shadow: 0 2px 8px #0003;
        transition: background 0.2s, color 0.2s, border 0.2s;
    }

    .sidebar-hide-btn:hover {
        background: #007bff;
        color: #fff;
        border-color: #fff;
    }

    .sidebar-show-btn {
        position: fixed;
        top: 60px;
        left: 0;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 0 20px 20px 0;
        width: 40px;
        height: 40px;
        font-size: 1.5em;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1302;
        display: none;
    }
</style>
<div class="sidebar" id="sidebar">
    <div class="logo" ><img src="./image/logo.png" alt=""></div>
    <ul class="menu">
        <li><a href="dashboard.php" onclick="closeSidebarOnMobile()">Dashboard</a></li>
        <li><a href="customer_menu.php" onclick="closeSidebarOnMobile()">Orders</a></li>
        <!-- <li><a href="menu.php" onclick="closeSidebarOnMobile()">Menu</a></li> -->
         <li><a href="order.php" onclick="closeSidebarOnMobile()">Recipe</a></li>
         <li><a href="report.php" onclick="closeSidebarOnMobile()">Report</a></li>
        <li><a href="stock.php" onclick="closeSidebarOnMobile()">Manage Stock</a></li>
        <li><a href="admin_menu.php" onclick="closeSidebarOnMobile()">Manage Menu</a></li>
        <li><a href="user_management.php" onclick="closeSidebarOnMobile()">User Management</a></li>
        <li><a href="logout.php" onclick="closeSidebarOnMobile()">Logout</a></li>
    </ul>
    <button class="sidebar-hide-btn" id="sidebar-hide-btn" onclick="hideSidebar()" title="Hide menu">❮</button>
</div>
<button class="sidebar-show-btn" id="sidebar-show-btn" onclick="showSidebar()" title="Show menu" style="display:none;">☰</button>
<script>
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
        updateSidebarShowBtn();
    }

    function hideSidebar() {
        document.getElementById('sidebar').classList.remove('active');
        document.getElementById('sidebar-show-btn').style.display = 'flex';
    }

    function showSidebar() {
        document.getElementById('sidebar').classList.add('active');
        document.getElementById('sidebar-show-btn').style.display = 'none';
    }

    function closeSidebarOnMobile() {
        if (window.innerWidth <= 900) {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('sidebar-show-btn').style.display = 'flex';
        }
    }

    function updateSidebarShowBtn() {
        const sidebar = document.getElementById('sidebar');
        const showBtn = document.getElementById('sidebar-show-btn');
        if (!sidebar.classList.contains('active')) {
            showBtn.style.display = 'flex';
        } else {
            showBtn.style.display = 'none';
        }
    }

    function setSidebarInitialState() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth > 900) {
            sidebar.classList.add('active');
        } else {
            sidebar.classList.remove('active');
        }
        updateSidebarShowBtn();
    }

    window.addEventListener('resize', setSidebarInitialState);
    document.addEventListener('DOMContentLoaded', setSidebarInitialState);
</script>