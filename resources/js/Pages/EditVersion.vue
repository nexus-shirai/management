<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  common: Object,
  type: String,
  version: Object,
});

const form = useForm({
    version_id: props.version ? props.version.version_id : null,
    version_name: props.version ? props.version.version_name : '',
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
        form.post(route('create-version'));
    } else if (props.type == "Edit") {
        form.put(route('edit-version', { 'version_id': props.version.version_id }));
    } else {
        // do nothing
    }
};
</script>

<template>
    <Head :title="`Backlog - ${props.type} Version`" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <form class="bg-slate-100 py-2 px-3 my-2" @submit.prevent="submit">
            <div class="font-bold mb-5">バージョン{{ title }}</div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    バージョン名<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="バージョン名" class="min-w-[300px] rounded py-1"
                        v-model="form.version_name" :class="form.errors.version_name ? 'border-red-500' : ''">
                    <div>
                        <small class="text-red-500">{{ form.errors.version_name }}</small>
                    </div>
                </div>
            </div>

            <div class="mt-5 mb-3 text-center">
                <Link :href="route('versions')">
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
