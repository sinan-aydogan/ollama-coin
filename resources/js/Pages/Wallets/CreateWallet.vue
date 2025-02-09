<script setup>

import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import {computed, reactive} from "vue";
import TrashIcon from "@/Components/Icons/TrashIcon.vue";

defineProps({
    is_visible: Boolean,
    exchangeOptions: Array,
    userOptions: Array,
})

const emit = defineEmits(['close', 'submit'])

const form = useForm({
    name: '',
    exchange: null,
    user_id: null,
    api_credentials: {},
})
const apiCredentialOptions = [
    {id: 'key', name: 'API Key'},
    {id: 'secret', name: 'API Secret'},
    {id: 'passphrase', name: 'API Passphrase'},
]

const apiCredentialOptionsList = computed(()=>{
    return apiCredentialOptions.filter(option=>!Object.keys(form.api_credentials).includes(option.id))
})

const apiCredentialTemp = reactive({
    type: '',
    value: ''
})

const onClose = ()=>{
    emit('close')
}

const addApiCredential = ()=>{
    if (form.api_credentials.hasOwnProperty(apiCredentialTemp.type)) return
    form.api_credentials[apiCredentialTemp.type] = apiCredentialTemp.value
    apiCredentialTemp.type = ''
    apiCredentialTemp.value = ''
}

const removeApiCredential = (key)=>{
    delete form.api_credentials[key]
}

const onsubmit = ()=>{
    form.post(route('wallets.store'), {
        preserveScroll: true,
        onSuccess: ()=> {
            form.reset()
            emit('submit')
            onClose()
        },
    })
}
</script>

<template>
    <DialogModal :show="is_visible" @close="onClose">
        <template #title>
            Yeni Cüzdan Ekle
        </template>

        <template #content>
            <!--Wallet Name-->
            <div class="mt-4">
                <InputLabel for="name" value="Cüzdan Adı" />
                <TextInput
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Cüzdan Adı"
                />

                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!--Exchange-->
            <div class="mt-4">
                <InputLabel for="exchange" value="Borsa" />
                <SelectInput
                    v-model="form.exchange"
                    :options="exchangeOptions"
                    class="mt-1 block w-3/4"
                    placeholder="Borsa"
                />

                <InputError :message="form.errors.exchange" class="mt-2" />
            </div>

            <!--Owner-->
            <div class="mt-4">
                <InputLabel for="user_id" value="Kullanıcı" />
                <SelectInput
                    v-model="form.user_id"
                    :options="userOptions"
                    class="mt-1 block w-3/4"
                    placeholder="Kullanıcı"
                />

                <InputError :message="form.errors.user_id" class="mt-2" />
            </div>

            <!--API Credentials-->
            <div class="mt-4">
                <InputLabel for="api_credentials" value="API Bilgileri" />

                <div v-for="(value, key) in form.api_credentials" :key="key">
                    <div class="flex items-center justify-between space-x-5 border w-full rounded-md p-2 my-2">
                        <div class="flex items-center space-x-5">
                            <div class="text-sm font-medium text-gray-900 w-24 border-r shrink-0 pl-3">
                                {{ key }}
                            </div>
                            <div class="text-sm text-gray-900 w-full overflow-ellipsis">
                                {{ value }}
                            </div>
                        </div>
                        <div class="">
                            <button type="button" @click="removeApiCredential(key)">
                                <TrashIcon class="w-4 h-4 text-red-500" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-5">
                    <TextInput
                        v-model="apiCredentialTemp.value"
                        type="text"
                        class="mt-1 block w-3/4"
                        placeholder="Anahtar Değeri"
                    />
                    <SelectInput
                        v-model="apiCredentialTemp.type"
                        :options="apiCredentialOptionsList"
                        class="mt-1 block w-1/4"
                        placeholder="Anahtar Tipi"
                    />
                    <button type="button" @click="addApiCredential" class="disabled:cursor-not-allowed" :disabled="!apiCredentialTemp.type || !apiCredentialTemp.value">
                        <PlusIcon class="w-4 h-4 text-gray-500" />
                    </button>
                </div>

                <InputError :message="form.errors.api_credentials" class="mt-2" />
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="onClose">
                İptal
            </SecondaryButton>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="onsubmit">
                Kaydet
            </PrimaryButton>
        </template>
    </DialogModal>
</template>

<style scoped>

</style>
