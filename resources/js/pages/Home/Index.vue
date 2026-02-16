<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface Props {
    latestNews: unknown[];
    powwowEvents: unknown[];
    teachings: unknown[];
    groups: unknown[];
    languageResources: unknown[];
    featuredStories: unknown[];
}

defineProps<Props>();
</script>

<template>
    <PublicLayout title="Diidjaaheer — Anishinaabe News, Culture & Community">
        <Head title="Diidjaaheer — Anishinaabe News, Culture & Community" />

        <div class="container space-y-16 px-4 py-12">
            <!-- Hero -->
            <section class="space-y-4 text-center">
                <h1
                    class="text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl"
                >
                    Diidjaaheer
                </h1>
                <p
                    class="mx-auto max-w-2xl text-lg text-muted-foreground sm:text-xl"
                >
                    North American Anishinaabe news, culture, history, powwows,
                    and community
                </p>
            </section>

            <!-- Latest Anishinaabe News -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold tracking-tight">
                    Latest Anishinaabe News
                </h2>
                <div
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    :class="{ 'opacity-60': latestNews.length === 0 }"
                >
                    <Card
                        v-for="(item, i) in latestNews.length
                            ? latestNews
                            : [{}, {}, {}]"
                        :key="i"
                    >
                        <CardHeader>
                            <CardTitle>Placeholder</CardTitle>
                            <CardDescription>
                                News content will appear here
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </section>

            <!-- Powwow Calendar -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold tracking-tight">
                    Powwow Calendar
                </h2>
                <Card :class="{ 'opacity-60': powwowEvents.length === 0 }">
                    <CardHeader>
                        <CardTitle>Upcoming Powwows</CardTitle>
                        <CardDescription>
                            Powwow events will be listed here
                        </CardDescription>
                    </CardHeader>
                    <CardContent v-if="powwowEvents.length === 0">
                        <p class="text-muted-foreground">
                            No upcoming events — check back soon
                        </p>
                    </CardContent>
                </Card>
            </section>

            <!-- Cultural Teachings -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold tracking-tight">
                    Cultural Teachings
                </h2>
                <div
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    :class="{ 'opacity-60': teachings.length === 0 }"
                >
                    <Card>
                        <CardHeader>
                            <CardTitle>Culture</CardTitle>
                            <CardDescription>
                                Cultural teachings will appear here
                            </CardDescription>
                        </CardHeader>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>History</CardTitle>
                            <CardDescription>
                                Historical content will appear here
                            </CardDescription>
                        </CardHeader>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Language</CardTitle>
                            <CardDescription>
                                Language resources will appear here
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </section>

            <!-- Community Groups -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold tracking-tight">
                    Community Groups
                </h2>
                <p class="text-muted-foreground">
                    Online and offline Anishinaabe community organizations and
                    groups
                </p>
                <Card :class="{ 'opacity-60': groups.length === 0 }">
                    <CardHeader>
                        <CardTitle>Groups</CardTitle>
                        <CardDescription>
                            Community groups will be listed here
                        </CardDescription>
                    </CardHeader>
                </Card>
            </section>

            <!-- Language Resources -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold tracking-tight">
                    Language Resources
                </h2>
                <Card :class="{ 'opacity-60': languageResources.length === 0 }">
                    <CardHeader>
                        <CardTitle>Ojibwe / Anishinaabemowin</CardTitle>
                        <CardDescription>
                            Language learning resources will appear here
                        </CardDescription>
                    </CardHeader>
                </Card>
            </section>

            <!-- Featured Stories -->
            <section class="space-y-4">
                <h2 class="text-2xl font-semibold tracking-tight">
                    Featured Stories
                </h2>
                <div
                    class="grid gap-6 sm:grid-cols-2"
                    :class="{ 'opacity-60': featuredStories.length === 0 }"
                >
                    <Card>
                        <CardHeader>
                            <CardTitle>Featured</CardTitle>
                            <CardDescription>
                                Featured stories will appear here
                            </CardDescription>
                        </CardHeader>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle>Featured</CardTitle>
                            <CardDescription>
                                Featured stories will appear here
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
