<template>
    <div class="bg-white shadow-md rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">İnfoLine</h1>
            <p class="text-gray-600">Məktəb İdarəetmə Sistemi</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" v-model="form.email"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Şifrə</label>
                <input type="password" v-model="form.password"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2" required>
            </div>

            <div v-if="error" class="text-red-500 text-sm">
                {{ error }}
            </div>

            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                :disabled="loading">
                <span v-if="loading">Yüklənir...</span>
                <span v-else>Daxil ol</span>
            </button>
        </form>
    </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

export default {
    setup() {
        const router = useRouter()
        const store = useStore()

        const form = ref({
            email: '',
            password: ''
        })

        const error = ref('')
        const loading = ref(false)

        const handleLogin = async () => {
            try {
                loading.value = true
                error.value = ''

                await store.dispatch('auth/login', form.value)

                // İstifadəçinin roluna görə yönləndirmə
                const user = store.state.auth.user
                if (user.role === 'super_admin') {
                    router.push('/dashboard/super-admin')
                } else if (user.role === 'sector_admin') {
                    router.push('/dashboard/sector-admin')
                } else {
                    router.push('/dashboard/school-admin')
                }
            } catch (err) {
                error.value = err.response?.data?.message || 'Xəta baş verdi'
            } finally {
                loading.value = false
            }
        }

        return {
            form,
            error,
            loading,
            handleLogin
        }
    }
}
</script>