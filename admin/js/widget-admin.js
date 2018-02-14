jQuery(function($){

    function init() {

        var rw = $('.recently-ph').closest('div.widget:not(".ui-draggable")').find('.widget-content');

        // taxonomy filter
        rw.children('p.taxonomy-filter').children('label').children('input[type=radio]').on('change', function(){

            var me = $(this),
            tax_field = rw.find('.tax-field');

            if ( '' == me.val() ) {
                tax_field.hide();

                tax_field.children('.tax-id-field').show();
                tax_field.children('.tax-slug-field').hide();

                // Clear fields
                tax_field.children('.tax-id-field').children('input[type=text]').val('');
                tax_field.children('.tax-slug-field').children('input[type=text]').val('');
            } else {
                tax_field.show();

                if ( 'post_format' == me.val() ) {
                    tax_field.children('.tax-slug-field').show();
                    tax_field.children('.tax-id-field').hide();

                    tax_field.children('.tax-slug-field').children('input[type=text]').val('');
                } else {
                    tax_field.children('.tax-id-field').show();
                    tax_field.children('.tax-slug-field').hide();

                    tax_field.children('.tax-id-field').children('input[type=text]').val('');
                }
            }

        });

        // checkboxes
        rw.children('input.checkbox-shorten-title').on('change', function(){
            var me = $(this);

            if ( me.is(':checked') ) {
                me.parent().children('div.shorten-title').show();
            } else {
                me.parent().children('div.shorten-title').hide();
            }
        });

        rw.children('input.checkbox-excerpt').on('change', function(){
            var me = $(this);

            if ( me.is(':checked') ) {
                me.parent().children('div.excerpt').show();
            } else {
                me.parent().children('div.excerpt').hide();
            }
        });

        rw.children('input.checkbox-thumbnail').on('change', function(){
            var me = $(this);

            if ( me.is(':checked') ) {
                me.parent().children('div.thumbnail').show();
            } else {
                me.parent().children('div.thumbnail').hide();
            }
        });

        rw.children('input.checkbox-date').on('change', function(){
            var me = $(this);

            if ( me.is(':checked') ) {
                me.parent().children('div.date-formats').show();
            } else {
                me.parent().children('div.date-formats').hide();
            }
        });

        rw.children('input.checkbox-taxonomy').on('change', function(){
            var me = $(this);

            if ( me.is(':checked') ) {
                me.parent().children('div.taxonomies').show();
            } else {
                me.parent().children('div.taxonomies').hide();
            }
        });

        rw.children('input.checkbox-custom-html').on('change', function(){
            var me = $(this);

            if ( me.is(':checked') ) {
                me.parent().children('div.custom-html').show();
            } else {
                me.parent().children('div.custom-html').hide();
            }
        });

    }

    $( document ).on( 'widget-added', init );
    $( document ).on( 'widget-updated', init );

    init();

});