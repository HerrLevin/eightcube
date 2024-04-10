<script lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {faCircleInfo, faLocationPin} from '@fortawesome/free-solid-svg-icons'

export default {
    components: {
        FontAwesomeIcon,
        AuthenticatedLayout,
        Head,
    },
    setup() {
        const faHouse = faLocationPin;
        return { faHouse, faCircleInfo };
    },
    data() {
        return {
            venues: [] as any[],
        }
    },
    methods: {
        test() {
            fetch('/api/nearby?latitude=49.009925117955575&longitude=8.42856106114697')
                .then(response => response.json())
                .then((data) => {
                    this.venues = data.data;
                });
        }
    },
    mounted() {
        this.test();
    }
};
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Where are you?</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-for="venue in venues"
                    class="bg-white grid grid-cols-12 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-b border-b-gray-500">
                    <!-- icon -->
                    <div class="col-span-2 text-gray-900 p-4 text-center dark:text-gray-100">
                        <font-awesome-icon :icon="faHouse" />
                    </div>
                    <div class="col-span-9 py-4 text-gray-900 dark:text-gray-100">{{ venue.name }}</div>
                    <div class="py-4 text-gray-900 text-center dark:text-gray-100">
                        <font-awesome-icon :icon="faCircleInfo" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
