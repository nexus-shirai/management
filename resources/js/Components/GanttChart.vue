<script setup>
import { ref, watch } from 'vue';
import dayjs from 'dayjs';
import Swal from 'sweetalert2';
import axios from 'axios';

const props = defineProps({
    grouping: Number,
    grouping_value: Object,
    status: Number,
    range: Number, // months
    start_date: String,
    project: Object,
    issues: Object,
});

const cal_start_date = ref(dayjs(props.start_date));
const cal_end_date = ref(cal_start_date.value.add(props.range, "month"));

const synching = ref(false);
const update_data = ref([]);
const status = ref(props.status);
const grouping = ref(props.grouping);
const grouping_value = ref(props.grouping_value);
const dates = ref([]);

const getDate = () => {
    dates.value = [];
    for (let d = cal_start_date.value; cal_end_date.value.diff(d, "day") >= 0; d = d.add(1, "day")) {
        if(typeof dates.value[d.format("YYYY") + d.format("MM")] === 'undefined') {
            dates.value[d.format("YYYY") + d.format("MM")] = [];
        }
        dates.value[d.format("YYYY") + d.format("MM")].push(d);
    }
    dates.value = dates.value.filter(Boolean);
};

getDate();

watch(() => props.range, (newRange) => {
    cal_end_date.value = cal_start_date.value.add(newRange, "month");
    getDate();
});

watch(() => props.grouping_value, (newGroupingValue) => {
    grouping_value.value = newGroupingValue;
});

watch(() => props.start_date, (newStartDate) => {
    cal_start_date.value = dayjs(newStartDate);
    cal_end_date.value = cal_start_date.value.add(props.range, "month");
    getDate();
});

const cell_width = 40;

let data = [];
const issues = ref(data);

const getData = (issue_arr) => {
    data = issue_arr.map(issue => {
        issue = Object.assign({}, issue);
        issue["start_date"] = dayjs(issue["start_date"]).startOf("day");
        issue["end_date"] = dayjs(issue["end_date"]).startOf("day");
        issue["tmp_start_date"] = dayjs(issue["start_date"]).startOf("day");
        issue["tmp_end_date"] = dayjs(issue["end_date"]).startOf("day");
        return issue;
    })

    issues.value = data;
};

getData(props.issues);

watch(() => props.issues, (newIssues) => {
    Swal.close();
    synching.value = true;
    getData(newIssues);
    setTimeout(() => synching.value = false, 500);
});

watch(() => props.status, (newStatus) => {
    status.value = newStatus;
});

watch(() => props.grouping, (newGrouping) => {
    grouping.value = newGrouping;
});

const active_issue_id = ref(null);
const change_start_date = ref(false);
const change_end_date = ref(false);
const change_position = ref(false);
const prev_date = ref(null);

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

const onDragOver = (event, new_date) => {
    if (change_end_date.value && event.target.tagName.toLowerCase() == 'i') return;

    if (prev_date.value == null) {
        prev_date.value = new_date;
    }

    updateShadowTimeline(new_date);

    if (prev_date.value != new_date) {
        prev_date.value = new_date;
    }
};

const onDrop = (_, new_date) => {
    update_data.value = [];
    updateTimeline(new_date);

    active_issue_id.value = null;
    change_start_date.value = false;
    change_end_date.value = false;
    change_position.value = false;
    prev_date.value = null;
};

const saveChanges = () => {
    update_data.value = update_data.value.filter(Boolean);
    update_data.value = update_data.value.map(issue => {
        return {
            'issue_id': issue.issue_id,
            'start_date': issue.start_date.format("YYYY-MM-DD"),
            'end_date': issue.end_date.format("YYYY-MM-DD"),
        }
    });

    axios.put(
        route('update-gantt-chart', { 'project_id': props.project.project_id }),
        { issues: update_data.value }
    ).then(response => {
        if (response.status == 200) {
            // do nothing
        } else {
            console.log(response);
        }
    });
};

const updateIssueAddDays = (issue_id, add) => {
    issues.value.map(issue => {
        if (issue.issue_id == issue_id) {
            issue.start_date = issue.start_date.add(add, "day");
            issue.end_date = issue.end_date.add(add, "day");
            issue.tmp_start_date = issue.tmp_start_date.add(add, "day");
            issue.tmp_end_date = issue.tmp_end_date.add(add, "day");
            update_data.value[issue.issue_id] = issue;
        }
    });
};

const updateIssueChangeStartDate = (issue_id, start_date) => {
    issues.value.map(issue => {
        if (issue.issue_id == issue_id) {
            issue.start_date = start_date;
            issue.tmp_start_date = start_date;
            update_data.value[issue.issue_id] = issue;
        }
    });
};

const updateIssueChangeEndDate = (issue_id, end_date) => {
    issues.value.map(issue => {
        if (issue.issue_id == issue_id) {
            issue.end_date = end_date;
            issue.tmp_end_date = end_date;
            update_data.value[issue.issue_id] = issue;
        }
    });
};

const getIssueById = (issue_id) => {
    return issues.value.find(issue => issue.issue_id == issue_id);
}

const confirmMoveIssue = async (issue_rank) => {
    let message = "";
    switch (issue_rank) {
        case "parent":
            message = `親課題の期間が変更されました。<br/>
                子課題/孫課題の期間と整合性が合わない場合、<br/>
                親課題に合わせて自動調整されます。`;
            break;
        case "child":
            message = `子課題の期間が変更されました。<br/>
                親課題/孫課題の期間と整合性が合わない場合、<br/>
                子課題に合わせて自動調整されます。`;
            break;
        case "grandchild":
            message = `孫課題の期間が変更されました。<br/>
                親課題/子課題の期間と整合性が合わない場合、<br/>
                子課題に合わせて自動調整されます。`;
            break;
        default:
            break;
    }

    return Swal.fire({
        title: '実行しますか？',
        html: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#94A3B8',
        confirmButtonText: 'はい',
        cancelButtonText: 'キャンセル'
    }).then((result) => {
        return result.isConfirmed;
    });
}

const updateTimeline = (_) => {
    issues.value.map(async (issue) => {
        if (issue.issue_id == active_issue_id.value) {
            if (issue.start_date.isSame(issue.tmp_start_date, "day") && issue.end_date.isSame(issue.tmp_end_date)) return;
            
            let confirm = null;

            // change_position
            if (change_position.value) {

                if (issue.child_issues.length) {
                    confirm = await confirmMoveIssue(issue.issue_rank);
                    if (confirm) {
                        let add = issue.tmp_start_date.diff(issue.start_date, "day");
                        issue.child_issues.forEach(child_issue => {
                            updateIssueAddDays(child_issue.issue_id, add);
                            if (child_issue.child_issues.length) {
                                child_issue.child_issues.forEach(grandchild_issue => {
                                    updateIssueAddDays(grandchild_issue.issue_id, add);
                                });
                            }
                        });

                    } else {
                        issue.tmp_start_date = issue.start_date;
                        issue.tmp_end_date = issue.end_date;
                    }
                }

                if (issue.issue_rank == 'child') {
                    let parent_issue = getIssueById(issue.parent_issue_id);
                    if (parent_issue.start_date.isAfter(issue.tmp_start_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeStartDate(issue.parent_issue_id, issue.tmp_start_date);
                        }
                    }
                    if (parent_issue.end_date.isBefore(issue.tmp_end_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeEndDate(issue.parent_issue_id, issue.tmp_end_date);
                        }
                    }
                }

                if (issue.issue_rank == 'grandchild') {
                    let parent_issue = getIssueById(issue.parent_issue_id);
                    if (parent_issue.start_date.isAfter(issue.tmp_start_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeStartDate(issue.parent_issue_id, issue.tmp_start_date);
                        }
                    }
                    if (parent_issue.end_date.isBefore(issue.tmp_end_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeEndDate(issue.parent_issue_id, issue.tmp_end_date);
                        }
                    }

                    let grantparent_issue = getIssueById(parent_issue.parent_issue_id);
                    if (grantparent_issue.start_date.isAfter(issue.tmp_start_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeStartDate(parent_issue.parent_issue_id, issue.tmp_start_date);
                        }
                    }
                    if (grantparent_issue.end_date.isBefore(issue.tmp_end_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeEndDate(parent_issue.parent_issue_id, issue.tmp_end_date);
                        }
                    }
                }

                if (confirm == null || confirm == true) {
                    updateIssueChangeStartDate(issue.issue_id, issue.tmp_start_date);
                    updateIssueChangeEndDate(issue.issue_id, issue.tmp_end_date);
                }

                if (confirm == false) {
                    issue.tmp_start_date = issue.start_date;
                    issue.tmp_end_date = issue.end_date;
                }
            
            }

            // change_start_date
            else if (change_start_date.value) {
                if (issue.child_issues.length) {
                    for (let child_issue of issue.child_issues) {
                        child_issue = getIssueById(child_issue.issue_id);
                        
                        if (issue.tmp_start_date.isAfter(child_issue.start_date, "day")) {
                            let add = issue.tmp_start_date.diff(child_issue.start_date, "day");
                            if (confirm == null) {
                                confirm = await confirmMoveIssue(issue.issue_rank);
                            }
                    
                            if (confirm) {
                                updateIssueAddDays(child_issue.issue_id, add);
                                if (child_issue.end_date.isAfter(issue.tmp_end_date, "day")) {
                                    updateIssueChangeEndDate(child_issue.issue_id, issue.tmp_end_date);
                                }

                                for (let grandchild_issue of child_issue.child_issues) {
                                    grandchild_issue = getIssueById(grandchild_issue.issue_id);
                                    if (child_issue.tmp_start_date.isAfter(grandchild_issue.start_date, "day")) {
                                        add = child_issue.tmp_start_date.diff(grandchild_issue.start_date, "day");
                                        updateIssueAddDays(grandchild_issue.issue_id, add);
                                        if (grandchild_issue.end_date.isAfter(child_issue.tmp_end_date)) {
                                            updateIssueChangeEndDate(grandchild_issue.issue_id, child_issue.tmp_end_date);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (issue.issue_rank == 'child') {
                    let parent_issue = getIssueById(issue.parent_issue_id);
                    if (parent_issue.start_date.isAfter(issue.tmp_start_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeStartDate(issue.parent_issue_id, issue.tmp_start_date);
                        }
                    }
                }

                if (issue.issue_rank == 'grandchild') {
                    let parent_issue = getIssueById(issue.parent_issue_id);
                    if (parent_issue.start_date.isAfter(issue.tmp_start_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeStartDate(issue.parent_issue_id, issue.tmp_start_date);
                        }
                    }

                    let grantparent_issue = getIssueById(parent_issue.parent_issue_id);
                    if (grantparent_issue.start_date.isAfter(issue.tmp_start_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeStartDate(parent_issue.parent_issue_id, issue.tmp_start_date);
                        }
                    }
                }

                if (confirm == null || confirm == true) {
                    updateIssueChangeStartDate(issue.issue_id, issue.tmp_start_date);
                    updateIssueChangeEndDate(issue.issue_id, issue.tmp_end_date);
                }

                if (confirm == false) {
                    issue.tmp_start_date = issue.start_date;
                    issue.tmp_end_date = issue.end_date;
                }
            
            }
            
            // change_end_date
            else if (change_end_date.value) {
                if (issue.child_issues.length) {
                    for (let child_issue of issue.child_issues) {
                        child_issue = getIssueById(child_issue.issue_id);
                        
                        if (issue.tmp_end_date.isBefore(child_issue.end_date, "day")) {
                            let add = issue.tmp_end_date.diff(child_issue.end_date, "day");
                            if (confirm == null) {
                                confirm = await confirmMoveIssue(issue.issue_rank);
                            }
                    
                            if (confirm) {
                                updateIssueAddDays(child_issue.issue_id, add);
                                if (child_issue.start_date.isBefore(issue.tmp_start_date, "day")) {
                                    updateIssueChangeStartDate(child_issue.issue_id, issue.tmp_start_date);
                                }

                                for (let grandchild_issue of child_issue.child_issues) {
                                    grandchild_issue = getIssueById(grandchild_issue.issue_id);
                                    if (child_issue.tmp_end_date.isBefore(grandchild_issue.end_date, "day")) {
                                        add = child_issue.tmp_end_date.diff(grandchild_issue.end_date, "day");
                                        updateIssueAddDays(grandchild_issue.issue_id, add);
                                        if (grandchild_issue.start_date.isBefore(child_issue.tmp_start_date)) {
                                            updateIssueChangeStartDate(grandchild_issue.issue_id, child_issue.tmp_start_date);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (issue.issue_rank == 'child') {
                    let parent_issue = getIssueById(issue.parent_issue_id);
                    if (parent_issue.end_date.isBefore(issue.tmp_end_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeEndDate(issue.parent_issue_id, issue.tmp_end_date);
                        }
                    }
                }

                if (issue.issue_rank == 'grandchild') {
                    let parent_issue = getIssueById(issue.parent_issue_id);
                    if (parent_issue.end_date.isBefore(issue.tmp_end_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeEndDate(issue.parent_issue_id, issue.tmp_end_date);
                        }
                    }

                    let grantparent_issue = getIssueById(parent_issue.parent_issue_id);
                    if (grantparent_issue.end_date.isBefore(issue.tmp_end_date, "day")) {
                        if (confirm == null) {
                            confirm = await confirmMoveIssue(issue.issue_rank);
                        }
                        if (confirm) {
                            updateIssueChangeEndDate(parent_issue.parent_issue_id, issue.tmp_end_date);
                        }
                    }
                }

                if (confirm == null || confirm == true) {
                    updateIssueChangeStartDate(issue.issue_id, issue.tmp_start_date);
                    updateIssueChangeEndDate(issue.issue_id, issue.tmp_end_date);
                }

                if (confirm == false) {
                    issue.tmp_start_date = issue.start_date;
                    issue.tmp_end_date = issue.end_date;
                }

            }
            
            else {
                // do nothing
            }    

            if (confirm == false) return;

            saveChanges();
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

    if (change_start_date.value || change_position.value) {
        let issue_bar_change_end_date = document.querySelector(".issue-" + active_issue_id.value + " .issue-bar-change-end-date");
        if (issue_bar_change_end_date) {
            issue_bar_change_end_date.style.pointerEvents = "auto";
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
                let add = new_date.diff(prev_date.value, "day");
                issue.tmp_start_date = issue.tmp_start_date.add(add, "day");
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
    <template v-if="grouping != 0">
        <div class="text-xl bg-white border border-black px-2 py-2 mt-5 mb-1">
            <!-- 担当者 -->
            <template v-if="grouping == 1">
                <i class="fa-solid fa-circle-user"></i>
                <template v-if="Object.keys(grouping_value).length">
                    <span class="ms-2">{{ grouping_value.username }}</span>
                </template>
                <template v-else>
                    <span class="ms-2">設定なし</span>
                </template>
            </template>
            <!-- マイルストーン -->
            <template v-else-if="grouping == 2">
                <i class="fa-solid fa-flag"></i>
                <template v-if="Object.keys(grouping_value).length">
                    <span class="ms-2">{{ grouping_value.milestone_name }}</span>
                </template>
                <template v-else>
                    <span class="ms-2">設定なし</span>
                </template>
            </template>
            <!-- 親課題 -->
            <template v-else-if="grouping == 3">
                <i class="fa-sharp fa-solid fa-file"></i>
                <template v-if="Object.keys(grouping_value).length">
                    <span class="ms-2">{{ grouping_value.issue_title }} ({{ grouping_value.issue_cd }})</span>
                </template>
                <template v-else>
                    <span class="ms-2">設定なし</span>
                </template>
            </template>
        </div>
    </template>

    <div class="relative">
        <!-- synching overlay -->
        <div v-if="synching"
            class="absolute w-full h-full top-0 right-0 bottom-0 left-0 bg-slate-500 opacity-70 z-10">
            <div class="text-white font-bold text-3xl absolute select-none"
                :style="{ top: '50%', left: '50%', transform: 'translate(-50%, -50%)' }">
                同期中
            </div>
        </div>

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
                        <template v-if="(status == 0 || issue.status_id == status)
                            && (grouping == 0
                            || (grouping == 1 && grouping_value == null && issue.assignee_id == null)
                            || (grouping == 1 && grouping_value != null && grouping_value.user_id == issue.assignee_id)
                            || (grouping == 2 && grouping_value == null && issue.milestone_id == null)
                            || (grouping == 2 && grouping_value != null && grouping_value.milestone_id == issue.milestone_id)
                            || (grouping == 3 && grouping_value == null && issue.parent_issue_id == null)
                            || (grouping == 3 && grouping_value != null && grouping_value.issue_id == issue.parent_issue_id))">

                            <tr class="bg-white">
                                <td class="border border-solid border-black px-2 sticky left-0">
                                    <div class="flex justify-between min-w-[250px]">
                                        <div>
                                            <Link :href="route('view-issue', {'project_id': issue.project_id, 'issue_id': issue.issue_id})"
                                                class="font-bold text-blue-700 hover:underline">
                                                {{ issue.issue_title }} ({{ issue.issue_cd }})
                                            </Link>
                                        </div>
                                        <div>{{ issue.assignee ? issue.assignee.username : '設定なし' }}</div>
                                    </div>
                                </td>
                            </tr>

                        </template>
                    </template>
                </table>
            </div>

            <div class="flex-1 overflow-x-scroll">
                <table class="timeline-table w-full">
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
                        
                        <template v-if="(status == 0 || issue.status_id == status)
                            && (grouping == 0
                            || (grouping == 1 && grouping_value == null && issue.assignee_id == null)
                            || (grouping == 1 && grouping_value != null && grouping_value.user_id == issue.assignee_id)
                            || (grouping == 2 && grouping_value == null && issue.milestone_id == null)
                            || (grouping == 2 && grouping_value != null && grouping_value.milestone_id == issue.milestone_id)
                            || (grouping == 3 && grouping_value == null && issue.parent_issue_id == null)
                            || (grouping == 3 && grouping_value != null && grouping_value.issue_id == issue.parent_issue_id))">
                            
                            <tr class="bg-white">
                                <template v-for="date_arr in dates">
                                    <template v-for="date in date_arr">
                                        <td class="table-cell border border-solid border-black relative h-[27px] select-none"
                                            @dragover="onDragOver($event, date)" @drop="onDrop($event, date)"
                                            @dragenter.prevent @dragover.prevent>

                                            <!-- issue-bar-shadow -->
                                            <template v-if="dayjs(date).isSame(issue.tmp_start_date, 'day')
                                                || (issue.tmp_start_date.isBefore(cal_start_date, 'day') && dayjs(date).isSame(cal_start_date, 'day'))
                                                && issue.tmp_end_date.diff(date) >= 0">
                                                <div :class="'issue-' + issue.issue_id" :style="{ width: computeWidth(issue, 2) + 'px' }"
                                                    class="issue-bar issue-bar-shadow absolute bg-blue-400 px-3 whitespace-nowrap">
                                                    <span class="text-pink-400 text-opacity-0">{{ issue.issue_title }}</span>
                                                </div>
                                            </template>

                                            <!-- issue-bar-actual -->
                                            <template v-if="dayjs(date).isSame(issue.start_date, 'day')
                                                || (issue.start_date.isBefore(cal_start_date, 'day') && dayjs(date).isSame(cal_start_date, 'day'))
                                                && issue.end_date.diff(date) >= 0">

                                                <div :class="'issue-' + issue.issue_id"
                                                    :style="{ width: computeWidth(issue, 1) + 'px', backgroundColor: issue.status.hex_color }"
                                                    class="issue-bar issue-bar-actual absolute px-3 whitespace-nowrap text-pink-400 text-opacity-0">
                                                    {{ issue.issue_title }}
                                                </div>

                                                <div :class="'issue-' + issue.issue_id"
                                                    :style="{
                                                        width: computeWidth(issue, 1) + 'px',
                                                        zIndex: 1,
                                                        backgroundColor: issue.status.hex_color,
                                                        paddingLeft: '1px',
                                                        paddingRight: '1px'
                                                    }"
                                                    class="issue-bar issue-bar-resizer absolute">
                                                    <div class="flex justify-between h-[0px]">
                                                        <!-- issue-bar-change-start-date -->
                                                        <template v-if="issue.start_date.isAfter(cal_start_date, 'day')
                                                            || dayjs(issue.start_date).isSame(cal_start_date, 'day')">
                                                            <div class="text-slate-200 hover:text-slate-500" draggable="true"
                                                                @drag="onDrag($event)" @dragstart="onDragStart($event, issue.issue_id, 'CHANGE_START_DATE')">
                                                                <i class="fa-solid fa-grip-lines-vertical"></i>
                                                            </div>
                                                        </template>

                                                        <!-- issue-bar-change-position -->
                                                        <div class="issue-bar-change-position flex-1 px-1 text-slate-200 hover:text-slate-500"
                                                            draggable="true" @dragstart="onDragStart($event, issue.issue_id, 'CHANGE_POSITION')">
                                                            <i class="fa-solid fa-left-right"></i>
                                                        </div>

                                                        <!-- issue-bar-change-end-date -->
                                                        <template v-if="issue.end_date.diff(cal_end_date) <= 0">
                                                            <div class="issue-bar-change-end-date text-slate-200 hover:text-slate-500" draggable="true"
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
                    </template>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>
.timeline-table tr th:first-child,
.timeline-table tr td:first-child {
    border-left: none;
}
div.issue-bar {
  top: 1px;
  left: 1px;
}
</style>
