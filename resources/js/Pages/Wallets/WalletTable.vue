<script setup>
import TrashIcon from "@/Components/Icons/TrashIcon.vue";
import {router} from "@inertiajs/vue3";
import CoinsIcon from "@/Components/Icons/CoinsIcon.vue";
import WalletBalance from "@/Pages/Wallets/WalletBalance.vue";

defineProps({
    wallets: {
        type: Array,
        default: () => [],
    },
})

const deleteWallet = (id) => {
    if (confirm('Bu cüzdanı silmek istediğinize emin misiniz?')) {
        axios.delete(route('wallets.destroy', id)).then(() => {
            router.visit(route('wallets.index'), {
                method: 'get',
                preserveState: true,
                preserveScroll: true,
            })
        })
    }
}
</script>

<template>
    <div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cüzdan Adı
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Bakiye
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sahip
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        İşlemler
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="wallet in wallets" :key="wallet.id">
                    <!--Name-->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ wallet.name }}
                            </div>
                        </div>
                    </td>

                    <!--Exchange-->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ wallet.exchange }}</div>
                    </td>

                    <!--Owner-->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ wallet.user.name }}</div>
                    </td>

                    <!--Actions-->
                    <td>
                        <div class="flex justify-end items-center space-x-2 px-6">
                            <!--Get Balance-->
                            <button type="button" @click="$emit('showWalletBalance', wallet)">
                                <CoinsIcon class="w-4 h-4 text-gray-500" />
                            </button>
                            <!--Delete-->
                            <button type="button" @click="deleteWallet(wallet.id)">
                                <TrashIcon class="w-4 h-4 text-red-500" />
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
