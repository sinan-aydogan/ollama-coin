<script setup>
import {computed, reactive} from "vue";
import { useStorage } from '@vueuse/core'
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import SwitchInput from "@/Components/SwitchInput.vue";

const props = defineProps({
    coins: Array,
})

const filters = reactive({
    symbol: '',
    quote_symbol: '',
})
const quotedSymbolOptions = computed(() => {
    let quotedSymbols = [];
    props.coins.forEach(coin => {
        if (quotedSymbols.find(c=>c.id === coin.quote_symbol)) {
            return;
        }

        quotedSymbols.push({
            id: coin.quote_symbol,
            name: coin.quote_symbol
        })
    })
    return quotedSymbols;
})

const formatDate = (date) => {
    return new Date(date).toLocaleString()
}
const filteredCoins = computed(() => {
    let coins = props.coins;

    /*Filter*/
    if (filters.symbol) {
        coins = coins.filter(coin => coin.symbol.toLowerCase().includes(filters.symbol.toLowerCase()))
    }

    if (filters.quote_symbol !== '') {
        coins = coins.filter(coin =>  coin.quote_symbol === filters.quote_symbol)
    }

    return coins;
})

const selected_symbols = useStorage('selectedSymbolsForAnalysis', []);

const toggleSymbol = (symbol) => {
    if (selected_symbols.value.includes(symbol)) {
        selected_symbols.value.splice(selected_symbols.value.indexOf(symbol), 1)
    } else {
        selected_symbols.value.push(symbol)
    }
}
const cleanedPrice = (price) => {
    if (!price) {
        return ''
    }
    /*Clean end of zero numbers*/
    return price.replace(/\.?0+$/, "")
}
</script>

<template>
    <!--Filters-->
    <div class="space-x-4 flex justify-between items-center mb-4">
        <div class="space-x-4 flex items-center">
            <!--Base Symbol:text-->
            <TextInput type="text" v-model="filters.symbol" placeholder="Coin Adı"/>
        </div>

        <!--Quoted Symbol:select-->
        <SelectInput :options="quotedSymbolOptions" label="Fiyat Birimi" placeholder="Fiyat Birimi" v-model="filters.quote_symbol"/>
    </div>
    <!--Grid view of the coins list-->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="coin in filteredCoins" :key="coin.symbol" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!--Header-->
                <div class="flex justify-between items-start">
                    <h2 class="text-xl font-semibold text-gray-800">{{ coin.symbol }}</h2>
                    <div class="flex flex-col text-right text-sm">
                        <SwitchInput :checked="selected_symbols.includes(coin.symbol)" @change="toggleSymbol(coin.symbol)"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Empty-->
    <div v-if="coins.length === 0" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800">Coin Bulunamadı</h2>
            <p class="text-gray-600">Henüz coin eklenmemiş</p>
        </div>
    </div>
</template>
