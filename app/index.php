<?php
session_name('lion_parcel_app');
session_start();

if (!isset($_SESSION['user_data'])) {
    header("Location: /");
    exit();
}

$user_data = $_SESSION['user_data'];
$current_page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lion Parcel - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="icon" type="image/x-icon" href="./assets/icon.png">
    <style>
        .lp-orange-bg {
            background-color: #ff9500;
        }

        .lp-red-bg {
            background-color: #e31821;
        }

        .lp-red-text {
            color: #e31821;
        }

        .lp-orange-text {
            color: #ff9500;
        }

        body {
            width: 100vw;
            overflow-x: hidden;
            /* Tambahkan ini untuk mencegah scrolling horizontal */
        }

        #content {
            transition: margin-left 0.3s ease-in-out;
        }

        @media (min-width: 768px) {
            #content.md\:ml-64 {
                width: calc(100% - 16rem);
                margin-left: 16rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased"
    x-data="{ 
        sidebarOpen: window.innerWidth > 768 ? (localStorage.getItem('sidebarOpen') !== 'false') : false,
        isDesktop: window.innerWidth > 768
    }"
    x-init="
        window.addEventListener('resize', () => { 
            isDesktop = window.innerWidth > 768; 
            if (!isDesktop) {
                sidebarOpen = false;
            } else {
                sidebarOpen = (localStorage.getItem('sidebarOpen') !== 'false');
            }
        });
    ">

    <?php include 'components/navbar.php'; ?>

    <button id="sidebarToggleDesktop" @click="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', sidebarOpen)"
        class="hidden md:block fixed top-4 z-50 p-2 ml-4 rounded-md shadow-lg transition-all duration-300 ease-in-out lp-red-bg text-white"
        :class="{'ml-64': sidebarOpen, 'ml-4': !sidebarOpen}">
        <i class="fas" :class="{'fa-times': sidebarOpen, 'fa-bars': !sidebarOpen}"></i>
    </button>

    <button id="sidebarToggleMobile" @click="sidebarOpen = !sidebarOpen"
        class="md:hidden fixed top-4 left-4 z-50 p-2 rounded-md shadow-lg transition-all duration-300 lp-red-bg text-white">
        <i class="fas" :class="{'fa-times': sidebarOpen, 'fa-bars': !sidebarOpen}"></i>
    </button>

    <div x-cloak x-show="sidebarOpen && !isDesktop" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 transition-opacity duration-300 ease-in-out"></div>

    <div class="flex pt-16">
        <?php include 'components/sidebar.php'; ?>
        <main id="content" class="flex-1 p-4 transition-all duration-300 ease-in-out overflow-x-hidden"
            :class="{'md:ml-64': sidebarOpen && isDesktop, 'ml-0': !isDesktop || !sidebarOpen}">
            <?php
            $pagePath = 'pages/' . $current_page . '.php';

            if (file_exists($pagePath)) {
                include $pagePath;
            } else {
                include 'pages/dashboard.php';
            }
            ?>
        </main>
    </div>
</body>

</html>