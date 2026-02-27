<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { BookOpen, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface CulturalGroup {
    id: number;
    name: string;
    slug: string;
}

interface Teaching {
    id: number;
    title: string;
    slug: string;
    type: string;
    content: string;
    cultural_group: CulturalGroup | null;
}

interface Props {
    teachings: {
        data: Teaching[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    filters: { type?: string; search?: string };
}

const props = defineProps<Props>();

const search = ref(props.filters.search ?? '');

const typeFilters = [
    { label: 'All', value: '' },
    { label: 'Culture', value: 'culture' },
    { label: 'History', value: 'history' },
    { label: 'Language', value: 'language' },
];

const activeType = computed(() => props.filters.type ?? '');

const truncate = (text: string, length: number = 100) => {
    return text.length > length ? text.slice(0, length) + '...' : text;
};

const applySearch = () => {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    if (props.filters.type) params.type = props.filters.type;
    router.get('/teachings', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const goToPage = (url: string | null) => {
    if (url) router.get(url);
};

const typePillUrl = (value: string) => {
    const params = new URLSearchParams();
    if (value) params.set('type', value);
    if (props.filters.search) params.set('search', props.filters.search);
    const qs = params.toString();
    return qs ? `/teachings?${qs}` : '/teachings';
};
</script>

<template>
    <PublicLayout title="Teachings — Diidjaaheer">
        <!-- Hero intro -->
        <section class="noise-texture py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h1
                    class="font-serif text-5xl tracking-tight sm:text-6xl md:text-7xl"
                >
                    Teachings
                </h1>
                <div
                    class="mx-auto mt-6 h-1 w-24 rounded-full bg-primary"
                />
                <p
                    class="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground sm:text-xl"
                >
                    Explore cultural teachings, historical knowledge, and
                    language resources passed down through generations of
                    Indigenous peoples.
                </p>
            </div>
        </section>

        <!-- Filters + grid -->
        <section class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filter row -->
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <!-- Type pills -->
                    <div class="flex flex-wrap items-center gap-2">
                        <Link
                            v-for="filter in typeFilters"
                            :key="filter.value"
                            :href="typePillUrl(filter.value)"
                            preserve-state
                            :class="[
                                'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-colors',
                                activeType === filter.value
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-secondary text-muted-foreground hover:bg-secondary/80 hover:text-foreground',
                            ]"
                        >
                            {{ filter.label }}
                        </Link>
                    </div>

                    <!-- Search -->
                    <div class="flex gap-2">
                        <Input
                            v-model="search"
                            placeholder="Search teachings..."
                            class="w-full sm:w-64"
                            @keyup.enter="applySearch"
                        />
                        <Button
                            variant="outline"
                            size="icon"
                            @click="applySearch"
                        >
                            <Search class="size-4" />
                            <span class="sr-only">Search</span>
                        </Button>
                    </div>
                </div>

                <!-- Teaching cards grid -->
                <div
                    v-if="teachings.data.length > 0"
                    class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        v-for="teaching in teachings.data"
                        :key="teaching.id"
                        :href="`/teachings/${teaching.slug}`"
                        class="block"
                    >
                        <Card
                            class="group h-full border-t-4 border-t-primary transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                        >
                            <CardHeader>
                                <div
                                    class="mb-3 flex size-12 items-center justify-center rounded-full bg-primary/10"
                                >
                                    <BookOpen class="size-6 text-primary" />
                                </div>
                                <CardTitle class="font-serif text-xl">
                                    {{ teaching.title }}
                                </CardTitle>
                                <div class="flex flex-wrap items-center gap-2">
                                    <Badge variant="secondary">
                                        {{ teaching.type }}
                                    </Badge>
                                    <Link
                                        v-if="teaching.cultural_group"
                                        :href="`/culture/${teaching.cultural_group.slug}`"
                                        class="text-xs text-sage transition-colors hover:text-sage/80 hover:underline"
                                        @click.stop
                                    >
                                        {{
                                            teaching.cultural_group.name
                                        }}
                                    </Link>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <p
                                    class="text-sm leading-relaxed text-muted-foreground"
                                >
                                    {{ truncate(teaching.content) }}
                                </p>
                            </CardContent>
                        </Card>
                    </Link>
                </div>

                <!-- Empty state -->
                <div
                    v-else
                    class="mt-8 text-center text-muted-foreground"
                >
                    <BookOpen
                        class="mx-auto mb-4 size-12 text-muted-foreground/50"
                    />
                    <p>No teachings found. Try adjusting your filters.</p>
                </div>

                <!-- Pagination -->
                <div
                    v-if="teachings.last_page > 1"
                    class="mt-10 flex items-center justify-between"
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
        </section>
    </PublicLayout>
</template>
