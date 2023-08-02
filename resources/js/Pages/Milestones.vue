<script setup>
import { ref } from 'vue';
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import GridTable from '../Components/GridTable.vue';
import { h } from "gridjs";
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  common: Object
});

const milestoneTableRef = ref();
const form = useForm({});

const grid_columns = [
    {
        name: 'マイルストーン名',
        id: 'milestone_name',
        sort: true
    },
    {
        name: 'アクション',
        id: 'milestone_id',
        formatter: (_, row) => {
            let buttons = [];
            buttons.push(h('button', {
                className: 'bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4',
                onClick: () => goToEdit(row.cells[1].data)
            }, '編集'));
            buttons.push(h('button', {
                className: 'bg-red-500 hover:bg-red-700 text-white rounded py-1 px-4 ms-2',
                onClick: () => onClickDelete(row.cells[1].data)
            }, '削除'));
            return buttons;
        }
    }
];

const goToEdit = (milestone_id) => {
    form.get(route('edit-milestone', { 'milestone_id': milestone_id }));
};
const onClickDelete = (milestone_id) => {
    Swal.fire({
        title: '削除しますか?',
        text: "戻すことはできません。",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#94A3B8',
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('delete-milestone', { 'milestone_id': milestone_id }), {
                onFinish: () => {
                    milestoneTableRef.value.rerender();
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Backlog - Milestones" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div class="bg-slate-100 py-2 px-3 my-2">
            <div class="font-bold mb-2">マイルストーン一覧</div>
            
            <Link :href="route('create-milestone')">
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-1">マイルストーン作成</button>
            </Link>

            <GridTable
                id="milestone_grid_table"
                ref="milestoneTableRef"
                :data_url="route('search-milestones')"
                :grid_columns="grid_columns"
                :auto_width="false"
                :pagination="true" />
        </div>
    </main>

    <AppFooter />
</template>

<style>
#milestone_grid_table th:last-child,
#milestone_grid_table td:last-child {
    width: 350px !important;
    text-align: center;
}
</style>
