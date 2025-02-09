<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CoinTable from "@/Pages/Coins/CoinTable.vue";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

defineProps({
    coins: Array,
})

const in_progress = ref(false)

const getKlineData = async ()=>{
    in_progress.value = true
    await axios.get(route('analysis.get-kline-data-all-coins')).then(response=>{
        if (response.data.length>0){
            alert('Kline verisi alındı')
            router.reload({
                preserveScroll: true,
                only: ['coins']
            })
        }else {
            alert('Kline verisi alınamadı')
        }
    }).finally(()=>{
        in_progress.value = false
    })
}
</script>

<template>
    <AppLayout title="Coinler">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Analiz
            </h2>
        </template>

        <template #actions>
            <div class="flex items-center justify-end space-x-2">
                <PrimaryButton @click="getKlineData" :disabled="in_progress">
                    Kline Verisi Al
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <!--Loading message-->
                    <div v-if="in_progress" class="mb-8 bg-blue-200 rounded-lg p-4">
                        <span class="animate-pulse text-blue-800 font-semibold">Veri güncellemesi yapılıyor, lütfen bekleyiniz...</span>
                    </div>

                    <!--Coin Table-->
                    <CoinTable :coins/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
