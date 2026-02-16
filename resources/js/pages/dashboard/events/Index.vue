<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Calendar,
    CalendarCheck,
    CalendarX,
    Pencil,
    Plus,
    Trash2,
} from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteConfirmDialog from '@/components/admin/DeleteConfirmDialog.vue';
import StatCard from '@/components/admin/StatCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

interface Event {
    id: number;
    title: string;
    slug: string;
    starts_at: string;
    ends_at: string | null;
    location: string | null;
    type: string;
    description: string | null;
}

interface Props {
    events: {
        data: Event[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    filters: { search?: string; type?: string };
    stats: { total: number; upcoming: number; past: number };
}

const props = defineProps<Props>();

const routePrefix = '/dashboard/events';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Events', href: routePrefix },
];

const search = ref(props.filters.search ?? '');
const deleteDialogOpen = ref(false);
const eventToDelete = ref<Event | null>(null);
const isDeleting = ref(false);

const applySearch = () => {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    router.get(routePrefix, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleDelete = (event: Event) => {
    eventToDelete.value = event;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!eventToDelete.value) return;
    isDeleting.value = true;
    router.delete(`${routePrefix}/${eventToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogOpen.value = false;
            eventToDelete.value = null;
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const goToPage = (url: string | null) => {
    if (url) router.get(url);
};
</script>

<template>
    <Head title="Events - Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Events</h1>
                    <p class="mt-1 text-muted-foreground">
                        Manage powwow events and gatherings
                    </p>
                </div>
                <Button as="a" :href="`${routePrefix}/create`">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Event
                </Button>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatCard
                    label="Total Events"
                    :value="stats.total"
                    :icon="Calendar"
                />
                <StatCard
                    label="Upcoming"
                    :value="stats.upcoming"
                    :icon="CalendarCheck"
                />
                <StatCard label="Past" :value="stats.past" :icon="CalendarX" />
            </div>

            <!-- Search -->
            <div class="flex gap-2">
                <Input
                    v-model="search"
                    placeholder="Search events..."
                    class="max-w-sm"
                    @keyup.enter="applySearch"
                />
                <Button variant="outline" @click="applySearch">Search</Button>
            </div>

            <!-- Table -->
            <div class="rounded-md border">
                <table class="w-full">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                Title
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                Type
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                Date
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                Location
                            </th>
                            <th
                                class="px-4 py-3 text-right text-sm font-medium"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="event in events.data"
                            :key="event.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ event.title }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <Badge variant="secondary">{{
                                    event.type
                                }}</Badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ formatDate(event.starts_at) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ event.location || '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        as="a"
                                        :href="`${routePrefix}/${event.id}/edit`"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="handleDelete(event)"
                                    >
                                        <Trash2
                                            class="h-4 w-4 text-destructive"
                                        />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="events.data.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                No events found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="events.last_page > 1"
                class="flex items-center justify-between"
            >
                <p class="text-sm text-muted-foreground">
                    Showing {{ events.from }} to {{ events.to }} of
                    {{ events.total }}
                </p>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!events.prev_page_url"
                        @click="goToPage(events.prev_page_url)"
                    >
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!events.next_page_url"
                        @click="goToPage(events.next_page_url)"
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>

        <DeleteConfirmDialog
            v-model:open="deleteDialogOpen"
            title="Delete Event"
            :description="`Are you sure you want to delete &quot;${eventToDelete?.title}&quot;? This action cannot be undone.`"
            :loading="isDeleting"
            @confirm="confirmDelete"
            @cancel="
                () => {
                    deleteDialogOpen = false;
                    eventToDelete = null;
                }
            "
        />
    </AppLayout>
</template>
