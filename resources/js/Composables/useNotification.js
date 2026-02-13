export function useNotification() {
    const getNotificationLink = (notif) => {
        // Check if url is directly on the model or inside data json
        // Using optional chaining and logical OR to handle various structures
        const baseUrl = notif.url || notif.data?.url;

        if (!baseUrl || baseUrl === '#') return '#';

        const separator = baseUrl.includes('?') ? '&' : '?';
        return `${baseUrl}${separator}notification_id=${notif.id}`;
    };

    return {
        getNotificationLink
    };
}
