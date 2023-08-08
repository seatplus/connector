<template>
<Card>
<div class="rounded-md bg-blue-50 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <InformationCircleIcon class="h-5 w-5 text-blue-400" aria-hidden="true" />
        </div>
        <div class="ml-3 flex-1 md:flex md:justify-between">
            <p class="text-sm text-blue-700">The setup seems not to be completed. See how it should be done in the package description.</p>
            <p class="mt-3 text-sm md:ml-6 md:mt-0">
                <a :href="url" target="_blank" class="whitespace-nowrap font-medium text-blue-700 hover:text-blue-600">
                    Details
                    <span aria-hidden="true"> &rarr;</span>
                </a>
            </p>
        </div>
    </div>
</div>
</Card>

</template>

<script setup>

import { InformationCircleIcon } from '@heroicons/vue/20/solid'
import Card from "@/Shared/Layout/Cards/Card.vue";

defineProps({
    url: {
        type: String,
        required: true
    }
})

</script>

<style scoped>

</style>
