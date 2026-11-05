type User = {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'cashier'
};

export function useAuth() {
    const {request, token} = useApi();
    const user = useState < User | null > ('auth-user', () => null);
    async function login(email : string, password : string) {
        const res = await request < {
            data: {
                token: string;
                user: User
            }
        } > ('/auth/login', {
            method: 'POST',
            body: {
                email,
                password
            }
        });
        
        token.value = res.data.token;
        user.value = res.data.user;
        return user.value
    }
    async function me() {
        if (!token.value) 
            return null;
        try {
            const res = await request < {
                data: User
            } > ('/auth/me');
            user.value = res.data;
            return user.value
        } catch {logout();
            return null}
}
async function logout() {
    try {
        await request('/auth/logout', {method: 'POST'})
    } finally {
        token.value = null;
        user.value = null;
        await navigateTo('/login')
    }
}
return {user, token, login, me, logout}
}
