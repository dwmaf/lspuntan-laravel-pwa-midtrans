export function useFormat() {
    
    const formatCurrency = (value) => {
        if (value === null || value === undefined || value === '') return '';
        
        const number = typeof value === 'string' ? parseFloat(value) : value;
        
        if (isNaN(number)) return '';

        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    };

    const formatDate = (dateString, mode = 'long') => {
        if (!dateString) return '-';
        
        try {
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: mode,
                year: 'numeric'
            });
        } catch (e) {
            return dateString;
        }
    };

    const formatDateTime = (dateString, mode = 'long', withWIB = true) => {
        if (!dateString) return '-';
        
        try {
            const formatted = new Date(dateString).toLocaleString('id-ID', {
                day: 'numeric',
                month: mode,
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            }).replace('pukul', ',').replace('.', ':');
            
            return withWIB ? `${formatted} WIB` : formatted;
        } catch (e) {
            return dateString;
        }
    };

    return {
        formatCurrency,
        formatDate,
        formatDateTime
    };
}