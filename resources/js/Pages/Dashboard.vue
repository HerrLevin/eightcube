<script lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Status} from "@/types/venue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextBox from "@/Components/TextBox.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";

export default {
    components: {
        Modal, TextBox, SecondaryButton, PrimaryButton,
        AuthenticatedLayout,
    },
    data() {
        return {
            statuses: [] as Status[],
            createdStatus: null as Status|null,
            isShowModal: false as boolean,
        };
    },
    mounted() {
        // get status from local storage
        if (localStorage.getItem('createdStatus')) {
            this.createdStatus = JSON.parse(localStorage.getItem('createdStatus')!);
            this.isShowModal = true;
            localStorage.removeItem('createdStatus');
        }
        this.fetchStatuses();
    },
    methods: {
        hideModal() {
            this.isShowModal = false;
        },
        fetchStatuses() {
            axios.get('/api/statuses')
                .then(response => {
                    this.statuses = response.data.data;
                });
        },
        goToStatus(status: Status) {
            router.visit(route('status', {statusId: status.id}));
        }
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-for="status in statuses"
                    @click="goToStatus(status)"
                    class="cursor-pointer bg-white grid grid-cols-12 md:mt-2 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg max-md:border-b border-b-gray-500">
                    <!-- icon -->
                    <div class="col-span-2 text-gray-900 p-4 text-center dark:text-gray-100">
                    </div>
                    <div class="col-span-9 py-4 text-gray-900 dark:text-gray-100">
                        <p class="text-xs text-gray-400">{{ status.user.name }}</p>
                        <p>{{ status.venue.name }}</p>
                        <p class="text-xs text-gray-400">{{ status.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <!-- Checkin Modal -->
    <Modal :show="isShowModal" @close="hideModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Success!
            </h2>

            <div class="mt-6">
                <p class="dark:text-gray-100">
                    You've successfully checked in at {{ createdStatus?.venue.name }}.
                </p>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="hideModal">Okay!</SecondaryButton>
            </div>
        </div>
    </Modal>

</template>
