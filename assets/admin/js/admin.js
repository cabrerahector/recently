'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const upload_thumb_button = document.getElementById('upload_thumb_button'),
        reset_thumb_button = document.getElementById('reset_thumb_button'),
        upload_thumb_src_field = document.getElementById('upload_thumb_src'),
        thumbnail_preview = document.getElementById('thumb-review'),
        thumb_source_dropdown = document.getElementById('thumb_source'),
        reset_thumbnail_cache_button = document.getElementById('recently_reset_cache');

    /** Thumbnail upload */
    upload_thumb_button.addEventListener('click', () => {
        const custom_uploader = wp.media({
            title: 'Recently',
            library: { type: 'image' },
            button: { text: 'Use this image' },
            id: 'library-' + (Math.floor(Math.random() * 10) + 1),
            multiple: false
        }).on('select', () => {
            const attachment = custom_uploader.state().get('selection').first().toJSON();
            upload_thumb_src_field.value = attachment.url;

            const img = new Image();
            img.onload = function() {
                thumbnail_preview.innerHTML = '';
                thumbnail_preview.append(this);
            }
            img.alt = '';
            img.src = attachment.url;
        })
        .open();
    });

    reset_thumb_button.addEventListener('click', () => {
        fetch(ajaxurl + '?action=recently_reset_thumbnail')
        .then((response) => {
            return response.text();
        })
        .then((thumb_url) => {
            const img = new Image();
            img.onload = function() {
                thumbnail_preview.innerHTML = '';
                thumbnail_preview.append(this);
            }
            img.src = thumb_url;
            upload_thumb_src_field.value = thumb_url;
        })
        .catch(() => {
            // handle the error
        });
    });

    /** thumbnail source dropdown */
    thumb_source_dropdown.addEventListener('change', (e) => {
        const thumb_custom_field_row = document.getElementById('row_custom_field'),
            thumb_custom_field_rezise_row = document.getElementById('row_custom_field_resize');

        thumb_custom_field_row.style.display = ( 'custom_field' === e.target.value ) ? 'table-row' : 'none';
        thumb_custom_field_rezise_row.style.display = ( 'custom_field' === e.target.value ) ? 'table-row' : 'none';
    });

    /** thumbnail cache reset button */
    reset_thumbnail_cache_button.addEventListener('click', () => {
        if ( confirm(`${recently_admin_params.msg_thumbnail_deletion_warning} \n\n${recently_admin_params.msg_continue}`) ) {
            reset_thumbnail_cache_button.disabled = true;

            fetch(ajaxurl, {
                method: 'POST',
                body: new URLSearchParams({
                    action: 'recently_clear_thumbnail',
                    token: recently_admin_params.nonce
                })
            })
            .then((response) => response.text())
            .then((response) => {
                let msg = '';

                reset_thumbnail_cache_button.disabled = false;

                switch(response) {
                    case '1':
                        msg = recently_admin_params.msg_clear_thumbnail_success;
                        break;
                    case '2':
                        msg = recently_admin_params.msg_thumbnail_cache_already_empty;
                        break;
                    case '4':
                        msg = recently_admin_params.msg_no_permission;
                        break;
                    default:
                        msg = recently_admin_params.msg_invalid_action;
                        break;
                }

                alert(msg);
            });
        }
    });
});