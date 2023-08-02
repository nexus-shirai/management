<script setup>
import { ref, onMounted } from 'vue';
import dayjs from 'dayjs';
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import GanttChart from '../Components/GanttChart.vue';

const props = defineProps({
  common: Object,
  project: Object,
  issues: Object,
  statuses: Object,
});

const range = ref(1);
const status = ref(0);
const grouping = ref(0);
const start_date = ref(dayjs().format("YYYY-MM-DD"));
const groups = ref([]);
const issues = ref(props.issues);

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

const onChangeGrouping = () => {
    groups.value = [];
    issues.value.map(issue => {
        if (grouping.value == 1) { // 担当者
            if (typeof groups.value[issue.assignee_id] === 'undefined') {
                groups.value[issue.assignee_id] = issue.assignee;
            }
        } else if (grouping.value == 2) { // マイルストーン
            if (typeof groups.value[issue.milestone_id] === 'undefined') {
                groups.value[issue.milestone_id] = issue.milestone;
            }
        } else if (grouping.value == 3) { // 親課題
            if (typeof groups.value[issue.parent_issue_id] === 'undefined') {
                groups.value[issue.parent_issue_id] = issue.parent_issue;
            }
        }
    });
    
    if (grouping.value) { // 親課題
        groups.value.unshift({}); // 親課題なし
    }

    groups.value = groups.value.filter(Boolean);
}

let eventSource = null;
const sync = () => {
    if (typeof(EventSource) !== "undefined") {
        if (eventSource != null && eventSource.readyState != EventSource.CLOSED) return;
        
        eventSource = new EventSource(route('refresh-grantt-chart', { project_id: props.project.project_id }));

        eventSource.onmessage = (event) => {
            let data = JSON.parse(event.data);
            
            if (data.refresh) {
                if (eventSource != null) {
                    eventSource.close();
                }

                axios
                    .get(route('fetch-chart-data', { 'project_id': props.project.project_id }))
                    .then(response => {
                        issues.value = response.data;
                        onChangeGrouping();
                        setTimeout(sync, 1000);
                    });
            }
        };

        eventSource.onerror = (event) => {
            eventSource.close();
            console.log(event);
            console.log(eventSource);
            setTimeout(sync, 5000);
        }
    } else {
        Swal.fire({
            title: '警告',
            html: "申し訳ありませんが、お使いのブラウザは「Server Sent Events(SSE)」をサポートしていません。",
            icon: 'warning',
            confirmButtonColor: '#3B82F6',
            confirmButtonText: 'OK',
        });
    }
};

onMounted(() => {
    setTimeout(sync, 1000);
});
</script>

<template>
    <Head title="Backlog - Gantt Chart" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div>
            <Link :href="route('view-project', { 'project_id': props.project.project_id })">
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</button>
            </Link>
        </div>
        
        <div class="bg-slate-100 pt-2 pb-4 px-3 mt-2">
            <!-- breadcrumbs -->
            <div class="font-bold mt-2 mb-4">
                <Link :href="route('projects')" class="font-bold text-blue-700 hover:underline">
                    プロジェクト一覧
                </Link>
                <span class="ms-4"><i class="fa-solid fa-angle-right"></i></span>
                <Link :href="route('view-project', { project_id: props.project.project_id })"
                    class="font-bold text-blue-700 hover:underline ms-4">
                    {{ props.project.project_name }} ({{ props.project.project_cd  }})
                </Link>
                <span class="ms-4"><i class="fa-solid fa-angle-right"></i></span>
                <span class="ms-4">ガントチャート</span>
            </div>

            <div class="bg-slate-200 p-3 mb-4">
                <div class="flex">
                    <div>
                        <div class="mb-3">
                            <label class="font-bold min-w-[120px] inline-block">表示開始日：</label>
                            <input v-model="start_date" class="rounded py-1 min-w-[170px]" type="date" />
                        </div>
                        <div>
                            <label class="font-bold min-w-[120px] inline-block">グルーピング：</label>
                            <select v-model="grouping" class="rounded py-1 min-w-[170px]" @change="onChangeGrouping()">
                                <option :value="0">なし</option>
                                <option :value="1">担当者</option>
                                <option :value="2">マイルストーン</option>
                                <option :value="3">親課題</option>
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
                            <select v-model="status" class="rounded py-1 min-w-[170px]">
                                <option :value="0">すべて</option>
                                <template v-for="status in props.statuses">
                                    <option :value="status.status_id">{{ status.status_name }}</option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between mb-1">
                <div>
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
                <div class="pb-1">
                    <template v-for="status in props.statuses">
                        <small class="px-3 py-1 text-white" :style="{ backgroundColor: status.hex_color }">{{ status.status_name }}</small>
                    </template>
                </div>
            </div>

            <template v-if="grouping == 0">
                <GanttChart
                    :grouping="grouping"
                    :status="status"
                    :range="range"
                    :start_date="start_date
                        ? dayjs(start_date).format('YYYY-MM-DD')
                        : dayjs().format('YYYY-MM-DD')"
                    :project="props.project"
                    :issues="issues" />
            </template>

            <template v-else>
                <template v-for="group in groups">
                    <GanttChart
                        :grouping="grouping"
                        :grouping_value="group"
                        :status="status"
                        :range="range"
                        :start_date="start_date
                            ? dayjs(start_date).format('YYYY-MM-DD')
                            : dayjs().format('YYYY-MM-DD')"
                        :project="props.project"
                        :issues="issues" />
                </template>
            </template>
        </div>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
