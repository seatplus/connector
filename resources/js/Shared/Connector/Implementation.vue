<template>
    <div v-if="isReady" class="border-b border-gray-200 pb-5 flex justify-between space-x-2 align-baseline">
        <div class="flex space-x-2 self-center">
            <img :src="connector.image" class="h-6 w-6 items-center">
            <h3 class="text-base font-semibold leading-6 text-gray-900 capitalize">{{ connector.name }}</h3>
        </div>
        <slot name="header" :status="connector.status" :connector="connector"/>
    </div>

    <div class="space-y-2" v-if="isReady">
        <!--if connector is not configured show an alert-->
        <Alert v-if="connector.status === 'Missing Configuration'" :url="connector.url" />
        <!--if status is incomplete setup or user is not registered show register-->
        <Registration v-if="connector.status === 'Incomplete Setup'" :url="connector.url"/>
        <slot :status="connector.status" :connector="connector" />
    </div>
</template>

<script setup>
import { defineProps, ref } from 'vue'
import axios from "axios";
import Registration from "./Registration.vue";
import Alert from "./Alert.vue";

const props = defineProps({
    url: String,
})

const isReady = ref(false)
const connector = ref({
    name: '',
    image: '',
    status: '',
    url: '',
})

axios.get(props.url)
    .then(response => {
        isReady.value = true
        connector.value = response.data
    })
</script>




