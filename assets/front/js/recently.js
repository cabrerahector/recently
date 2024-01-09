const recently_params = document.currentScript.dataset;
const RecentlyWidget = (function(){

    "use strict";

    const noop = function(){};

    const get = function( url, params, callback, additional_headers ){
        callback = ( 'function' === typeof callback ) ? callback : noop;
        ajax( "GET", url, params, callback, additional_headers );
    };

    const post = function( url, params, callback, additional_headers ){
        callback = ( 'function' === typeof callback ) ? callback : noop;
        ajax( "POST", url, params, callback, additional_headers );
    };

    const ajax = function( method, url, params, callback, additional_headers ){
        /* Create XMLHttpRequest object and set variables */
        let xhr = new XMLHttpRequest(),
            target = url,
            args = params,
            valid_methods = ["GET", "POST"],
            headers = {
                'X-Requested-With': 'XMLHttpRequest'
            };

        method = -1 != valid_methods.indexOf( method ) ? method : "GET";

        /* Set request headers */
        if ( 'POST' == method ) {
            headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if ( 'object' == typeof additional_headers && Object.keys(additional_headers).length ) {
            headers = Object.assign({}, headers, additional_headers);
        }

        /* Set request method and target URL */
        xhr.open( method, target + ( 'GET' == method ? '?' + args : '' ), true );

        for (const key in headers) {
            if ( headers.hasOwnProperty(key) ) {
                xhr.setRequestHeader( key, headers[key] );
            }
        }

        /* Hook into onreadystatechange */
        xhr.onreadystatechange = function() {
            if ( 4 === xhr.readyState && 200 <= xhr.status && 300 > xhr.status ) {
                if ( 'function' === typeof callback ) {
                    callback.call( undefined, xhr.response );
                }
            }
        };

        /* Send request */
        xhr.send( ( 'POST' == method ? args : null ) );
    };

    const theme = function(recently_list) {
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
    };

    return {
        get: get,
        post: post,
        ajax: ajax,
        theme: theme
    };

})();

document.addEventListener('DOMContentLoaded', function() {
    const widget_placeholders = document.querySelectorAll('.recently-widget-placeholder, .recently-widget-block-placeholder');
    let w = 0;

    while ( w < widget_placeholders.length ) {
        fetchWidget(widget_placeholders[w]);
        w++;
    }

    let sr = document.querySelectorAll('.recently-sr');

    if ( sr.length ) {
        for( let s = 0; s < sr.length; s++ ) {
            RecentlyWidget.theme(sr[s]);
        }
    }

    function fetchWidget(widget_placeholder) {
        let widget_id_attr = widget_placeholder.getAttribute('data-widget-id'),
            method = 'GET',
            url = '',
            headers = {},
            params = '';

        if ( widget_id_attr ) {
            url = recently_params.apiUrl + '/v1/widget/' + widget_id_attr.split('-')[1];
            params = 'is_single=' + recently_params.postId + ( recently_params.lang ? '&lang=' + recently_params.lang : '' );
        } else {
            method = 'POST';
            url = recently_params.apiUrl + '/v2/widget?is_single=' + recently_params.postId + ( recently_params.lang ? '&lang=' + recently_params.lang : '' );
            headers = {
                'Content-Type': 'application/json'
            };

            let json_tag = widget_placeholder.parentNode.querySelector('script[type="application/json"]');

            if ( json_tag ) {
                let args = JSON.parse(json_tag.textContent);
                params = JSON.stringify(args);
            }
        }

        RecentlyWidget.ajax(
            method,
            url,
            params,
            function(response) {
                renderWidget(response, widget_placeholder);
            },
            headers
        );
    }

    function renderWidget(response, widget_placeholder) {
        widget_placeholder.insertAdjacentHTML('afterend', JSON.parse(response).widget);

        let parent = widget_placeholder.parentNode,
            sr = parent.querySelector('.recently-sr'),
            json_tag = parent.querySelector('script[type="application/json"]');

        if ( json_tag ) {
            parent.removeChild(json_tag);
        }

        parent.removeChild(widget_placeholder);
        parent.classList.add('recently-ajax');

        if ( sr ) {
            RecentlyWidget.theme(sr);
        }

        let event = new Event("recently-onload", {"bubbles": true, "cancelable": false});
        parent.dispatchEvent(event);
    }
});