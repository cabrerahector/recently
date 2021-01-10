var recently_params = null;
var RecentlyWidget = (function(){

    "use strict";

    var noop = function(){};
    var supportsShadowDOMV1 = !! HTMLElement.prototype.attachShadow;

    var get = function( url, params, callback ){
        callback = ( 'function' === typeof callback ) ? callback : noop;
        ajax("GET", url, params, callback);
    };

    var post = function( url, params, callback ){
        callback = ( 'function' === typeof callback ) ? callback : noop;
        ajax("POST", url, params, callback);
    };

    var ajax = function( method, url, params, callback ){
        /* Create XMLHttpRequest object and set variables */
        var xhr = new XMLHttpRequest(),
        target = url,
        args = params,
        valid_methods = ["GET", "POST"];
        method = -1 != valid_methods.indexOf(method) ? method : "GET";
        /* Set request method and target URL */
        xhr.open(method, target + ( "GET" == method ? '?' + args : '' ), true);
        /* Set request headers */
        if ( "POST" == method ) {
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        }
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        /* Hook into onreadystatechange */
        xhr.onreadystatechange = function() {
            if ( 4 === xhr.readyState && 200 <= xhr.status && 300 > xhr.status ) {
                if ( 'function' === typeof callback ) {
                    callback.call(undefined, xhr.response);
                }
            }
        };
        /* Send request */
        xhr.send(( "POST" == method ? args : null ));
    };

    var theme = function(recently_list) {
        if ( supportsShadowDOMV1 ) {
            let base_styles = document.createElement('style'),
                dummy_list = document.createElement('ul');

            dummy_list.innerHTML = '<li><a href="#"></a></li>';
            recently_list.parentNode.appendChild(dummy_list);

            let dummy_list_item_styles = getComputedStyle(dummy_list.querySelector('li')),
                dummy_link_item_styles = getComputedStyle(dummy_list.querySelector('li a'));

            base_styles.innerHTML = '.recently-list li {font-size: '+ dummy_list_item_styles.fontSize +'}';
            base_styles.innerHTML += '.recently-list li a {color: '+ dummy_link_item_styles.color +'}';

            recently_list.parentNode.removeChild(dummy_list);

            let recently_list_sr = recently_list.attachShadow({mode: "open"});

            recently_list_sr.append(base_styles);

            while(recently_list.firstElementChild) {
                recently_list_sr.append(recently_list.firstElementChild);
            }
        }
    };

    return {
        get: get,
        post: post,
        ajax: ajax,
        theme: theme
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
    } else {
        var sr = document.querySelectorAll('.recently-sr');

        if ( sr.length ) {
            for( var s = 0; s < sr.length; s++ ) {
                RecentlyWidget.theme(sr[s]);
            }
        }
    }

    function fetchWidget(widget_placeholder) {
        RecentlyWidget.get(
            recently_params.ajax_url + '/widget/' + widget_placeholder.getAttribute('data-widget-id').split('-')[1],
            'is_single=' + recently_params.ID + ( recently_params.lang ? '&lang=' + recently_params.lang : '' ),
            function(response) {
                widget_placeholder.insertAdjacentHTML('afterend', JSON.parse(response).widget);

                let parent = widget_placeholder.parentNode,
                    sr = parent.querySelector('.recently-sr');

                parent.removeChild(widget_placeholder);

                if ( sr ) {
                    RecentlyWidget.theme(sr);
                }
            }
        );
    }
});