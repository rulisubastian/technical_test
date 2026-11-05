<script setup lang="ts">
    definePageMeta({ layout: false });
    const { login } = useAuth();
    
    const email = ref('');
    const password = ref('');
    const error = ref('');
    const isLoading = ref(false);
    
    async function submit() {
        error.value = '';
        isLoading.value = true; 
        
        try {
            await login(email.value, password.value);
            await navigateTo('/');
        } catch (e) {
            error.value = 'Login gagal. Periksa kembali email dan password Anda.';
        } finally {
            isLoading.value = false; 
        }
    }
</script>

<template>
    <main class="min-h-screen grid place-items-center bg-gradient-to-br from-slate-50 to-slate-100 p-4 antialiased">
        <div class="w-full max-w-md">
            <form class="bg-white p-8 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 space-y-5" @submit.prevent="submit">
                <div class="space-y-1 text-center pb-2">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-950">Azatech Sign in</h1>
                    <p class="text-sm text-slate-500">Silakan masuk ke akun Anda</p>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700 block">Email</label>
                    <input 
                        v-model="email" 
                        type="email" 
                        required 
                        :disabled="isLoading"
                        placeholder="nama@perusahaan.com"
                        class="input"
                    >
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700 block">Password</label>
                    <input 
                        v-model="password" 
                        type="password" 
                        required 
                        :disabled="isLoading"
                        placeholder="••••••••"
                        class="input"
                    >
                </div>
                
                <Transition name="fade">
                    <div v-if="error" class="flex items-center gap-2 p-3 text-sm text-red-600 bg-red-50 border border-red-100 rounded-lg">
                        <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <span>{{ error }}</span>
                    </div>
                </Transition>
                
                <button 
                    type="submit" 
                    :disabled="isLoading"
                    class="w-full flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium p-3 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:bg-blue-600 shadow-lg shadow-blue-500/20"
                >
                    <svg v-if="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://w3.org" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    
                    <span>{{ isLoading ? 'Memproses...' : 'Sign in' }}</span>
                </button>
            </form>
        </div>
    </main>
</template>

<style scoped>
    .input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.65rem 0.85rem;
        font-size: 0.95rem;
        color: #0f172a;
        transition: all 0.15s ease-in-out;
        background-color: #ffffff;
    }
    .input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .input:disabled {
        background-color: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }
    
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.2s ease;
    }
    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }
</style>
