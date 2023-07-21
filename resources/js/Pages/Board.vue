<script setup>
import { ref, onMounted } from 'vue';
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';
import draggable from 'vuedraggable';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
  common: Object,
  project: Object,
  issues: Object,
  kinds: Object,
  categories: Object,
  milestones: Object,
  statuses: Object,
  project_users: Object,
});

const kind = ref("");
const category = ref(0);
const milestone = ref(0);
const project_user = ref(0);
const issues = ref(props.issues);
const issue_groups = ref([]);
const synching = ref(false);
const timestamp = ref(null);
const newIndex = ref(null);

const priority = {
    "high": 3,
    "medium": 2,
    "low": 1,
};

const loadData = () => {
    issue_groups.value = [];
    props.statuses.forEach(status => {
        issue_groups.value[status.status_id] = [];
        issues.value.forEach(issue => {
            if (issue.status_id == status.status_id) {
                issue_groups.value[status.status_id].push(issue);
            }
        });
    });

    props.statuses.forEach(status => {
        issue_groups.value[status.status_id].sort((a, b) => {
            if (priority[a.issue_priority] < priority[b.issue_priority]) return 1;
            if (priority[a.issue_priority] > priority[b.issue_priority]) return -1;
            if (a.issue_id > b.issue_id) return 1;
            if (a.issue_id < b.issue_id) return -1;
        });
    });
};

loadData();

const onChange = (event) => {
    if (event.moved) {
        update(event.moved.element, "move");
    }
    
    if (event.added) {
        newIndex.value = event.added.newIndex;
    }

    if (event.removed) {
        update(event.removed.element, "removed", newIndex.value, event.removed.oldIndex);
    }
};

const update = async (issue, action, newIndex, oldIndex) => {
    let status_id = getGroupStatusId(issue.issue_id);

    // hardcoded complete flg to 5
    if (status_id == 5 && hasIncompleteChild(issue)) {
        issue_groups.value[issue.status_id].splice(oldIndex, 0, issue);
        issue_groups.value[status_id].splice(newIndex, 1);
        return;
    }

    if (issue.status_id == 5 && status_id != 5 && issue.parent_issue_id != null) {
        let continue_flg = await parentCompleted(issue);
        if (continue_flg) {
            // hardcoded processing flg to 2
            let parent_issue = getIssueById(issue.parent_issue_id);
            parent_issue.status_id = 2;
            updateDB(issue.parent_issue_id, 2);
            if (parent_issue.parent_issue_id) {
                let grandparent_issue = getIssueById(parent_issue.parent_issue_id);
                grandparent_issue.status_id = 2;
                updateDB(parent_issue.parent_issue_id, 2);
            }
        } else {
            issue_groups.value[issue.status_id].splice(oldIndex, 0, issue);
            issue_groups.value[status_id].splice(newIndex, 1);
            return;
        }
    }

    issue.status_id = status_id;
    loadData();

    issue_groups.value[status_id].sort((a, b) => {
        if (priority[a.issue_priority] < priority[b.issue_priority]) return 1;
        if (priority[a.issue_priority] > priority[b.issue_priority]) return -1;
        if (a.issue_id > b.issue_id) return 1;
        if (a.issue_id < b.issue_id) return -1;
    });

    if (action == "move") return;

    updateDB(issue.issue_id, status_id);
};

const updateDB = (issue_id, status_id) => {
    eventSource.close();
    axios.put(
        route('update-board', { 'project_id': props.project.project_id }),
        { issue_id: issue_id, status_id: status_id }
    ).then(response => {
        if (response.status == 200) {
            setTimeout(sync, 1000);
        } else {
            console.log(response);
        }
    });
};

const getIssueById = (issue_id) => {
    return issues.value.find(issue => issue.issue_id == issue_id);
}

const parentCompleted = async (issue) => {
    let complete_flg = false;
    // hardcoded complete flg to 5
    if (issue.parent_issue_id != null) {
        let parent_issue = getIssueById(issue.parent_issue_id);
        if (parent_issue.status_id == 5) {
            complete_flg = true;
        }
    }

    if (complete_flg) {
        return Swal.fire({
            title: '実行しますか？',
            html: '親課題が完了状態です。<br/>親課題を処理中に変更しますか？',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#94A3B8',
            confirmButtonText: 'はい',
            cancelButtonText: 'キャンセル'
        }).then((result) => {
            return result.isConfirmed;
        });
    } else {
        return true;
    }    
};

const hasIncompleteChild = (issue) => {
    let incomplete_flg = false;
    if (issue.child_issues.length) {
        // hardcoded complete flg to 5
        for (let a of issue.child_issues) {
            if (incomplete_flg) break;
            let child_issue = getIssueById(a.issue_id);
            if (child_issue.status_id != 5) {
                incomplete_flg = true;
                break;
            } else {
                if (child_issue.child_issues.length) {
                    for (let b of child_issue.child_issues) {
                        let grandchild_issue = getIssueById(b.issue_id);
                        if (grandchild_issue.status_id != 5) {
                            incomplete_flg = true;
                            break;
                        }
                    }
                }
            }
        }
    }

    if (incomplete_flg) {
        Swal.fire({
            icon: 'warning',
            toast: true,
            showConfirmButton: false,
            position: 'top-end',
            timer: 2000,
            timerProgressBar: true,
            html: '<span class="font-bold">子課題/孫課題が未完了の為、完了に変更できません。</span>',
        });
    }

    return incomplete_flg;
};

const getGroupStatusId = (issue_id) => {
    let exit = false;
    let status_id = 0;

    issue_groups.value.map((group, i) => {
        if (exit) return;
        group.map((issue) => {
            if (issue.issue_id == issue_id) {
                exit = true;
                status_id = i;
                return;
            }
        });
    });

    return status_id;
}

const issuePriority = (priority) => {
    switch (priority) {
        case "high":
            return "高";
        case "medium":
            return "中";
        case "low":
            return "低";
        default:
            return "";
    }
}

let eventSource = null;
const sync = () => {
    if (typeof(EventSource) !== "undefined") {
        if (eventSource != null && eventSource.readyState != EventSource.CLOSED) return;
        
        eventSource = new EventSource(route('refresh-board', { project_id: props.project.project_id, 'timestamp': timestamp.value }));

        eventSource.onmessage = (event) => {
            let data = JSON.parse(event.data);
            
            if (data.refresh) {
                eventSource.close();
                timestamp.value = data.timestamp;

                axios
                    .get(route('fetch-board-data', { 'project_id': props.project.project_id }))
                    .then(response => {
                        if (checkIfResfresh(response.data)) {
                            synching.value = true;
                            issues.value = response.data;
                            loadData();
                            setTimeout(() => synching.value = false, 500);
                        }

                        sync();
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

const checkIfResfresh = (newData) => {
    let ogData = issue_groups.value.flat();
    if (ogData.length != newData.length) return true;

    for (let a of ogData) {
        let issue = newData.find(b => b.issue_id == a.issue_id && b.status_id == a.status_id);
        if (issue) {
            // do nothing
        } else {
            return true;
        }
    }

    return false;
};

onMounted(() => {
    sync();
});

const addFilter = (issue) => {
    return (
        (kind.value == 0 || issue.kind_id == kind.value)
        && (milestone.value == 0 || issue.milestone_id == milestone.value)
        && (project_user.value == 0 || issue.assignee_id == project_user.value)
        && (category.value == 0 || issue.issue_categories.find((issue_category) => issue_category.category_id == category.value))
    );
};
</script>

<template>
    <Head title="Backlog - Board" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div>
            <Link :href="route('view-project', { 'project_id': props.project.project_id })">
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4">戻る</button>
            </Link>
        </div>
        
        <div class="bg-slate-100 pt-2 pb-5 px-3 my-2">
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
                <span class="ms-4">ボード</span>
            </div>

            <div class="py-1 px-2 mt-3 mb-4">
                <div class="inline-block">
                    <div class="font-bold"><small>種別</small></div>
                    <div>
                        <select v-model="kind" class="rounded py-1 min-w-[170px]">
                            <option :value="''">すべて</option>
                            <template v-for="kind in props.kinds">
                                <option :value="kind.kind_id">{{ kind.kind_name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="inline-block ms-2">
                    <div class="font-bold"><small>カテゴリー</small></div>
                    <div>
                        <select v-model="category" class="rounded py-1 min-w-[170px]">
                            <option :value="0">すべて</option>
                            <template v-for="category in props.categories">
                                <option :value="category.category_id">{{ category.category_name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="inline-block ms-2">
                    <div class="font-bold"><small>マイルストーン</small></div>
                    <div>
                        <select v-model="milestone" class="rounded py-1 min-w-[170px]">
                            <option :value="0">すべて</option>
                            <template v-for="milestone in props.milestones">
                                <option :value="milestone.milestone_id">{{ milestone.milestone_name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="inline-block ms-2">
                    <div class="font-bold"><small>担当者</small></div>
                    <div>
                        <select v-model="project_user" class="rounded py-1 min-w-[170px]">
                            <option :value="0">すべて</option>
                            <template v-for="project_user in props.project_users">
                                <option :value="project_user.user_id">{{ project_user.user.username }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div class="relative">
                <!-- synching overlay -->
                <div v-if="synching"
                    class="absolute w-full h-full top-0 right-0 bottom-0 left-0 bg-slate-500 opacity-70 z-10">
                    <div class="text-white font-bold text-3xl absolute select-none"
                        :style="{ top: '50%', left: '50%', transform: 'translate(-50%, -50%)' }">
                        同期中
                    </div>
                </div>
                
                <div class="flex mx-0 overflow-x-scroll overflow-y-hidden">
                    <template v-for="status in props.statuses">
                        <div class="flex-1 px-2 select-none">
                            <div class="bg-slate-300 pt-3 pb-5 px-2 h-full">
                                <div class="font-bold mb-2">
                                    <i class="fa-sharp fa-solid fa-circle" :style="{ color: status.hex_color }"></i>
                                    {{ status.status_name }}
                                </div>

                                <draggable
                                    class="list-group h-full min-w-[220px] min-h-[400px]"
                                    item-key="no"
                                    :list="issue_groups[status.status_id]"
                                    group="issue_group"
                                    @change="onChange"
                                    draggable=".handle">
                                    <template #item="{ element }">
                                        <Link :href="route('view-issue', { 'project_id': element.project_id, 'issue_id': element.issue_id })"
                                            v-if="addFilter(element)" class="handle">
                                            <div class="bg-slate-200 hover:bg-slate-100 p-2 mb-2">
                                                <div class="flex justify-between mb-1">
                                                    <div>
                                                        <span class="rounded-full px-4 text-white" :style="{ backgroundColor: element.kind.hex_color }">
                                                            <small>{{ element.kind.kind_name }}</small>
                                                        </span>
                                                    </div>
                                                    <div><small class="font-bold">{{ issuePriority(element.issue_priority) }}</small></div>
                                                </div>
                                                <div class="font-bold mb-1">
                                                    <small>{{ element.issue_title }} ({{ element.issue_cd }})</small>
                                                </div>
                                                <div class="mb-1">
                                                    <small class="">{{ element.issue_desc }}</small>
                                                </div>
                                                <div class="mb-1">
                                                    <i class="fa-solid fa-circle-user"></i>
                                                    <small class="ms-2">{{ element.assignee ? element.assignee.username : '設定なし' }}</small>
                                                </div>
                                                <div class="mb-1">
                                                    <i class="fa-solid fa-calendar"></i>
                                                    <small class="ms-2">{{ element.end_date }}</small>
                                                </div>
                                            </div>
                                        </Link>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
