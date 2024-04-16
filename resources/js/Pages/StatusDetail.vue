<script lang="ts">
import 'leaflet/dist/leaflet.css';
import {Status} from "@/types/venue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextBox from "@/Components/TextBox.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import DangerButton from "@/Components/DangerButton.vue";
import StatusComponent from "@/Components/Status.vue";
import {Map, map, latLng, tileLayer, MapOptions, marker, divIcon} from "leaflet";
import GeneralLayout from "@/Layouts/GeneralLayout.vue";
import {getIconFromTags} from "@/Services/Icons/Icon";

export default {
    components: {
        DangerButton,
        Modal, TextBox, SecondaryButton, PrimaryButton,
        StatusComponent,
        GeneralLayout,
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
            map: null as Map|null,
            status: null as Status|null,
            isShowEditModal: false as boolean,
            isShowDeleteModal: false as boolean,
            checkinText: "" as string,
        };
    },
    mounted() {
        this.status = this.statusObject;
        if (this.status === null) {
            this.fetchStatus();
        }
    },
    methods: {
        mountMap() {
            const latitude = this.status?.venue.latitude || 0;
            const longitude = this.status?.venue.longitude || 0;
            const options: MapOptions = {
                center: latLng(latitude, longitude),
                zoom: 16,
                scrollWheelZoom: false,
            };


            // @ts-ignore
            const maplayer = map(this.$refs.map, options);
            tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(maplayer);

            const categoryIcon = '<i class="fas ' + getIconFromTags(this.status?.venue.tags || []) + '"></i>';
            console.log(categoryIcon);

            const icon = divIcon({
                className: 'status-icon',
                html: "<div class='custom-div-icon'>" + categoryIcon + "</div>",
                iconSize: [30, 42],
                iconAnchor: [15, 15]
            })

            marker([latitude, longitude], {icon}).addTo(maplayer);
        },
        fetchStatus() {
            axios.get(`/api/statuses/${this.statusId}`)
                .then(response => {
                    this.status = response.data.data;
                    this.mountMap();
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

    <GeneralLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Status from {{ status?.user.name }}
                <span v-if="status?.user.id === user.id" @click="editStatus" class="cursor-pointer text-blue-500 text-sm border-e-2 me-1 pe-1">Edit</span>
                <span v-if="status?.user.id === user.id" @click="deleteStatus" class="cursor-pointer text-blue-500 text-sm">Delete</span>
            </h2>
        </template>

        <div class="flex flex-col h-screen">
            <div ref="map" id="map" class="flex h-3/5">&nbsp;</div>

            <div class="py-12 h-1/5">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <StatusComponent v-if="status !== null" :status="status" />
                </div>
            </div>
        </div>
    </GeneralLayout>


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

<style>
.custom-div-icon {
    background-color: rgba(0, 123, 255, 0.7);
    border-color: rgb(0, 123, 255);
    border-width: 2px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    text-align: center;
    color: white;
}
.custom-div-icon i {
    color: white;
    text-shadow: 1px 1px 1px black;
    line-height: 22px;
}

.leaflet-pane {
    z-index: 10 !important;
}
</style>
