<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';

const props = defineProps({
  auth_user: Object
});
</script>

<template>
    <Head title="Backlog - Board" />
    <AppHeader :auth_user="props.auth_user" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        
        <div class="bg-slate-100 py-2 px-3 my-2">
            <div class="font-bold">ボード</div>

            <div class="py-1 px-2 mt-3 mb-2">
                <div class="inline-block">
                    <div class="font-bold"><small>種別</small></div>
                    <div>
                        <select class="rounded py-1 min-w-[170px]">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="inline-block ms-2">
                    <div class="font-bold"><small>カテゴリー</small></div>
                    <div>
                        <select class="rounded py-1 min-w-[170px]">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="inline-block ms-2">
                    <div class="font-bold"><small>マイルストーン</small></div>
                    <div>
                        <select class="rounded py-1 min-w-[170px]">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="inline-block ms-2">
                    <div class="font-bold"><small>担当者</small></div>
                    <div>
                        <select class="rounded py-1 min-w-[170px]">
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex mx-0 font-bold mt-4">
                <div class="flex-1 px-2">
                    <i class="fa-sharp fa-solid fa-circle text-rose-300"></i> 未対応
                </div>
                <div class="flex-1 px-2">
                    <i class="fa-sharp fa-solid fa-circle text-blue-500"></i> 処理中
                </div>
                <div class="flex-1 px-2">
                    <i class="fa-sharp fa-solid fa-circle text-green-300"></i> 処理済み
                </div>
                <div class="flex-1 px-2">
                    <i class="fa-sharp fa-solid fa-circle text-yellow-300"></i> 完了
                </div>
            </div>

            <div class="flex mx-0 mb-4">
                <div class="flex-1 px-2">
                    <div class="bg-slate-300 pt-3 pb-5 px-2 h-full min-h-[400px]">
                        <div class="bg-slate-200 hover:bg-slate-100 p-2 mb-2" role="button">
                            <div class="mb-1">
                                <span class="bg-green-300 rounded-full px-4"><small>タスク</small></span>
                            </div>
                            <div class="font-bold mb-1"><small>課題〇〇 (PROJECT-01-01)</small></div>
                            <div class="mb-1">
                                <small>テストタスクについて、あれこれをどれそれでどうこうしてそういう風に修正する</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-circle-user"></i>
                                <small class="ms-2">担当者01</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-calendar"></i>
                                <small class="ms-2">2023/06/21</small>
                            </div>
                        </div>
                        <div class="bg-slate-200 hover:bg-slate-100 p-2 mb-2" role="button">
                            <div class="mb-1">
                                <span class="bg-green-300 rounded-full px-4"><small>タスク</small></span>
                            </div>
                            <div class="font-bold mb-1"><small>課題〇〇 (PROJECT-01-02)</small></div>
                            <div class="mb-1">
                                <small>テストタスクについて、あれこれをどれそれでどうこうしてそういう風に修正する</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-circle-user"></i>
                                <small class="ms-2">担当者01</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-calendar"></i>
                                <small class="text-red-500 font-bold ms-2">2023/06/21 <i class="fa-solid fa-fire"></i></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 px-2">
                    <div class="bg-slate-300 pt-3 pb-5 px-2 h-full min-h-[400px]">
                        <div class="bg-slate-200 hover:bg-slate-100 p-2 mb-2" role="button">
                            <div class="mb-1">
                                <span class="bg-green-300 rounded-full px-4"><small>タスク</small></span>
                            </div>
                            <div class="font-bold mb-1"><small>課題〇〇 (PROJECT-01-01)</small></div>
                            <div class="mb-1">
                                <small>テストタスクについて、あれこれをどれそれでどうこうしてそういう風に修正する</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-circle-user"></i>
                                <small class="ms-2">担当者01</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-calendar"></i>
                                <small class="ms-2">2023/06/21</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 px-2">
                    <div class="bg-slate-300 pt-3 pb-5 px-2 h-full min-h-[400px]">
                        <div class="bg-slate-200 hover:bg-slate-100 p-2 mb-2" role="button">
                            <div class="mb-1">
                                <span class="bg-green-300 rounded-full px-4"><small>タスク</small></span>
                            </div>
                            <div class="font-bold mb-1"><small>課題〇〇 (PROJECT-01-01)</small></div>
                            <div class="mb-1">
                                <small>テストタスクについて、あれこれをどれそれでどうこうしてそういう風に修正する</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-circle-user"></i>
                                <small class="ms-2">担当者01</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-calendar"></i>
                                <small class="ms-2">2023/06/21</small>
                            </div>
                        </div>
                        <div class="bg-slate-200 hover:bg-slate-100 p-2 mb-2" role="button">
                            <div class="mb-1">
                                <span class="bg-green-300 rounded-full px-4"><small>タスク</small></span>
                            </div>
                            <div class="font-bold mb-1"><small>課題〇〇 (PROJECT-01-02)</small></div>
                            <div class="mb-1">
                                <small>テストタスクについて、あれこれをどれそれでどうこうしてそういう風に修正する</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-circle-user"></i>
                                <small class="ms-2">担当者01</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-calendar"></i>
                                <small class="text-red-500 font-bold ms-2">2023/06/21 <i class="fa-solid fa-fire"></i></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 px-2">
                    <div class="bg-slate-300 pt-3 pb-5 px-2 h-full min-h-[400px]">
                        <div class="bg-slate-200 hover:bg-slate-100 p-2 mb-2" role="button">
                            <div class="mb-1">
                                <span class="bg-green-300 rounded-full px-4"><small>タスク</small></span>
                            </div>
                            <div class="font-bold mb-1"><small>課題〇〇 (PROJECT-01-01)</small></div>
                            <div class="mb-1">
                                <small>テストタスクについて、あれこれをどれそれでどうこうしてそういう風に修正する</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-circle-user"></i>
                                <small class="ms-2">担当者01</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-calendar"></i>
                                <small class="ms-2">2023/06/21</small>
                            </div>
                        </div>
                        <div class="bg-slate-200 hover:bg-slate-100 p-2 mb-2" role="button">
                            <div class="mb-1">
                                <span class="bg-green-300 rounded-full px-4"><small>タスク</small></span>
                            </div>
                            <div class="font-bold mb-1"><small>課題〇〇 (PROJECT-01-02)</small></div>
                            <div class="mb-1">
                                <small>テストタスクについて、あれこれをどれそれでどうこうしてそういう風に修正する</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-circle-user"></i>
                                <small class="ms-2">担当者01</small>
                            </div>
                            <div class="mb-1">
                                <i class="fa-solid fa-calendar"></i>
                                <small class="ms-2">2023/06/21</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
