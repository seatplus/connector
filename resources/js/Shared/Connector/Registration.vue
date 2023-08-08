<template>
    <Card>
        <a :href="url" type="button" class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <UserPlusIcon class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
            <span class="mt-2 block text-sm font-semibold text-gray-900">Register</span>
        </a>
    </Card>

</template>

<script setup>
import Card from "@/Shared/Layout/Cards/Card.vue";
import {UserPlusIcon} from "@heroicons/vue/24/outline";

defineProps({
    url: {
        type: String,
        required: true
    }
})

</script>
