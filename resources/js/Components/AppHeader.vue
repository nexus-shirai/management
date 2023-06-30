<script setup>
import Swal from 'sweetalert2';

const props = defineProps({
  common: Object
});

if (props.common.flash_message) {
    Swal.fire({
        icon: 'success',
        toast: true,
        showConfirmButton: false,
        position: 'top-end',
        timer: 2000,
        timerProgressBar: true,
        html: '<span class="font-bold">' + props.common.flash_message + '</span>',
    });
}
</script>

<template>
    <header class="bg-slate-100 py-3 px-5">
        <div class="flex justify-between">
            <div>
                <img class="inline" src="/images/nexusjapan_logo.png" width="220" />
                <div v-if="props.common.auth_user" class="inline-block ms-5">
                    <Link :href="route('dashboard')" class="font-bold text-blue-700 hover:underline ms-9">
                        <i class="fa-solid fa-desktop"></i>
                        <span class="ms-2">ダッシュボード</span>
                    </Link>
                    <Link :href="route('issues')" class="font-bold text-blue-700 hover:underline ms-9">
                        <i class="fa-solid fa-flag"></i>
                        <span class="ms-2">課題</span>
                    </Link>
                    <Link :href="route('board')" class="font-bold text-blue-700 hover:underline ms-9">
                        <i class="fa-solid fa-clipboard"></i>
                        <span class="ms-2">ボード</span>
                    </Link>
                    <Link :href="route('gantt-chart')" class="font-bold text-blue-700 hover:underline ms-9">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span class="ms-2">ガントチャート</span>
                    </Link>
                    <div class="inline ms-9 relative">
                        <span class="peer text-2xl text-blue-300 hover:text-blue-700">
                            <i class="fa-solid fa-circle-plus"></i>
                        </span>
                        <div class="absolute left-0 hidden peer-hover:flex hover:flex w-[170px] flex-col bg-white drop-shadow-lg">
                            <Link :href="route('create-project')" class="px-5 py-2 font-bold text-blue-700 hover:underline hover:bg-gray-200">
                                <span class="ms-2">プロジェクト作成</span>
                            </Link>
                            <Link :href="route('create-user')" class="px-5 py-2 font-bold text-blue-700 hover:underline hover:bg-gray-200">
                                <span class="ms-2">ユーザー作成</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div v-if="props.common.auth_user" class="pt-2">
                    <Link :href="route('logout')" method="post" as="button" class="font-bold text-blue-700 hover:underline">
                        ログアウト<i class="fa-solid fa-right-from-bracket ms-2"></i>
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
</style>
