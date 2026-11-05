<script setup lang="ts">
    definePageMeta({middleware: 'auth'});
    const {request} = useApi();
    const {data, error, pending} = await useAsyncData('summary', () => request<{
        data: {
            orders: number;
            revenue: number;
            pending: number
        }
    }>('/dashboard/summary'));
</script>

<template>
    <div class="dashboard-container p-6 max-w-7xl mx-auto">
        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Dashboard Overview</h1>
            <p class="text-slate-500 mt-1 text-sm">Ringkasan metrik performa toko dan manajemen order Anda saat ini.</p>
        </header>

        <!-- Loading State -->
        <div v-if="pending" class="flex flex-col items-center justify-center min-h-[250px] space-y-3 bg-white rounded-2xl border border-slate-100 shadow-sm">
            <div class="animate-spin rounded-full h-8 w-8 border-2 border-indigo-600 border-t-transparent"></div>
            <p class="text-slate-500 text-sm font-medium">Memuat metrik dashboard...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="flex flex-col items-center justify-center min-h-[250px] p-6 text-center bg-rose-50/50 rounded-2xl border border-rose-100">
            <div class="text-rose-500 font-semibold mb-1 text-lg">Gagal Memuat Dashboard</div>
            <p class="text-rose-400 text-sm max-w-sm">Terjadi kendala koneksi ke server API. Silakan refresh halaman beberapa saat lagi.</p>
        </div>

        <!-- Data Grid Display -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total Orders -->
            <article class="stat-card">
                <div class="card-inner">
                    <div class="icon-wrapper bg-indigo-50 text-indigo-600">
                        <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                    </div>
                    <div class="data-wrapper">
                        <p class="card-label">Total Orders</p>
                        <b class="card-value text-slate-800">{{ data?.data.orders }}</b>
                    </div>
                </div>
            </article>

            <!-- Card 2: Revenue -->
            <article class="stat-card">
                <div class="card-inner">
                    <div class="icon-wrapper bg-emerald-50 text-emerald-600">
                        <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797-2.101c.727.083 1.223.742 1.223 1.469V22.5m4.09-3.04a11.947 11.947 0 0 1-5.712-4.473M18.75 1.5l1.72 3.441 3.823.555-2.766 2.697.653 3.807-3.43-1.804-3.43 1.804.654-3.807-2.767-2.697 3.822-.555L18.75 1.5Zm-7.5 7.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M18.75 12a11.953 11.953 0 0 1-2.912.496M3 14.102V11.25a9 9 0 0 1 6-8.484M22.5 10.5a12.46 12.46 0 0 1-1.218 5.383m-4.698-11.82.29-.58a2.25 2.25 0 0 1 3.182-.993l1.59.954a2.25 2.25 0 0 1 .993 3.183l-.29.58" /></svg>
                    </div>
                    <div class="data-wrapper">
                        <p class="card-label">Revenue</p>
                        <b class="card-value text-slate-800">Rp {{ Number(data?.data.revenue).toLocaleString('id-ID') }}</b>
                    </div>
                </div>
            </article>

            <!-- Card 3: Pending -->
            <article class="stat-card">
                <div class="card-inner">
                    <div class="icon-wrapper bg-amber-50 text-amber-600">
                        <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <div class="data-wrapper">
                        <p class="card-label">Pending Orders</p>
                        <b class="card-value text-slate-800">{{ data?.data.pending }}</b>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>

<style scoped>
    .stat-card {
        background: #ffffff;
        padding: 1.5rem;
        border-radius: 1rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Animasi hover yang smooth */
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(148, 163, 184, 0.08), 0 4px 6px -4px rgba(148, 163, 184, 0.08);
        border-color: #e2e8f0;
    }

    .card-inner {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 0.75rem;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }

    .stat-card:hover .icon-wrapper {
        transform: scale(1.05);
    }

    .data-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .card-label {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0;
    }

    .card-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.025em;
    }
</style>
