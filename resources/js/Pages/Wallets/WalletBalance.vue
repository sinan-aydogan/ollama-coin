<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import {computed, onMounted, ref, watch} from "vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";

const props = defineProps({
    wallet: Object,
    is_visible: Boolean
})

const emit = defineEmits(['close'])
const balance = ref([])
const in_progress = ref(false)

const onClose = ()=>{
    emit('close')
}

const getBalance = async ()=>{
    in_progress.value = true
    await axios.get(route('wallets.get-balance', props.wallet?.id)).then(response=>{
        balance.value = response.data
    }).finally(()=>{
        in_progress.value = false
    })
}

const quoteSymbolsInBalance = computed(()=>{
    let symbols = []
    balance.value.forEach(coin=>{
        Object.keys(coin.price).forEach(symbol=>{
            if (!symbols.includes(symbol)){
                symbols.push(symbol)
            }
        })
    })
    return symbols
})
onMounted(()=>{
    if (props.wallet) getBalance()
})

watch(()=>props.wallet, ()=>{
    if (props.wallet) getBalance()
})
</script>

<template>
    <DialogModal :show="is_visible" @close="onClose" max-width="7xl">
        <template #title>
            {{wallet.name}} cüzdanının bakiyesi
        </template>

        <template #content>
            <LoadingSpinner v-model="in_progress" class="mx-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Coin
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Miktar
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            TRY Değeri
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="coin in balance" :key="coin.asset">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900" v-text="coin.assetname"></span>
                                    <span class="text-sm text-gray-500" v-text="coin.asset"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="float-right text-right lotext-sm text-gray-900 w-fit">
                                    <div class="space-x-2 font-semibold">
                                        <span v-text="Math.ceil(coin.balance).toFixed(4)"></span>
                                    </div>

                                    <div v-if="coin.locked > 0" class="space-x-2 border-t pt-1 text-xs">
                                        <span>İşlemde:</span>
                                        <span v-text="Math.ceil(coin.locked).toFixed(4)"></span>
                                    </div>

                                    <div v-if="coin.free !== coin.balance" class="space-x-2 text-xs">
                                        <span>Müsait:</span>
                                        <span v-text="Math.ceil(coin.free).toFixed(4)"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div v-for="(price,index) in coin.price" class="flex justify-end items-center text-sm text-gray-900 text-right space-x-2">
                                    <span v-text="Math.ceil(price).toFixed(2)" class="text-right"></span>
                                    <span v-text="index" class="w-10 text-left"></span>
                                </div>
                            </td>
                        </tr>

                        <!--Total-->
                        <tr class="bg-slate-100">
                            <td class="py-4 px-4 whitespace-nowrap" colspan="3">
                                <div v-for="quoteSymbol in quoteSymbolsInBalance" class="flex justify-end text-sm text-gray-900 space-x-2">
                                    <span v-text="balance.reduce((acc,b)=> acc + (b?.price?.[quoteSymbol] ?? 0),0).toFixed(2)" class="text-right"></span>
                                    <span v-text="quoteSymbol" class="w-10 text-left"></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </LoadingSpinner>
        </template>
    </DialogModal>
</template>
