<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { watch } from 'vue';

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

const routePrefix = '/dashboard/groups';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Groups', href: routePrefix },
    { title: 'Create', href: `${routePrefix}/create` },
];

const form = useForm({
    name: '',
    slug: '',
    type: '',
    url: '',
    description: '',
    region: '',
});

const slugify = (text: string) =>
    text
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

watch(
    () => form.name,
    (val) => {
        form.slug = slugify(val);
    },
);

const submit = () => {
    form.post(routePrefix);
};
</script>

<template>
    <Head title="Create Group - Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6"
        >
            <!-- Header -->
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
                <h1 class="text-3xl font-bold tracking-tight">Create Group</h1>
                <p class="mt-1 text-muted-foreground">
                    Add a new community group
                </p>
            </div>

            <!-- Form -->
            <form class="max-w-2xl space-y-6" @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Group name"
                    />
                    <p v-if="form.errors.name" class="text-sm text-destructive">
                        {{ form.errors.name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="slug">Slug</Label>
                    <Input
                        id="slug"
                        v-model="form.slug"
                        placeholder="group-slug"
                    />
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
                    <Input
                        id="url"
                        v-model="form.url"
                        type="url"
                        placeholder="https://..."
                    />
                    <p v-if="form.errors.url" class="text-sm text-destructive">
                        {{ form.errors.url }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="region">Region</Label>
                    <Input
                        id="region"
                        v-model="form.region"
                        placeholder="e.g. Great Lakes"
                    />
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
                        placeholder="Group description"
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
                        {{ form.processing ? 'Creating...' : 'Create Group' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
