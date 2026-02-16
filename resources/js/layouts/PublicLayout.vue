<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { dashboard, login, register } from '@/routes';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { Menu } from 'lucide-vue-next';

type Props = {
    title?: string;
};

withDefaults(defineProps<Props>(), {
    title: 'Diidjaaheer',
});

const mobileOpen = ref(false);

const navLinks = [
    { label: 'News', href: '#news' },
    { label: 'Events', href: '#events' },
    { label: 'Teachings', href: '#teachings' },
    { label: 'Community', href: '#community' },
    { label: 'Language', href: '#language' },
];

const footerExplore = [
    { label: 'News', href: '#news' },
    { label: 'Events', href: '#events' },
    { label: 'Teachings', href: '#teachings' },
];

const footerCommunity = [
    { label: 'Groups', href: '#community' },
    { label: 'Language', href: '#language' },
    { label: 'About', href: '#about' },
];
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Head :title="title" />

        <!-- Header -->
        <header
            class="sticky top-0 z-50 w-full border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <!-- Left: Wordmark -->
                <Link
                    href="/"
                    class="font-serif text-2xl font-normal tracking-tight text-primary"
                >
                    Diidjaaheer
                </Link>

                <!-- Center: Desktop nav -->
                <nav class="hidden items-center gap-1 lg:flex">
                    <a
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                    >
                        {{ link.label }}
                    </a>
                </nav>

                <!-- Right: Auth + mobile menu -->
                <div class="flex items-center gap-2">
                    <!-- Auth buttons (desktop) -->
                    <template v-if="$page.props.auth?.user">
                        <Link :href="dashboard()" class="hidden lg:inline-flex">
                            <Button variant="ghost" size="sm">
                                Dashboard
                            </Button>
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="login()" class="hidden lg:inline-flex">
                            <Button variant="ghost" size="sm">
                                Log in
                            </Button>
                        </Link>
                        <Link :href="register()" class="hidden lg:inline-flex">
                            <Button size="sm">
                                Register
                            </Button>
                        </Link>
                    </template>

                    <!-- Mobile menu trigger -->
                    <Sheet v-model:open="mobileOpen">
                        <SheetTrigger as-child>
                            <Button
                                variant="ghost"
                                size="icon-sm"
                                class="lg:hidden"
                            >
                                <Menu class="size-5" />
                                <span class="sr-only">Open menu</span>
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="right" class="w-72">
                            <SheetHeader>
                                <SheetTitle>Menu</SheetTitle>
                            </SheetHeader>
                            <nav class="mt-4 flex flex-col gap-2">
                                <a
                                    v-for="link in navLinks"
                                    :key="link.href"
                                    :href="link.href"
                                    class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                                    @click="mobileOpen = false"
                                >
                                    {{ link.label }}
                                </a>
                            </nav>
                            <div class="my-4 border-t border-border" />
                            <div class="flex flex-col gap-2">
                                <template v-if="$page.props.auth?.user">
                                    <Link
                                        :href="dashboard()"
                                        @click="mobileOpen = false"
                                    >
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="w-full justify-start"
                                        >
                                            Dashboard
                                        </Button>
                                    </Link>
                                </template>
                                <template v-else>
                                    <Link
                                        :href="login()"
                                        @click="mobileOpen = false"
                                    >
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="w-full justify-start"
                                        >
                                            Log in
                                        </Button>
                                    </Link>
                                    <Link
                                        :href="register()"
                                        @click="mobileOpen = false"
                                    >
                                        <Button
                                            size="sm"
                                            class="w-full"
                                        >
                                            Register
                                        </Button>
                                    </Link>
                                </template>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t-2 border-primary bg-foreground text-background">
            <div
                class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8"
            >
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <!-- Col 1: Brand -->
                    <div>
                        <div class="font-serif text-xl text-background">
                            Diidjaaheer
                        </div>
                        <p class="mt-2 text-sm text-background/60">
                            North American Anishinaabe news, culture &amp;
                            community
                        </p>
                    </div>

                    <!-- Col 2: Explore -->
                    <div>
                        <h3
                            class="mb-3 font-medium text-background/90"
                        >
                            Explore
                        </h3>
                        <ul class="space-y-2">
                            <li
                                v-for="link in footerExplore"
                                :key="link.href"
                            >
                                <a
                                    :href="link.href"
                                    class="text-sm text-background/70 transition-colors hover:text-background"
                                >
                                    {{ link.label }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Col 3: Community -->
                    <div>
                        <h3
                            class="mb-3 font-medium text-background/90"
                        >
                            Community
                        </h3>
                        <ul class="space-y-2">
                            <li
                                v-for="link in footerCommunity"
                                :key="link.href"
                            >
                                <a
                                    :href="link.href"
                                    class="text-sm text-background/70 transition-colors hover:text-background"
                                >
                                    {{ link.label }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom bar -->
                <div
                    class="mt-8 border-t border-background/10 pt-8 text-center text-sm text-background/50"
                >
                    &copy; 2026 Diidjaaheer. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
