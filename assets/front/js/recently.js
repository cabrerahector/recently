var recently_params = null;
var RecentlyWidget = (function(){

    "use strict";

    var noop = function(){};

    var get = function( url, params, callback ){
        callback = ( 'function' === typeof callback ) ? callback : noop;
        ajax( "GET", url, params, callback );
    };

    var post = function( url, params, callback ){
        callback = ( 'function' === typeof callback ) ? callback : noop;
        ajax( "POST", url, params, callback );
    };

    var ajax = function( method, url, params, callback ){
        /* Create XMLHttpRequest object and set variables */
        var xhr = ( window.XMLHttpRequest )
            ? new XMLHttpRequest()
            : new ActiveXObject( "Microsoft.XMLHTTP" ),
        target = url,
        args = params,
        valid_methods = ["GET", "POST"];
        method = -1 != valid_methods.indexOf( method ) ? method : "GET";
        /* Set request method and target URL */
        xhr.open( method, target + ( "GET" == method ? '?' + args : '' ), true );
        /* Set request headers */
        if ( "POST" == method ) {
            xhr.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
        }
        xhr.setRequestHeader( "X-Requested-With","XMLHttpRequest" );
        /* Hook into onreadystatechange */
        xhr.onreadystatechange = function() {
            if ( 4 === xhr.readyState && 200 === xhr.status ) {
                if ( 'function' === typeof callback ) {
                    callback.call( undefined, xhr.response );
                }
            }
        };
        /* Send request */
        xhr.send( ( "POST" == method ? args : null ) );
    };

    return {
        get: get,
        post: post,
        ajax: ajax
    };

})();

(function(){
    try {
        var recently_json = document.querySelector("script#recently-json");
        recently_params = JSON.parse(recently_json.textContent);
    } catch (err) {
        console.error("Recently: Couldn't read JSON data");
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    var widget_placeholders = document.querySelectorAll('.recently-widget-placeholder');

    if ( widget_placeholders.length ) {
        for( var w = 0; w < widget_placeholders.length; w++ ) {
            fetchWidget(widget_placeholders[w]);
        }
    }

    function fetchWidget(widget_placeholder) {
        RecentlyWidget.get(
            recently_params.ajax_url + '/widget/' + widget_placeholder.getAttribute('data-widget-id').split('-')[1],
            'is_single=' + recently_params.ID + ( recently_params.lang ? '&lang=' + recently_params.lang : '' ),
            function(response) {
                widget_placeholder.insertAdjacentHTML('afterend', JSON.parse(response).widget);

                let parent = widget_placeholder.parentNode;
                parent.removeChild(widget_placeholder);
            }
        );
    }
});