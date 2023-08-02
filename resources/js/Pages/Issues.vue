<script setup>
import { ref } from 'vue';
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import GridTable from '../Components/GridTable.vue';
import { h, html } from "gridjs";
import dayjs from 'dayjs';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  common: Object,
  project: Object,
  issues: Object,
  statuses: Object,
  categories: Object,
  milestones: Object,
  project_users: Object,
});

const grid_columns = [
    {
        name: '種別',
        id: 'kind',
        // sort: true,
        formatter: (_, row) => html(
            `<span class="rounded-full px-4 whitespace-nowrap text-white"
                title="${row.cells[0].data.kind_desc}"
                role="button"
                style="background-color: ${row.cells[0].data.hex_color};">
                <small>
                    ${row.cells[0].data.kind_name}
                </small>
            </span>`),
    },
    {
        name: 'キー',
        id: 'issue_cd',
        sort: true
    },
    {
        name: '件名',
        id: 'issue_title',
        sort: true
    },
    {
        name: '優先度',
        id: 'issue_priority',
        // sort: true,
        formatter: (_, row) => {
            switch(row.cells[3].data) {
                case 'low':
                    return '低';
                case 'medium':
                    return '中';
                case 'high':
                    return '高';
                default:
                    return '';
            }
        }
    },
    {
        name: '状態',
        id: 'status',
        sort: true,
        formatter: (_, row) => html(
            `<span class="rounded-full px-4 whitespace-nowrap text-white"
                role="button"
                style="background-color: ${row.cells[4].data.hex_color};">
                <small>
                    ${row.cells[4].data.status_name}
                </small>
            </span>`),
    },
    {
        name: '終了日',
        id: 'end_date',
        sort: true,
        formatter: (_, row) => dayjs(row.cells[5].data).format("MM/DD")
    },
    {
        name: 'アクション',
        id: 'issue_id',
        formatter: (_, row) => {
            let buttons = [];
            buttons.push(h('button', {
                className: 'bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4',
                onClick: () => goToDetail(row.cells[6].data)
            }, '詳細'));
            return buttons;
        }
    }
];

const form = useForm({
    status_id: 0,
    rank_id: '',
    category_id: null,
    milestone_id: null,
    project_user_id: null,
});

const goToDetail = (issue_id) => {
    form.get(route('view-issue', { 'project_id': props.project.project_id, 'issue_id': issue_id }));
};

const issueTableRef = ref();
const grid_data = ref([]);

grid_data.value = props.issues.map(value => grid_columns.map(column => value[column.id]));

const onChangeStatus = (status_id) => {
    if (form.status_id == status_id) return;
    form.status_id = status_id;
    filterData();
}

const onChangeRank = (rank_id) => {
    if (form.rank_id == rank_id) return;
    form.rank_id = rank_id;
    filterData();
}

const filterData = () => {
    grid_data.value = [];
    props.issues.forEach(issue => {
        if (!(form.status_id == 0 || form.status_id == issue.status_id)) return;
        if (!(form.rank_id == '' || form.rank_id == issue.rank_id)) return;
        if (!(form.milestone_id == null || form.milestone_id == issue.milestone_id)) return;
        if (!(form.project_user_id == null || form.project_user_id == issue.assignee_id)) return;
        let issue_category_ids = issue.issue_categories.map((issue_category) => {
            return issue_category.category_id;
        });
        if (!(form.category_id == null || issue_category_ids.includes(form.category_id))) return;

        grid_data.value.push(grid_columns.map(column => issue[column.id]));
    });
};
</script>

<template>
    <Head title="Backlog - Issues" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div class="bg-slate-100 py-2 px-3 my-2">
            <div class="font-bold">検索条件</div>
            <div class="bg-slate-200 py-2 px-3 my-2">
                <div class="py-1">
                    <div class="font-bold inline-block min-w-[80px]"><small>状態：</small></div>
                    <div class="inline-block">
                        <button :class="form.status_id == 0 ? 'bg-blue-500 text-white' : ''" @click="onChangeStatus(0)"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>すべて</small>
                        </button>
                        <template v-for="status in props.statuses">
                            <button :class="form.status_id == status.status_id ? 'bg-blue-500 text-white' : ''"
                                @click="onChangeStatus(status.status_id)"
                                class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                                <small>{{ status.status_name }}</small>
                            </button>
                        </template>
                    </div>
                </div>
                <div class="py-1">
                    <div class="font-bold inline-block min-w-[80px]"><small>親子課題：</small></div>
                    <div class="inline-block">
                        <button :class="form.rank_id == '' ? 'bg-blue-500 text-white' : ''" @click="onChangeRank('')"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>すべて</small>
                        </button>
                        <button :class="form.rank_id == 'parent' ? 'bg-blue-500 text-white' : ''" @click="onChangeRank('parent')"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>親課題</small>
                        </button>
                        <button :class="form.rank_id == 'child' ? 'bg-blue-500 text-white' : ''" @click="onChangeRank('child')"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>子課題</small>
                        </button>
                        <button :class="form.rank_id == 'grandchild' ? 'bg-blue-500 text-white' : ''" @click="onChangeRank('grandchild')"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>孫課題</small>
                        </button>
                    </div>
                </div>
                <div class="py-1 mt-2">
                    <div class="inline-block">
                        <div class="font-bold"><small>カテゴリー</small></div>
                        <div>
                            <select v-model="form.category_id" class="w-[210px] rounded py-1" @change="filterData()">
                                <option :value="null">カテゴリーを選択する</option>
                                <template v-for="category in props.categories">
                                    <option :value="category.category_id">
                                        {{ category.category_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="inline-block ms-2">
                        <div class="font-bold"><small>マイルストーン</small></div>
                        <div>
                            <select v-model="form.milestone_id" class="w-[210px] rounded py-1" @change="filterData()">
                                <option :value="null">マイルストーンを選択する</option>
                                <template v-for="milestone in props.milestones">
                                    <option :value="milestone.milestone_id">
                                        {{ milestone.milestone_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="inline-block ms-2">
                        <div class="font-bold"><small>担当者</small></div>
                        <div>
                            <select v-model="form.project_user_id" class="w-[210px] rounded py-1" @change="filterData()">
                                <option :value="null">担当者を選択する</option>
                                <template v-for="project_user in props.project_users">
                                    <option :value="project_user.user_id">
                                        {{ project_user.user.username }}
                                    </option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-100 py-3 px-3">
            <div class="font-bold mb-2">課題一覧</div>
            
            <Link :href="route('create-issue', { 'project_id': props.project.project_id })">
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-1">課題作成</button>
            </Link>

            <GridTable
                id="issue_grid_table"
                ref="issueTableRef"
                :data="grid_data"
                :grid_columns="grid_columns"
                :auto_width="true"
                :pagination="true" />
        </div>
    </main>

    <AppFooter />
</template>

<style>
#issue_grid_table th:nth-child(1),
#issue_grid_table th:nth-child(4),
#issue_grid_table th:nth-child(5),
#issue_grid_table th:nth-child(6),
#issue_grid_table th:nth-child(7),
#issue_grid_table td:nth-child(1),
#issue_grid_table td:nth-child(4),
#issue_grid_table td:nth-child(5),
#issue_grid_table td:nth-child(6),
#issue_grid_table td:nth-child(7) {
    text-align: center;
}
</style>
