<script setup>
import Modal from '@/Components/Modal.vue';
import DeleteButton from '@/Components/Button/DeleteButton.vue';
import PrimaryButton from '@/Components/Button/PrimaryButton.vue';
import SecondaryButton from '@/Components/Button/SecondaryButton.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    isSubscribed: Boolean,
});
const showFCMModal = ref(false);

const subscribed = ref(props.isSubscribed);
const permission = ref('default');
const isLoading = ref(false);
const status = ref({ message: '', type: '' });

const closeModal = () => {
    if (isLoading.value) return;
    showFCMModal.value = false;
};

async function confirmAction() {
    isLoading.value = true;
    status.value = { message: '', type: '' }; // Reset status
    try {
        if (subscribed.value) {
            await unsubscribe();
            status.value = { message: 'Berhasil menonaktifkan notifikasi.', type: 'success' };
        } else {
            await subscribe();
            status.value = { message: 'Berhasil mengaktifkan notifikasi!', type: 'success' };
        }
    } catch (error) {
        console.error('Action failed:', error);
        let msg = 'Gagal memproses permintaan.';

        // Handle error koneksi/internet
        if (error.name === 'AbortError' || error.message?.includes('network') || !navigator.onLine) {
            msg = 'Gagal: Periksa koneksi internet Anda atau coba lagi nanti.';
        } else if (error.message === 'Permission denied') {
            msg = 'Gagal: Izin notifikasi ditolak.';
        }

        status.value = { message: msg, type: 'error' };
    } finally {
        isLoading.value = false; // Reset loading dulu

        // Hilangkan pesan (sukses/error) setelah 5 detik
        if (status.value.message) {
            setTimeout(() => {
                status.value = { message: '', type: '' };
            }, 5000);
        }
    }

    // Baru panggil closeModal setelah isLoading = false
    closeModal();
}

function unsubscribe() {
    return axios.delete(route('fcm.token.remove'))
        .then(() => {
            console.log('Successfully unsubscribed.');
            subscribed.value = false;
        })
        .catch(error => {
            console.error('Error unsubscribing:', error);
            throw error;
        });
}

function subscribe() {
    if (permission.value === 'granted') {
        console.log('Permission already granted. Getting token...');
        return getAndSendToken();
    }

    if (permission.value === 'default') {
        return Notification.requestPermission().then((perm) => {
            permission.value = perm;
            if (perm === 'granted') {
                console.log('Permission granted. Getting token...');
                return getAndSendToken();
            } else {
                console.log('Permission denied');
                throw new Error('Permission denied');
            }
        });
    }
    return Promise.resolve();
}

// Saat komponen dimuat, periksa status izin saat ini
function getAndSendToken() {
    return window.getToken(window.messaging, { vapidKey: import.meta.env.VAPID_PUBLIC_KEY })
        .then((currentToken) => {
            if (currentToken) {
                return window.sendTokenToServer(currentToken).then(() => {
                    subscribed.value = true;
                });
            } else {
                console.log('Could not get token');
                throw new Error('Could not get token');
            }
        }).catch((err) => {
            console.log('An error occured while retrieving token. ', err);
            throw err;
        });
}

onMounted(() => {
    if ('Notification' in window && 'serviceWorker' in navigator && 'PushManager' in window) {
        permission.value = Notification.permission;
    } else {
        permission.value = 'unsupported';
    }
});

function handleToggleClick() {
    showFCMModal.value = true;
}
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Pengaturan
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Anda bisa mengatur notifikasi untuk mendapatkan info penting secara real-time.
            </p>
        </header>

        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="flex items-center justify-between">
                <span class="flex flex-col">
                    <div v-if="status.message" :class="{
                        'text-green-600 dark:text-green-400': status.type === 'success',
                        'text-red-600 dark:text-red-400': status.type === 'error'
                    }" class="text-xs font-bold mb-1 animate-pulse">
                        {{ status.message }}
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Notifikasi Push</span>
                    <span v-if="permission === 'denied'" class="text-xs text-red-600 dark:text-red-400">
                        Anda telah memblokir notifikasi. Aktifkan melalui pengaturan browser.
                    </span>
                    <!-- Skenario: Berhasil berlangganan -->
                    <span v-else-if="subscribed" class="text-xs text-green-600 dark:text-green-400">
                        Notifikasi aktif untuk perangkat ini.
                    </span>
                    <!-- Skenario: Belum berlangganan (izin default/granted) -->
                    <span v-else class="text-xs text-gray-500 dark:text-gray-400">
                        Aktifkan untuk mendapatkan notifikasi push (perubahan status berkas, status hak akses ke
                        asesmen, etc).
                    </span>
                    <span v-if="isLoading"
                        class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 pt-1">
                        <LoadingSpinner />
                        <span>Sedang memproses...</span>
                    </span>
                </span>

                <button type="button" @click="handleToggleClick"
                    :class="{ 'bg-indigo-600': subscribed, 'bg-gray-200 dark:bg-gray-600': !subscribed }"
                    :disabled="permission === 'denied' || permission === 'unsupported' || isLoading"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    role="switch" :aria-checked="subscribed">
                    <span aria-hidden="true" :class="{ 'translate-x-5': subscribed, 'translate-x-0': !subscribed }"
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>
        </div>
    </section>
    <Modal :show="showFCMModal" @close="showFCMModal = false">

        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Konfirmasi Notifikasi Push
            </h2>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                Apakah Anda yakin ingin
                <span class="font-bold underline">
                    {{ subscribed ? 'menonaktifkan' : 'mengaktifkan' }}
                </span>
                notifikasi push aplikasi LSP UNTAN pada perangkat ini?
            </p>
            <p v-if="!subscribed" class="mt-2 text-xs text-gray-500 dark:text-gray-500 italic">
                *Anda mungkin akan diminta memberikan izin akses notifikasi oleh browser.
            </p>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal" :disabled="isLoading"> Batal </SecondaryButton>
                <DeleteButton v-if="subscribed" class="ml-3" @click="confirmAction" :disabled="isLoading">
                    <span v-if="isLoading" class="mr-2">
                        <LoadingSpinner />
                    </span>
                    Ya, Nonaktifkan
                </DeleteButton>
                <PrimaryButton v-else @click="confirmAction" class="ml-3" :disabled="isLoading">
                    <span v-if="isLoading" class="mr-2 text-white">
                        <LoadingSpinner class="!text-white" />
                    </span>
                    Ya, Aktifkan
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>