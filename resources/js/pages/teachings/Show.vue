<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
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
    teaching: Teaching;
    culturalGroup: CulturalGroup | null;
}

defineProps<Props>();
</script>

<template>
    <PublicLayout :title="`${teaching.title} — Diidjaaheer`">
        <section class="py-16 sm:py-20">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- Back link -->
                <Link
                    href="/teachings"
                    class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="size-4" />
                    Back to Teachings
                </Link>

                <!-- Cultural group breadcrumb -->
                <div v-if="culturalGroup" class="mt-4">
                    <Link
                        :href="`/culture/${culturalGroup.slug}`"
                        class="text-sm text-sage transition-colors hover:text-sage/80 hover:underline"
                    >
                        {{ culturalGroup.name }}
                    </Link>
                </div>

                <!-- Type badge -->
                <div class="mt-4">
                    <Badge variant="secondary">
                        {{ teaching.type }}
                    </Badge>
                </div>

                <!-- Title -->
                <h1 class="mt-4 font-serif text-4xl tracking-tight sm:text-5xl">
                    {{ teaching.title }}
                </h1>

                <div class="mt-6 h-1 w-16 rounded-full bg-primary" />

                <!-- Content -->
                <article class="prose prose-lg mt-8 max-w-none text-foreground">
                    <p
                        v-for="(paragraph, i) in teaching.content.split('\n')"
                        :key="i"
                        class="mb-4 leading-relaxed text-muted-foreground"
                    >
                        {{ paragraph }}
                    </p>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>
