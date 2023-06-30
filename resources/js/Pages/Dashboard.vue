<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';

const props = defineProps({
  common: Object
});


</script>

<template>
    <Head title="Backlog - Dashboard" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <!-- projects table start -->
        <div class="font-bold mb-2">プロジェクト一覧</div>
        <div class="bg-slate-100 py-2 px-3 hidden">
            <div class="bg-white text-center py-3">
                まだプロジェクトに参加していません。<br/>
                <a href="#">プロジェクトの追加</a>を行ってください。
            </div>
        </div>
        <div class="bg-slate-100 py-2 px-3">
            <div class="even:bg-slate-200 odd:bg-white hover:bg-slate-300 mb-2 px-3 py-2" onclick="goToProject(1)" role='button'>
                プロジェクト01 (PROJECT-01)
            </div>
            <div class="even:bg-slate-200 odd:bg-white hover:bg-slate-300 mb-2 px-3 py-2" onclick="goToProject(2)" role='button'>
                プロジェクト02 (PROJECT-02)
            </div>
            <div class="even:bg-slate-200 odd:bg-white hover:bg-slate-300 px-3 py-2" onclick="goToProject(3)" role='button'>
                プロジェクト03 (PROJECT-03)
            </div>
        </div>
        <!-- projects table end -->

        <!-- issues table start -->
        <div class="flex justify-between mx-0 mt-5">
            <div class="font-bold px-0">課題一覧</div>
            <div class="text-end px-0">
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4"><small>ガントチャート</small></button>
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-2"><small>課題一括操作</small></button>
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-2"><small>カレンダー取込</small></button>
            </div>
        </div>
        
        <div class="bg-slate-100 py-2 px-3 my-2">
            <div class="font-bold mb-1">絞り込み条件</div>
            <div class="flex py-1">
                <div class="font-bold inline-block min-w-[80px]"><small>対象：</small></div>
                <div class="inline-block">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>担当 (1)</small></button>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>登録 (1)</small></button>
                </div>
            </div>
            <div class="flex py-1">
                <div class="font-bold inline-block min-w-[80px]"><small>期限日：</small></div>
                <div class="inline-block">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>すべて (1)</small></button>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>4日以内 (1)</small></button>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>今日まで (1)</small></button>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>期限切れ (1)</small></button>
                </div>
            </div>
        </div>

        <div class="bg-slate-100 py-3 px-3">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-center py-2">種別</th>
                        <th class="text-center py-2">キー</th>
                        <th class="text-center py-2">件名</th>
                        <th class="text-center py-2">優先度</th>
                        <th class="text-center py-2">状態</th>
                        <th class="text-center py-2">期限日</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="even:bg-slate-200 odd:bg-white hover:bg-slate-300" role="button">
                        <td class="py-2 text-center"><span class="bg-green-300 rounded-full px-4"><small>タスク</small></span></td>
                        <td class="py-2">PROJECT-01-01</td>
                        <td class="py-2">課題〇〇</td>
                        <td class="py-2 text-center">高</td>
                        <td class="py-2 text-center"><span class="bg-rose-300 rounded-full px-4"><small>未対応</small></span></td>
                        <td class="py-2 text-center">2/28</td>
                    </tr>
                    <tr class="even:bg-slate-200 odd:bg-white hover:bg-slate-300" role="button">
                        <td class="py-2 text-center"><span class="bg-rose-300 rounded-full px-4"><small>バグ</small></span></td>
                        <td class="py-2">PROJECT-01-02</td>
                        <td class="py-2">課題〇〇</td>
                        <td class="py-2 text-center">中</td>
                        <td class="py-2 text-center"><span class="bg-yellow-300 rounded-full px-4"><small>処理中</small></span></td>
                        <td class="py-2 text-center">2/28</td>
                    </tr>
                    <tr class="even:bg-slate-200 odd:bg-white hover:bg-slate-300" role="button">
                        <td class="py-2 text-center"><span class="bg-green-300 rounded-full px-4"><small>タスク</small></span></td>
                        <td class="py-2">PROJECT-01-03</td>
                        <td class="py-2">課題〇〇</td>
                        <td class="py-2 text-center">低</td>
                        <td class="py-2 text-center"><span class="bg-green-300 rounded-full px-4"><small>完了</small></span></td>
                        <td class="py-2 text-center">2/28</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- issues table end -->
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
