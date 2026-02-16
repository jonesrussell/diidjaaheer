<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    groups: {
        data: Array<{
            id: number;
            name: string;
            slug: string;
            type: string;
            url: string | null;
            description: string | null;
            region: string | null;
        }>;
    };
    filters: {
        search?: string;
        type?: string;
    };
    stats: {
        total: number;
        online: number;
        offline: number;
    };
}>();
</script>

<template>
    <AppLayout>
        <Head title="Groups" />
        <div class="p-6">
            <h1 class="text-2xl font-bold">Groups</h1>
            <!-- TODO: Full implementation in Task 8 -->
        </div>
    </AppLayout>
</template>
