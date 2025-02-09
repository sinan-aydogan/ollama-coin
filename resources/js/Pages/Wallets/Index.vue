<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import WalletTable from "@/Pages/Wallets/WalletTable.vue";
import {ref} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CreateWallet from "@/Pages/Wallets/CreateWallet.vue";
import WalletBalance from "@/Pages/Wallets/WalletBalance.vue";

defineProps({
    wallets: Array,
    exchangeOptions: Array,
    userOptions: Array,
})

const is_visible_create_wallet_modal = ref(false);
const is_visible_wallet_balance_modal = ref(false);
const selectedWallet = ref(null);

const showWalletBalanceModal = (wallet)=>{
    selectedWallet.value = wallet
    is_visible_wallet_balance_modal.value = true
}

const closeWalletBalanceModal = ()=>{
    is_visible_wallet_balance_modal.value = false
    selectedWallet.value = null
}
</script>

<template>
    <AppLayout title="Cüzdanlar">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cüzdanlar
            </h2>
        </template>

        <template #actions>
            <div class="flex items-center justify-end">
                <PrimaryButton @click="is_visible_create_wallet_modal = true">
                    Cüzdan Ekle
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <WalletTable
                        :wallets
                        @showWalletBalance="showWalletBalanceModal"
                    />
                </div>
            </div>
        </div>

        <CreateWallet
            :is_visible="is_visible_create_wallet_modal"
            :exchangeOptions="exchangeOptions"
            :userOptions="userOptions"
            @close="is_visible_create_wallet_modal = false"
            @submit="is_visible_create_wallet_modal = false"
        />

        <WalletBalance
            :is_visible="is_visible_wallet_balance_modal"
            :wallet="selectedWallet"
            @close="closeWalletBalanceModal"
        />
    </AppLayout>
</template>
