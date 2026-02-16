<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    teaching: {
        id: number;
        title: string;
        slug: string;
        type: string;
        content: string;
    };
}>();
</script>

<template>
    <AppLayout>
        <Head title="Edit Teaching" />
        <div class="p-6">
            <h1 class="text-2xl font-bold">Edit Teaching</h1>
            <!-- TODO: Full implementation in Task 9 -->
        </div>
    </AppLayout>
</template>
