<script setup lang="ts">
import type { SelectRootEmits, SelectRootProps } from 'reka-ui';
import { SelectRoot } from 'reka-ui';

const props = defineProps<SelectRootProps>();
const emits = defineEmits<SelectRootEmits>();
</script>

<template>
    <SelectRoot
        data-slot="select"
        v-bind="props"
        @update:model-value="emits('update:modelValue', $event)"
        @update:open="emits('update:open', $event)"
    >
        <slot v-bind="{ modelValue: props.modelValue, open: undefined }" />
    </SelectRoot>
</template>
