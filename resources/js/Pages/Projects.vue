<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import GridTable from '../Components/GridTable.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { h } from "gridjs";

const props = defineProps({
  common: Object
});

const grid_columns = [
    {
        name: 'プロジェクトコード',
        id: 'project_cd',
        sort: true
    },
    {
        name: 'プロジェクト名',
        id: 'project_name',
        sort: true
    },
    {
        name: 'アクション',
        id: 'project_id',
        formatter: (_, row) => {
            let buttons = [];
            buttons.push(h('button', {
                className: 'bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4',
                onClick: () => goToDetail(row.cells[2].data)
            }, '詳細'));
            return buttons;
        }
    }
];

const goToDetail = (project_id) => {
    const form = useForm({});
    form.get(route('view-project', { 'project_id': project_id }));
};
</script>

<template>
    <Head title="Backlog - Projects" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div class="bg-slate-100 py-2 px-3 my-2">
            <div class="font-bold mb-2">プロジェクト一覧</div>
            
            <Link :href="route('create-project')">
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-1">プロジェクト作成</button>
            </Link>

            <GridTable
                id="project_grid_table"
                :data_url="route('search-projects')"
                :grid_columns="grid_columns"
                :auto_width="false"
                :pagination="true" />
        </div>
    </main>

    <AppFooter />
</template>

<style>
#project_grid_table th:last-child,
#project_grid_table td:last-child {
    width: 300px !important;
    text-align: center;
}
</style>
