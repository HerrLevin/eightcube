<script lang="ts">
import PrimaryButton from "@/Components/PrimaryButton.vue";
import axios from "axios";
import {User} from "@/types/venue";


export default {
    components: {PrimaryButton},
    // add stuff for v-model
    props: {
        user: {
            type: Object as () => User,
            required: true,
        },
    },
    emits: ['update:user'],
    data() {
        return {
            userModel: this.$props.user,
            loading: false as boolean,
        };
    },
    watch: {
        user: {
            handler() {
                this.userModel = this.$props.user;
            },
            immediate: true,
        },
    },
    methods: {
        follow() {
            this.loading = true;
            axios.post(`/api/follow/${this.$props.user.id}`)
                .then((body) => {
                    this.loading = false;
                    this.userModel = body.data.data;
                    this.$emit('update:user', this.userModel);
                })
                .catch(() => this.loading = false);
        },
        unfollow() {
            this.loading = true;
            axios.delete(`/api/follow/${this.$props.user.id}`)
                .then((body) => {
                    this.loading = false;
                    this.userModel = body.data.data;
                    this.$emit('update:user', this.userModel);
                }).catch(() => this.loading = false);
        }
    }
};
</script>

<template>
    <template v-if="userModel">
        <PrimaryButton v-if="loading "disabled>
            Loading...
        </PrimaryButton>
        <PrimaryButton @click="follow" v-else-if="!userModel.following && !loading">
            Follow
        </PrimaryButton>
        <PrimaryButton @click="unfollow" v-else-if="userModel.following && !loading">
            Unfollow
        </PrimaryButton>
    </template>
</template>
