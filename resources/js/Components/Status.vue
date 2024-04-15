<script lang="ts">
import {defineComponent} from 'vue'
import {Status} from "@/types/venue";
import Loading from "@/Components/Loading.vue";
import {DateTime} from "luxon";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {faCircleInfo, faComment} from "@fortawesome/free-solid-svg-icons";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import {Link} from "@inertiajs/vue3";

export default defineComponent({
    name: "Status",
    setup() {
        return {faComment, faCircleInfo}
    },
    computed: {
        DateTime() {
            return DateTime
        }
    },
    data() {
        return {
            isShowTagModal: false,
        }
    },
    methods: {
        showTagModal() {
            this.isShowTagModal = true;
        },
        hideTagModal() {
            this.isShowTagModal = false;
        }
    },
    components: {Modal, SecondaryButton, FontAwesomeIcon, Loading, Link},
    props: {
        status: {
            required: true,
            type: Object as () => Status,
        }
    }
})
</script>

<template>

    <div
        class="cursor-pointer bg-white grid grid-cols-12 md:mt-2 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg max-md:border-b border-b-gray-500">
        <div class="col-span-2 text-gray-900 p-4 text-center dark:text-gray-100">
            <!-- profile picture -->
        </div>
        <div class="col-span-9 py-4 text-gray-900 dark:text-gray-100">
            <p class="text-xs text-gray-400"><Link
                @click.stop
                :href="route('profile', {userId: status.user.id})" class="text-blue-500 hover:underline"
                >
            {{ status.user.name }}</Link></p>
            <p>{{ status.venue.name }}</p>
            <p v-if="status.body"><FontAwesomeIcon :icon="faComment"/>&nbsp;{{ status.body }}</p>
            <p class="text-xs text-gray-400">{{ DateTime.fromISO(status.created_at).toRelative() }}</p>
        </div>
        <div class="py-4 text-gray-900 text-center dark:text-gray-100">
            <a href="#" @click.stop="showTagModal">
                <font-awesome-icon :icon="faCircleInfo"/>
            </a>
        </div>
    </div>

    <!-- Tag Modal -->
    <Modal :show="isShowTagModal" @close="hideTagModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ status.venue.name }}
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
                <tr v-for="tag in status.venue.tags">
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
</template>
