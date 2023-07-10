<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  common: Object,
  type: String,
  status: Object,
});

const form = useForm({
    status_id: props.status ? props.status.status_id : null,
    status_name: props.status ? props.status.status_name : '',
    hex_color: props.status ? props.status.hex_color : '#f8aaaa',
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
        form.post(route('create-status'));
    } else if (props.type == "Edit") {
        form.put(route('edit-status', { 'status_id': props.status.status_id }));
    } else {
        // do nothing
    }
};

const previewText = () => form.status_name != '' ? form.status_name : '状態名';
</script>

<template>
    <Head :title="`Backlog - ${props.type} Status`" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <form class="bg-slate-100 py-2 px-3 my-2" @submit.prevent="submit">
            <div class="font-bold mb-5">状態{{ title }}</div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    状態名<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="状態名" class="min-w-[300px] rounded py-1"
                        v-model="form.status_name" :class="form.errors.status_name ? 'border-red-500' : ''">
                    <div>
                        <small class="text-red-500">{{ form.errors.status_name }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    カラーコード<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div id="color-picker" class="relative">
                    <input type="color" class="border min-w-[300px] h-[32px] rounded" v-model="form.hex_color">
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">プレビュー</div>
                <div>
                    <span class="rounded-full px-4 whitespace-nowrap text-white" :style="{ backgroundColor: form.hex_color }">
                        <small>{{ previewText() }}</small>
                    </span>
                </div>
            </div>

            <div class="mt-5 mb-3 text-center">
                <Link :href="route('statuses')">
                    <button type="button" class="bg-slate-300 hover:bg-slate-400 rounded py-1 px-4">戻る</button>
                </Link>
                
                <button v-if="type == 'Create' || type == 'Edit'" type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-2">
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
