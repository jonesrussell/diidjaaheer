<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';

type Props = {
    title?: string;
};

withDefaults(defineProps<Props>(), {
    title: 'Diidjaaheer',
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <Head :title="title" />
        <header
            class="sticky top-0 z-50 w-full border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
        >
            <div class="container flex h-16 items-center justify-between px-4">
                <Link href="/" class="flex items-center gap-2 font-semibold">
                    Diidjaaheer
                </Link>
                <nav class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="dashboard()"
                        class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="true"
                            :href="register()"
                            class="rounded-md border border-border px-4 py-2 text-sm font-medium transition-colors hover:bg-accent"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </div>
        </header>
        <main class="flex-1">
            <slot />
        </main>
        <footer class="border-t border-border py-8">
            <div class="container px-4 text-center text-sm text-muted-foreground">
                Diidjaaheer — North American Anishinaabe community, news, and
                culture
            </div>
        </footer>
    </div>
</template>
