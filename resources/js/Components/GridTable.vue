<script setup>
import { ref, onMounted } from 'vue';
import { Grid } from "gridjs";
import { jaJP } from "gridjs/l10n";
import "gridjs/dist/theme/mermaid.css";

const props = defineProps({
  data_url: String,
  grid_columns: Object,
  auto_width: Boolean,
  pagination: Boolean,
});

const tableRef = ref();

const gridjs = new Grid({
    columns: props.grid_columns,
    autoWidth: props.auto_width,
    search: true,
    pagination: props.pagination,
    language: jaJP,
    server: {
        url: props.data_url,
        then: data =>  data.map(value => props.grid_columns.map(column => value[column.id]))
    },
});

onMounted(() => {
    gridjs.render(tableRef.value);
});

const rerender = () => {
    gridjs.updateConfig({}).forceRender();
};

defineExpose({ rerender });
</script>

<template>
    <table ref="tableRef"></table>
</template>

<style scoped>
</style>
