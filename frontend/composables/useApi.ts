export function useApi() {
    const config = useRuntimeConfig()
    const token = useCookie<string | null>('azatech_token', {
        sameSite: 'strict',
        secure: !import.meta.dev // Bernilai true jika aplikasi berjalan di mode production
    });

    async function request<T>(path: string, options: Record<string, unknown> = {}) {
        return await $fetch<T>(`${config.public.apiBase}${path}`, {
            ...options,
            headers: {
                Accept: 'application/json',
                ...(token.value ? { Authorization: `Bearer ${token.value}` } : {}),
                ...(options.headers as object || {})
            }
        });
    }

    return { request, token }
}


