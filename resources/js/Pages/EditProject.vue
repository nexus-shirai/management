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
  statuses: Object,
  milestones: Object,
});

let statuses = [];
let milestones = [];
let total_issues = props.project.issues ? props.project.issues.length : 0;

if (props.statuses) {
    props.statuses.forEach(status => {
        statuses.push({
            'status_name' : status.status_name,
            'hex_color' : status.hex_color,
            'count': props.project.issues.filter(issue => issue.status_id == status.status_id).length
        });
    });
}

if (props.milestones) {
    props.milestones.forEach(milestone => {
        let total_milestone_issues = props.project.issues.filter(issue => issue.milestone_id == milestone.milestone_id).length;
        milestones[milestone.milestone_id] = {};
        milestones[milestone.milestone_id]['milestone_name'] = milestone.milestone_name;
        milestones[milestone.milestone_id]['issues'] = [];

        props.statuses.forEach(status => {
            let count = props.project.issues.filter(issue => issue.status_id == status.status_id && issue.milestone_id == milestone.milestone_id).length;
            let percentage = (count / total_milestone_issues) * 100;

            if (!isNaN(percentage)) {
                milestones[milestone.milestone_id]['issues'].push({
                    'hex_color' : status.hex_color,
                    'percentage': percentage
                });

                // hardcoded complete flg to 5
                if (status.status_id == 5) {
                    milestones[milestone.milestone_id]['completed_percentage'] = percentage
                }
            }
        });
    });

    milestones.unshift({
        'milestone_name': '設定なし',
        'issues': []
    });

    let total_milestone_issues = props.project.issues.filter(issue => issue.milestone_id == null).length;
    props.statuses.forEach(status => {
        let count = props.project.issues.filter(issue => issue.status_id == status.status_id && issue.milestone_id == null).length;
        let percentage = (count / total_milestone_issues) * 100;

        if (!isNaN(percentage)) {
            milestones[0].issues.push({
                'hex_color' : status.hex_color,
                'percentage': percentage
            });

            // hardcoded complete flg to 5
            if (status.status_id == 5) {
                milestones[0]['completed_percentage'] = percentage
            }
        }
    });

    milestones = milestones.filter(Boolean);
}

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
        html: "紐づいている課題も削除対象になります。<br/>戻すことはできません。",
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
        <div class="flex justify-between">
            <div>
                <template v-if="props.type == 'Create' || props.type == 'View'">
                    <Link :href="route('projects')">
                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</button>
                    </Link>
                </template>
                <template v-if="props.type == 'Edit'">
                    <Link :href="route('view-project', { 'project_id': props.project.project_id })">
                        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</button>
                    </Link>
                </template>
            </div>
            <div>
                <template v-if="props.type == 'View'">
                    <Link :href="route('gantt-chart', { 'project_id': props.project.project_id })">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">ガントチャート</button>
                    </Link>
                    <Link :href="route('board', { 'project_id': props.project.project_id })">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-2">ボード</button>
                    </Link>
                    <Link :href="route('issues', { 'project_id': props.project.project_id })">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-2">課題一覧</button>
                    </Link>
                </template>
            </div>
        </div>

        <div class="bg-slate-100 py-2 px-3 my-2">
            <!-- breadcrumbs -->
            <div class="font-bold mt-2 mb-4">
                <Link :href="route('projects')" class="font-bold text-blue-700 hover:underline">
                    プロジェクト一覧
                </Link>
                <span class="ms-4"><i class="fa-solid fa-angle-right"></i></span>
                <span class="ms-4">プロジェクト{{ title }}</span>
            </div>
            
            <template v-if="props.type == 'View'">
                <template v-if="props.project.issues.length">
                    <div class="flex mx-0 pb-7">
                        <div class="flex-1 px-2">
                            <div class="font-bold mb-2">状態</div>
                            <div class="bg-slate-200 px-3 py-3">
                                <div class="flex">
                                    <template v-for="status in statuses">
                                        <div class="inline-block h-[30px]"
                                            :style="{
                                            width: ((status.count / total_issues) * 100) + '%',
                                            backgroundColor: status.hex_color}"></div>
                                    </template>
                                </div>
                                <div class="mt-3">
                                    <template v-for="status in statuses">
                                        <div class="inline-block text-center mb-1">
                                            <div><small>{{ status.status_name }}</small></div>
                                            <div>
                                                <span class="rounded-full inline-block min-w-[70px] pb-1 me-2"
                                                    :style="{ backgroundColor: status.hex_color }">
                                                    <small>{{ status.count }}</small>
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 px-2">
                            <div class="font-bold mb-2">マイルストーン</div>
                            <div class="bg-slate-200 px-3 py-3">
                                <template v-for="milestone in milestones">
                                    <div class="mb-2">
                                        <div>
                                            <span class="font-bold">{{ milestone.milestone_name }}</span>
                                            <template v-if="milestone.issues.length == 0">
                                                <small class="ms-2">（0件）</small>
                                            </template>
                                        </div>
                                        <template v-if="milestone.issues.length">
                                            <div class="flex">
                                                <div style="width: 82%;">
                                                    <div class="flex">
                                                        <template v-for="issue in milestone.issues">
                                                            <div class="inline-block h-[30px]"
                                                                :style="{ backgroundColor: issue.hex_color, width: issue.percentage + '%' }">
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div style="width: 18%;">
                                                    <div class="text-end">
                                                        <small>{{ milestone.completed_percentage }}%<span class="ms-1">完了</span></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="mb-7">
                        <div class="bg-white text-center py-3">
                            まだ課題が作成していません。<br/>
                            <Link :href="route('issues', { 'project_id': props.project.project_id })"
                                class="text-blue-700 hover:underline font-bold">
                                課題の追加
                            </Link>を行ってください。
                        </div>
                    </div>
                </template>
            </template>

            <form @submit.prevent="submit">
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
                    <template v-if="props.type == 'View'">
                        <button class="bg-red-500 hover:bg-red-700 text-white rounded py-1 px-4"
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
        </div>
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
