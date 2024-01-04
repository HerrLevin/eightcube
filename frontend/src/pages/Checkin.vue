<script>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import axios from "@/lib/axios";

export default {
    components: {
        AuthenticatedLayout,
    },
    data() {
        return {
            venues: [],
            latitude: null,
            longitude: null,
        }
    },
    methods: {
        getGeolocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    this.latitude = position.coords.latitude;
                    this.longitude = position.coords.longitude;

                    this.getVenues();
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        },
        getVenues() {
            axios.get('/api/venues?latitude=' + this.latitude + '&longitude=' + this.longitude)
                .then(response => {
                    this.venues = response.data
                })
                .catch(error => {
                    console.log(error)
                })
        },
    },
    mounted() {
        this.getGeolocation();
    }
}

</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Checkin
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div
                        class="p-2 bg-white border-b border-gray-200"
                        v-for="venue in venues"
                        :key="venue.id"
                    >
                        {{ venue.name }} <span class="text-xs text-gray-500">{{ venue.amenity }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
