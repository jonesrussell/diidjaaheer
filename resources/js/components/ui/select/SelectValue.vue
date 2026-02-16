<script setup lang="ts">
import type { SelectValueProps } from 'reka-ui';
import { SelectValue as RekaSelectValue } from 'reka-ui';

defineProps<SelectValueProps>();
</script>

<template>
    <RekaSelectValue v-bind="$props">
        <slot />
    </RekaSelectValue>
</template>
