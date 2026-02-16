<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteConfirmDialog from '@/components/admin/DeleteConfirmDialog.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';

interface Props {
    group: {
        id: number;
        name: string;
        slug: string;
        type: string;
        url: string | null;
        description: string | null;
        region: string | null;
    };
}

const props = defineProps<Props>();

const routePrefix = '/dashboard/groups';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Groups', href: routePrefix },
    { title: 'Edit', href: `${routePrefix}/${props.group.id}/edit` },
];

const form = useForm({
    name: props.group.name,
    slug: props.group.slug,
    type: props.group.type,
    url: props.group.url ?? '',
    description: props.group.description ?? '',
    region: props.group.region ?? '',
});

const deleteDialogOpen = ref(false);
const isDeleting = ref(false);

const submit = () => {
    form.put(`${routePrefix}/${props.group.id}`);
};

const confirmDelete = () => {
    isDeleting.value = true;
    router.delete(`${routePrefix}/${props.group.id}`, {
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Edit: ${group.name} - Dashboard`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Button
                        variant="ghost"
                        size="sm"
                        as="a"
                        :href="routePrefix"
                        class="mb-2"
                    >
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Groups
                    </Button>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Edit Group
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Update group details
                    </p>
                </div>
                <Button variant="destructive" @click="deleteDialogOpen = true">
                    <Trash2 class="mr-2 h-4 w-4" />
                    Delete
                </Button>
            </div>

            <!-- Form -->
            <form class="max-w-2xl space-y-6" @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="name">Name</Label>
                    <Input id="name" v-model="form.name" />
                    <p v-if="form.errors.name" class="text-sm text-destructive">
                        {{ form.errors.name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="slug">Slug</Label>
                    <Input id="slug" v-model="form.slug" />
                    <p v-if="form.errors.slug" class="text-sm text-destructive">
                        {{ form.errors.slug }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="type">Type</Label>
                    <Select v-model="form.type">
                        <SelectTrigger>
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="online">Online</SelectItem>
                            <SelectItem value="offline">Offline</SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.type" class="text-sm text-destructive">
                        {{ form.errors.type }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="url">URL</Label>
                    <Input id="url" v-model="form.url" type="url" />
                    <p v-if="form.errors.url" class="text-sm text-destructive">
                        {{ form.errors.url }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="region">Region</Label>
                    <Input id="region" v-model="form.region" />
                    <p
                        v-if="form.errors.region"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.region }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        class="min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30"
                    />
                    <p
                        v-if="form.errors.description"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>

                <div class="flex gap-3 border-t pt-4">
                    <Button
                        variant="outline"
                        as="a"
                        :href="routePrefix"
                        :disabled="form.processing"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </div>

        <DeleteConfirmDialog
            v-model:open="deleteDialogOpen"
            title="Delete Group"
            :description="`Are you sure you want to delete &quot;${group.name}&quot;? This action cannot be undone.`"
            :loading="isDeleting"
            @confirm="confirmDelete"
            @cancel="() => (deleteDialogOpen = false)"
        />
    </AppLayout>
</template>
