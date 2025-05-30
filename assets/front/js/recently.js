'use strict';

const recently_params = document.currentScript.dataset;
const RecentlyWidget = (() => {
    const noop = () => {};

    const get = (url, params, callback = noop, additional_headers = {}) => {
        ajax('GET', url, params, callback, additional_headers);
    };

    const post = (url, params, callback = noop, additional_headers = {}) => {
        ajax('POST', url, params, callback, additional_headers);
    };

    const ajax = (method, url, params, callback, additional_headers) => {
        const valid_methods = ['GET', 'POST'];
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            ...additional_headers
        };

        if ( ! valid_methods.includes(method) ) {
            method = 'GET';
        }

        fetch(url + (method === 'GET' ? '?' + params : ''), {
            method,
            headers,
            body: method === 'POST' ? params : null
        })
        .then(response => {
            if ( ! response.ok ) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => callback(data))
        .catch(error => console.error('Fetch error:', error));
    };

    const theme = (recently_list) => {
        const base_styles = document.createElement('style'),
            dummy_list = document.createElement('ul');

        dummy_list.innerHTML = '<li><a href="#"></a></li>';
        recently_list.parentNode.appendChild(dummy_list);

        const dummy_list_item_styles = getComputedStyle(dummy_list.querySelector('li')),
            dummy_link_item_styles = getComputedStyle(dummy_list.querySelector('li a'));

        base_styles.innerHTML = `.recently-list li {font-size: ${dummy_list_item_styles.fontSize}}`;
        base_styles.innerHTML += `.recently-list li a {color: ${dummy_link_item_styles.color}}`;

        recently_list.parentNode.removeChild(dummy_list);

        const recently_list_sr = recently_list.attachShadow({mode: 'open'});

        recently_list_sr.append(base_styles);

        while (recently_list.firstElementChild) {
            recently_list_sr.append(recently_list.firstElementChild);
        }
    };

    return {
        get,
        post,
        ajax,
        theme
    };

})();

document.addEventListener('DOMContentLoaded', () => {
    if ( ! Object.keys(recently_params).length ) {
        console.error('Recently params not found, if you are using a JS minifier tool please add recently.min.js to its exclusion list');
        return;
    }

    const widget_placeholders = document.querySelectorAll('.recently-widget-placeholder, .recently-widget-block-placeholder');
    widget_placeholders.forEach(widget_placeholder => fetchWidget(widget_placeholder));

    const sr = document.querySelectorAll('.recently-sr');
    sr.forEach(s => RecentlyWidget.theme(s));

    function fetchWidget(widget_placeholder) {
        const widget_id_attr = widget_placeholder.getAttribute('data-widget-id'),
            method = (widget_id_attr) ? 'GET' : 'POST',
            headers = {
                'Content-Type': widget_id_attr ? 'application/x-www-form-urlencoded' : 'application/json'
            };

        let params = `is_single=${recently_params.postId}${recently_params.lang ? '&lang=' + recently_params.lang : ''}`,
            url = recently_params.apiUrl;

        if ( widget_id_attr ) {
            url += `/v1/widget/${widget_id_attr.split('-')[1]}`;
        } else {
            url += `/v2/widget`;
            const json_tag = widget_placeholder.parentNode.querySelector('script[type="application/json"]');

            if ( json_tag ) {
                try {
                    const args = JSON.parse(json_tag.textContent);
                    params = JSON.stringify({...Object.fromEntries(new URLSearchParams(params)), ...args});
                } catch (e) {
                    console.error('Failed to parse widget JSON tag:', e.message);
                    return;
                }
            }
        }

        RecentlyWidget.ajax(
            method,
            url,
            params,
            response => renderWidget(response, widget_placeholder),
            headers
        );
    }

    function renderWidget(response, widget_placeholder) {
        let widgetHTML;

        try {
            widgetHTML = JSON.parse(response).widget;
        } catch (e) {
            console.error('Failed to parse widget response:', e.message, response);
            return;
        }

        widget_placeholder.insertAdjacentHTML('afterend', widgetHTML);

        const parent = widget_placeholder.parentNode,
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

        const event = new Event('recently-onload', { bubbles: true, cancelable: false });
        parent.dispatchEvent(event);
    }
});