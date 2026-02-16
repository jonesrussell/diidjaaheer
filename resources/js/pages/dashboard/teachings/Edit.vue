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
    teaching: {
        id: number;
        title: string;
        slug: string;
        type: string;
        content: string;
    };
}

const props = defineProps<Props>();

const routePrefix = '/dashboard/teachings';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Teachings', href: routePrefix },
    { title: 'Edit', href: `${routePrefix}/${props.teaching.id}/edit` },
];

const form = useForm({
    title: props.teaching.title,
    slug: props.teaching.slug,
    type: props.teaching.type,
    content: props.teaching.content,
});

const deleteDialogOpen = ref(false);
const isDeleting = ref(false);

const submit = () => {
    form.put(`${routePrefix}/${props.teaching.id}`);
};

const confirmDelete = () => {
    isDeleting.value = true;
    router.delete(`${routePrefix}/${props.teaching.id}`, {
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Edit: ${teaching.title} - Dashboard`" />

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
                        Back to Teachings
                    </Button>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Edit Teaching
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Update teaching details
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
                    <Label for="title">Title</Label>
                    <Input id="title" v-model="form.title" />
                    <p
                        v-if="form.errors.title"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.title }}
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
                            <SelectItem value="culture">Culture</SelectItem>
                            <SelectItem value="history">History</SelectItem>
                            <SelectItem value="language">Language</SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.type" class="text-sm text-destructive">
                        {{ form.errors.type }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="content">Content</Label>
                    <textarea
                        id="content"
                        v-model="form.content"
                        class="min-h-[120px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30"
                    />
                    <p
                        v-if="form.errors.content"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.content }}
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
            title="Delete Teaching"
            :description="`Are you sure you want to delete &quot;${teaching.title}&quot;? This action cannot be undone.`"
            :loading="isDeleting"
            @confirm="confirmDelete"
            @cancel="() => (deleteDialogOpen = false)"
        />
    </AppLayout>
</template>
