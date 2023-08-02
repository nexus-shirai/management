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
  user_projects: Object,
  user_issues: Object,
});

const issueTableRef = ref();
const grid_data = ref([]);

const grid_columns = [
    {
        name: '種別',
        id: 'kind',
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
                onClick: () => goToDetail(row.cells[7].data, row.cells[6].data)
            }, '詳細'));
            return buttons;
        }
    },
    {
        name: '',
        id: 'project_id',
        hidden: true
    }
];

let target_01 = 0;
let target_02 = 0;
let dealine_01 = 0;
let dealine_02 = 0;
let dealine_03 = 0;
let dealine_04 = 0;

props.user_issues.forEach(issue => {
    if (issue.assignee_id == props.common.auth_user.user_id) {
        target_01++;
    }
    if (issue.create_user_id == props.common.auth_user.user_id) {
        target_02++;
    }

    dealine_01++;
    let today = dayjs().startOf("day");
    let end_date = dayjs(issue.end_date).startOf("day");

    // hardcoded complete flg to 5
    if ((end_date.isSame(today, "day") || end_date.isAfter(today, "day"))
        && end_date.diff(today, "day") <= 3 && issue.status_id != 5) {
        dealine_02++;
    }
    if (end_date.isSame(today, "day") && issue.status_id != 5) {
        dealine_03++;
    }
    if (end_date.isBefore(today, "day") && issue.status_id != 5) {
        dealine_04++;
    }
});

const target = ref(1);
const deadline = ref(1);
const form = useForm({});

const goToDetail = (project_id, issue_id) => {
    form.get(route('view-issue', { 'project_id': project_id, 'issue_id': issue_id }));
};

const filterData = () => {
    grid_data.value = [];
    props.user_issues.forEach(issue => {
        if (target.value == 1 && issue.assignee_id != props.common.auth_user.user_id) return;
        if (target.value == 2 && issue.create_user_id != props.common.auth_user.user_id) return;
        
        let today = dayjs().startOf("day");
        let end_date = dayjs(issue.end_date).startOf("day");
        if (deadline.value == 2 && !((end_date.isSame(today, "day") || end_date.isAfter(today, "day")) && end_date.diff(today, "day") <= 3)) return;
        if (deadline.value == 3 && !(end_date.isSame(today, "day"))) return;
        if (deadline.value == 4 && !(end_date.isBefore(today, "day"))) return;

        // hardcoded complete flg to 5
        if (([2, 3, 4]).includes(deadline.value) && issue.status_id == 5) return;

        grid_data.value.push(grid_columns.map(column => issue[column.id]));
    });
};

filterData();

const onChangeTarget = (val) => {
    if (target.value == val) return;
    target.value = val;
    filterData();
}

const onChangeDeadline = (val) => {
    if (deadline.value == val) return;
    deadline.value = val;
    filterData();
}
</script>

<template>
    <Head title="Backlog - Dashboard" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <!-- projects table start -->
        <div class="font-bold mb-2">プロジェクト一覧</div>
        <template v-if="props.user_projects.length">
            <div class="bg-slate-100 py-3 px-3">
                <template v-for="user_project in props.user_projects">
                    <Link :href="route('view-project', { 'project_id': user_project.project_id })"
                        class="even:bg-slate-200 odd:bg-white hover:bg-slate-300 mb-2 px-3 py-3 block">
                        {{ user_project.project.project_name }} ({{ user_project.project.project_cd }})
                    </Link>
                </template>
            </div>
        </template>
        <template v-else>
            <div class="bg-slate-100 py-2 px-3">
                <div class="bg-white text-center py-3">
                    まだプロジェクトに参加していません。<br/>
                    <Link :href="route('create-project')" class="text-blue-700 hover:underline font-bold">
                        プロジェクトの追加
                    </Link>を行ってください。
                </div>
            </div>
        </template>
        <!-- projects table end -->

        <!-- issues table start -->
        <div class="mt-5 font-bold">課題一覧</div>
        
        <div class="bg-slate-100 py-2 px-3 my-2">
            <div class="font-bold mb-1">絞り込み条件</div>
            <div class="bg-slate-200 py-2 px-3 my-2">
                <div class="flex py-1">
                    <div class="font-bold inline-block min-w-[80px]"><small>対象：</small></div>
                    <div class="inline-block">
                        <button :class="target == 1 ? 'bg-blue-500 text-white' : ''" @click="onChangeTarget(1)"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>担当 ({{ target_01 }})</small>
                        </button>
                        <button :class="target == 2 ? 'bg-blue-500 text-white' : ''" @click="onChangeTarget(2)"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>登録 ({{ target_02 }})</small>
                        </button>
                    </div>
                </div>
                <div class="flex py-1">
                    <div class="font-bold inline-block min-w-[80px]"><small>期限日：</small></div>
                    <div class="inline-block">
                        <button :class="deadline == 1 ? 'bg-blue-500 text-white' : ''" @click="onChangeDeadline(1)"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>すべて ({{ dealine_01 }})</small>
                        </button>
                        <button :class="deadline == 2 ? 'bg-blue-500 text-white' : ''" @click="onChangeDeadline(2)"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>4日以内 ({{ dealine_02 }})</small>
                        </button>
                        <button :class="deadline == 3 ? 'bg-blue-500 text-white' : ''" @click="onChangeDeadline(3)"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>今日まで ({{ dealine_03 }})</small>
                        </button>
                        <button :class="deadline == 4 ? 'bg-blue-500 text-white' : ''" @click="onChangeDeadline(4)"
                            class="hover:bg-blue-700 hover:text-white rounded-full py-1 px-4 ms-2">
                            <small>期限切れ ({{ dealine_04 }})</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-100 py-3 px-3">
            <GridTable
                id="issue_grid_table"
                ref="issueTableRef"
                :data="grid_data"
                :grid_columns="grid_columns"
                :auto_width="true"
                :pagination="true" />
        </div>
        <!-- issues table end -->
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