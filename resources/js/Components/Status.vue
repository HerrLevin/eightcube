<script lang="ts">
import {defineComponent} from 'vue'
import {Status} from "@/types/venue";
import Loading from "@/Components/Loading.vue";
import {DateTime} from "luxon";

export default defineComponent({
    name: "Status",
    computed: {
        DateTime() {
            return DateTime
        }
    },
    components: {Loading},
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
            <p class="text-xs text-gray-400">{{ status.user.name }}</p>
            <p>{{ status.venue.name }}</p>
            <p class="text-xs text-gray-400">{{ DateTime.fromISO(status.created_at).toRelative() }}</p>
        </div>
    </div>
</template>

<style scoped>

</style>