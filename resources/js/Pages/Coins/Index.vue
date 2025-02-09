<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CoinTable from "@/Pages/Coins/CoinTable.vue";
import {onMounted, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    coins: Array,
})

const in_progress = ref(false)
const coinsList = ref([])

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

            router.visit(route('coins.index'), {
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

onMounted(()=>{
    coinsList.value = props.coins

    Echo.channel("coin-ticker-updates")
        .listen(".ticker.updated", (event) => {
            console.log("Güncellenen Coin Ticker:", event.ticker);

            // Güncellenen fiyatı frontend üzerinde göster
            const coin = coinsList.value.find(c => c.id === event.ticker.coin_id);
            if (coin) {
                coin.price = event.ticker.last;
            }
        });
})

watch(()=>props.coins, ()=>{
    coinsList.value = props.coins
})
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
                        <span class="animate-pulse text-blue-800 font-semibold">Veri güncellemesi yapılıyor, lütfen bekleyiniz...</span>
                    </div>

                    <!--Coin Table-->
                    <CoinTable :coins="coinsList"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
