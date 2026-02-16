<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        :class="className"
        v-bind="$attrs"
    >
        <text
            x="50%"
            y="52%"
            dominant-baseline="central"
            text-anchor="middle"
            fill="currentColor"
            font-family="'DM Serif Display', serif"
            font-size="32"
            font-weight="400"
        >D</text>
    </svg>
</template>
