<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  common: Object,
  type: String,
  kind: Object,
});

const form = useForm({
    kind_id: props.kind ? props.kind.kind_id : null,
    kind_name: props.kind ? props.kind.kind_name : '',
    kind_desc: props.kind ? props.kind.kind_desc : '',
    hex_color: props.kind ? props.kind.hex_color : '#f8aaaa',
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
        form.post(route('create-kind'));
    } else if (props.type == "Edit") {
        form.put(route('edit-kind', { 'kind_id': props.kind.kind_id }));
    } else {
        // do nothing
    }
};

const previewText = () => form.kind_name != '' ? form.kind_name : '種別名';
const previewTootltip = () => form.kind_desc != '' ? form.kind_desc : '種別内容';
</script>

<template>
    <Head :title="`Backlog - ${props.type} Kind`" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <form class="bg-slate-100 py-2 px-3 my-2" @submit.prevent="submit">
            <div class="font-bold mb-5">種別{{ title }}</div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    種別名<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="種別名" class="min-w-[300px] rounded py-1"
                        v-model="form.kind_name" :class="form.errors.kind_name ? 'border-red-500' : ''">
                    <div>
                        <small class="text-red-500">{{ form.errors.kind_name }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    種別内容<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="種別内容" class="min-w-[300px] rounded py-1"
                        v-model="form.kind_desc" :class="form.errors.kind_desc ? 'border-red-500' : ''">
                    <div>
                        <small class="text-red-500">{{ form.errors.kind_desc }}</small>
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
                    <span class="rounded-full px-4 whitespace-nowrap text-white" :style="{ backgroundColor: form.hex_color }"
                        :title="previewTootltip()" role="button">
                        <small>{{ previewText() }}</small>
                    </span>
                </div>
            </div>

            <div class="mt-5 mb-3 text-center">
                <Link :href="route('kinds')">
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
    #color-picker input[type=color] {
        border-color: #6b7280;
    }
</style>
