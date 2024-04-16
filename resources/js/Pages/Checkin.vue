<script lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {faCircleInfo, faLocationPin} from '@fortawesome/free-solid-svg-icons'
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Modal from "@/Components/Modal.vue";
import {Status, Venue} from "@/types/venue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextBox from "@/Components/TextBox.vue";
import axios from "axios";
import Loading from "@/Components/Loading.vue";

export default {
    components: {
        Loading,
        TextBox,
        PrimaryButton,
        Modal, TextInput, InputLabel, DangerButton, SecondaryButton, InputError,
        FontAwesomeIcon,
        AuthenticatedLayout,
        Head,
    },
    remember: {
        data: ['status'],
        key: 'createdStatus',
    },
    setup() {
        const faHouse = faLocationPin;
        return {faHouse, faCircleInfo};
    },
    data() {
        return {
            venues: [] as Venue[],
            filteredVenues: [] as Venue[],
            isShowTagModal: false as boolean,
            isShowCheckinModal: false as boolean,
            selectedVenue: null as Venue | null,
            checkinText: "" as string,
            createdStatus: null as Status | null,
            latitude: null as number | null,
            longitude: null as number | null,
            loading: false as boolean,
            positionError: false as boolean,
            search: "" as string,
        }
    },
    methods: {
        async locate() {
            this.positionError = false;
            const pos = new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject);
            });

            pos.then((position: any) => {
                this.latitude = position.coords.latitude;
                this.longitude = position.coords.longitude;

                this.fetchVenues();
            }).catch(() => {
                this.positionError = true;
            });
        },
        fetchVenues() {
            this.loading = true;
            this.search = "";
            axios.get('/api/nearby', {
                params: {
                    latitude: this.latitude,
                    longitude: this.longitude,
                }
            }).then(response => {
                this.loading = false;
                this.venues = response.data.data;
                this.filteredVenues = this.venues;
            }).catch(() => this.loading = false);
        },
        checkIn() {
            axios.post('/api/statuses', {
                venue_id: this.selectedVenue?.id,
                body: this.checkinText,
            }).then(response => {
                this.createdStatus = response.data.data;
                //save status to local storage
                //Todo: change to vuex
                localStorage.setItem('createdStatus', JSON.stringify(this.createdStatus));
                router.visit('/dashboard');
            }).catch(
                // Todo: handle error
            );
        },
        hideTagModal() {
            this.isShowTagModal = false;
        },
        showCheckinModal(venue: Venue) {
            this.selectedVenue = venue;
            this.isShowCheckinModal = true;
        },
        hideCheckinModal() {
            this.isShowCheckinModal = false;
        },
        showInfo(venue: any) {
            this.selectedVenue = venue;
            this.isShowTagModal = true;
        },
        filterVenues() {
            if (this.search.length > 0) {
                this.filteredVenues = this.venues.filter(venue => venue.name.toLowerCase().includes(this.search.toLowerCase()));
            } else {
                this.fetchVenues();
            }
        }
    },
    watch: {
        search() {
            this.filterVenues();
        }
    },
    mounted() {
        this.locate();
    }
};
</script>

<template>
    <Head title="Checkin"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Where are you?</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <InputError v-if="positionError" message="Position could not be found"/>
                <Loading v-if="loading"/>
                <InputError v-else-if="filteredVenues.length == 0" message="No venues found for current location"/>
                <template v-else>
                    <div
                        class="bg-white md:mt-2 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg max-md:border-b border-b-gray-500">
                        <input type="text" class="w-full" v-model="search" placeholder="Search for a venue"/>
                    </div>
                    <div
                        v-for="venue in filteredVenues"
                        @click="showCheckinModal(venue)"
                        class="cursor-pointer bg-white grid grid-cols-12 md:mt-2 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg max-md:border-b border-b-gray-500">
                        <!-- icon -->
                        <div class="col-span-2 text-gray-900 p-4 text-center dark:text-gray-100">
                            <font-awesome-icon :icon="faHouse"/>
                        </div>
                        <div class="col-span-9 py-4 text-gray-900 dark:text-gray-100">
                            <p>{{ venue.name }}</p>
                            <p class="text-xs text-gray-400">{{ venue.distance }} m</p>
                        </div>
                        <div class="py-4 text-gray-900 text-center dark:text-gray-100">
                            <a href="#" @click.stop="showInfo(venue)">
                                <font-awesome-icon :icon="faCircleInfo"/>
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Checkin Modal -->
        <Modal :show="isShowCheckinModal" @close="hideCheckinModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ selectedVenue?.name }}
                </h2>

                <div class="mt-6">
                    <TextBox v-model="checkinText" label="Test" placeholder="What are you up to?" class="w-full"
                             rows="3">Test
                    </TextBox>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="hideCheckinModal">Cancel</SecondaryButton>
                    <PrimaryButton @click="checkIn" class="ms-3">Check In</PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Tag Modal -->
        <Modal :show="isShowTagModal" @close="hideTagModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ selectedVenue?.name }}
                </h2>

                <table
                    class="mt-2 w-full border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-800 text-sm shadow-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left">
                            key
                        </th>
                        <th class="border border-slate-300 dark:border-slate-600 font-semibold p-4 text-slate-900 dark:text-slate-200 text-left">
                            value
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="tag in selectedVenue?.tags">
                        <td class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                            {{ tag.key }}
                        </td>
                        <td class="border border-slate-300 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                            {{ tag.value }}
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="mt-6">

                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="hideTagModal">Close</SecondaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
