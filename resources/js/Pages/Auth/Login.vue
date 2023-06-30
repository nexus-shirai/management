<script setup>
import AppHeader from '../../Components/AppHeader.vue';
import AppFooter from '../../Components/AppFooter.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  common: Object
});

const form = useForm({
    username: '',
    password: ''
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            if (form.errors.message) {
                Swal.fire({
                    icon: 'error',
                    toast: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    timer: 2000,
                    timerProgressBar: true,
                    html: '<span class="font-bold">' + form.errors.message + '</span>',
                });
            }
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Backlog - Login" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        
        <form class="rounded mx-auto max-w-[450px] bg-slate-200 py-7 px-10" @submit.prevent="submit">
            <div class="text-center mb-5">
                <span class="text-2xl font-bold">ログイン</span>
            </div>
            <div class="mb-2">
                <label for="username" class="inline-block w-full"><small>ユーザー名</small></label>
                <input type="text" id="username" name="username" :class="form.errors.username ? 'border-red-500' : ''"
                    v-model="form.username" class="inline-block w-full py-1 rounded">
                <small class="text-red-500">{{ form.errors.username }}</small>
            </div>
            <div class="mb-7">
                <label for="password" class="inline-block w-full"><small>パスワード</small></label>
                <input type="password" id="password" name="password" :class="form.errors.password ? 'border-red-500' : ''"
                    v-model="form.password" class="inline-block w-full py-1 rounded">
                <small class="text-red-500">{{ form.errors.password }}</small>
            </div>
            <div class="text-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-10">
                    <small>ログイン</small>
                </button>
            </div>
        </form>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
