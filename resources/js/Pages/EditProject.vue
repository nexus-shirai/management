<script setup>
import { ref } from 'vue';
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import MultiSelect from '@vueform/multiselect';
import Swal from 'sweetalert2';

const props = defineProps({
  common: Object,
  type: String,
  users: Object,
  project: Object,
});

const user_options = ref([]);

const form = useForm({
    project_id: null,
    project_cd: '',
    project_name: '',
    project_desc: '',
    project_users: [],
});

props.users.forEach(user => {
    user_options.value.push({
        label: user.username,
        value: user.user_id,
    });
});

if (props.type == "View" || props.type == "Edit") {
    form.project_id = props.project.project_id;
    form.project_cd = props.project.project_cd;
    form.project_name = props.project.project_name;
    form.project_desc = props.project.project_desc;
    props.project.project_users.forEach(project_user => {
        form.project_users.push(project_user.user_id);
    });
}

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

const submit = () => {
    if (props.type == "Create") {
        form.post(route('create-project'));
    } else if (props.type == "Edit") {
        form.put(route('edit-project', { 'project_id': props.project.project_id }));
    } else {
        // do nothing
    }
};

const onClickDelete = () => {
    Swal.fire({
        title: '削除しますか?',
        text: "戻すことはできません。",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#CBD5E1',
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('delete-project', { 'project_id': props.project.project_id }));
        }
    });
};
</script>

<template>
    <Head :title="`Backlog - ${props.type} Project`" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">

        <template v-if="props.type == 'View'">
            <div class="text-right">
                <Link :href="route('issues', { 'project_id': props.project.project_id })">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 mb-2">課題一覧</button>
                </Link>
            </div>

            <div class="flex bg-slate-100 mx-0 py-3 px-3">
                <div class="flex-1 px-2">
                    <div class="font-bold mb-2">状態</div>
                    <div class="bg-slate-200 px-3 py-3">
                        <div class="flex">
                            <div class="inline-block bg-rose-300" style="width: 25%; height: 30px;"></div>
                            <div class="inline-block bg-blue-200" style="width: 45%; height: 30px;"></div>
                            <div class="inline-block bg-green-300" style="width: 30%; height: 30px;"></div>
                        </div>
                        <div class="mt-3">
                            <div class="inline-block text-center">
                                <div><small>Todo</small></div>
                                <div>
                                    <span class="bg-rose-300 rounded-full inline-block min-w-[70px]"><small>25</small></span>
                                </div>
                            </div>
                            <div class="inline-block text-center ms-2">
                                <div><small>完了</small></div>
                                <div>
                                    <span class="bg-blue-200 rounded-full inline-block min-w-[70px]"><small>45</small></span>
                                </div>
                            </div>
                            <div class="inline-block text-center ms-2">
                                <div><small>未着手</small></div>
                                <div>
                                    <span class="bg-green-300 rounded-full inline-block min-w-[70px]"><small>30</small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-1 px-2">
                    <div class="font-bold mb-2">マイルストーン</div>
                    <div class="bg-slate-200 px-3 py-3">
                        <div class="mb-2">
                            <div class="font-bold">初期リリース</div>
                            <div class="flex">
                                <div style="width: 80%;">
                                    <div class="flex">
                                        <div class="inline-block bg-rose-300" style="width: 15%; height: 30px;"></div>
                                        <div class="inline-block bg-green-300" style="width: 85%; height: 30px;"></div>
                                    </div>
                                </div>
                                <div style="width: 20%;">
                                    <div class="text-end">80%完了</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="font-bold">2次リリース</div>
                            <div class="flex">
                                <div style="width: 80%;">
                                    <div class="flex">
                                        <div class="inline-block bg-rose-300" style="width: 50%; height: 30px;"></div>
                                        <div class="inline-block bg-green-300" style="width: 50%; height: 30px;"></div>
                                    </div>
                                </div>
                                <div style="width: 20%;">
                                    <div class="text-end">50%完了</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="font-bold">追加機能対応</div>
                            <div class="flex">
                                <div style="width: 80%;">
                                    <div class="flex">
                                        <div class="inline-block bg-green-300" style="width: 30%; height: 30px;"></div>
                                        <div class="inline-block bg-blue-200" style="width: 70%; height: 30px;"></div>
                                    </div>
                                </div>
                                <div style="width: 20%;">
                                    <div class="text-end">30%完了</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <form class="bg-slate-100 py-2 px-3 my-2" @submit.prevent="submit">
            <div class="font-bold mb-5">プロジェクト{{ title }}</div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    プロジェクトコード<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="プロジェクトコード" class="min-w-[300px] rounded py-1"
                        v-model="form.project_cd" :class="form.errors.project_cd ? 'border-red-500' : ''"
                        :disabled="props.type == 'View' || props.type == 'Edit'">
                    <div>
                        <small class="text-slate-400">半角英大文字と半角数字とアンダースコアが使用できます。</small>
                    </div>
                    <div>
                        <small class="text-slate-400">例：プロジェクト名がバックログ→コードは「BLG_2」</small>
                    </div>
                    <div>
                        <small class="text-red-500">{{ form.errors.project_cd }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    プロジェクト名<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div>
                    <input type="text" placeholder="プロジェクト名" class="min-w-[300px] rounded py-1"
                        v-model="form.project_name" :class="form.errors.project_name ? 'border-red-500' : ''"
                        :disabled="props.type == 'View'">
                    <div>
                        <small class="text-red-500">{{ form.errors.project_name }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    プロジェクト概要<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div class="flex-1">
                    <textarea rows="3" placeholder="プロジェクト概要" class="w-full rounded py-1"
                        :class="form.errors.project_desc ? 'border-red-500' : ''"
                        v-model="form.project_desc" :disabled="props.type == 'View'"></textarea>
                    <div>
                        <small class="text-red-500">{{ form.errors.project_desc }}</small>
                    </div>
                </div>
            </div>
            
            <hr class="mb-2" />

            <div class="mb-2 flex">
                <div class="font-bold w-[170px]">
                    参加ユーザー<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div class="flex-1">
                    <MultiSelect
                        v-model="form.project_users"
                        locale="ja"
                        mode="tags"
                        :options="user_options"
                        :close-on-select="false"
                        :searchable="true"
                        :class="form.errors.project_users ? 'border-red-500' : ''"
                        :disabled="props.type == 'View'"
                    />
                    <div>
                        <small class="text-red-500">{{ form.errors.project_users }}</small>
                    </div>
                </div>
            </div>

            <div class="mt-5 mb-3 text-center">
                    <Link :href="route('projects')">
                        <button type="button" class="bg-slate-300 hover:bg-slate-400 rounded py-1 px-4">戻る</button>
                    </Link>

                <template v-if="props.type == 'View'">
                    <button class="bg-red-500 hover:bg-red-700 text-white rounded py-1 px-4 ms-2"
                        type="button" @click="onClickDelete">
                        削除
                    </button>
                    <Link :href="route('edit-project', { 'project_id': props.project.project_id })">
                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-2">
                            編集画面へ
                        </button>
                    </Link>
                </template>

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
    @import '@vueform/multiselect/themes/default.css';
    .multiselect {
        border-color: #6b7280;
    }
    .multiselect.border-red-500 {
        border-color: red;
    }
</style>
