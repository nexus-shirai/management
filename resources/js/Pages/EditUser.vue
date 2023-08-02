<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  common: Object,
  type: String,
  user: Object,
});

let title = "";

if (props.type == "Create") {
    title = "作成";
} else if (props.type == "Edit") {
    title = "編集";
} else if (props.type == "View") {
    title = "詳細";
} else {
    // do nothing
}

const form = useForm({
    user_id: props.user ? props.user.user_id : null,
    user_type: props.user ? props.user.user_type : 'general',
    username: props.user ? props.user.username : '',
    first_name: props.user ? props.user.first_name : '',
    last_name: props.user ? props.user.last_name : '',
    first_name_kana: props.user ? props.user.first_name_kana : '',
    last_name_kana: props.user ? props.user.last_name_kana : '',
    email: props.user ? props.user.email : '',
    change_password: 0,
    current_password: props.user ? null : '',
    password: props.user ? null : '',
    confirm_password: props.user ? null : '',
});

const submit = () => {
    if (props.type == "Create") {
        form.post(route('create-user'));
    } else if (props.type == "Edit") {
        form.put(route('edit-user', { 'user_id': props.user.user_id }));
    } else {
        // do nothing
    }
};

const changeChangeChangePassword = () => {
    form.current_password = "";
    form.password = "";
    form.confirm_password = "";
};
</script>

<template>
    <Head :title="`Backlog - ${props.type} User`" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <Link :href="route('users')">
            <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</button>
        </Link>

        <form class="bg-slate-100 py-2 px-3 my-2" @submit.prevent="submit">
            <!-- breadcrumbs -->
            <div class="font-bold mt-2 mb-4">
                <Link :href="route('users')" class="font-bold text-blue-700 hover:underline">
                    ユーザー一覧
                </Link>
                <span class="ms-4"><i class="fa-solid fa-angle-right"></i></span>
                <span class="ms-4">ユーザー{{ title }}</span>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    種別<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <select v-model="form.user_type" class="rounded py-1 min-w-[170px]"
                        :class="form.errors.user_type ? 'border-red-500' : ''"
                        :disabled="props.type == 'View' || props.type == 'Edit'">
                        <option value="admin">管理者</option>
                        <option value="general" selected>一般ユーザー</option>
                    </select>
                    <div>
                        <small class="text-red-500">{{ form.errors.user_type }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    ユーザー名<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="ユーザー名" class="min-w-[300px] rounded py-1"
                        v-model="form.username" :class="form.errors.username ? 'border-red-500' : ''"
                        :disabled="props.type == 'View'">
                    <div>
                        <small class="text-red-500">{{ form.errors.username }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    性<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="性" class="min-w-[300px] rounded py-1"
                        v-model="form.last_name" :class="form.errors.last_name ? 'border-red-500' : ''"
                        :disabled="props.type == 'View'">
                    <div>
                        <small class="text-red-500">{{ form.errors.last_name }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    名<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="名" class="min-w-[300px] rounded py-1"
                        v-model="form.first_name" :class="form.errors.first_name ? 'border-red-500' : ''"
                        :disabled="props.type == 'View'">
                    <div>
                        <small class="text-red-500">{{ form.errors.first_name }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    セイ<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="セイ" class="min-w-[300px] rounded py-1"
                        v-model="form.last_name_kana" :class="form.errors.last_name_kana ? 'border-red-500' : ''"
                        :disabled="props.type == 'View'">
                    <div>
                        <small class="text-red-500">{{ form.errors.last_name_kana }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    メイ<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="メイ" class="min-w-[300px] rounded py-1"
                        v-model="form.first_name_kana" :class="form.errors.first_name_kana ? 'border-red-500' : ''"
                        :disabled="props.type == 'View'">
                    <div>
                        <small class="text-red-500">{{ form.errors.first_name_kana }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    メールアドレス<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="email" placeholder="メールアドレス" class="min-w-[300px] rounded py-1"
                        v-model="form.email" :class="form.errors.email ? 'border-red-500' : ''"
                        :disabled="props.type == 'View'">
                    <div>
                        <small class="text-red-500">{{ form.errors.email }}</small>
                    </div>
                </div>
            </div>

            <div v-if="props.type == 'Edit'" class="mb-2 flex">
                <div class="font-bold w-[200px]"></div>
                <div>
                    <input v-model="form.change_password" type="checkbox" id="change_password" :value="1" @change="changeChangeChangePassword()">
                    <label for="change_password" class="ms-3 select-none">パスワードを変更</label>
                </div>
            </div>

            <div v-if="props.type == 'Edit'" class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    現在パスワード<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="password" placeholder="現在パスワード" class="min-w-[300px] rounded py-1"
                        v-model="form.current_password" :class="form.errors.current_password ? 'border-red-500' : ''"
                        :disabled="props.type == 'View' || (props.type == 'Edit' && form.change_password == 0)">
                    <div>
                        <small class="text-red-500">{{ form.errors.current_password }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    <template v-if="props.type == 'Edit'">新</template>パスワード
                    <sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="password" :placeholder="(props.type == 'Edit' ? '新' : '') + 'パスワード'"
                        class="min-w-[300px] rounded py-1"
                        v-model="form.password" :class="form.errors.password ? 'border-red-500' : ''"
                        :disabled="props.type == 'View' || (props.type == 'Edit' && form.change_password == 0)">
                    <div>
                        <small class="text-red-500">{{ form.errors.password }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[200px]">
                    <template v-if="props.type == 'Edit'">新</template>パスワード（再確認）
                    <sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="password" :placeholder="(props.type == 'Edit' ? '新' : '') + 'パスワード（再確認）'"
                        class="min-w-[300px] rounded py-1"
                        v-model="form.confirm_password" :class="form.errors.confirm_password ? 'border-red-500' : ''"
                        :disabled="props.type == 'View' || (props.type == 'Edit' && form.change_password == 0)">
                    <div>
                        <small class="text-red-500">{{ form.errors.confirm_password }}</small>
                    </div>
                </div>
            </div>

            <div class="mt-5 mb-3 text-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">
                    <template v-if="props.type == 'Create'">
                        保存
                    </template>
                    <template v-else-if="props.type == 'Edit'">
                        編集
                    </template>
                    <template v-else>
                        <!-- do nothing -->
                    </template>
                </button>
            </div>
        </form>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
