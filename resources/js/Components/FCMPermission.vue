<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    isSubscribed: Boolean,
});

const subscribed = ref(props.isSubscribed);
const permission = ref('default');

function unsubscribe() {
    axios.delete(route('fcm.token.remove'))
    .then(() => {
        console.log('Successfully unsubscribed.');
        subscribed.value = false;
    })
    .catch(error => {
        console.error('Error unsubscribing:', error);
    });
}

function subscribe() {
    if (permission.value === 'granted') {
        console.log('Permission already granted. Getting token...');
        getAndSendToken();
        return;
    }

    if (permission.value === 'default') {
        Notification.requestPermission().then((perm) => {
            permission.value = perm;
            if (perm === 'granted') {
                console.log('Permission granted. Getting token...');
                getAndSendToken();
            } else {
                console.log('Permission denied');
            }
        });
    }
}
// function requestPermissionAndGetToken() {
//     console.log("Requesting permission for notifications...");
//     Notification.requestPermission().then((perm) => {
//         permission.value = perm;
//         if (perm === 'granted') {
//             enabled.value = true;
//             console.log('Notification permission granted.');
//             window.getToken(messaging, { vapidKey: 'BMrTLuoCHunyHVyUW3iA8b-_os4U84ESXBG-NMch2nR6gFRCaiO5xYcbU2p1S_ZFr95JSCXHvCNvNj3YWX8D75k' })
//                 .then((currentToken) => {
//                     if (currentToken) {
//                         console.log('FCM Token:', currentToken);
//                         window.sendTokenToServer(currentToken);
//                     } else {
//                         console.log('No registration token available. Request permission to generate one.');
//                     }
//                 }).catch((err) => {
//                     console.log('An error occurred while retrieving token. ', err);
//                 });
//         } else {
//             enabled.value = false;
//             console.log('Unable to get permission to notify.');
//         }
//     });
// }

// Saat komponen dimuat, periksa status izin saat ini
function getAndSendToken() {
    window.getToken(window.messaging, { vapidKey: import.meta.env.VAPID_PUBLIC_KEY})
    .then((currentToken) => {
        if (currentToken) {
            window.sendTokenToServer(currentToken);
            subscribed.value = true;
        } else {
            console.log('Could not get token');
        }
    }).catch((err) => {
        console.log('An error occured while retrieving token. ', err);
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
    if (subscribed.value) {
        unsubscribe();
    } else {
        subscribe();
    }
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
                
                <!-- Label dan Deskripsi -->
                <span class="flex flex-col">
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
                        Aktifkan untuk mendapatkan info penting.
                    </span>
                </span>

                <!-- Toggle Switch -->
                <button 
                    type="button" 
                    @click="handleToggleClick"
                    :class="{ 'bg-indigo-600': subscribed, 'bg-gray-200 dark:bg-gray-600': !subscribed }"
                    :disabled="permission === 'denied' || permission === 'unsupported'"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                    role="switch" 
                    :aria-checked="subscribed">
                    <span 
                        aria-hidden="true" 
                        :class="{ 'translate-x-5': subscribed, 'translate-x-0': !subscribed }"
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
            </div>
        </div>
    </section>
</template>