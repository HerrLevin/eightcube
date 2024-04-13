<script lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Status} from "@/types/venue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextBox from "@/Components/TextBox.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import { DateTime } from "luxon";
import StatusComponent from "@/Components/Status.vue";

export default {
    computed: {
        DateTime() {
            return DateTime
        }
    },
    components: {
        StatusComponent,
        Loading,
        Modal, TextBox, SecondaryButton, PrimaryButton,
        AuthenticatedLayout,
    },
    data() {
        return {
            statuses: [] as Status[],
            createdStatus: null as Status|null,
            isShowModal: false as boolean,
            loading: false as boolean,
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
            this.loading = true;
            axios.get('/api/statuses')
                .then(response => {
                    this.loading = false;
                    this.statuses = response.data.data;
                }).catch(() => this.loading = false);
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
                <Loading v-if="loading" />
                <div v-else v-for="status in statuses" @click="goToStatus(status)" >
                    <StatusComponent :status="status"/>
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
