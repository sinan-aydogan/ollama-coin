<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CoinTable from "@/Pages/Analysis/CoinTable.vue";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import {useStorage} from "@vueuse/core";
import DialogModal from "@/Components/DialogModal.vue";
import VueMarkdown from 'vue-markdown-render'

const props = defineProps({
    coins: Array,
})

const in_progress = ref(false)
const selected_symbols = useStorage('selectedSymbolsForAnalysis', []);

const getKlineData = async ()=>{
    in_progress.value = true
    await axios.post(route('analysis.get-kline-data-selected-coins'),{
        symbols: selected_symbols.value
    }).then(response=>{
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

const tickerDataAiReport = ref('')
const getTickerDataAiReport = async ()=>{
    in_progress.value = true

    let data = props.coins.filter(coin=>selected_symbols.value.includes(coin.symbol)).map(coin=>({
        symbol: coin.symbol,
        ticker: coin.last_ticker
    }))

    const prompt = `
You are a financial analyst specialized in cryptocurrency markets. Analyze the given cryptocurrency ticker data and provide a structured report in Markdown format.

### **Data Format**
You will receive JSON-like data for one or multiple cryptocurrencies, containing the following fields:
- **open**: Opening price of the asset
- **high**: Highest price recorded
- **low**: Lowest price recorded
- **last**: Most recent trading price
- **average**: Average price over the given period
- **bid**: Highest price a buyer is willing to pay
- **ask**: Lowest price a seller is willing to accept
- **volume**: Total trading volume
- **price_change**: Absolute price change from the previous period
- **price_change_percent**: Percentage price change from the previous period

### **Analysis Requirements**
For each cryptocurrency in the dataset, provide an analysis containing:
1. **Trend Analysis**: Identify whether the asset is in an uptrend, downtrend, or range-bound based on price movement and price change percentage.
2. **Volatility Assessment**: Evaluate market volatility using the high-low spread and price change percentage.
3. **Liquidity Check**: Assess liquidity based on bid-ask spread and trading volume.
4. **Support & Resistance**: Highlight potential support (low) and resistance (high) levels.
5. **Market Sentiment**: Interpret the price movement direction based on price change and momentum.
6. **Trading Signal Suggestion**: Provide a simple "Bullish", "Bearish", or "Neutral" recommendation based on observed patterns.

### **Output Format (Markdown)**
Provide the analysis in a structured Markdown format. Ensure that **you do not generate Python code or explain how to analyze the data manually**. Instead, return a fully formatted Markdown report with the actual analysis.

Example output format:
\`\`\`markdown
## Cryptocurrency Analysis Report

### **SPELLUSDT**
- **Trend**: Downtrend
- **Volatility**: High
- **Liquidity**: Moderate (Low bid-ask spread, high volume)
- **Support & Resistance**: Support at $0.000956, Resistance at $0.001080
- **Market Sentiment**: Bearish momentum
- **Trading Signal**: 🔽 Bearish
\`\`\`
            `;


    await axios.post(route('analysis.get-ticker-data-ai-report'), {
        prompt,
        data,
        options: {
            "raw": true,
            "stream": false,
        }
    }).then(response=>{
        tickerDataAiReport.value = response.data.response
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
                <!--Analysis Ticker Data-->
                <PrimaryButton @click="getTickerDataAiReport" :disabled="in_progress">
                    Fiyat AI Raporu
                </PrimaryButton>

                <!--Analysis Kline Data-->
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

        <DialogModal :show="tickerDataAiReport.length>0" @close="tickerDataAiReport = ''" max-width="7xl" closeable>
            <template #title>
                Fiyat AI Raporu
            </template>
            <template #content>
                <vue-markdown :source="tickerDataAiReport" />
            </template>
        </DialogModal>
    </AppLayout>
</template>
