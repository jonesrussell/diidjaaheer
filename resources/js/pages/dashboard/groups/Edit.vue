<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    group: {
        id: number;
        name: string;
        slug: string;
        type: string;
        url: string | null;
        description: string | null;
        region: string | null;
    };
}>();
</script>

<template>
    <AppLayout>
        <Head title="Edit Group" />
        <div class="p-6">
            <h1 class="text-2xl font-bold">Edit Group</h1>
            <!-- TODO: Full implementation in Task 8 -->
        </div>
    </AppLayout>
</template>
