<script lang="ts">
import {Profile, Status} from "@/types/venue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextBox from "@/Components/TextBox.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import { DateTime } from "luxon";
import StatusComponent from "@/Components/Status.vue";
import GeneralLayout from "@/Layouts/GeneralLayout.vue";

export default {
    computed: {
        DateTime() {
            return DateTime
        }
    },
    props: {
        userId: {
            type: Number,
            required: true,
            default: null,
        },
    },
    components: {
        GeneralLayout,
        StatusComponent,
        Loading,
        Modal, TextBox, SecondaryButton, PrimaryButton,
    },
    data() {
        return {
            profile: null as Profile|null,
            createdStatus: null as Status|null,
            loading: false as boolean,
        };
    },
    mounted() {
        this.fetchStatuses();
    },
    methods: {
        fetchStatuses() {
            this.loading = true;
            axios.get(`/api/profile/${this.$props.userId}`)
                .then(response => {
                    this.loading = false;
                    this.profile = response.data.data;
                }).catch(() => this.loading = false);
        },
        goToStatus(status: Status) {
            router.visit(route('status', {statusId: status.id}));
        }
    }
};
</script>

<template>
    <Head title="Profile" />

    <GeneralLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Profile of {{ profile?.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Loading v-if="loading" />
                <div v-else v-for="status in profile?.statuses" @click="goToStatus(status)" >
                    <StatusComponent :status="status"/>
                </div>
            </div>
        </div>
    </GeneralLayout>

</template>
