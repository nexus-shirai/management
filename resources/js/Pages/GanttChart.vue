<script setup>
import { ref } from 'vue';
import dayjs from 'dayjs';
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import GanttChart from '../Components/GanttChart.vue';

const range = ref(1);
const start_date = ref(dayjs().format("YYYY-MM-DD"));

const oneWeekBackward = () => {
    if (start_date.value) {
        start_date.value = dayjs(start_date.value)
            .subtract(7, "day")
            .format("YYYY-MM-DD");
    }
};
const oneWeekForward = () => {
    if (start_date.value) {
        start_date.value = dayjs(start_date.value)
            .add(7, "day")
            .format("YYYY-MM-DD");
    }
};
</script>

<template>
    <Head title="Backlog - Gantt Chart" />
    <AppHeader />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div class="bg-slate-100 pt-2 pb-4 px-3 mt-2">
            <div class="font-bold mb-3">ガントチャート</div>

            <div class="bg-slate-200 p-3 mb-4">
                <div class="flex">
                    <div>
                        <div class="mb-3">
                            <label class="font-bold min-w-[120px] inline-block">表示開始日：</label>
                            <input v-model="start_date" class="rounded py-1 min-w-[170px]" type="date" />
                        </div>
                        <div>
                            <label class="font-bold min-w-[120px] inline-block">グルーピング：</label>
                            <select class="rounded py-1 min-w-[170px]">
                                <option>なし</option>
                                <option>担当者</option>
                                <option>マイルストーン</option>
                                <option>カテゴリー</option>
                                <option>親課題</option>
                            </select>
                        </div>
                    </div>
                    <div class="ms-5"></div>
                    <div class="ms-5"></div>
                    <div class="ms-5">
                        <div class="mb-3">
                            <label class="font-bold min-w-[120px] inline-block">表示範囲：</label>
                            <select v-model="range" class="rounded py-1 min-w-[170px]">
                                <option :value="1">1ヶ月</option>
                                <option :value="2">2ヶ月</option>
                                <option :value="3">3ヶ月</option>
                                <option :value="6">6ヶ月</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold min-w-[120px] inline-block">状態：</label>
                            <select class="rounded py-1 min-w-[170px]">
                                <option>すべて</option>
                                <option>未対応</option>
                                <option>要望</option>
                                <option>処理中</option>
                                <option>処理済み</option>
                                <option>確認待ち</option>
                                <option>完了</option>
                                <option>完了以外</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-1">
                <span role="button" @click="oneWeekBackward" class="text-blue-700 hover:underline">
                    <i class="fa-solid fa-chevron-left"></i>
                    <span class="ms-2">1週間前へ</span>
                </span>
                <span class="ms-4">|</span>
                <span class="ms-4">{{ start_date ? dayjs(start_date).format("YYYY-MM-DD") : dayjs().format("YYYY-MM-DD") }}</span>
                <span class="ms-4">|</span>
                <span role="button"  @click="oneWeekForward" class="text-blue-700 hover:underline">
                    <span class="ms-4">1週間後へ</span>
                    <i class="fa-solid fa-chevron-right ms-2"></i>
                </span>
            </div>

            <GanttChart :range="range" :start_date="start_date ? dayjs(start_date).format('YYYY-MM-DD') : dayjs().format('YYYY-MM-DD')" :issues="[]" />
        </div>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
