<!-- components/DashboardCard.vue -->
<script setup lang="ts">
    interface Props {
        label: string;
        value: number | string | undefined;
        isCurrency?: boolean;
        variant?: 'indigo' | 'emerald' | 'amber';
    }

    const props = withDefaults(defineProps<Props>(), {
        isCurrency: false,
        variant: 'indigo'
    });

    const variantClasses = computed(() => {
        const maps = {
            indigo: { bg: 'bg-indigo-50', text: 'text-indigo-600' },
            emerald: { bg: 'bg-emerald-50', text: 'text-emerald-600' },
            amber: { bg: 'bg-amber-50', text: 'text-amber-600' }
        };
        return maps[props.variant];
    });

    const formattedValue = computed(() => {
        if (props.value === undefined) return '0';
        if (props.isCurrency && typeof props.value === 'number') {
            return `Rp ${props.value.toLocaleString('id-ID')}`;
        }
        return props.value;
    });
</script>

<template>
    <article class="bg-white p-6 rounded-2xl border border-slate-150 shadow-sm hover:-translate-y-0.5 hover:shadow-md hover:border-slate-200 transition-all duration-300 group">
        <div class="flex items-center gap-5">
            <!-- Icon Wrapper -->
            <div :class="['w-14 h-14 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-105', variantClasses.bg, variantClasses.text]">
                <slot name="icon" />
            </div>
            
            <!-- Data Wrapper -->
            <div class="space-y-0.5">
                <p class="text-xs font-semibold tracking-wide uppercase text-slate-400">{{ label }}</p>
                <b class="text-2xl font-bold tracking-tight text-slate-800 transition-colors duration-200">{{ formattedValue }}</b>
            </div>
        </div>
    </article>
</template>
