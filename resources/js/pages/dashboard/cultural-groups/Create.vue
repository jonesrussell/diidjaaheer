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

interface ParentOption {
    id: number;
    name: string;
    depth_type: string;
}

interface Props {
    parentOptions: ParentOption[];
}

defineProps<Props>();

const routePrefix = '/dashboard/cultural-groups';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Cultural Groups', href: routePrefix },
    { title: 'Create', href: `${routePrefix}/create` },
];

const form = useForm({
    name: '',
    slug: '',
    depth_type: '',
    description: '',
    parent_id: '',
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
    form.transform((data) => ({
        ...data,
        parent_id: data.parent_id || null,
    })).post(routePrefix);
};
</script>

<template>
    <Head title="Create Cultural Group - Dashboard" />

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
                    Back to Cultural Groups
                </Button>
                <h1 class="text-3xl font-bold tracking-tight">
                    Create Cultural Group
                </h1>
                <p class="mt-1 text-muted-foreground">
                    Add a new cultural group
                </p>
            </div>

            <!-- Form -->
            <form class="max-w-2xl space-y-6" @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Cultural group name"
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
                        placeholder="cultural-group-slug"
                    />
                    <p v-if="form.errors.slug" class="text-sm text-destructive">
                        {{ form.errors.slug }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="depth_type">Depth Type</Label>
                    <Select v-model="form.depth_type">
                        <SelectTrigger>
                            <SelectValue placeholder="Select depth type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="root">Root</SelectItem>
                            <SelectItem value="family">Family</SelectItem>
                            <SelectItem value="group">Group</SelectItem>
                            <SelectItem value="sub_group">Sub Group</SelectItem>
                            <SelectItem value="community">Community</SelectItem>
                            <SelectItem value="clan">Clan</SelectItem>
                        </SelectContent>
                    </Select>
                    <p
                        v-if="form.errors.depth_type"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.depth_type }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="parent_id">Parent Group</Label>
                    <Select v-model="form.parent_id">
                        <SelectTrigger>
                            <SelectValue placeholder="No parent (top level)" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in parentOptions"
                                :key="option.id"
                                :value="String(option.id)"
                            >
                                {{ option.name }} ({{ option.depth_type }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p
                        v-if="form.errors.parent_id"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.parent_id }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Cultural group description"
                        class="min-h-[120px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30"
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
                        {{
                            form.processing
                                ? 'Creating...'
                                : 'Create Cultural Group'
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
