<script setup lang="ts">
    definePageMeta({middleware: 'auth'});
    const {request} = useApi();
    const {data, error, pending} = await useAsyncData('summary', () => request < {
        data: {
            orders: number;
            revenue: number;
            pending: number
        }
    } > ('/dashboard/summary'));
</script>
<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Dashboard</h1>
        <p v-if="pending">Loading…</p>
        <p v-else-if="error">Gagal memuat dashboard.</p>
        <div v-else class="grid md:grid-cols-3 gap-4">
            <article class="card">
                <p>Total Orders</p>
                <b>{{data?.data.orders}}</b>
            </article>
            <article class="card">
                <p>Revenue</p>
                <b>Rp
                    {{Number(data?.data.revenue).toLocaleString('id-ID')}}</b>
            </article>
            <article class="card">
                <p>Pending</p>
                <b>{{data?.data.pending}}</b>
            </article>
        </div>
    </div>
</template>
<style scoped>
    .card {
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px #0001;
    }
    .card b {
        font-size: 1.7rem;
    }
</style>