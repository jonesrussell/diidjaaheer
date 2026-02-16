<script setup lang="ts">
import {
    BookOpen,
    ExternalLink,
    Globe,
    History,
    Languages,
    MapPin,
} from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface NewsItem {
    id: number;
    title?: string;
    category?: string;
    source?: string;
    time?: string;
    url?: string;
    image_url?: string;
    excerpt?: string;
}

interface PowwowEvent {
    id: number;
    day: string;
    month: string;
    name: string;
    location: string;
    type?: string;
}

interface CommunityGroup {
    id: number;
    name: string;
    type: string;
    region: string;
    url?: string;
}

interface Props {
    latestNews: NewsItem[];
    powwowEvents: PowwowEvent[];
    groups: CommunityGroup[];
}

defineProps<Props>();

const placeholderNews: NewsItem[] = [
    {
        id: 0,
        title: 'Treaty Rights and Resource Management',
        category: 'Politics',
        source: 'Indigenous Wire',
        time: '2 hours ago',
    },
    {
        id: 0,
        title: 'Powwow Trail Returns to Northern Ontario',
        category: 'Culture',
        source: 'Anishinaabe Today',
        time: '5 hours ago',
    },
    {
        id: 0,
        title: 'Language Revitalization Program Expands',
        category: 'Education',
        source: 'First Nations News',
        time: '8 hours ago',
    },
];

const placeholderEvents: PowwowEvent[] = [
    {
        id: 0,
        day: '15',
        month: 'Mar',
        name: 'Spring Traditional Powwow',
        location: 'Sault Ste. Marie, ON',
    },
    {
        id: 0,
        day: '22',
        month: 'Apr',
        name: 'Gathering of Nations',
        location: 'Albuquerque, NM',
    },
    {
        id: 0,
        day: '08',
        month: 'May',
        name: 'Anishinaabe Cultural Festival',
        location: 'Thunder Bay, ON',
    },
];

const teachingCards = [
    {
        title: 'Culture',
        description:
            'Traditions, ceremonies, and the Anishinaabe way of life passed down through generations.',
        icon: 'BookOpen',
        borderColor: 'border-primary',
    },
    {
        title: 'History',
        description:
            'The rich history of the Anishinaabe people across Turtle Island.',
        icon: 'History',
        borderColor: 'border-accent',
    },
    {
        title: 'Language',
        description:
            'Anishinaabemowin language resources and learning materials.',
        icon: 'Languages',
        borderColor: 'border-sage',
    },
];

const sectionAccentColors = ['bg-primary', 'bg-accent', 'bg-sage'];
</script>

<template>
    <PublicLayout title="Diidjaaheer — Anishinaabe News, Culture & Community">
        <!-- Hero Section -->
        <section class="noise-texture overflow-hidden py-16 sm:py-20 md:py-28">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h1
                    class="font-serif text-5xl tracking-tight sm:text-6xl md:text-7xl lg:text-8xl"
                >
                    <span
                        class="animate-fade-in-up block"
                        style="animation-delay: 0s"
                        >Anishinaabe</span
                    >
                    <span
                        class="animate-fade-in-up block"
                        style="animation-delay: 0.15s"
                        >News, Culture &</span
                    >
                    <span
                        class="animate-fade-in-up block text-primary"
                        style="animation-delay: 0.3s"
                        >Community</span
                    >
                </h1>
                <div
                    class="animate-fade-in-up mx-auto mt-6 h-1 w-24 rounded-full bg-primary"
                    style="animation-delay: 0.45s"
                />
                <p
                    class="animate-fade-in-up mx-auto mt-6 max-w-2xl text-lg text-muted-foreground sm:text-xl"
                    style="animation-delay: 0.55s"
                >
                    Your gathering place for Anishinaabe news, powwow events,
                    cultural teachings, and community connections across Turtle
                    Island.
                </p>
                <!-- Category pills -->
                <div
                    class="animate-fade-in-up mt-8 flex flex-wrap items-center justify-center gap-2"
                    style="animation-delay: 0.65s"
                >
                    <a
                        v-for="pill in [
                            {
                                label: 'News',
                                href: '#news',
                                color: 'bg-primary/10 text-primary hover:bg-primary/20',
                            },
                            {
                                label: 'Powwows',
                                href: '#events',
                                color: 'bg-accent/10 text-accent hover:bg-accent/20',
                            },
                            {
                                label: 'Teachings',
                                href: '#teachings',
                                color: 'bg-primary/10 text-primary hover:bg-primary/20',
                            },
                            {
                                label: 'Community',
                                href: '#community',
                                color: 'bg-sage/10 text-sage hover:bg-sage/20',
                            },
                            {
                                label: 'Language',
                                href: '#language',
                                color: 'bg-sage/10 text-sage hover:bg-sage/20',
                            },
                        ]"
                        :key="pill.label"
                        :href="pill.href"
                        :class="[
                            'inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-colors',
                            pill.color,
                        ]"
                    >
                        {{ pill.label }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Latest News -->
        <section id="news" class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="animate-slide-in-left flex items-center gap-3">
                    <div class="h-8 w-1 rounded-full bg-accent" />
                    <h2 class="font-serif text-3xl sm:text-4xl">Latest News</h2>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <component
                        :is="item.url ? 'a' : 'div'"
                        v-for="(item, i) in latestNews.length
                            ? latestNews
                            : placeholderNews"
                        :key="item.id ?? i"
                        v-bind="
                            item.url
                                ? {
                                      href: item.url,
                                      target: '_blank',
                                      rel: 'noopener',
                                  }
                                : {}
                        "
                        :class="{ 'sm:col-span-2 sm:row-span-2': i === 0 }"
                    >
                        <Card
                            class="group h-full transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                        >
                            <!-- Image area with gradient fallback -->
                            <div
                                class="relative overflow-hidden rounded-t-[var(--radius)]"
                                :class="
                                    i === 0 ? 'h-48 sm:h-64' : 'h-32 sm:h-40'
                                "
                            >
                                <img
                                    v-if="item.image_url"
                                    :src="item.image_url"
                                    :alt="item.title || ''"
                                    class="absolute inset-0 h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="absolute inset-0"
                                    :class="[
                                        i === 0
                                            ? 'bg-gradient-to-br from-primary/30 via-accent/20 to-primary/10'
                                            : i === 1
                                              ? 'bg-gradient-to-br from-accent/25 via-primary/15 to-accent/10'
                                              : 'bg-gradient-to-br from-sage/25 via-accent/15 to-primary/10',
                                    ]"
                                />
                            </div>
                            <CardHeader>
                                <div class="flex items-center gap-2">
                                    <Badge variant="outline" class="text-xs">{{
                                        item.category || 'News'
                                    }}</Badge>
                                    <span
                                        class="text-xs text-muted-foreground"
                                        >{{ item.time || '' }}</span
                                    >
                                </div>
                                <CardTitle
                                    :class="
                                        i === 0
                                            ? 'font-serif text-xl sm:text-2xl'
                                            : 'text-base'
                                    "
                                >
                                    {{ item.title || 'Article Title' }}
                                </CardTitle>
                                <CardDescription v-if="item.source">
                                    {{ item.source }}
                                </CardDescription>
                                <p
                                    v-if="i === 0 && item.excerpt"
                                    class="mt-2 line-clamp-3 text-sm text-muted-foreground"
                                >
                                    {{ item.excerpt }}
                                </p>
                            </CardHeader>
                        </Card>
                    </component>
                </div>
            </div>
        </section>

        <!-- Powwow Calendar -->
        <section id="events" class="bg-secondary/50 py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="animate-slide-in-left flex items-center gap-3">
                    <div class="h-8 w-1 rounded-full bg-accent" />
                    <h2 class="font-serif text-3xl sm:text-4xl">
                        Powwow Calendar
                    </h2>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="(event, i) in powwowEvents.length
                            ? powwowEvents
                            : placeholderEvents"
                        :key="event.id ?? i"
                        class="border-l-4 border-l-accent transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                    >
                        <CardHeader>
                            <div class="flex items-start gap-4">
                                <div class="text-center">
                                    <div
                                        class="font-serif text-4xl text-primary"
                                    >
                                        {{ event.day }}
                                    </div>
                                    <div
                                        class="text-sm font-medium text-muted-foreground uppercase"
                                    >
                                        {{ event.month }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <CardTitle class="text-base">{{
                                        event.name
                                    }}</CardTitle>
                                    <CardDescription
                                        class="mt-1 flex items-center gap-1"
                                    >
                                        <MapPin class="size-3.5" />
                                        {{ event.location }}
                                    </CardDescription>
                                    <Badge
                                        v-if="event.type"
                                        variant="secondary"
                                        class="mt-2 text-xs"
                                    >
                                        {{ event.type }}
                                    </Badge>
                                </div>
                            </div>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Cultural Teachings -->
        <section id="teachings" class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="animate-slide-in-left flex items-center gap-3">
                    <div class="h-8 w-1 rounded-full bg-primary" />
                    <h2 class="font-serif text-3xl sm:text-4xl">
                        Cultural Teachings
                    </h2>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="(card, i) in teachingCards"
                        :key="i"
                        :class="[
                            'border-t-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg',
                            card.borderColor,
                        ]"
                    >
                        <CardHeader>
                            <div
                                :class="[
                                    'mb-3 flex size-12 items-center justify-center rounded-full',
                                    sectionAccentColors[i] + '/10',
                                ]"
                            >
                                <BookOpen
                                    v-if="card.icon === 'BookOpen'"
                                    class="size-6 text-primary"
                                />
                                <History
                                    v-if="card.icon === 'History'"
                                    class="size-6 text-accent"
                                />
                                <Languages
                                    v-if="card.icon === 'Languages'"
                                    class="size-6 text-sage"
                                />
                            </div>
                            <CardTitle class="font-serif text-xl">{{
                                card.title
                            }}</CardTitle>
                            <CardDescription>{{
                                card.description
                            }}</CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Community Groups -->
        <section id="community" class="bg-secondary/50 py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="animate-slide-in-left flex items-center gap-3">
                    <div class="h-8 w-1 rounded-full bg-sage" />
                    <h2 class="font-serif text-3xl sm:text-4xl">
                        Community Groups
                    </h2>
                </div>
                <p class="mt-3 text-muted-foreground">
                    Online and offline Anishinaabe community organizations and
                    groups.
                </p>
                <div class="mt-8 grid gap-6 sm:grid-cols-2">
                    <Card
                        v-for="(group, i) in groups.length
                            ? groups
                            : [
                                  {
                                      id: 0,
                                      name: 'Anishinaabe Cultural Network',
                                      type: 'Cultural',
                                      region: 'Great Lakes',
                                      url: '#',
                                  },
                                  {
                                      id: 0,
                                      name: 'Ojibwe Language Society',
                                      type: 'Language',
                                      region: 'Ontario / Manitoba',
                                      url: '#',
                                  },
                              ]"
                        :key="group.id ?? i"
                        class="border-l-4 border-l-sage transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                    >
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div>
                                    <CardTitle class="text-base">{{
                                        group.name
                                    }}</CardTitle>
                                    <CardDescription class="mt-1">
                                        <Badge
                                            variant="secondary"
                                            class="mr-2 text-xs"
                                            >{{ group.type }}</Badge
                                        >
                                        {{ group.region }}
                                    </CardDescription>
                                </div>
                                <a
                                    v-if="group.url && group.url !== '#'"
                                    :href="group.url"
                                    target="_blank"
                                    rel="noopener"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    <ExternalLink class="size-4" />
                                </a>
                            </div>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Language Resources -->
        <section id="language" class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="animate-slide-in-left flex items-center gap-3">
                    <div class="h-8 w-1 rounded-full bg-sage" />
                    <h2 class="font-serif text-3xl sm:text-4xl">
                        Language Resources
                    </h2>
                </div>
                <Card class="mt-8 border-sage/20 bg-sage/10">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-12 items-center justify-center rounded-full bg-sage/10"
                            >
                                <Globe class="size-6 text-sage" />
                            </div>
                            <div>
                                <CardTitle class="font-serif text-xl"
                                    >Ojibwe / Anishinaabemowin</CardTitle
                                >
                                <CardDescription
                                    >Language learning resources and
                                    revitalization efforts</CardDescription
                                >
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">
                            Explore resources for learning Anishinaabemowin,
                            from beginner lessons to advanced conversation
                            practice. Supporting language revitalization across
                            communities.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </section>
    </PublicLayout>
</template>
