<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    teachings: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            type: string;
            content: string;
        }>;
    };
    filters: {
        search?: string;
        type?: string;
    };
    stats: {
        total: number;
        culture: number;
        history: number;
        language: number;
    };
}>();
</script>

<template>
    <AppLayout>
        <Head title="Teachings" />
        <div class="p-6">
            <h1 class="text-2xl font-bold">Teachings</h1>
            <!-- TODO: Full implementation in Task 9 -->
        </div>
    </AppLayout>
</template>
