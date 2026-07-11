export function useActivityLog() {
    const getParsedProperties = (log) => {
        return typeof log.properties === 'string' ? JSON.parse(log.properties) : (log.properties || {});
    };

    const getRoleLabel = (log) => {
        if (!log || !log.causer || !log.causer.roles) {
            return '';
        }
        const isAdmin = log.causer.roles.some(role => role.name === 'admin');
        return isAdmin ? 'Admin ' : '';
    };

    const getUserLogMessage = (log) => {
        if (log.event !== 'updated' || log.subject_type !== 'App\\Models\\User') return null;
        const props = getParsedProperties(log);
        return props?.attributes?.banned_at ? ' memblokir akses login ' : ' memulihkan akses login ';
    };

    const getSkemaLogMessage = (log) => {
        if (log.subject_type !== 'App\\Models\\Skema') return null;
        const props = getParsedProperties(log);
        const namaSkema = log.subject?.nama_skema ?? '';

        if (log.event === 'created') return ' menambahkan ' + namaSkema;
        if (log.event === 'deleted') return ' menghapus ' + namaSkema;

        if (log.event === 'updated') {
            const oldKeys = Object.keys(props.old || {});
            const attrKeys = Object.keys(props.attributes || {});
            const onlyIsActive = oldKeys.length === 1 && oldKeys[0] === 'is_active'
                && attrKeys.length === 1 && attrKeys[0] === 'is_active';

            if (onlyIsActive) {
                return props.attributes.is_active == 0
                    ? ' menonaktifkan skema ' + namaSkema
                    : ' mengaktifkan skema ' + namaSkema;
            }
            return ' mengubah data ' + namaSkema;
        }

        return null;
    };

    const getAsesorLogMessage = (log) => {
        if (log.subject_type !== 'App\\Models\\Asesor') return null;
        const props = getParsedProperties(log);
        const namaAsesor = props.asesor_user_name ?? log.subject?.user?.name ?? '';

        if (log.event === 'created') return ' menambahkan asesor ' + namaAsesor;
        if (log.event === 'deleted') return ' menghapus akun asesor ' + namaAsesor;

        if (log.event === 'updated') {
            const oldKeys = Object.keys(props.old || {});
            const attrKeys = Object.keys(props.attributes || {});
            const onlyIsActive = oldKeys.length === 1 && oldKeys[0] === 'is_active'
                && attrKeys.length === 1 && attrKeys[0] === 'is_active';

            if (onlyIsActive) {
                return props.attributes.is_active == 0
                    ? ' menonaktifkan asesor ' + namaAsesor
                    : ' mengaktifkan asesor ' + namaAsesor;
            }
            if ('is_active' in (props.old ?? {})) {
                const suffix = props.attributes?.is_active == 0
                    ? ' dan menonaktifkannya'
                    : ' dan mengaktifkannya';
                return ' mengubah data asesor ' + namaAsesor + suffix;
            }
            return ' mengubah data asesor ' + namaAsesor;
        }

        return null;
    };

    return {
        getParsedProperties,
        getRoleLabel,
        getUserLogMessage,
        getSkemaLogMessage,
        getAsesorLogMessage,
    };
}
