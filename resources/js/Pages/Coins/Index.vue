<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CoinTable from "@/Pages/Coins/CoinTable.vue";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";

defineProps({
    coins: Array,
})

const in_progress = ref(false)

const syncCoinList = async ()=>{
    in_progress.value = true
    await axios.get(route('coins.sync-coin-list')).then(response=>{
        if (response.data.length>0){
            alert('Coin listesi güncellendi')
            router.reload({
                preserveScroll: true,
                only: ['coins']
            })
        }else {
            alert('Coin listesi zaten güncel')
        }
    }).finally(()=>{
        in_progress.value = false
    })
}

const syncCoinPrices = async ()=>{
    in_progress.value = true
    await axios.get(route('coins.sync-coin-prices')).then(response=>{

        if (response.data.length>0){
            alert('Fiyatlar güncellendi')

            router.reload({
                preserveScroll: true,
                only: ['coins']
            })
        }else {
            alert('Fiyatlar güncellenmedi')
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
                Coinler
            </h2>
        </template>

        <template #actions>
            <div class="flex items-center justify-end space-x-2">
                <PrimaryButton @click="syncCoinList" :disabled="in_progress">
                    Coin Listesini Güncelle
                </PrimaryButton>

                <PrimaryButton @click="syncCoinPrices" :disabled="in_progress">
                    Fiyatları Güncelle
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <!--Loading message-->
                    <div v-if="in_progress" class="mb-8 bg-blue-200 rounded-lg p-4">
                        <span class="text-gray-600 animate-pulse text-blue-800 font-semibold">Veri güncellemesi yapılıyor, lütfen bekleyiniz...</span>
                    </div>

                    <!--Coin Table-->
                    <CoinTable :coins/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
