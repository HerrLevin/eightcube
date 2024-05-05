<script lang="ts">
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import axios from "axios";

export default {
    components: {PrimaryButton, DangerButton},
    props: {
        externalService: {
            type: Array as () => string[],
            required: false,
        },
    },
    data() {
        return {
            oauth: [] as string[],
            providers: [
                'osm',
            ]
        };
    },
    mounted() {
        this.oauth = this.$props.externalService || [];
    },
    watch: {
        externalOAuthProviders() {
            this.oauth = this.$props.externalService || [];
        },
    },
    methods: {
        connect(provider: string) {
            window.location.href = `/auth/redirect/${provider}`;
        },
        disconnect(provider: string) {
            axios.delete(`/api/settings/external-services/${provider}`)
                .then(() => {
                    this.oauth = this.oauth.filter((item) => item !== provider);
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">External Services</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage your external service connections.
            </p>

            <p v-for="provider in providers" class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                <template v-if="oauth.includes(provider)">
                    <span class="text-green
                        dark:text-green-400">Connected</span> to {{ provider }}.

                    <DangerButton @click="disconnect(provider)">
                        Disconnect
                    </DangerButton>
                </template>
                <template v-else>
                    <span class="text-red-500 dark:text-red-400">Not connected</span> to {{ provider }}

                    <PrimaryButton @click="connect(provider)">
                        Connect
                    </PrimaryButton>
                </template>
            </p>

        </header>

    </section>
</template>
