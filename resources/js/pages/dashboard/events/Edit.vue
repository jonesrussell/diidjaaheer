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
}

const props = defineProps<Props>();

const routePrefix = '/dashboard/events';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Events', href: routePrefix },
    { title: 'Edit', href: `${routePrefix}/${props.event.id}/edit` },
];

const toDatetimeLocal = (date: string | null) => {
    if (!date) return '';
    return new Date(date).toISOString().slice(0, 16);
};

const form = useForm({
    title: props.event.title,
    slug: props.event.slug,
    starts_at: toDatetimeLocal(props.event.starts_at),
    ends_at: toDatetimeLocal(props.event.ends_at),
    location: props.event.location ?? '',
    type: props.event.type,
    description: props.event.description ?? '',
});

const deleteDialogOpen = ref(false);
const isDeleting = ref(false);

const submit = () => {
    form.put(`${routePrefix}/${props.event.id}`);
};

const confirmDelete = () => {
    isDeleting.value = true;
    router.delete(`${routePrefix}/${props.event.id}`, {
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Edit: ${event.title} - Dashboard`" />

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
                        Back to Events
                    </Button>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Edit Event
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Update event details
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
                            <SelectItem value="powwow">Powwow</SelectItem>
                            <SelectItem value="gathering">Gathering</SelectItem>
                            <SelectItem value="ceremony">Ceremony</SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.type" class="text-sm text-destructive">
                        {{ form.errors.type }}
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="starts_at">Start Date</Label>
                        <Input
                            id="starts_at"
                            v-model="form.starts_at"
                            type="datetime-local"
                        />
                        <p
                            v-if="form.errors.starts_at"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.starts_at }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="ends_at">End Date</Label>
                        <Input
                            id="ends_at"
                            v-model="form.ends_at"
                            type="datetime-local"
                        />
                        <p
                            v-if="form.errors.ends_at"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.ends_at }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="location">Location</Label>
                    <Input id="location" v-model="form.location" />
                    <p
                        v-if="form.errors.location"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.location }}
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
            title="Delete Event"
            :description="`Are you sure you want to delete &quot;${event.title}&quot;? This action cannot be undone.`"
            :loading="isDeleting"
            @confirm="confirmDelete"
            @cancel="() => (deleteDialogOpen = false)"
        />
    </AppLayout>
</template>
