<script setup>
import { ref } from 'vue';
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Swal from 'sweetalert2';
import MultiSelect from '@vueform/multiselect';

const props = defineProps({
  common: Object,
  type: String,
  project: Object,
  issue: Object,
  kinds: Object,
  categories: Object,
  statuses: Object,
  project_users: Object,
  milestones: Object,
  versions: Object,
  issues: Object,
});

const form = useForm({
    issue_id: props.issue ? props.issue.issue_id : null,
    project_id: props.project.project_id,
    kind_id: props.issue ? props.issue.kind_id : null,
    issue_title: props.issue ? props.issue.issue_title : '',
    issue_desc: props.issue ? props.issue.issue_desc : '',
    status_id: props.issue ? props.issue.status_id : null,
    assignee_id: props.issue ? props.issue.assignee_id : null,
    issue_priority: props.issue ? props.issue.issue_priority : null,
    milestone_id: props.issue ? props.issue.milestone_id : null,
    estimated_time: props.issue ? props.issue.estimated_time : null,
    completion_time: props.issue ? props.issue.completion_time : null,
    start_date: props.issue ? props.issue.start_date : null,
    end_date: props.issue ? props.issue.end_date : null,
    complete_reason: props.issue ? props.issue.complete_reason : '',
    issue_rank: props.issue ? props.issue.issue_rank : '',
    parent_issue_id: props.issue ? props.issue.parent_issue_id : null,
    version_id: props.issue ? props.issue.version_id : null,
    issue_categories: props.issue
        ? props.issue.issue_categories.map(issue_category => issue_category.category_id)
        : [],
    timeline_flg: false,
    status_flg: false
});

const category_options = ref([]);
category_options.value = props.categories.map(category => {
        return {
            label: category.category_name,
            value: category.category_id
        };
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

const submit = () => {
    if (props.type == "Create") {
        form.post(route('create-issue', { 'project_id': props.project.project_id }), {
            onFinish: () => {
                if (form.errors.timeline) {
                    confirmTimelineDialog(form.errors.timeline);
                }
                if (form.errors.status) {
                    confirmStatusDialog();
                }
            },
        });
    } else if (props.type == "Edit") {
        form.put(route('edit-issue', { 'project_id': props.project.project_id, 'issue_id': props.issue.issue_id }), {
            onFinish: () => {
                if (form.errors.timeline) {
                    confirmTimelineDialog(form.errors.timeline);
                }
                if (form.errors.status) {
                    confirmStatusDialog();
                }
                if (form.errors.children_not_completed) {
                    childrenNotCompletedDialog();
                    form.status_flg = false;
                    form.timeline_flg = false;
                }
            },
        });
    } else {
        // do nothing
    }
};

const confirmTimelineDialog = (issue_rank) => {
    let message = "";
    switch (issue_rank) {
        case "parent":
            message = `親課題の期間が変更されました。<br/>
                子課題/孫課題の期間と整合性が合わない場合、<br/>
                親課題に合わせて自動調整されます。`;
            break;
        case "child":
            message = `子課題の期間が変更されました。<br/>
                親課題/孫課題の期間と整合性が合わない場合、<br/>
                子課題に合わせて自動調整されます。`;
            break;
        case "grandchild":
            message = `孫課題の期間が変更されました。<br/>
                親課題/子課題の期間と整合性が合わない場合、<br/>
                子課題に合わせて自動調整されます。`;
            break;
        default:
            break;
    }

    Swal.fire({
        title: '実行しますか？',
        html: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#94A3B8',
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル'
    }).then((result) => {
        form.timeline_flg = result.isConfirmed;
        if (result.isConfirmed) {
            submit();
        } else {
            form.status_flg = result.isConfirmed;
        }
    });
}

const confirmStatusDialog = () => {
    Swal.fire({
        title: '実行しますか？',
        html: '親課題が完了状態です。<br/>親課題を処理中に変更しますか？',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#94A3B8',
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル'
    }).then((result) => {
        form.status_flg = result.isConfirmed;
        if (result.isConfirmed) {
            submit();
        } else {
            form.timeline_flg = result.isConfirmed;
        }
    });
}

const childrenNotCompletedDialog = () => {
    Swal.fire({
        icon: 'warning',
        toast: true,
        showConfirmButton: false,
        position: 'top-end',
        timer: 2000,
        timerProgressBar: true,
        html: '<span class="font-bold">子課題/孫課題が未完了の為、完了に変更できません。</span>',
    });
};

if (form.complete_reason == null) {
    form.complete_reason = '';
}

const onChangeStatus = () => {
    form.complete_reason = '';
};

const onChangeRank = () => {
    form.parent_issue_id = null;
}

const onClickDelete = () => {
    Swal.fire({
        title: '削除しますか?',
        html: "子課題・孫課題も削除対象になります。<br/>戻すことはできません。",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#CBD5E1',
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('delete-issue', { 'project_id': props.project.project_id, 'issue_id': props.issue.issue_id }));
        }
    });
};
</script>

<template>
    <Head :title="`Backlog - ${props.type} Issue`" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <Link :href="route('issues', { project_id: props.project.project_id })">
            <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</button>
        </Link>

        <form class="bg-slate-100 py-2 px-3 mt-2 mb-2" @submit.prevent="submit">
            <!-- breadcrumbs -->
            <div class="font-bold mt-2 mb-4">
                <Link :href="route('projects')" class="font-bold text-blue-700 hover:underline">
                    プロジェクト一覧
                </Link>
                <span class="ms-4"><i class="fa-solid fa-angle-right"></i></span>
                <Link :href="route('view-project', { project_id: props.project.project_id })"
                    class="font-bold text-blue-700 hover:underline ms-4">
                    {{ props.project.project_name }} ({{ props.project.project_cd  }})
                </Link>
                <span class="ms-4"><i class="fa-solid fa-angle-right"></i></span>
                <Link :href="route('issues', { project_id: props.project.project_id })"
                    class="font-bold text-blue-700 hover:underline ms-4">
                    課題一覧
                </Link>
                <span class="ms-4"><i class="fa-solid fa-angle-right"></i></span>
                <span class="ms-4">課題{{ title }}</span>
            </div>

            <div class="mb-4 flex">
                <div class="font-bold w-[135px]">プロジェクト</div>
                <div class="flex-1">{{ props.project.project_name }} ({{ props.project.project_cd }})</div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[135px]">
                    種別<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div class="flex-1">
                    <select v-model="form.kind_id" class="rounded py-1 min-w-[225px]" :disabled="props.type == 'View'"
                        :class="form.errors.kind_id ? 'border-red-500' : ''">
                        <option :value="null">種別を選択する</option>
                        <template v-for="kind in props.kinds">
                            <option :value="kind.kind_id">{{ kind.kind_name }}</option>
                        </template>
                    </select>
                    <div>
                        <small class="text-red-500">{{ form.errors.kind_id }}</small>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[135px]">
                    課題名<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                </div>
                <div class="flex-1">
                    <input v-model="form.issue_title" type="text" placeholder="課題名" class="w-full rounded py-1"
                        :disabled="props.type == 'View'" :class="form.errors.issue_title ? 'border-red-500' : ''">
                    <div>
                        <small class="text-red-500">{{ form.errors.issue_title }}</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-2 flex">
                <div class="font-bold w-[135px]">課題の詳細</div>
                <div class="flex-1">
                    <textarea v-model="form.issue_desc" rows="3" placeholder="課題の詳細" class="w-full rounded py-1" 
                        :class="form.errors.issue_desc ? 'border-red-500' : ''" :disabled="props.type == 'View'"></textarea>
                    <div>
                        <small class="text-red-500">{{ form.errors.issue_desc }}</small>
                    </div>
                </div>
            </div>

            <hr class="mb-2" />

            <div class="mb-2">
                <div class="flex">
                    <div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">
                                状態<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                            </div>
                            <div>
                                <select v-model="form.status_id" class="rounded py-1 min-w-[225px]" @change="onChangeStatus()"
                                    :class="form.errors.status_id ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                    <option :value="null">状態を選択する</option>
                                    <template v-for="status in props.statuses">
                                        <option :value="status.status_id">{{ status.status_name }}</option>
                                    </template>
                                </select>
                                <div>
                                    <small class="text-red-500">{{ form.errors.status_id }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">
                                優先度<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                            </div>
                            <div>
                                <select v-model="form.issue_priority" class="rounded py-1 min-w-[225px]"
                                    :class="form.errors.issue_priority ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                    <option :value="null">優先度を選択する</option>
                                    <option value="low">低</option>
                                    <option value="medium">中</option>
                                    <option value="high">高</option>
                                </select>
                                <div>
                                    <small class="text-red-500">{{ form.errors.issue_priority }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">予定時間</div>
                            <div>
                                <input v-model="form.estimated_time" class="rounded py-1 min-w-[225px]" type="number" min="0"
                                    :class="form.errors.estimated_time ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                <div>
                                    <small class="text-red-500">{{ form.errors.estimated_time }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">
                                開始日<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                            </div>
                            <div>
                                <input v-model="form.start_date" class="rounded py-1 min-w-[225px]" type="date"
                                    :class="form.errors.start_date ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                <div>
                                    <small class="text-red-500">{{ form.errors.start_date }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">
                                課題ランク<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                            </div>
                            <div>
                                <div class="mb-1">
                                    <input v-model="form.issue_rank" type="radio" id="rank_parent" value="parent"
                                        :disabled="props.type == 'View' || props.type == 'Edit'" @change="onChangeRank()">
                                    <label for="rank_parent" class="ms-3">親課題</label>
                                </div>
                                <div class="mb-1">
                                    <input v-model="form.issue_rank" type="radio" id="rank_child" value="child"
                                        :disabled="props.type == 'View' || props.type == 'Edit'" @change="onChangeRank()">
                                    <label for="rank_child" class="ms-3">子課題</label>
                                </div>
                                <div>
                                    <input v-model="form.issue_rank" type="radio" id="rank_grandchild" value="grandchild"
                                        :disabled="props.type == 'View' || props.type == 'Edit'" @change="onChangeRank()">
                                    <label for="rank_grandchild" class="ms-3">孫課題</label>
                                </div>
                                <div>
                                    <small class="text-slate-400">登録してから更新できない。</small>
                                </div>
                                <div>
                                    <small class="text-red-500">{{ form.errors.issue_rank }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">
                                親課題<sup v-if="form.issue_rank != 'parent'" class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                            </div>
                            <div>
                                <select v-model="form.parent_issue_id" class="rounded py-1 min-w-[225px]"
                                    :class="form.errors.parent_issue_id ? 'border-red-500' : 'parent'"
                                    :disabled="props.type == 'View' || form.issue_rank == 'parent' || form.issue_rank == '' || props.type == 'Edit'">
                                    <option :value="null">親課題を選択する</option>
                                    <template v-for="issue in props.issues">
                                        <option v-if="form.issue_rank == 'child' && issue.issue_rank == 'parent'" :value="issue.issue_id">
                                            {{ issue.issue_title }} ({{ issue.issue_cd }})
                                        </option>
                                        <option v-if="form.issue_rank == 'grandchild' && issue.issue_rank == 'child'" :value="issue.issue_id">
                                            {{ issue.issue_title }} ({{ issue.issue_cd }})
                                        </option>
                                    </template>
                                </select>
                                <div>
                                    <small class="text-slate-400">登録してから更新できない。</small>
                                </div>
                                <div>
                                    <small class="text-red-500" v-html="form.errors.parent_issue_id"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ms-12"></div>
                    <div class="ms-5"></div>

                    <div class="ms-5">
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">担当者</div>
                            <div>
                                <select v-model="form.assignee_id" class="rounded py-1 min-w-[225px]"
                                    :class="form.errors.assignee_id ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                    <option :value="null">担当者を選択する</option>
                                    <template v-for="project_user in props.project_users">
                                        <option :value="project_user.user_id">{{ project_user.user.username }}</option>
                                    </template>
                                </select>
                                <div>
                                    <small class="text-red-500">{{ form.errors.assignee_id }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">マイルストーン</div>
                            <div>
                                <select v-model="form.milestone_id" class="rounded py-1 min-w-[225px]"
                                    :class="form.errors.milestone_id ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                    <option :value="null">マイルストーン者を選択する</option>
                                    <template v-for="milestone in props.milestones">
                                        <option :value="milestone.milestone_id">{{ milestone.milestone_name }}</option>
                                    </template>
                                </select>
                                <div>
                                    <small class="text-red-500">{{ form.errors.milestone_id }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">実施時間</div>
                            <div>
                                <input v-model="form.completion_time" class="rounded py-1 min-w-[225px]" type="number" min="0"
                                    :class="form.errors.completion_time ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                <div>
                                    <small class="text-red-500">{{ form.errors.completion_time }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">
                                期限日<sup class="text-red-500 ms-2"><i class="fa-solid fa-asterisk"></i></sup>
                            </div>
                            <div>
                                <input v-model="form.end_date" class="rounded py-1 min-w-[225px]" type="date"
                                    :class="form.errors.end_date ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                <div>
                                    <small class="text-red-500">{{ form.errors.end_date }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">バージョン</div>
                            <div>
                                <select v-model="form.version_id" class="rounded py-1 min-w-[225px]"
                                    :class="form.errors.version_id ? 'border-red-500' : ''" :disabled="props.type == 'View'">
                                    <option :value="null">バージョンを選択する</option>
                                    <template v-for="version in props.versions">
                                        <option :value="version.version_id">{{ version.version_name }}</option>
                                    </template>
                                </select>
                                <div>
                                    <small class="text-red-500">{{ form.errors.version_id }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">完了理由</div>
                            <div>
                                <!-- hardcoded complete flg to 5 -->
                                <select v-model="form.complete_reason" class="rounded py-1 min-w-[225px]"
                                    :class="form.errors.complete_reason ? 'border-red-500' : ''"
                                    :disabled="props.type == 'View' || form.status_id != 5">
                                    <option :value="''">完了理由を選択する</option>
                                    <option value="対応済み">対応済み</option>
                                    <option value="対応しない">対応しない</option>
                                    <option value="無効">無効</option>
                                    <option value="重複">重複</option>
                                    <option value="再現しない">再現しない</option>
                                </select>
                                <div>
                                    <small class="text-red-500">{{ form.errors.complete_reason }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[135px]">カテゴリー</div>
                            <div>
                                <MultiSelect
                                    v-model="form.issue_categories"
                                    locale="ja"
                                    mode="tags"
                                    :options="category_options"
                                    :close-on-select="false"
                                    :searchable="true"
                                    :class="form.errors.issue_categories ? 'border-red-500' : ''"
                                    :disabled="props.type == 'View'"
                                />
                                <div>
                                    <small class="text-slate-400">複数選択できる。</small>
                                </div>
                                <div>
                                    <small class="text-red-500">{{ form.errors.issue_categories }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="mb-2" />

            <div id="file-upload-div" class="py-4 text-center border-dotted border-blue-500 border-2">
                <small>ファイルをドラッグ＆ドロップまたは
                    <button class="bg-blue-500 hover:bg-blue-700 text-white 
                        disabled:bg-slate-300 disabled:text-black rounded py-1 px-3 ms-2"
                        :disabled="type == 'View'">
                        ファイルを選択
                    </button>
                </small>
                <input id="file-upload-input" class="hidden" type="file">
            </div>

            <div class="mt-5 mb-3 text-center">
                <template v-if="type == 'View'">
                    <button class="bg-red-500 hover:bg-red-700 text-white rounded py-1 px-4 ms-2"
                        type="button" @click="onClickDelete">
                        削除
                    </button>
                    <Link :href="route('edit-issue', { 'project_id': props.project.project_id, 'issue_id': props.issue.issue_id })">
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
                    <template v-if="type == 'Edit'">
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
        width: 225px;
    }
    .multiselect.border-red-500 {
        border-color: red;
    }
</style>
