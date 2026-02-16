<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    events: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            starts_at: string;
            ends_at: string | null;
            location: string | null;
            type: string;
            description: string | null;
        }>;
    };
    filters: {
        search?: string;
        type?: string;
    };
    stats: {
        total: number;
        upcoming: number;
        past: number;
    };
}>();
</script>

<template>
    <AppLayout>
        <Head title="Events" />
        <div class="p-6">
            <h1 class="text-2xl font-bold">Events</h1>
            <!-- TODO: Full implementation in Task 7 -->
        </div>
    </AppLayout>
</template>
