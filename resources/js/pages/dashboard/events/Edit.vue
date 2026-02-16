<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    event: {
        id: number;
        title: string;
        slug: string;
        starts_at: string;
        ends_at: string | null;
        location: string | null;
        type: string;
        description: string | null;
    };
}>();
</script>

<template>
    <AppLayout>
        <Head title="Edit Event" />
        <div class="p-6">
            <h1 class="text-2xl font-bold">Edit Event</h1>
            <!-- TODO: Full implementation in Task 7 -->
        </div>
    </AppLayout>
</template>
