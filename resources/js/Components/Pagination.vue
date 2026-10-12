<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ links: Array });
</script>

<template>
  <nav v-if="links && links.length > 3" class="mt-3">
    <ul class="pagination">
      <li v-for="(l, i) in links" :key="i" class="page-item" :class="{ active: l.active, disabled: !l.url }">
        <Link v-if="l.url" class="page-link" :href="l.url" preserve-scroll v-html="l.label" />
        <span v-else class="page-link" v-html="l.label" />
      </li>
    </ul>
  </nav>
</template>
