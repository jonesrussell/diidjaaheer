<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ChevronRight, TreePine, Users } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface BreadcrumbItem {
    name: string;
    slug: string;
}

interface CulturalGroupChild {
    id: number;
    name: string;
    slug: string;
    depth_type: string;
    children_count: number;
    teachings_count: number;
}

interface Teaching {
    id: number;
    title: string;
    slug: string;
    type: string;
    content: string;
}

interface PaginatedTeachings {
    data: Teaching[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface CulturalGroup {
    id: number;
    name: string;
    slug: string;
    depth_type: string;
    description: string | null;
}

interface Props {
    group: CulturalGroup;
    children: CulturalGroupChild[];
    teachings: PaginatedTeachings;
    breadcrumb: BreadcrumbItem[];
}

const props = defineProps<Props>();

const truncate = (text: string, length: number = 150) => {
    return text.length > length ? text.slice(0, length) + '...' : text;
};

const goToPage = (url: string | null) => {
    if (url) router.get(url);
};
</script>

<template>
    <PublicLayout :title="`${props.group.name} — Diidjaaheer`">
        <section class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="mb-8 flex items-center gap-1 text-sm">
                    <Link
                        href="/culture"
                        class="text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Indigenous Culture
                    </Link>
                    <template
                        v-for="(crumb, index) in breadcrumb"
                        :key="crumb.slug"
                    >
                        <ChevronRight
                            class="size-4 shrink-0 text-muted-foreground/50"
                        />
                        <Link
                            v-if="index < breadcrumb.length - 1"
                            :href="`/culture/${crumb.slug}`"
                            class="text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ crumb.name }}
                        </Link>
                        <span v-else class="font-medium text-foreground">
                            {{ crumb.name }}
                        </span>
                    </template>
                </nav>

                <!-- Group header -->
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1 rounded-full bg-sage" />
                    <h1 class="font-serif text-4xl tracking-tight sm:text-5xl">
                        {{ group.name }}
                    </h1>
                </div>
                <p
                    v-if="group.description"
                    class="mt-4 max-w-3xl text-lg text-muted-foreground"
                >
                    {{ group.description }}
                </p>

                <!-- Sub-groups -->
                <section v-if="children.length > 0" class="mt-12">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-1 rounded-full bg-primary" />
                        <h2 class="font-serif text-3xl sm:text-4xl">
                            Sub-groups
                        </h2>
                    </div>

                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="child in children"
                            :key="child.id"
                            :href="`/culture/${child.slug}`"
                            class="block"
                        >
                            <Card
                                class="group h-full transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                            >
                                <CardHeader>
                                    <div
                                        class="mb-3 flex size-10 items-center justify-center rounded-full bg-primary/10"
                                    >
                                        <Users class="size-5 text-primary" />
                                    </div>
                                    <CardTitle class="font-serif text-lg">
                                        {{ child.name }}
                                    </CardTitle>
                                    <CardDescription>
                                        <span v-if="child.children_count > 0">
                                            {{ child.children_count }}
                                            sub-group{{
                                                child.children_count !== 1
                                                    ? 's'
                                                    : ''
                                            }}
                                        </span>
                                        <span
                                            v-if="
                                                child.children_count > 0 &&
                                                child.teachings_count > 0
                                            "
                                        >
                                            &middot;
                                        </span>
                                        <span v-if="child.teachings_count > 0">
                                            {{ child.teachings_count }}
                                            teaching{{
                                                child.teachings_count !== 1
                                                    ? 's'
                                                    : ''
                                            }}
                                        </span>
                                    </CardDescription>
                                </CardHeader>
                            </Card>
                        </Link>
                    </div>
                </section>

                <!-- Teachings -->
                <section v-if="teachings.data.length > 0" class="mt-12">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-1 rounded-full bg-accent" />
                        <h2 class="font-serif text-3xl sm:text-4xl">
                            Teachings
                        </h2>
                    </div>

                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="teaching in teachings.data"
                            :key="teaching.id"
                            :href="`/teachings/${teaching.slug}`"
                            class="block"
                        >
                            <Card
                                class="group h-full transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                            >
                                <CardHeader>
                                    <div class="flex items-center gap-2">
                                        <Badge
                                            variant="secondary"
                                            class="text-xs capitalize"
                                        >
                                            {{ teaching.type }}
                                        </Badge>
                                    </div>
                                    <CardTitle class="text-base">
                                        {{ teaching.title }}
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p
                                        class="line-clamp-3 text-sm text-muted-foreground"
                                    >
                                        {{ truncate(teaching.content) }}
                                    </p>
                                </CardContent>
                            </Card>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="teachings.last_page > 1"
                        class="mt-8 flex items-center justify-between"
                    >
                        <p class="text-sm text-muted-foreground">
                            Showing {{ teachings.from }} to
                            {{ teachings.to }} of
                            {{ teachings.total }} teachings
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
                </section>

                <!-- Empty state when no children and no teachings -->
                <div
                    v-if="children.length === 0 && teachings.data.length === 0"
                    class="mt-12 text-center text-muted-foreground"
                >
                    <TreePine
                        class="mx-auto mb-4 size-12 text-muted-foreground/50"
                    />
                    <p>
                        Content for this group is being gathered. Check back
                        soon.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
