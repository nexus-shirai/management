<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';

const props = defineProps({
  common: Object
});
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
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>すべて</small></button>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>未対応</small></button>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>処理中</small></button>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>処理済み</small></button>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>完了</small></button>
                    </div>
                </div>
                <div class="py-1">
                    <div class="font-bold inline-block min-w-[80px]"><small>親子課題：</small></div>
                    <div class="inline-block">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>すべて</small></button>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>親課題</small></button>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>子課題</small></button>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white rounded-full py-1 px-4 ms-2"><small>孫課題</small></button>
                    </div>
                </div>
                <div class="py-1 mt-2">
                    <div class="inline-block">
                        <div class="font-bold"><small>カテゴリー</small></div>
                        <div>
                            <select class="min-w-[130px] rounded py-1">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="inline-block ms-2">
                        <div class="font-bold"><small>マイルストーン</small></div>
                        <div>
                            <select class="min-w-[130px] rounded py-1">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="inline-block ms-2">
                        <div class="font-bold"><small>担当者</small></div>
                        <div>
                            <select class="min-w-[130px] rounded py-1">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="inline-block ms-2">
                        <div class="font-bold"><small>キーワード</small></div>
                        <div>
                            <select class="min-w-[130px] rounded py-1">
                                <option></option>
                            </select>
                        </div>
                    </div>
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
                        <th class="text-center py-2">担当者</th>
                        <th class="text-center py-2">状態</th>
                        <th class="text-center py-2">カテゴリー</th>
                        <th class="text-center py-2">優先度</th>
                        <th class="text-center py-2">マイルストーン</th>
                        <th class="text-center py-2">更新用</th>
                        <th class="text-center py-2">期限日</th>
                        <th class="text-center py-2">更新者</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="even:bg-slate-200 odd:bg-white hover:bg-slate-300" onclick="goToIssue(1)" role="button">
                        <td class="py-2 text-center">タスク</td>
                        <td class="py-2">PROJECT-01-01</td>
                        <td class="py-2">課題〇〇</td>
                        <td class="py-2">担当者1</td>
                        <td class="py-2 text-center">未対応</td>
                        <td class="py-2 text-center">タスク</td>
                        <td class="py-2 text-center">中</td>
                        <td class="py-2 text-center">初期リリース</td>
                        <td class="py-2 text-center">2/1</td>
                        <td class="py-2 text-center">2/1</td>
                        <td class="py-2 text-center">山田</td>
                    </tr>
                    <tr class="even:bg-slate-200 odd:bg-white hover:bg-slate-300" onclick="goToIssue(2)" role="button">
                        <td class="py-2 text-center">質問</td>
                        <td class="py-2">PROJECT-01-02</td>
                        <td class="py-2">課題〇〇</td>
                        <td class="py-2">担当者2</td>
                        <td class="py-2 text-center">処理中</td>
                        <td class="py-2 text-center">不具合</td>
                        <td class="py-2 text-center">高</td>
                        <td class="py-2 text-center">2次リリース</td>
                        <td class="py-2 text-center">2/10</td>
                        <td class="py-2 text-center">2/10</td>
                        <td class="py-2 text-center">青木</td>
                    </tr>
                    <tr class="even:bg-slate-200 odd:bg-white hover:bg-slate-300" onclick="goToIssue(3)" role="button">
                        <td class="py-2 text-center">不具合</td>
                        <td class="py-2">PROJECT-01-03</td>
                        <td class="py-2">課題〇〇</td>
                        <td class="py-2">担当者3</td>
                        <td class="py-2 text-center">完了</td>
                        <td class="py-2 text-center">不具合</td>
                        <td class="py-2 text-center">低</td>
                        <td class="py-2 text-center">初期リリース</td>
                        <td class="py-2 text-center">12/10</td>
                        <td class="py-2 text-center">12/10</td>
                        <td class="py-2 text-center">村上</td>
                    </tr>
                    <tr class="even:bg-slate-200 odd:bg-white hover:bg-slate-300" onclick="goToIssue(4)" role="button">
                        <td class="py-2 text-center">そのた</td>
                        <td class="py-2">PROJECT-01-04</td>
                        <td class="py-2">課題〇〇</td>
                        <td class="py-2">担当者4</td>
                        <td class="py-2 text-center">未着手</td>
                        <td class="py-2 text-center">要望</td>
                        <td class="py-2 text-center">低</td>
                        <td class="py-2 text-center">追加機能</td>
                        <td class="py-2 text-center">12/5</td>
                        <td class="py-2 text-center">12/5</td>
                        <td class="py-2 text-center">川端</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
