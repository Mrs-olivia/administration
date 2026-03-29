/**
 * Rafraîchissement du statut / commentaire chef (SSOT : table formulaires) pour le secrétariat.
 */

function openActionDeniedModal() {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'action-denied' }));
}

document.addEventListener('alpine:init', () => {
    const badgeBase = 'inline-flex px-2.5 py-0.5 rounded text-xs font-medium ';

    const badgeClasses = {
        yellow: badgeBase + 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
        blue: badgeBase + 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
        green: badgeBase + 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
        red: badgeBase + 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
        gray: badgeBase + 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100',
    };

    const badgeShowBase = 'inline-flex text-sm font-medium px-3 py-1 rounded ';
    const badgeClassesShow = {
        yellow: badgeShowBase + 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
        blue: badgeShowBase + 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
        green: badgeShowBase + 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
        red: badgeShowBase + 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
        gray: badgeShowBase + 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100',
    };

    Alpine.data('secretaireDossierPollBatch', (config) => ({
        pollUrl: config.pollUrl,
        ids: config.ids || [],
        intervalMs: config.intervalMs || 5000,
        timer: null,
        lastSync: null,

        init() {
            if (!this.ids.length) {
                return;
            }
            this.fetch();
            this.timer = setInterval(() => this.fetch(), this.intervalMs);
        },

        destroy() {
            if (this.timer) {
                clearInterval(this.timer);
            }
        },

        async fetch() {
            try {
                const params = new URLSearchParams();
                this.ids.forEach((id) => params.append('ids[]', id));
                const r = await fetch(`${this.pollUrl}?${params.toString()}`, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (r.status === 403) {
                    openActionDeniedModal();
                    return;
                }
                if (!r.ok) {
                    return;
                }
                const data = await r.json();
                const items = data.items || {};
                for (const [id, row] of Object.entries(items)) {
                    const el = document.querySelector(`[data-secretairepoll-status="${id}"]`);
                    if (el && row.status_label) {
                        el.textContent = row.status_label;
                        const variant = row.status_badge_variant || 'gray';
                        el.className = badgeClasses[variant] || badgeClasses.gray;
                    }
                }
                this.lastSync = new Date();
            } catch {
                /* réseau : on retente au prochain cycle */
            }
        },
    }));

    Alpine.data('secretaireDossierPollShow', (config) => ({
        pollUrl: config.pollUrl,
        intervalMs: config.intervalMs || 5000,
        timer: null,
        statusLabel: config.initialStatusLabel,
        variant: config.initialVariant,
        annotation: config.initialAnnotation || '',
        lastSync: null,

        init() {
            this.fetch();
            this.timer = setInterval(() => this.fetch(), this.intervalMs);
        },

        destroy() {
            if (this.timer) {
                clearInterval(this.timer);
            }
        },

        badgeClass() {
            return badgeClassesShow[this.variant] || badgeClassesShow.gray;
        },

        async fetch() {
            try {
                const r = await fetch(this.pollUrl, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (r.status === 403) {
                    openActionDeniedModal();
                    return;
                }
                if (!r.ok) {
                    return;
                }
                const row = await r.json();
                this.statusLabel = row.status_label;
                this.variant = row.status_badge_variant || 'gray';
                this.annotation = row.annotation_chef || '';
                this.lastSync = new Date();
            } catch {
                /* ignore */
            }
        },
    }));
});
