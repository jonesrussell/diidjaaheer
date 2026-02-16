<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { BookOpen, Calendar, ExternalLink, Globe, History, Languages, MapPin, Users } from 'lucide-vue-next';

interface NewsItem {
    title?: string;
    category?: string;
    source?: string;
    time?: string;
}

interface PowwowEvent {
    day: string;
    month: string;
    name: string;
    location: string;
}

interface CommunityGroup {
    name: string;
    type: string;
    region: string;
    url?: string;
}

interface Props {
    latestNews: NewsItem[];
    powwowEvents: PowwowEvent[];
    teachings: unknown[];
    groups: CommunityGroup[];
    languageResources: unknown[];
    featuredStories: unknown[];
}

defineProps<Props>();

const placeholderNews = [
    { title: 'Treaty Rights and Resource Management', category: 'Politics', source: 'Indigenous Wire', time: '2 hours ago' },
    { title: 'Powwow Trail Returns to Northern Ontario', category: 'Culture', source: 'Anishinaabe Today', time: '5 hours ago' },
    { title: 'Language Revitalization Program Expands', category: 'Education', source: 'First Nations News', time: '8 hours ago' },
];

const placeholderEvents = [
    { day: '15', month: 'Mar', name: 'Spring Traditional Powwow', location: 'Sault Ste. Marie, ON' },
    { day: '22', month: 'Apr', name: 'Gathering of Nations', location: 'Albuquerque, NM' },
    { day: '08', month: 'May', name: 'Anishinaabe Cultural Festival', location: 'Thunder Bay, ON' },
];

const teachingCards = [
    { title: 'Culture', description: 'Traditions, ceremonies, and the Anishinaabe way of life passed down through generations.', icon: 'BookOpen', borderColor: 'border-primary' },
    { title: 'History', description: 'The rich history of the Anishinaabe people across Turtle Island.', icon: 'History', borderColor: 'border-accent' },
    { title: 'Language', description: 'Anishinaabemowin language resources and learning materials.', icon: 'Languages', borderColor: 'border-[#5a7a5e] dark:border-[#6b9470]' },
];

const sectionAccentColors = ['bg-primary', 'bg-accent', 'bg-[#5a7a5e] dark:bg-[#6b9470]'];
</script>

<template>
    <PublicLayout title="Diidjaaheer — Anishinaabe News, Culture & Community">
        <Head title="Diidjaaheer — Anishinaabe News, Culture & Community" />

        <!-- Hero Section -->
        <section class="noise-texture overflow-hidden py-16 sm:py-20 md:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="font-serif tracking-tight text-5xl sm:text-6xl md:text-7xl lg:text-8xl">
                    <span class="block animate-fade-in-up" style="animation-delay: 0s">Anishinaabe</span>
                    <span class="block animate-fade-in-up" style="animation-delay: 0.15s">News, Culture &</span>
                    <span class="block animate-fade-in-up text-primary" style="animation-delay: 0.3s">Community</span>
                </h1>
                <div class="mx-auto mt-6 h-1 w-24 rounded-full bg-primary animate-fade-in-up" style="animation-delay: 0.45s" />
                <p class="mx-auto mt-6 max-w-2xl text-lg sm:text-xl text-muted-foreground animate-fade-in-up" style="animation-delay: 0.55s">
                    Your gathering place for Anishinaabe news, powwow events, cultural teachings, and community connections across Turtle Island.
                </p>
                <!-- Category pills -->
                <div class="mt-8 flex flex-wrap items-center justify-center gap-2 animate-fade-in-up" style="animation-delay: 0.65s">
                    <a v-for="pill in [
                        { label: 'News', href: '#news', color: 'bg-primary/10 text-primary hover:bg-primary/20' },
                        { label: 'Powwows', href: '#events', color: 'bg-accent/10 text-accent-foreground hover:bg-accent/20' },
                        { label: 'Teachings', href: '#teachings', color: 'bg-primary/10 text-primary hover:bg-primary/20' },
                        { label: 'Community', href: '#community', color: 'bg-[#5a7a5e]/10 text-[#5a7a5e] dark:text-[#6b9470] hover:bg-[#5a7a5e]/20' },
                        { label: 'Language', href: '#language', color: 'bg-[#5a7a5e]/10 text-[#5a7a5e] dark:text-[#6b9470] hover:bg-[#5a7a5e]/20' },
                    ]" :key="pill.label" :href="pill.href"
                       :class="['inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium transition-colors', pill.color]">
                        {{ pill.label }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Latest News -->
        <section id="news" class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3 animate-slide-in-left">
                    <div class="h-8 w-1 rounded-full bg-accent" />
                    <h2 class="font-serif text-3xl sm:text-4xl">Latest News</h2>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="(item, i) in (latestNews.length ? latestNews : placeholderNews)" :key="i"
                          class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                          :class="{ 'sm:col-span-2 sm:row-span-2': i === 0 }">
                        <!-- Gradient placeholder for image area -->
                        <div class="relative overflow-hidden rounded-t-[var(--radius)]"
                             :class="i === 0 ? 'h-48 sm:h-64' : 'h-32 sm:h-40'">
                            <div class="absolute inset-0" :class="[
                                i === 0 ? 'bg-gradient-to-br from-primary/30 via-accent/20 to-primary/10' :
                                i === 1 ? 'bg-gradient-to-br from-accent/25 via-primary/15 to-accent/10' :
                                'bg-gradient-to-br from-[#5a7a5e]/25 via-accent/15 to-primary/10'
                            ]" />
                        </div>
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <Badge variant="outline" class="text-xs">{{ item.category || 'News' }}</Badge>
                                <span class="text-xs text-muted-foreground">{{ item.time || '' }}</span>
                            </div>
                            <CardTitle :class="i === 0 ? 'text-xl sm:text-2xl font-serif' : 'text-base'">
                                {{ item.title || 'Article Title' }}
                            </CardTitle>
                            <CardDescription v-if="item.source">
                                {{ item.source }}
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Powwow Calendar -->
        <section id="events" class="bg-secondary/50 py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3 animate-slide-in-left">
                    <div class="h-8 w-1 rounded-full bg-accent" />
                    <h2 class="font-serif text-3xl sm:text-4xl">Powwow Calendar</h2>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="(event, i) in (powwowEvents.length ? powwowEvents : placeholderEvents)" :key="i"
                          class="border-l-4 border-l-accent transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <CardHeader>
                            <div class="flex items-start gap-4">
                                <div class="text-center">
                                    <div class="font-serif text-4xl text-primary">{{ event.day }}</div>
                                    <div class="text-sm font-medium uppercase text-muted-foreground">{{ event.month }}</div>
                                </div>
                                <div class="flex-1">
                                    <CardTitle class="text-base">{{ event.name }}</CardTitle>
                                    <CardDescription class="mt-1 flex items-center gap-1">
                                        <MapPin class="size-3.5" />
                                        {{ event.location }}
                                    </CardDescription>
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
                <div class="flex items-center gap-3 animate-slide-in-left">
                    <div class="h-8 w-1 rounded-full bg-primary" />
                    <h2 class="font-serif text-3xl sm:text-4xl">Cultural Teachings</h2>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="(card, i) in teachingCards" :key="i"
                          :class="['border-t-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg', card.borderColor]">
                        <CardHeader>
                            <div :class="['mb-3 flex size-12 items-center justify-center rounded-full', sectionAccentColors[i] + '/10']">
                                <BookOpen v-if="card.icon === 'BookOpen'" class="size-6 text-primary" />
                                <History v-if="card.icon === 'History'" class="size-6 text-accent-foreground" />
                                <Languages v-if="card.icon === 'Languages'" class="size-6 text-[#5a7a5e] dark:text-[#6b9470]" />
                            </div>
                            <CardTitle class="font-serif text-xl">{{ card.title }}</CardTitle>
                            <CardDescription>{{ card.description }}</CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Community Groups -->
        <section id="community" class="bg-secondary/50 py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3 animate-slide-in-left">
                    <div class="h-8 w-1 rounded-full bg-[#5a7a5e] dark:bg-[#6b9470]" />
                    <h2 class="font-serif text-3xl sm:text-4xl">Community Groups</h2>
                </div>
                <p class="mt-3 text-muted-foreground">Online and offline Anishinaabe community organizations and groups.</p>
                <div class="mt-8 grid gap-6 sm:grid-cols-2">
                    <Card v-for="(group, i) in (groups.length ? groups : [
                        { name: 'Anishinaabe Cultural Network', type: 'Cultural', region: 'Great Lakes', url: '#' },
                        { name: 'Ojibwe Language Society', type: 'Language', region: 'Ontario / Manitoba', url: '#' },
                    ])" :key="i"
                          class="border-l-4 border-l-[#5a7a5e] dark:border-l-[#6b9470] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div>
                                    <CardTitle class="text-base">{{ group.name }}</CardTitle>
                                    <CardDescription class="mt-1">
                                        <Badge variant="secondary" class="mr-2 text-xs">{{ group.type }}</Badge>
                                        {{ group.region }}
                                    </CardDescription>
                                </div>
                                <a v-if="group.url && group.url !== '#'" :href="group.url" target="_blank" rel="noopener"
                                   class="text-muted-foreground transition-colors hover:text-foreground">
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
                <div class="flex items-center gap-3 animate-slide-in-left">
                    <div class="h-8 w-1 rounded-full bg-[#5a7a5e] dark:bg-[#6b9470]" />
                    <h2 class="font-serif text-3xl sm:text-4xl">Language Resources</h2>
                </div>
                <Card class="mt-8 bg-[#5a7a5e]/10 dark:bg-[#6b9470]/10 border-[#5a7a5e]/20 dark:border-[#6b9470]/20">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="flex size-12 items-center justify-center rounded-full bg-[#5a7a5e]/10 dark:bg-[#6b9470]/10">
                                <Globe class="size-6 text-[#5a7a5e] dark:text-[#6b9470]" />
                            </div>
                            <div>
                                <CardTitle class="font-serif text-xl">Ojibwe / Anishinaabemowin</CardTitle>
                                <CardDescription>Language learning resources and revitalization efforts</CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">
                            Explore resources for learning Anishinaabemowin, from beginner lessons to advanced conversation practice.
                            Supporting language revitalization across communities.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </section>
    </PublicLayout>
</template>
