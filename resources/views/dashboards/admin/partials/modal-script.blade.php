<script>
    (() => {
        const openModal = (modal) => {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            const firstField = modal.querySelector('input, select, textarea, button');
            firstField?.focus();
        };

        const closeModal = (modal) => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        };

        document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                const modal = document.getElementById(trigger.dataset.modalOpen);
                if (modal) {
                    openModal(modal);
                }
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                const modal = trigger.closest('.admin-modal');
                if (modal) {
                    closeModal(modal);
                }
            });
        });

        document.querySelectorAll('.admin-modal').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal(modal);
                }
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            document.querySelectorAll('.admin-modal.is-open').forEach(closeModal);
        });
    })();
</script>
