document.addEventListener('DOMContentLoaded', () => {
    const pages = document.querySelector('.widget-banner-pages');
    const addPageButton = document.querySelector('#widget-banner-add-page');
    const pageTemplate = document.querySelector('#widget-banner-page-template');

    if (pages && addPageButton && pageTemplate) {
        let nextIndex = Number(pages.dataset.nextIndex) || 0;

        const normalizeSlug = value => value.trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
        const updateShortcode = card => {
            const slugInput = card.querySelector('.widget-banner-page-slug');
            const shortcode = card.querySelector('.widget-banner-page-shortcode');
            const slug = normalizeSlug(slugInput.value);
            shortcode.textContent = `[widget_banner_${slug || 'identificativo'}]`;

            const duplicate = slug && Array.from(pages.querySelectorAll('.widget-banner-page-slug'))
                .some(input => input !== slugInput && normalizeSlug(input.value) === slug);
            slugInput.setCustomValidity(duplicate ? 'Questo identificativo shortcode è già utilizzato.' : '');
        };
        const slugFromName = name => name.normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        const uniqueSlug = (name, card) => {
            const base = slugFromName(name) || 'configurazione';
            let slug = base;
            let suffix = 2;
            const used = candidate => Array.from(pages.querySelectorAll('.widget-banner-page-slug'))
                .some(input => card.contains(input)
                    ? false
                    : normalizeSlug(input.value) === candidate);
            while (used(slug)) {
                slug = `${base}-${suffix}`;
                suffix += 1;
            }
            return slug;
        };

        pages.addEventListener('input', event => {
            const card = event.target.closest('[data-profile-card]');
            if (event.target.matches('.widget-banner-page-name')) {
                const slugInput = card.querySelector('.widget-banner-page-slug');
                if (!slugInput.dataset.manual || !slugInput.value.trim()) {
                    slugInput.value = uniqueSlug(event.target.value, card);
                }
                updateShortcode(card);
            } else if (event.target.matches('.widget-banner-page-slug')) {
                event.target.dataset.manual = 'true';
                updateShortcode(card);
            }
        });

        pages.addEventListener('click', event => {
            const selectLogoButton = event.target.closest('.widget-banner-select-logo');
            if (selectLogoButton) {
                if (!window.wp || !window.wp.media) {
                    window.alert('La Libreria media di WordPress non è disponibile. Ricarica la pagina e riprova.');
                    return;
                }

                const card = selectLogoButton.closest('[data-profile-card]');
                const mediaFrame = window.wp.media({
                    title: 'Seleziona il logo',
                    button: {text: 'Usa questo logo'},
                    library: {type: 'image'},
                    multiple: false
                });
                mediaFrame.on('select', () => {
                    const attachment = mediaFrame.state().get('selection').first().toJSON();
                    card.querySelector('.widget-banner-logo-id').value = attachment.id;
                    card.querySelector('.widget-banner-logo-preview').src =
                        attachment.sizes && attachment.sizes.medium
                            ? attachment.sizes.medium.url
                            : attachment.url;
                    card.querySelector('.widget-banner-remove-logo').disabled = false;
                });
                mediaFrame.open();
                return;
            }

            const removeLogoButton = event.target.closest('.widget-banner-remove-logo');
            if (removeLogoButton) {
                const card = removeLogoButton.closest('[data-profile-card]');
                card.querySelector('.widget-banner-logo-id').value = '0';
                const preview = card.querySelector('.widget-banner-logo-preview');
                preview.src = preview.dataset.defaultLogo;
                removeLogoButton.disabled = true;
                return;
            }

            const removeButton = event.target.closest('.widget-banner-remove-page');
            if (removeButton) {
                removeButton.closest('[data-profile-card]').remove();
            }
        });

        addPageButton.addEventListener('click', () => {
            const fragment = pageTemplate.content.cloneNode(true);
            const card = fragment.querySelector('[data-profile-card]');
            card.querySelectorAll('[name], [id], label[for]').forEach(element => {
                ['name', 'id', 'for'].forEach(attribute => {
                    const value = element.getAttribute(attribute);
                    if (value) {
                        element.setAttribute(attribute, value.replaceAll('__INDEX__', String(nextIndex)));
                    }
                });
            });
            pages.append(fragment);
            nextIndex += 1;
            pages.querySelector('[data-profile-card]:last-child .widget-banner-page-name').focus();
        });

        pages.querySelectorAll('[data-profile-card]').forEach(updateShortcode);
    }
});