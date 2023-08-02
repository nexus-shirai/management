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

const userTableRef = ref();
const form = useForm({});

const grid_columns = [
    {
        name: 'ユーザー名',
        id: 'username',
        sort: true
    },
    {
        name: 'アクション',
        id: 'user_id',
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

const goToEdit = (user_id) => {
    form.get(route('edit-user', { 'user_id': user_id }));
};

const onClickDelete = (user_id) => {
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
            form.delete(route('delete-user', { 'user_id': user_id }), {
                onFinish: () => {
                    userTableRef.value.rerender();
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Backlog - Users" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div class="flex justify-between">
            <Link :href="route('dashboard')">
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</button>
            </Link>
            <Link :href="route('create-user')">
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">ユーザー作成</button>
            </Link>
        </div>
        
        <div class="bg-slate-100 py-2 px-3 my-2">
            <!-- breadcrumbs -->
            <div class="font-bold mt-2 mb-4">
                ユーザー一覧
            </div>

            <GridTable
                id="user_grid_table"
                ref="userTableRef"
                :data_url="route('search-users')"
                :grid_columns="grid_columns"
                :auto_width="false"
                :pagination="true" />
        </div>
    </main>

    <AppFooter />
</template>

<style>
#user_grid_table th:last-child,
#user_grid_table td:last-child {
    width: 350px !important;
    text-align: center;
}
</style>
