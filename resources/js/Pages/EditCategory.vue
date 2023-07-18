<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  common: Object,
  type: String,
  category: Object,
});

const form = useForm({
    category_id: props.category ? props.category.category_id : null,
    category_name: props.category ? props.category.category_name : '',
});

let title = "";
if (props.type == "Create") {
    title = "作成";
} else if (props.type == "Edit") {
    title = "編集";
} else {
    // do nothing
}

const submit = () => {
    if (props.type == "Create") {
        form.post(route('create-category'));
    } else if (props.type == "Edit") {
        form.put(route('edit-category', { 'category_id': props.category.category_id }));
    } else {
        // do nothing
    }
};
</script>

<template>
    <Head :title="`Backlog - ${props.type} Category`" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <Link :href="route('categories')">
            <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</button>
        </Link>

        <form class="bg-slate-100 py-2 px-3 my-2" @submit.prevent="submit">
            <!-- breadcrumbs -->
            <div class="font-bold mt-2 mb-4">
                <Link :href="route('projects')" class="font-bold text-blue-700 hover:underline">
                    カテゴリー一覧
                </Link>
                <span class="ms-4"><i class="fa-solid fa-angle-right"></i></span>
                <span class="ms-4">カテゴリー{{ title }}</span>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    カテゴリー名<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="カテゴリー名" class="min-w-[300px] rounded py-1"
                        v-model="form.category_name" :class="form.errors.category_name ? 'border-red-500' : ''">
                    <div>
                        <small class="text-red-500">{{ form.errors.category_name }}</small>
                    </div>
                </div>
            </div>

            <div class="mt-5 mb-3 text-center">                
                <button v-if="type == 'Create' || type == 'Edit'" type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">
                    <template v-if="type == 'Create'">
                        保存
                    </template>
                    <template v-else-if="type == 'Edit'">
                        編集
                    </template>
                </button>
            </div>
        </form>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
