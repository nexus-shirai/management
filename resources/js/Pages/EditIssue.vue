<script setup>
import AppHeader from '../Components/AppHeader.vue';
import AppFooter from '../Components/AppFooter.vue';

const props = defineProps({
  common: Object,
  type: String
});

let title = "";

if (props.type == "Create") {
    title = "作成";
} else if (props.type == "Edit") {
    title = "編集";
} else if (props.type == "View") {
    title = "詳細";
} else {
    // do nothing
}
</script>

<template>
    <Head :title="`Backlog - ${props.type} Issue`" />
    <AppHeader :common="props.common" />

    <main class="container flex-1 py-5 mx-auto max-w-[1000px]">
        <div class="bg-slate-100 py-2 px-3 mt-2 mb-2">
            <div class="font-bold mb-5">課題{{ title }}</div>

            <div class="mb-2 flex">
                <div class="font-bold w-[120px]">状態</div>
                <div class="flex-1">
                    <select class="rounded py-1 w-[170px]" :disabled="props.type == 'View'">
                        <option>種別を選択する</option>
                    </select>
                </div>
            </div>

            <div class="mb-2 flex">
                <div class="font-bold w-[120px]">課題名</div>
                <div class="flex-1">
                    <input type="text" placeholder="課題名" class="w-full rounded py-1" :disabled="props.type == 'View'">
                </div>
            </div>
            
            <div class="mb-2 flex">
                <div class="font-bold w-[120px]">課題の詳細</div>
                <div class="flex-1">
                    <textarea rows="3" placeholder="課題の詳細" class="w-full rounded py-1" :disabled="props.type == 'View'"></textarea>
                </div>
            </div>

            <hr class="mb-2" />

            <div class="mb-2">
                <div class="flex">
                    <div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">状態</div>
                            <div>
                                <select class="rounded py-1 w-[170px]" :disabled="props.type == 'View'">
                                    <option>未対応</option>
                                    <option>処理中</option>
                                    <option>処理済み</option>
                                    <option>完了</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">優先度</div>
                            <div>
                                <select class="rounded py-1 w-[170px]" :disabled="props.type == 'View'">
                                    <option>低</option>
                                    <option>中</option>
                                    <option>高</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">予定時間</div>
                            <div>
                                <input class="rounded py-1 w-[170px]" type="number" min="0" :disabled="props.type == 'View'">
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">開始日</div>
                            <div>
                                <input class="rounded py-1 w-[170px]" type="date" :disabled="props.type == 'View'">
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">カテゴリー</div>
                            <div>
                                <select class="rounded py-1 w-[170px]" :disabled="props.type == 'View'">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">バージョンID</div>
                            <div>
                                <select class="rounded py-1 w-[170px]" disabled>
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ms-5"></div>
                    <div class="ms-5"></div>
                    <div class="ms-5">
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">担当者</div>
                            <div>
                                <select class="rounded py-1 w-[170px]" :disabled="props.type == 'View'">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">マイルストーン</div>
                            <div>
                                <select class="rounded py-1 w-[170px]" :disabled="props.type == 'View'">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">実施時間</div>
                            <div>
                                <input class="rounded py-1 w-[170px]" type="number" min="0" :disabled="props.type == 'View'">
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">期限日</div>
                            <div>
                                <input class="rounded py-1 w-[170px]" type="date" :disabled="props.type == 'View'">
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">完了理由</div>
                            <div>
                                <select class="rounded py-1 min-w-[212px]" :disabled="props.type == 'View'">
                                    <option>完了理由を選択する</option>
                                    <option :value="1">完了</option>
                                    <option :value="2">未再現</option>
                                    <option :value="3">対応しない</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex mb-2">
                            <div class="font-bold w-[120px]">親課題</div>
                            <div>
                                <input class="rounded py-1 min-w-[200px]" type="text" value="設定なし" disabled>
                            </div>
                            <button class="bg-blue-500 hover:bg-blue-700 ms-2 text-white disabled:bg-slate-300 
                                disabled:text-black px-3 rounded" :disabled="props.type == 'View'">
                                <small>親課題を設定する</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="mb-2" />

            <div id="file-upload-div" class="py-4 text-center border-dotted border-blue-500 border-2">
                <small>ファイルをドラッグ＆ドロップまたは
                    <button onclick="uploadFile()" class="bg-blue-500 hover:bg-blue-700 text-white 
                        disabled:bg-slate-300 disabled:text-black rounded py-1 px-3 ms-2"
                        :disabled="type == 'View'">
                        ファイルを選択
                    </button>
                </small>
                <input id="file-upload-input" class="hidden" type="file">
            </div>

            <div class="mt-5 mb-3 text-center">
                <Link :href="route('dashboard')">
                    <button type="button" class="bg-slate-300 hover:bg-slate-400 rounded py-1 px-4">戻る</button>
                </Link>
                <button class="bg-blue-500 hover:bg-blue-700 text-white rounded py-1 px-4 ms-2">
                    <template v-if="type == 'Create'">
                        保存
                    </template>
                    <template v-else-if="type == 'Edit'">
                        編集
                    </template>
                    <template v-else-if="type == 'View'">
                        編集画面へ
                    </template>
                    <template v-else>
                        <!-- do nothing -->
                    </template>
                </button>
            </div>
        </div>
    </main>

    <AppFooter />
</template>

<style scoped>
</style>
