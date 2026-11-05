<!-- layouts/default.vue -->
<script setup lang="ts">
    const route = useRoute();
    const { logout } = useAuth();

    // Daftar menu dinamis berdasarkan API Routes
    const menus = [
        { name: 'Dashboard', path: '/', icon: 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25' },
        { name: 'Products', path: '/products', icon: 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z' },
        { name: 'Customers', path: '/customers', icon: 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z' },
        { name: 'Orders', path: '/orders', icon: 'M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z' },
        { name: 'Sales Report', path: '/reports/sales', icon: 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z' },
        { name: 'Top Products', path: '/reports/top-products', icon: 'M11.48 3.499c.151-.577 1.004-.577 1.154 0l1.996 5.93a1 1 0 00.95.69h6.462c.67 0 .948.814.406 1.18l-5.228 3.52a1 1 0 00-.364 1.118l1.996 5.93c.151.577-.533 1.066-1.074.691l-5.228-3.52a1 1 0 00-1.154 0l-5.228 3.52c-.54.375-1.225-.114-1.074-.691l1.996-5.93a1 1 0 00-.364-1.118L2.247 11.3c-.542-.366-.264-1.18.406-1.18h6.462a1 1 0 00.95-.69l1.996-5.93z' },
    ];

    async function handleLogout() {
        try {
            await logout();
            await navigateTo('/auth/login');
        } catch (e) {
            console.error(e);
        }
    }
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex antialiased">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-20">
            <div class="h-16 flex items-center px-6 border-b border-slate-100 gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-indigo-200">A</div>
                <span class="font-bold text-slate-800 tracking-tight text-lg">Azatech Panel</span>
            </div>

            <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
                <NuxtLink 
                    v-for="menu in menus" 
                    :key="menu.path" 
                    :to="menu.path"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-200 group"
                    :class="route.path === menu.path ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                >
                    <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                        class="w-5 h-5 transition-transform duration-200 group-hover:scale-105"
                        :class="route.path === menu.path ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600'"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" :d="menu.icon" />
                    </svg>
                    {{ menu.name }}
                </NuxtLink>
            </nav>

            <div class="p-4 border-t border-slate-100">
                <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm text-rose-600 hover:bg-rose-50 transition-all duration-200">
                    <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-rose-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Sign Out
                </button>
            </div>
        </aside>

        <!-- CONTAINER KANAN -->
        <div class="flex-1 pl-64 flex flex-col min-h-screen">
            <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-end sticky top-0 z-10">
                <div class="flex items-center gap-3 text-sm font-medium text-slate-700">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span>Connected</span>
                </div>
            </header>

            <!-- Halaman akan dirender di sini secara dinamis -->
            <main class="p-8 max-w-7xl w-full mx-auto flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
