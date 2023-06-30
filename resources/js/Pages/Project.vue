<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';

const props = defineProps({
  common: Object
});
</script>

<template>
    <Head title="Backlog - Project" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div class="mb-3">
            <Link :href="route('dashboard')" as="button" type="button" class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</Link>
        </div>

        <div class="flex bg-slate-100 mx-0 py-3 px-3">
            <div class="flex-1 px-2">
                <div class="font-bold mb-2">状態</div>
                <div class="bg-slate-200 px-3 py-3">
                    <div class="flex">
                        <div class="inline-block bg-rose-300" style="width: 25%; height: 30px;"></div>
                        <div class="inline-block bg-blue-200" style="width: 45%; height: 30px;"></div>
                        <div class="inline-block bg-green-300" style="width: 30%; height: 30px;"></div>
                    </div>
                    <div class="mt-3">
                        <div class="inline-block text-center">
                            <div><small>Todo</small></div>
                            <div>
                                <span class="bg-rose-300 rounded-full inline-block min-w-[70px]"><small>25</small></span>
                            </div>
                        </div>
                        <div class="inline-block text-center ms-2">
                            <div><small>完了</small></div>
                            <div>
                                <span class="bg-blue-200 rounded-full inline-block min-w-[70px]"><small>45</small></span>
                            </div>
                        </div>
                        <div class="inline-block text-center ms-2">
                            <div><small>未着手</small></div>
                            <div>
                                <span class="bg-green-300 rounded-full inline-block min-w-[70px]"><small>30</small></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 px-2">
                <div class="font-bold mb-2">マイルストーン</div>
                <div class="bg-slate-200 px-3 py-3">
                    <div class="mb-2">
                        <div class="font-bold">初期リリース</div>
                        <div class="flex">
                            <div style="width: 80%;">
                                <div class="flex">
                                    <div class="inline-block bg-rose-300" style="width: 15%; height: 30px;"></div>
                                    <div class="inline-block bg-green-300" style="width: 85%; height: 30px;"></div>
                                </div>
                            </div>
                            <div style="width: 20%;">
                                <div class="text-end">80%完了</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="font-bold">2次リリース</div>
                        <div class="flex">
                            <div style="width: 80%;">
                                <div class="flex">
                                    <div class="inline-block bg-rose-300" style="width: 50%; height: 30px;"></div>
                                    <div class="inline-block bg-green-300" style="width: 50%; height: 30px;"></div>
                                </div>
                            </div>
                            <div style="width: 20%;">
                                <div class="text-end">50%完了</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="font-bold">追加機能対応</div>
                        <div class="flex">
                            <div style="width: 80%;">
                                <div class="flex">
                                    <div class="inline-block bg-green-300" style="width: 30%; height: 30px;"></div>
                                    <div class="inline-block bg-blue-200" style="width: 70%; height: 30px;"></div>
                                </div>
                            </div>
                            <div style="width: 20%;">
                                <div class="text-end">30%完了</div>
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
