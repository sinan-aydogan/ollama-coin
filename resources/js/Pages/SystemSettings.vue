<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ActionSection from "@/Components/ActionSection.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectInput from "@/Components/SelectInput.vue";
import FormSection from "@/Components/FormSection.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    settings:{
        type: Object,
        default:()=>({
            ai_base_url: '',
            ai_model: '',
            default_exchange_for_market_data: '',
            default_kline_data_resolution: '',
            default_kline_data_time_range: '',
        })
    },
    exchangeOptions: Array,
});

const in_progress = ref(false)
const form = useForm({
    ai_base_url: '',
    ai_model: '',
    default_exchange_for_market_data: '',
    default_kline_data_resolution: '',
    default_kline_data_time_range: '',
})

const klineDataResolutionOptions = [
    {
        id: '1',
        name: '1 Dakika'
    },
    {
        id: '15',
        name: '15 Dakika'
    },
    {
        id: '30',
        name: '30 Dakika'
    },
    {
        id: '60',
        name: '1 Saat'
    },
    {
        id: '240',
        name: '4 Saat'
    },
    {
        id: '1440',
        name: '1 Gün'
    },
    {
        id: '10080',
        name: '1 Hafta'
    },
    {
        id: '518400',
        name: '1 Yıl',
    }
]
const klineDataTimeRangeOptions = [
    {
        id: '5',
        name: '5 Dakika'
    },
    {
        id: '15',
        name: '15 Dakika'
    },
    {
        id: '30',
        name: '30 Dakika'
    },
    {
        id: '60',
        name: '1 Saat'
    },
    {
        id: '180',
        name: '3 Saat'
    },
    {
        id: '360',
        name: '6 Saat'
    },
    {
        id: '720',
        name: '12 Saat'
    },
    {
        id: '1440',
        name: '1 Gün'
    },
    {
        id: '2880',
        name: '2 Gün'
    },
    {
        id: '4320',
        name: '3 Gün'
    },
    {
        id: '10080',
        name: '1 Hafta'
    },
    {
        id: '20160',
        name: '2 Hafta'
    },
    {
        id: '43200',
        name: '1 Ay'
    },
    {
        id: '86400',
        name: '2 Ay'
    },
    {
        id: '129600',
        name: '3 Ay'
    }
]
const aiModels = ref([])
const getAiModels = async()=>{
    in_progress.value = true

    await axios.get(route('system-settings.get-ai-models')).then(response=>{
        aiModels.value = response.data.models
    }).finally(()=>{
        in_progress.value = false
    })
}

const onSubmit = async()=>{
    in_progress.value = true
    form.put(route('system-settings.update'), {
        preserveScroll: true,
        onFinish: () => in_progress.value = false,
    });
}

onMounted(async()=>{
    form.ai_base_url = props.settings.ai_base_url
    form.ai_model = props.settings.ai_model
    form.default_exchange_for_market_data = props.settings.default_exchange_for_market_data
    form.default_kline_data_resolution = props.settings.default_kline_data_resolution
    form.default_kline_data_time_range = props.settings.default_kline_data_time_range
    await getAiModels();
})
</script>

<template>
    <AppLayout title="Sistem Ayarları">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sistem Ayarları
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!--Ai Settings-->
                <FormSection @submitted="onSubmit">
                    <template #title>
                        Yapay Zeka Ayarları
                    </template>

                    <template #description>
                        Yapay zeka servisine ait ayarları buradan güncelleyebilirsiniz.
                    </template>

                    <template #form>
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="ai_base_url" value="Ai End Point" />
                                <TextInput
                                    id="ai_base_url"
                                    v-model="form.ai_base_url"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.ai_base_url" class="mt-2" />
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="ai_model" value="Ai Model" />
                                <SelectInput
                                    id="ai_model"
                                    v-model="form.ai_model"
                                    class="mt-1 block w-full"
                                    :options="aiModels"
                                    value-key="model"
                                    label-key="name"
                                />
                                <InputError :message="form.errors.ai_model" class="mt-2" />
                            </div>
                    </template>

                    <template #actions>
                        <ActionMessage :on="form.recentlySuccessful" class="me-3">
                            Kaydedildi.
                        </ActionMessage>

                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing || in_progress">
                            Kaydet
                        </PrimaryButton>
                    </template>
                </FormSection>

                <!--Market Data-->
                <FormSection @submitted="onSubmit">
                    <template #title>
                        Market Verileri
                    </template>

                    <template #description>
                        Market verilerine ait ayarları buradan güncelleyebilirsiniz.
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="default_exchange_for_market_data" value="Market Verilerinin Çekileceği API Servisi" />
                            <SelectInput
                                id="default_exchange_for_market_data"
                                v-model="form.default_exchange_for_market_data"
                                class="mt-1 block w-full"
                                :options="exchangeOptions"
                                value-key="id"
                                label-key="name"
                            />
                            <InputError :message="form.errors.default_exchange_for_market_data" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <ActionMessage :on="form.recentlySuccessful" class="me-3">
                            Kaydedildi.
                        </ActionMessage>

                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing || in_progress">
                            Kaydet
                        </PrimaryButton>
                    </template>
                </FormSection>

                <!--Analysis-->
                <FormSection @submitted="onSubmit">
                    <template #title>
                        Analiz Ayarları
                    </template>

                    <template #description>
                        Analiz ayarlarını buradan güncelleyebilirsiniz.
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="default_kline_data_resolution" value="Kline Verilerinin Çözünürlüğü" />
                            <SelectInput
                                id="default_kline_data_resolution"
                                v-model="form.default_kline_data_resolution"
                                class="mt-1 block w-full"
                                :options="klineDataResolutionOptions"
                                value-key="id"
                                label-key="name"
                            />
                            <InputError :message="form.errors.default_kline_data_resolution" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="default_kline_data_time_range" value="Kline Verilerinin Zaman Aralığı" />
                            <SelectInput
                                id="default_kline_data_time_range"
                                v-model="form.default_kline_data_time_range"
                                class="mt-1 block w-full"
                                :options="klineDataTimeRangeOptions"
                                value-key="id"
                                label-key="name"
                            />
                            <InputError :message="form.errors.default_kline_data_time_range" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <ActionMessage :on="form.recentlySuccessful" class="me-3">
                            Kaydedildi.
                        </ActionMessage>

                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing || in_progress">
                            Kaydet
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
