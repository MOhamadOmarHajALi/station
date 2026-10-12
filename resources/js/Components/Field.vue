<script setup>
defineProps({ label: String, error: String });
</script>

<template>
  <div class="mb-3">
    <label v-if="label" class="form-label">{{ label }}</label>
    <slot />
    <div v-if="error" class="text-danger small mt-1">{{ error }}</div>
  </div>
</template>
