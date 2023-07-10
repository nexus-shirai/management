<script setup>
import { ref, watch } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
  range: Number, // months
  start_date: String,
  issues: Array,
});

const cal_start_date = ref(dayjs(props.start_date));
const cal_end_date = ref(cal_start_date.value.add(props.range, "month"));

const dates = ref([]);

const getDate = () => {
    dates.value = [];
    for (let d = cal_start_date.value; cal_end_date.value.diff(d, "day") >= 0; d = d.add(1, "day")) {
        if(typeof dates.value[d.month()] === 'undefined') {
            dates.value[d.month()] = [];
        }
        dates.value[d.month()].push(d);
    }
    dates.value = dates.value.filter(Boolean);
};

getDate();

watch(() => props.range, (newRange) => {
    cal_end_date.value = cal_start_date.value.add(newRange, "month");
    getDate();
});

watch(() => props.start_date, (newStartDate) => {
    cal_start_date.value = dayjs(newStartDate);
    cal_end_date.value = cal_start_date.value.add(props.range, "month");
    getDate();
});

const cell_width = 40;

const data = [
    {
        "issue_id": 1,
        "start_date": dayjs("2023-06-29"),
        "end_date": dayjs("2023-07-01"),
        "title": "Issue 1",
        "pic": "Person 1"
    },
    {
        "issue_id": 2,
        "start_date": dayjs("2023-06-30"),
        "end_date": dayjs("2023-07-06"),
        "title": "Issue 2",
        "pic": "Person 2"
    },
    {
        "issue_id": 3,
        "start_date": dayjs("2023-06-29"),
        "end_date": dayjs("2023-07-10"),
        "title": "Issue 3",
        "pic": "Person 3"
    },
    {
        "issue_id": 4,
        "start_date": dayjs("2023-06-29"),
        "end_date": dayjs("2023-06-29"),
        "title": "Issue 4",
        "pic": "Person 4"
    },
    {
        "issue_id": 5,
        "start_date": dayjs("2023-06-29"),
        "end_date": dayjs("2023-06-29"),
        "title": "Issue 5",
        "pic": "Person 5"
    },
];

data.map((issue) => {
    issue["tmp_start_date"] = issue.start_date;
    issue["tmp_end_date"] = issue.end_date;
});

const issues = ref(data);

const active_issue_id = ref(null);
const change_start_date = ref(false);
const change_end_date = ref(false);
const change_position = ref(false);

const onDragStart = (_, issue_id, change_type) => {
    active_issue_id.value = issue_id;
    change_start_date.value = change_type == "CHANGE_START_DATE";
    change_end_date.value = change_type == "CHANGE_END_DATE";
    change_position.value = change_type == "CHANGE_POSITION";

    let issue_bar_actual = document.querySelector(".issue-" + active_issue_id.value + ".issue-bar-actual");
    issue_bar_actual.style.opacity = "0.5";

    if (change_start_date.value || change_position.value) {
        let issue_bar_change_end_date = document.querySelector(".issue-" + active_issue_id.value + " .issue-bar-change-end-date");
        if (issue_bar_change_end_date) {
            issue_bar_change_end_date.style.pointerEvents = "none";
        }
    }

    if (change_start_date.value || change_end_date.value) {
        let issue_bar_change_position = document.querySelector(".issue-" + active_issue_id.value + " .issue-bar-change-position");
        if (issue_bar_change_position) {
            issue_bar_change_position.style.pointerEvents = "none";
        }
    }
};

const onDrag = (_) => {};

const onDragOver = (_, new_date) => {
    updateShadowTimeline(new_date);
};

const onDrop = (_, new_date) => {
    updateTimeline(new_date);

    active_issue_id.value = null;
    change_start_date.value = false;
    change_end_date.value = false;
    change_position.value = false;
};

const updateTimeline = (new_date) => {
    issues.value.map((issue) => {
        if (issue.issue_id == active_issue_id.value) {
            if (change_start_date.value) {
                if (issue.end_date.diff(new_date, "day") < 0) {
                    issue.start_date = issue.end_date;
                    issue.tmp_start_date = issue.end_date;
                } else {
                    issue.start_date = new_date;
                }
            } else if (change_end_date.value) {
                if (new_date.diff(issue.start_date, "day") < 0) {
                    issue.end_date = issue.start_date;
                    issue.tmp_end_date = issue.start_date;
                } else {
                    issue.end_date = new_date;
                }
            } else if (change_position.value) {
                let add = new_date.diff(issue.start_date, "day");
                issue.start_date = new_date;
                issue.end_date = issue.end_date.add(add, "day");
            } else {
                // do nothing
            }
        }
    });
    
    let issue_bar_actual = document.querySelector(".issue-" + active_issue_id.value + ".issue-bar-actual");
    issue_bar_actual.style.opacity = "1";

    if (change_start_date.value || change_end_date.value) {
        let issue_bar_change_position = document.querySelector(".issue-" + active_issue_id.value + " .issue-bar-change-position");
        if (issue_bar_change_position) {
            issue_bar_change_position.style.pointerEvents = "auto";
        }
    }
};

const updateShadowTimeline = (new_date) => {
    issues.value.map((issue) => {
        if (issue.issue_id == active_issue_id.value) {
            if (change_start_date.value) {
                if (issue.end_date.diff(new_date, "day") < 0) {
                    issue.tmp_start_date = issue.end_date;
                } else {
                    issue.tmp_start_date = new_date;
                }
            } else if (change_end_date.value) {
                if (new_date.diff(issue.start_date, "day") < 0) {
                    issue.tmp_end_date = start_date;
                } else {
                    issue.tmp_end_date = new_date;
                }
            } else if (change_position.value) {
                let add = new_date.diff(issue.tmp_start_date, "day");
                issue.tmp_start_date = new_date;
                issue.tmp_end_date = issue.tmp_end_date.add(add, "day");
            } else {
                // do nothing
            }
        }
    });
};

// type 1:actual 2:shadow
const computeWidth = (issue, type) => {
    let diff = 0;
    let start_date = null;
    let end_date = null;

    if (type == 1) {
        if (issue.start_date.isBefore(cal_start_date.value, "day")) {
            start_date = cal_start_date.value;
        } else {
            start_date = issue.start_date;
        }

        if (issue.end_date.isAfter(cal_end_date.value, "day")) {
            end_date = cal_end_date.value;
        } else {
            end_date = issue.end_date;
        }

        diff = end_date.diff(start_date, "day") + 1;
    } else if (type == 2) {
        if (issue.tmp_start_date.isBefore(cal_start_date.value, "day")) {
            start_date = cal_start_date.value
        } else {
            start_date = issue.tmp_start_date;
        }

        if (issue.tmp_end_date.isAfter(cal_end_date.value, "day")) {
            end_date = cal_end_date.value;
        } else {
            end_date = issue.tmp_end_date;
        }

        diff = end_date.diff(start_date, "day") + 1;
    } else {
        // do nothing
    }

    return cell_width * diff - 3;
};
</script>

<template>
    <div>
        <div class="flex">
            <div>
                <table class="min-w-[200px]">
                    <tr>
                        <th class="border border-solid border-black h-[27px]"></th>
                    </tr>
                    <tr>
                        <th class="border border-solid border-black px-2 sticky left-0">
                            <div class="flex justify-between">
                                <div>件名</div>
                                <div>担当者</div>
                            </div>
                        </th>
                    </tr>
                    <template v-for="issue in issues">
                        <tr class="bg-white">
                            <td class="border border-solid border-black px-2 sticky left-0">
                                <div class="flex justify-between min-w-[250px]">
                                    <div>
                                        <Link :href="route('view-issue', ['issue_id', 1])" class="font-bold text-blue-700 hover:underline">
                                            {{ issue.title }}
                                        </Link>
                                    </div>
                                    <div>{{ issue.pic }}</div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </table>
            </div>

            <div class="flex-1 overflow-x-scroll">
                <table id="timeline-table" class="w-full">
                    <tr class="h-[27px]">
                        <template v-for="date_arr in dates">
                            <th class="border border-solid border-black whitespace-nowrap relative" :colspan="date_arr.length">
                                <div class="absolute top-0 w-full">
                                    {{ date_arr[0].format("YYYY-MM") }}
                                </div>
                            </th>
                        </template>
                    </tr>

                    <tr>
                        <template v-for="date_arr in dates">
                            <template v-for="date in date_arr">
                                <th class="border border-solid border-black text-center" :style="{ minWidth: cell_width + 'px' }">
                                    {{ date.format("DD") }}
                                </th>
                            </template>
                        </template>
                    </tr>

                    <template v-for="issue in issues">
                        <tr class="bg-white">
                            <template v-for="date_arr in dates">
                                <template v-for="date in date_arr">
                                    <td class="table-cell border border-solid border-black relative h-[27px] select-none"
                                        @dragover="onDragOver($event, date)" @drop="onDrop($event, date)"
                                        @dragenter.prevent @dragover.prevent>

                                        <template v-if="dayjs(date).isSame(issue.tmp_start_date, 'day')
                                            || (issue.tmp_start_date.isBefore(cal_start_date, 'day') && dayjs(date).isSame(cal_start_date, 'day'))
                                            && issue.tmp_end_date.diff(date) >= 0">
                                            <div :class="'issue-' + issue.issue_id" :style="{ width: computeWidth(issue, 2) + 'px' }"
                                                class="issue-bar issue-bar-shadow absolute bg-blue-400 px-3 whitespace-nowrap">
                                                <span class="text-pink-400 text-opacity-0">{{ issue.title }}</span>
                                                <div class="relative">
                                                    <div class="absolute" :style="{ left: (computeWidth(issue, 2) + 5) + 'px', top: '-' + (27 - 3) + 'px' }">
                                                        <template v-if="issue.tmp_end_date.diff(cal_end_date) < 0">
                                                            <span class="rounded-full px-3 bg-slate-200 ">{{ issue.title }}</span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <template v-if="dayjs(date).isSame(issue.start_date, 'day')
                                            || (issue.start_date.isBefore(cal_start_date, 'day') && dayjs(date).isSame(cal_start_date, 'day'))
                                            && issue.end_date.diff(date) >= 0">

                                            <div :class="'issue-' + issue.issue_id" :style="{ width: computeWidth(issue, 1) + 'px' }"
                                                class="issue-bar issue-bar-actual absolute bg-pink-400 px-3 whitespace-nowrap text-pink-400 text-opacity-0">
                                                {{ issue.title }}
                                            </div>

                                            <div :class="'issue-' + issue.issue_id" :style="{ width: computeWidth(issue, 1) + 'px' }"
                                                class="issue-bar issue-bar-resizer absolute bg-pink-400" style="z-index: 1;">
                                                <div class="flex justify-between h-[0px]">
                                                    <template v-if="issue.start_date.isAfter(cal_start_date, 'day')
                                                        || dayjs(issue.start_date).isSame(cal_start_date, 'day')">
                                                        <div class="text-pink-500 hover:text-pink-700" draggable="true"
                                                            @drag="onDrag($event)" @dragstart="onDragStart($event, issue.issue_id, 'CHANGE_START_DATE')">
                                                            <i class="fa-solid fa-grip-lines-vertical"></i>
                                                        </div>
                                                    </template>

                                                    <div class="issue-bar-change-position flex-1 px-1 text-pink-500 hover:text-pink-700"
                                                        draggable="true" @dragstart="onDragStart($event, issue.issue_id, 'CHANGE_POSITION')">
                                                        <i class="fa-solid fa-left-right"></i>
                                                    </div>

                                                    <template v-if="issue.end_date.diff(cal_end_date) <= 0">
                                                        <div class="issue-bar-change-end-date text-pink-500 hover:text-pink-700" draggable="true"
                                                            @dragstart="onDragStart($event, issue.issue_id, 'CHANGE_END_DATE')" @drag="onDrag($event)">
                                                            <i class="fa-solid fa-grip-lines-vertical"></i>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                </template>
                            </template>
                        </tr>
                    </template>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>
#timeline-table tr th:first-child,
#timeline-table tr td:first-child {
    border-left: none;
}
div.issue-bar {
  top: 1px;
  left: 1px;
}
</style>
