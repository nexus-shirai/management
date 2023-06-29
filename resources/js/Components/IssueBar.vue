<script setup>
let props = defineProps({
  start_date: String,
  end_date: String,
});
</script>

<template>
    <div class="absolute min-w-[150px] bg-pink-400">〇</div>
</template>

<style scoped>
div {
  top: 1px;
  left: 1px;
}
</style>
