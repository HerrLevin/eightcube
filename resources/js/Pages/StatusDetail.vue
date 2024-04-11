<script lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Status} from "@/types/venue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextBox from "@/Components/TextBox.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import DangerButton from "@/Components/DangerButton.vue";
import StatusComponent from "@/Components/Status.vue";

export default {
    components: {
        DangerButton,
        Modal, TextBox, SecondaryButton, PrimaryButton,
        AuthenticatedLayout,
        StatusComponent,
    },
    props: {
        statusId: {
            type: Number,
            required: false,
            default: null,
        },
        statusObject: {
            type: Object as () => Status|null,
            required: false,
            default: null,
        },
    },
    data() {
        return {
            status: null as Status|null,
            isShowEditModal: false as boolean,
            isShowDeleteModal: false as boolean,
            checkinText: "" as String,
        };
    },
    mounted() {
        this.status = this.statusObject;
        if (this.status === null) {
            this.fetchStatus();
        }
    },
    methods: {
        fetchStatus() {
            axios.get(`/api/statuses/${this.statusId}`)
                .then(response => {
                    this.status = response.data.data;
                })
                .catch(() => router.visit(route('dashboard')));
        },
        destroyStatus() {
            axios.delete(`/api/statuses/${this.status?.id}`)
                .then(() => {
                    this.hideDeleteModal();
                    this.$emit('statusDeleted');
                    router.visit(route('dashboard'));
                });
        },
        deleteStatus() {
            this.isShowDeleteModal = true;
        },
        hideDeleteModal() {
            this.isShowDeleteModal = false;
        },
        editStatus() {
            this.checkinText = this.status?.body || "";
            this.isShowEditModal = true;
        },
        hideEditModal() {
            this.isShowEditModal = false;
        },
        updateStatus() {
            axios.put(`/api/statuses/${this.status?.id}`, {
                body: this.checkinText,
            }).then(response => {
                this.status = response.data.data;
                this.hideEditModal();
            });
        },
    },
    computed: {
        user() {
            return this.$page.props.auth.user
        }
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Status from {{ status?.user.name }}
                <span v-if="status?.user.id === user.id" @click="editStatus" class="cursor-pointer text-blue-500 text-sm border-e-2 me-1 pe-1">Edit</span>
                <span v-if="status?.user.id === user.id" @click="deleteStatus" class="cursor-pointer text-blue-500 text-sm">Delete</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <StatusComponent v-if="status !== null" :status="status" />
            </div>
        </div>
    </AuthenticatedLayout>


    <!-- Delete Modal -->
    <Modal :show="isShowDeleteModal" @close="hideDeleteModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Do you really want to delete your check in at {{ status?.venue.name }}?
            </h2>

            <div class="mt-6">
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="hideDeleteModal">Cancel</SecondaryButton>
                <DangerButton @click="destroyStatus" class="ms-3">Delete</DangerButton>
            </div>
        </div>
    </Modal>

    <!-- Edit Modal -->
    <Modal :show="isShowEditModal" @close="hideEditModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Edit status at {{ status?.venue.name }}
            </h2>

            <div class="mt-6">
                <TextBox v-model="checkinText" label="Test" placeholder="What are you up to?" class="w-full" rows="3" />
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="hideEditModal">Cancel</SecondaryButton>
                <PrimaryButton @click="updateStatus" class="ms-3">Update</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
