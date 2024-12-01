import icons from '../icons';
import { RecentlyWidgetBlockEdit } from './edit';

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;

registerBlockType('recently/widget', {
    title: 'Recently',
    category: 'widgets',
    icon: icons.recently,
    description: __('A highly customizable block that displays your most recent posts.', 'recently'),
    keywords: ['recent', 'posts', 'recently'],

    attributes: {
        _editMode: {
            type: 'boolean',
            default: true
        },
        _isSelected: {
            type: 'boolean',
            default: false
        },
        blockID: {
            type: 'string'
        },
        title: {
            type: 'string',
        },
        posts_per_page: {
            type: 'number',
            default: 10
        },
        offset: {
            type: 'number',
            default: 0
        },
        /* filters */
        post_type: {
            type: 'string',
            default: 'post'
        },
        post_id: {
            type: 'string',
            default: ''
        },
        author_id: {
            type: 'string',
            default: ''
        },
        taxonomy: {
            type: 'string',
            default: ''
        },
        term_id: {
            type: 'string',
            default: ''
        },
        term_slug: {
            type: 'string',
            default: ''
        },
        /* post settings */
        shorten_title: {
            type: 'boolean',
            default: false
        },
        title_length: {
            type: 'number',
            default: 0
        },
        title_by_words: {
            type: 'number',
            default: 0
        },
        display_post_excerpt: {
            type: 'boolean',
            default: false
        },
        excerpt_format: {
            type: 'boolean',
            default: false
        },
        excerpt_length: {
            type: 'number',
            default: 0
        },
        excerpt_by_words: {
            type: 'number',
            default: 0
        },
        display_post_thumbnail: {
            type: 'boolean',
            default: false
        },
        thumbnail_width: {
            type: 'number',
            default: 0
        },
        thumbnail_height: {
            type: 'number',
            default: 0
        },
        thumbnail_build: {
            type: 'string',
            default: 'manual'
        },
        thumbnail_size: {
            type: 'string',
            default: ''
        },
        rating: {
            type: 'boolean',
            default: false
        },
        /* stats tag settings */
        meta_comments: {
            type: 'boolean',
            default: true
        },
        meta_views: {
            type: 'boolean',
            default: false
        },
        meta_author: {
            type: 'boolean',
            default: false
        },
        meta_date: {
            type: 'boolean',
            default: false
        },
        meta_date_format: {
            type: 'string',
            default: 'F j, Y'
        },
        meta_taxonomy: {
            type: 'boolean',
            default: false
        },
        meta_taxonomy_list: {
            type: 'array',
            default: ['category']
        },
        /* HTML markup settings */
        custom_html: {
            type: 'boolean',
            default: false
        },
        header_start: {
            type: 'string',
            default: '<h2>'
        },
        header_end: {
            type: 'string',
            default: '</h2>'
        },
        recently_start: {
            type: 'string',
            default: '<ul class="recently-list">'
        },
        recently_end: {
            type: 'string',
            default: '</ul>'
        },
        post_html: {
            type: 'string',
            default: '<li class="{current_class}">{thumb} {title} {meta}</li>'
        },
        theme: {
            type: 'string',
            default: ''
        },
    },
    supports: {
        anchor: true,
        align: true,
        html: false
    },
    example: {
        attributes: {
            _editMode: false,
            title: 'Recent Posts',
            limit: 3,
            display_post_excerpt: true,
            excerpt_length: 75,
            display_post_thumbnail: true,
            thumbnail_width: 75,
            thumbnail_height: 75,
            meta_comments: false,
            meta_taxonomy: true,
            custom_html: true,
            recently_start: '<ul class="recently-list recently-cards">',
            post_html: '<li>{thumb_img} <div class="recently-item-data"><div class="taxonomies">{taxonomy}</div>{title} <p class="wpp-excerpt">{excerpt}</p></div></li>',
            theme: 'cards'
        }
    },

    edit: RecentlyWidgetBlockEdit,

    save: () => {
        return null;
    }
});
