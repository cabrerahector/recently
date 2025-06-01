'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const upload_thumb_button = document.getElementById('upload_thumb_button'),
        upload_thumb_src_field = document.getElementById('upload_thumb_src'),
        thumbnail_preview = document.getElementById('thumb-review'),
        thumb_source_dropdown = document.getElementById('thumb_source');

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

    /** thumbnail source dropdown */
    thumb_source_dropdown.addEventListener('change', (e) => {
        const thumb_custom_field_row = document.getElementById('row_custom_field'),
            thumb_custom_field_rezise_row = document.getElementById('row_custom_field_resize');

        thumb_custom_field_row.style.display = ( 'custom_field' === e.target.value ) ? 'table-row' : 'none';
        thumb_custom_field_rezise_row.style.display = ( 'custom_field' === e.target.value ) ? 'table-row' : 'none';
    });
});