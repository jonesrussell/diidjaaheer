<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Globe, MapPin, Pencil, Plus, Trash2, Users } from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteConfirmDialog from '@/components/admin/DeleteConfirmDialog.vue';
import StatCard from '@/components/admin/StatCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

interface Group {
    id: number;
    name: string;
    slug: string;
    type: string;
    url: string | null;
    description: string | null;
    region: string | null;
}

interface Props {
    groups: {
        data: Group[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    filters: { search?: string; type?: string };
    stats: { total: number; online: number; offline: number };
}

const props = defineProps<Props>();

const routePrefix = '/dashboard/groups';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Groups', href: routePrefix },
];

const search = ref(props.filters.search ?? '');
const deleteDialogOpen = ref(false);
const groupToDelete = ref<Group | null>(null);
const isDeleting = ref(false);

const applySearch = () => {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    router.get(routePrefix, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleDelete = (group: Group) => {
    groupToDelete.value = group;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!groupToDelete.value) return;
    isDeleting.value = true;
    router.delete(`${routePrefix}/${groupToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogOpen.value = false;
            groupToDelete.value = null;
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};

const goToPage = (url: string | null) => {
    if (url) router.get(url);
};
</script>

<template>
    <Head title="Groups - Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Groups</h1>
                    <p class="mt-1 text-muted-foreground">
                        Manage community groups and organizations
                    </p>
                </div>
                <Button as="a" :href="`${routePrefix}/create`">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Group
                </Button>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatCard
                    label="Total Groups"
                    :value="stats.total"
                    :icon="Users"
                />
                <StatCard label="Online" :value="stats.online" :icon="Globe" />
                <StatCard
                    label="Offline"
                    :value="stats.offline"
                    :icon="MapPin"
                />
            </div>

            <!-- Search -->
            <div class="flex gap-2">
                <Input
                    v-model="search"
                    placeholder="Search groups..."
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
                                Name
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                Type
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                Region
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                URL
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
                            v-for="group in groups.data"
                            :key="group.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ group.name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <Badge variant="secondary">{{
                                    group.type
                                }}</Badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ group.region || '—' }}
                            </td>
                            <td
                                class="max-w-[200px] truncate px-4 py-3 text-sm text-muted-foreground"
                            >
                                <a
                                    v-if="group.url"
                                    :href="group.url"
                                    target="_blank"
                                    rel="noopener"
                                    class="text-primary hover:underline"
                                >
                                    {{ group.url }}
                                </a>
                                <span v-else>—</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        as="a"
                                        :href="`${routePrefix}/${group.id}/edit`"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="handleDelete(group)"
                                    >
                                        <Trash2
                                            class="h-4 w-4 text-destructive"
                                        />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="groups.data.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                No groups found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="groups.last_page > 1"
                class="flex items-center justify-between"
            >
                <p class="text-sm text-muted-foreground">
                    Showing {{ groups.from }} to {{ groups.to }} of
                    {{ groups.total }}
                </p>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!groups.prev_page_url"
                        @click="goToPage(groups.prev_page_url)"
                    >
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!groups.next_page_url"
                        @click="goToPage(groups.next_page_url)"
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>

        <DeleteConfirmDialog
            v-model:open="deleteDialogOpen"
            title="Delete Group"
            :description="`Are you sure you want to delete &quot;${groupToDelete?.name}&quot;? This action cannot be undone.`"
            :loading="isDeleting"
            @confirm="confirmDelete"
            @cancel="
                () => {
                    deleteDialogOpen = false;
                    groupToDelete = null;
                }
            "
        />
    </AppLayout>
</template>
