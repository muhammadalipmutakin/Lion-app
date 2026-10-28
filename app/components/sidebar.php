<aside id="sidebar" class="bg-red-700 fixed top-16 z-40 h-screen p-4 shadow-md transition-all duration-300 ease-in-out transform"
    :class="{
        'md:w-64 md:translate-x-0': sidebarOpen && isDesktop,
        'md:w-0 md:-translate-x-full': !sidebarOpen && isDesktop,
        'w-64 translate-x-0': sidebarOpen && !isDesktop,
        'w-0 -translate-x-full': !sidebarOpen && !isDesktop,
    }">
    <div class="overflow-y-auto overflow-x-hidden h-full">
        <ul class="space-y-2">
            <li>
                <a href="?page=dashboard" class="flex items-center p-2 rounded-md transition-colors duration-200 <?php if ($current_page === 'dashboard') echo 'bg-orange-500 text-white font-semibold';
                                                                                                                    else echo 'text-gray-300 hover:bg-orange-500'; ?>">
                    <i class="fas fa-tachometer-alt mr-2 w-5"></i>
                    <span x-show="sidebarOpen" class="md:block">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="?page=stock" class="flex items-center p-2 rounded-md transition-colors duration-200 <?php if ($current_page === 'stock') echo 'bg-orange-500 text-white font-semibold';
                                                                                                                else echo 'text-gray-300 hover:bg-orange-500'; ?>">
                    <i class="fas fa-box-open mr-2 w-5"></i>
                    <span x-show="sidebarOpen" class="md:block">Stock</span>
                </a>
            </li>
            <li>
                <a href="?page=transaksi" class="flex items-center p-2 rounded-md transition-colors duration-200 <?php if ($current_page === 'transaksi') echo 'bg-orange-500 text-white font-semibold';
                                                                                                                    else echo 'text-gray-300 hover:bg-orange-500'; ?>">
                    <i class="fas fa-exchange-alt mr-2 w-5"></i>
                    <span x-show="sidebarOpen" class="md:block">Transaksi</span>
                </a>
            </li>
            <li>
                <a href="?page=movement" class="flex items-center p-2 rounded-md transition-colors duration-200 <?php if ($current_page === 'movement') echo 'bg-orange-500 text-white font-semibold';
                                                                                                                else echo 'text-gray-300 hover:bg-orange-500'; ?>">
                    <i class="fas fa-truck-loading mr-2 w-5"></i>
                    <span x-show="sidebarOpen" class="md:block">Movement</span>
                </a>
            </li>
            <li x-data="{ masterDataOpen: <?php echo (strpos($current_page, 'master_data') === 0) ? 'true' : 'false'; ?> }">
                <button @click="masterDataOpen = !masterDataOpen" class="flex items-center p-2 rounded-md w-full text-left transition-colors duration-200 <?php if (strpos($current_page, 'master_data') === 0) echo 'bg-orange-500 text-white font-semibold';
                                                                                                                                                            else echo 'text-gray-300 hover:bg-orange-500'; ?>">
                    <i class="fas fa-database mr-2 w-5"></i>
                    <span x-show="sidebarOpen" class="flex-1 md:block">Master Data</span>
                    <i class="fas ml-auto transition-transform duration-300" :class="{'fa-chevron-up': masterDataOpen, 'fa-chevron-down': !masterDataOpen}" x-show="sidebarOpen"></i>
                </button>
                <ul x-show="masterDataOpen" x-collapse.duration.300ms class="pl-8 space-y-2 text-sm">
                    <li><a href="?page=master_data_lokasi" class="block p-2 rounded-md transition-colors duration-200 text-gray-200 hover:bg-orange-500 <?php if ($current_page === 'master_data_lokasi') echo 'bg-orange-500 font-semibold'; ?>">Lokasi</a></li>
                    <li><a href="?page=master_data_group" class="block p-2 rounded-md transition-colors duration-200 text-gray-200 hover:bg-orange-500 <?php if ($current_page === 'master_data_group') echo 'bg-orange-500 font-semibold'; ?>">Group</a></li>
                    <li><a href="?page=master_data_users" class="block p-2 rounded-md transition-colors duration-200 text-gray-200 hover:bg-orange-500 <?php if ($current_page === 'master_data_users') echo 'bg-orange-500 font-semibold'; ?>">Users</a></li>
                </ul>
            </li>
            <li>
                <a href="?page=profile" class="flex items-center p-2 rounded-md transition-colors duration-200 <?php if ($current_page === 'profile') echo 'bg-orange-500 text-white font-semibold';
                                                                                                                else echo 'text-gray-300 hover:bg-orange-500'; ?>">
                    <i class="fas fa-user-circle mr-2 w-5"></i>
                    <span x-show="sidebarOpen" class="md:block">Profil</span>
                </a>
            </li>
        </ul>
    </div>
</aside>