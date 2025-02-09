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
            default_exchange_for_market_data: ''
        })
    },
    exchangeOptions: Array,
});

const in_progress = ref(false)
const form = useForm({
    ai_base_url: '',
    ai_model: '',
    default_exchange_for_market_data: '',
})

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
            </div>
        </div>
    </AppLayout>
</template>
