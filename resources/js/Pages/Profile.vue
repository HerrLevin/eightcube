<script lang="ts">
import {Profile, Status, User} from "@/types/venue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextBox from "@/Components/TextBox.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import {DateTime} from "luxon";
import StatusComponent from "@/Components/Status.vue";
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import FollowButton from "@/Components/FollowButton.vue";

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
        FollowButton,
        GeneralLayout,
        StatusComponent,
        Loading,
        Modal, TextBox, SecondaryButton, PrimaryButton,
    },
    data() {
        return {
            profile: null as Profile | null,
            createdStatus: null as Status | null,
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
        },
        updateUser(user: User) {
            if (this.profile?.user) {
                this.profile.user = user;
            }
        }
    }
};
</script>

<template>
    <Head title="Profile"/>

    <GeneralLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Profile of {{ profile?.user.name }}
            </h2>
        </template>

        <!-- Header for profile info -->
        <div class="bg-white dark:bg-gray-800 shadow p-4 rounded-lg">
            <div class="flex items center">
                <img src="https://picsum.photos/200" alt="avatar" class="w-16 h-16 rounded-full">
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ profile?.user.name }}</h2>
                    <p class="text-gray-500 dark:text-gray-300">{{ profile?.user.name }}</p>
                </div>
                <div class="ml-auto">
                    <FollowButton v-if="profile?.user" :user="profile?.user" @update:user="updateUser"/>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Loading v-if="loading"/>
                <div v-else v-for="status in profile?.statuses" @click="goToStatus(status)">
                    <StatusComponent :status="status"/>
                </div>
            </div>
        </div>
    </GeneralLayout>

</template>
