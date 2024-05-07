<script lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";

export default {
    components: {
        AuthenticatedLayout,
        GuestLayout,
        ApplicationLogo,
        Link,
    },
    computed: {
        authenticated() {
            return this.$page.props.auth.user !== null;
        }
    },
}
</script>

<template>
    <AuthenticatedLayout v-if="authenticated">
        <slot />
    </AuthenticatedLayout>

    <div v-else class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3 w-full max-w-2xl px-6 lg:max-w-7xl">
            <div class="flex lg:justify-center lg:col-start-2">
                <Link href="/">
                    <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" color="#33339C"/>
                </Link>
            </div>
            <nav class="-mx-3 flex flex-1 justify-end">
                <Link
                    v-if="!route().current('login')"
                    :href="route('login')"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Log in
                </Link>

                <Link
                    v-if="!route().current('register')"
                    :href="route('register')"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Register
                </Link>
            </nav>
        </header>
        <main class="w-full mt-4">
            <slot />
        </main>
    </div>
</template>
