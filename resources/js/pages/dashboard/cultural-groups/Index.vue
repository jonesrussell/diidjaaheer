<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, TreePine } from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteConfirmDialog from '@/components/admin/DeleteConfirmDialog.vue';
import StatCard from '@/components/admin/StatCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

interface CulturalGroup {
    id: number;
    name: string;
    slug: string;
    depth_type: string;
    description: string | null;
    parent: { id: number; name: string } | null;
}

interface Props {
    culturalGroups: {
        data: CulturalGroup[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    filters: { search?: string; depth_type?: string };
    stats: {
        total: number;
        root: number;
        family: number;
        group: number;
    };
}

const props = defineProps<Props>();

const routePrefix = '/dashboard/cultural-groups';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Cultural Groups', href: routePrefix },
];

const search = ref(props.filters.search ?? '');
const deleteDialogOpen = ref(false);
const groupToDelete = ref<CulturalGroup | null>(null);
const isDeleting = ref(false);

const applySearch = () => {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    router.get(routePrefix, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleDelete = (group: CulturalGroup) => {
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

const truncate = (text: string | null, length: number = 80) => {
    if (!text) return '\u2014';
    return text.length > length ? text.slice(0, length) + '...' : text;
};

const goToPage = (url: string | null) => {
    if (url) router.get(url);
};
</script>

<template>
    <Head title="Cultural Groups - Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Cultural Groups
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Manage cultural groups and hierarchies
                    </p>
                </div>
                <Button as="a" :href="`${routePrefix}/create`">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Cultural Group
                </Button>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <StatCard label="Total" :value="stats.total" :icon="TreePine" />
                <StatCard label="Root" :value="stats.root" :icon="TreePine" />
                <StatCard
                    label="Family"
                    :value="stats.family"
                    :icon="TreePine"
                />
                <StatCard label="Group" :value="stats.group" :icon="TreePine" />
            </div>

            <!-- Search -->
            <div class="flex gap-2">
                <Input
                    v-model="search"
                    placeholder="Search cultural groups..."
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
                                Depth Type
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                Parent
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium">
                                Description
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
                            v-for="group in culturalGroups.data"
                            :key="group.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ group.name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <Badge variant="secondary">{{
                                    group.depth_type
                                }}</Badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ group.parent?.name ?? '\u2014' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ truncate(group.description) }}
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
                        <tr v-if="culturalGroups.data.length === 0">
                            <td
                                colspan="5"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                No cultural groups found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="culturalGroups.last_page > 1"
                class="flex items-center justify-between"
            >
                <p class="text-sm text-muted-foreground">
                    Showing {{ culturalGroups.from }} to
                    {{ culturalGroups.to }} of
                    {{ culturalGroups.total }}
                </p>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!culturalGroups.prev_page_url"
                        @click="goToPage(culturalGroups.prev_page_url)"
                    >
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!culturalGroups.next_page_url"
                        @click="goToPage(culturalGroups.next_page_url)"
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>

        <DeleteConfirmDialog
            v-model:open="deleteDialogOpen"
            title="Delete Cultural Group"
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
