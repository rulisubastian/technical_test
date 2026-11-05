export default defineNuxtRouteMiddleware(async(to) => {
    const {token, me} = useAuth();
    if (to.path === '/login') 
        return;
    if (!token.value) 
        return navigateTo('/login');
    if (!useState('auth-user').value) 
        await me();
    if (!useState('auth-user').value) 
        return navigateTo('/login')
})
