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

interface Props {
    culturalGroups: Array<{ id: number; name: string }>;
}

defineProps<Props>();

const routePrefix = '/dashboard/teachings';
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Teachings', href: routePrefix },
    { title: 'Create', href: `${routePrefix}/create` },
];

const form = useForm({
    title: '',
    slug: '',
    type: '',
    content: '',
    cultural_group_id: '',
});

const slugify = (text: string) =>
    text
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

watch(
    () => form.title,
    (val) => {
        form.slug = slugify(val);
    },
);

const submit = () => {
    form.transform((data) => ({
        ...data,
        cultural_group_id: data.cultural_group_id || null,
    })).post(routePrefix);
};
</script>

<template>
    <Head title="Create Teaching - Dashboard" />

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
                    Back to Teachings
                </Button>
                <h1 class="text-3xl font-bold tracking-tight">
                    Create Teaching
                </h1>
                <p class="mt-1 text-muted-foreground">
                    Add a new cultural teaching
                </p>
            </div>

            <!-- Form -->
            <form class="max-w-2xl space-y-6" @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="title">Title</Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        placeholder="Teaching title"
                    />
                    <p
                        v-if="form.errors.title"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.title }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="slug">Slug</Label>
                    <Input
                        id="slug"
                        v-model="form.slug"
                        placeholder="teaching-slug"
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
                    <Label for="cultural_group_id">Cultural Group</Label>
                    <Select v-model="form.cultural_group_id">
                        <SelectTrigger>
                            <SelectValue
                                placeholder="No cultural group"
                            />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="group in culturalGroups"
                                :key="group.id"
                                :value="String(group.id)"
                            >
                                {{ group.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p
                        v-if="form.errors.cultural_group_id"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.cultural_group_id }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="content">Content</Label>
                    <textarea
                        id="content"
                        v-model="form.content"
                        placeholder="Teaching content"
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
                        {{
                            form.processing ? 'Creating...' : 'Create Teaching'
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
