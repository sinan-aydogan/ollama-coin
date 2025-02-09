<script setup>
import {computed, reactive} from "vue";
import DownTrendIcon from "@/Components/Icons/DownTrendIcon.vue";
import UpTrendIcon from "@/Components/Icons/UpTrendIcon.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import SortUpIcon from "@/Components/Icons/SortUpIcon.vue";
import SortDownIcon from "@/Components/Icons/SortDownIcon.vue";

const props = defineProps({
    coins: Array,
})

const filters = reactive({
    base_symbol: '',
    quoted_symbol: '',
    price_change_direction: '',
})

const sort = reactive({
    key: '',
    direction: 'asc',
})
const sortKeyOptions = [
    {
        id: 'price',
        name: 'Fiyat'
    },
    {
        id: 'price_change_percent',
        name: 'Değişim'
    },
]
const sortDirectionOptions = [
    {
        id: 'asc',
        name: 'Artan'
    },
    {
        id: 'desc',
        name: 'Azalan'
    },
]
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

const priceChangeDirectionOptions = [
    {
        id: 'up',
        name: 'Yukarı'
    },
    {
        id: 'down',
        name: 'Aşağı'
    },
]

const formatDate = (date) => {
    return new Date(date).toLocaleString()
}

const generatedCoinList = computed(() => {
    let coins = [];

    if (!props.coins) {
        return coins;
    }
    props.coins.forEach(coin => {
        let existCoin = coins.find(c => c.base_symbol === coin.base_symbol);

        if (coin.price === null) {
            coins.push({
                base_symbol: coin.base_symbol,
            })

            return;
        }

        if(existCoin === undefined){
            let price = {
                [coin.quote_symbol]: {
                    price: cleanedPrice(coin.price),
                    price_change_percent: cleanedPrice(coin.last_ticker.price_change_percent),
                    price_change_direction: coin.price_change_direction,
                }
            };

            coins.push({
                base_symbol: coin.base_symbol,
                price,
                updated_at: formatDate(coin.last_ticker.created_at),
            })
        }else{
            let c = coins.find(c => c.base_symbol === coin.base_symbol);
            if (c && c.price && !c.price[coin.quote_symbol]) {
                c.price[coin.quote_symbol] = {
                    price: cleanedPrice(coin.price),
                    price_change_percent: cleanedPrice(coin.last_ticker.price_change_percent),
                    price_change_direction: coin.price_change_direction,
                };
            }
        }
    })

    return coins;
})

const sortedCoins = computed(() => {
    let coins = [...generatedCoinList.value];

    if (sort.key) {
        coins = coins.sort((a, b) => {
            let aValue = a.price?.[filters.quoted_symbol]?.[sort.key];
            let bValue = b.price?.[filters.quoted_symbol]?.[sort.key];

            if (sort.direction === 'asc') {
                return aValue - bValue;
            } else {
                return bValue - aValue;
            }
        })
    }

    return coins;
})
const filteredCoins = computed(() => {
    let coins = sortedCoins.value;

    /*Filter*/
    if (filters.base_symbol) {
        coins = coins.filter(coin => coin.base_symbol.toLowerCase().includes(filters.base_symbol.toLowerCase()))
    }

    if (filters.price_change_direction) {
        coins = coins.filter(coin => {
            if (filters.quoted_symbol) {
                let price = coin.price[filters.quoted_symbol]
                return price !== undefined && price.price_change_direction === filters.price_change_direction
            }else{
                return Object.values(coin.price).find(p => p.price_change_direction === filters.price_change_direction) !== undefined
            }
        })
    }

    return coins;
})
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
            <TextInput type="text" v-model="filters.base_symbol" placeholder="Coin Adı"/>
            <!--Quote Symbol:select-->
            <SelectInput :options="priceChangeDirectionOptions" label="Trend" placeholder="Trend" v-model="filters.price_change_direction"/>
        </div>

        <!--Quoted Symbol:select-->
        <div class="flex items-center space-x-2">
            <!--Sort Direction Icon-->
            <button v-if="filters.quoted_symbol && sort.key" class="border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-3 bg-white" @click="sort.direction === 'desc' ? sort.direction = 'asc' : sort.direction = 'desc'">
                <SortUpIcon class="w-4 h-4" v-if="sort.direction === 'asc'"/>
                <SortDownIcon class="w-4 h-4" v-if="sort.direction === 'desc'"/>
            </button>
            <!--Sort Key:select-->
            <SelectInput v-if="filters.quoted_symbol" :options="sortKeyOptions" label="Sıralama" placeholder="Sıralama" v-model="sort.key"/>
            <!--Quoted Symbol:select-->
            <SelectInput :options="quotedSymbolOptions" label="Fiyat Birimi" placeholder="Fiyat Birimi" v-model="filters.quoted_symbol"/>
        </div>
    </div>
    <!--Grid view of the coins list-->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="coin in filteredCoins" :key="coin.base_symbol" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!--Header-->
                <div class="flex justify-between items-start">
                    <h2 class="text-xl font-semibold text-gray-800">{{ coin.base_symbol }}</h2>
                    <div class="flex flex-col text-right text-sm">
                        <template v-if="coin.price">
                            <div v-for="(price,quote_symbol) in coin.price" :key="quote_symbol">
                                <div v-if="filters.quoted_symbol === '' || filters.quoted_symbol === quote_symbol" class="flex flex-col items-center justify-end space-x-2 mb-2">
                                    <span class="text-gray-600">{{ price.price  + ' ' + quote_symbol }}</span>

                                    <div
                                        class="flex items-center justify-center space-x-1"
                                        :class="{
                                        'text-green-500': price.price_change_direction === 'up',
                                        'text-red-500': price.price_change_direction === 'down',
                                        'text-gray-600': price.price_change_direction === null,
                                    }"
                                    >
                                        <span class="text-xs">{{ price.price_change_percent }}%</span>
                                        <UpTrendIcon v-if="price.price_change_direction === 'up'" class="w-4 h-4"/>
                                        <DownTrendIcon v-if="price.price_change_direction === 'down'" class="w-4 h-4"/>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <span class="text-gray-600">Fiyatı Güncelleyin</span>
                        </template>
                    </div>
                </div>

                <!--Footer-->
                <div class="mt-4 flex justify-between items-center">
                    <!--Last Update-->
                    <div class="text-xs text-gray-600">
                        {{ coin.updated_at }}
                    </div>

                    <!--Details-->
                    <div>
                        <a :href="`/coins/${coin.base_symbol}`" class="text-indigo-600 hover:text-indigo-900">Detaylar</a>
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
