<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    BookOpen,
    History,
    Languages,
    Pencil,
    Plus,
    Sparkles,
    Trash2,
} from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteConfirmDialog from '@/components/admin/DeleteConfirmDialog.vue';
import StatCard from '@/components/admin/StatCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

interface Teaching {
    id: number;
    title: string;
    slug: string;
    type: string;
    content: string;
}

interface Props {
    teachings: {
        data: Teaching[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    filters: { search?: string; type?: string };
    stats: {
        total: number;
        culture: number;
        history: number;
        language: number;
    };
}

const props = defineProps<Props>();

const routePrefix = '/dashboard/teachings';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Teachings', href: routePrefix },
];

const search = ref(props.filters.search ?? '');
const deleteDialogOpen = ref(false);
const teachingToDelete = ref<Teaching | null>(null);
const isDeleting = ref(false);

const applySearch = () => {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    router.get(routePrefix, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleDelete = (teaching: Teaching) => {
    teachingToDelete.value = teaching;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!teachingToDelete.value) return;
    isDeleting.value = true;
    router.delete(`${routePrefix}/${teachingToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogOpen.value = false;
            teachingToDelete.value = null;
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};

const truncate = (text: string, length: number = 80) => {
    return text.length > length ? text.slice(0, length) + '...' : text;
};

const goToPage = (url: string | null) => {
    if (url) router.get(url);
};
</script>

<template>
    <Head title="Teachings - Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Teachings</h1>
                    <p class="mt-1 text-muted-foreground">
                        Manage cultural teachings and knowledge
                    </p>
                </div>
                <Button as="a" :href="`${routePrefix}/create`">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Teaching
                </Button>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <StatCard label="Total" :value="stats.total" :icon="BookOpen" />
                <StatCard
                    label="Culture"
                    :value="stats.culture"
                    :icon="Sparkles"
                />
                <StatCard
                    label="History"
                    :value="stats.history"
                    :icon="History"
                />
                <StatCard
                    label="Language"
                    :value="stats.language"
                    :icon="Languages"
                />
            </div>

            <!-- Search -->
            <div class="flex gap-2">
                <Input
                    v-model="search"
                    placeholder="Search teachings..."
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
                                Content
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
                            v-for="teaching in teachings.data"
                            :key="teaching.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ teaching.title }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <Badge variant="secondary">{{
                                    teaching.type
                                }}</Badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ truncate(teaching.content) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        as="a"
                                        :href="`${routePrefix}/${teaching.id}/edit`"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="handleDelete(teaching)"
                                    >
                                        <Trash2
                                            class="h-4 w-4 text-destructive"
                                        />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="teachings.data.length === 0">
                            <td
                                colspan="4"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                No teachings found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="teachings.last_page > 1"
                class="flex items-center justify-between"
            >
                <p class="text-sm text-muted-foreground">
                    Showing {{ teachings.from }} to {{ teachings.to }} of
                    {{ teachings.total }}
                </p>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!teachings.prev_page_url"
                        @click="goToPage(teachings.prev_page_url)"
                    >
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!teachings.next_page_url"
                        @click="goToPage(teachings.next_page_url)"
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>

        <DeleteConfirmDialog
            v-model:open="deleteDialogOpen"
            title="Delete Teaching"
            :description="`Are you sure you want to delete &quot;${teachingToDelete?.title}&quot;? This action cannot be undone.`"
            :loading="isDeleting"
            @confirm="confirmDelete"
            @cancel="
                () => {
                    deleteDialogOpen = false;
                    teachingToDelete = null;
                }
            "
        />
    </AppLayout>
</template>
