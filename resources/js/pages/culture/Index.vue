<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { TreePine } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface CulturalGroupChild {
    id: number;
    parent_id: number;
    name: string;
    slug: string;
    depth_type: string;
}

interface CulturalGroup {
    id: number;
    name: string;
    slug: string;
    depth_type: string;
    description: string | null;
    children_count: number;
    children: CulturalGroupChild[];
}

interface Props {
    groups: CulturalGroup[];
}

defineProps<Props>();
</script>

<template>
    <PublicLayout title="Indigenous Culture — Diidjaaheer">
        <!-- Hero intro -->
        <section class="noise-texture py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h1
                    class="font-serif text-5xl tracking-tight sm:text-6xl md:text-7xl"
                >
                    Indigenous Culture
                </h1>
                <div class="mx-auto mt-6 h-1 w-24 rounded-full bg-primary" />
                <p
                    class="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground sm:text-xl"
                >
                    Explore the cultural heritage of Indigenous peoples across
                    Turtle Island. Discover nations, language families, and
                    communities preserving traditions for future generations.
                </p>
            </div>
        </section>

        <!-- Groups grid -->
        <section class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1 rounded-full bg-sage" />
                    <h2 class="font-serif text-3xl sm:text-4xl">
                        Cultural Groups
                    </h2>
                </div>

                <div
                    v-if="groups.length > 0"
                    class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        v-for="group in groups"
                        :key="group.id"
                        :href="`/culture/${group.slug}`"
                        class="block"
                    >
                        <Card
                            class="group h-full border-t-4 border-t-sage transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                        >
                            <CardHeader>
                                <div
                                    class="mb-3 flex size-12 items-center justify-center rounded-full bg-sage/10"
                                >
                                    <TreePine class="size-6 text-sage" />
                                </div>
                                <CardTitle class="font-serif text-xl">
                                    {{ group.name }}
                                </CardTitle>
                                <CardDescription v-if="group.description">
                                    {{ group.description }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <p class="mb-3 text-sm text-muted-foreground">
                                    {{ group.children_count }} sub-group{{
                                        group.children_count !== 1 ? 's' : ''
                                    }}
                                </p>
                                <div
                                    v-if="
                                        group.children &&
                                        group.children.length > 0
                                    "
                                    class="flex flex-wrap gap-1.5"
                                >
                                    <Link
                                        v-for="child in group.children"
                                        :key="child.id"
                                        :href="`/culture/${child.slug}`"
                                        @click.stop
                                    >
                                        <Badge
                                            variant="secondary"
                                            class="text-xs transition-colors hover:bg-sage/20"
                                        >
                                            {{ child.name }}
                                        </Badge>
                                    </Link>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>

                <div v-else class="mt-8 text-center text-muted-foreground">
                    <TreePine
                        class="mx-auto mb-4 size-12 text-muted-foreground/50"
                    />
                    <p>Cultural groups are being gathered. Check back soon.</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
