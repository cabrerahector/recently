import { escape_html, unescape_html } from '../utils';

const { serverSideRender: ServerSideRender } = wp;
const { Component, Fragment } = wp.element;
const { BlockControls } = wp.blockEditor;
const { CheckboxControl, Disabled, SelectControl, Spinner, TextareaControl, TextControl, Toolbar, ToolbarButton } = wp.components;
const { __ } = wp.i18n;
const endpoint = 'recently/v1';

export class RecentlyWidgetBlockEdit extends Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            error: null,
            editMode: true,
            themes: null,
            imgSizes: null,
            taxonomies: null
        }

        const { attributes, setAttributes, clientId } = this.props;
        const { blockID } = attributes;

        if ( ! blockID ) {
            setAttributes( { blockID: clientId } );
        }
    }

    componentDidMount()
    {
        const { attributes } = this.props;

        this.getThemes();
        this.getImageSizes();
        this.getTaxonomies();

        this.setState({ editMode: attributes._editMode });
    }

    getThemes()
    {
        wp.apiFetch({ path: endpoint + '/themes' })
        .then(
            ( themes ) => {
                this.setState({
                    themes
                });
            },
            ( error ) => {
                this.setState({
                    error,
                    themes: null
                });
            }
        );
    }

    getImageSizes()
    {
        wp.apiFetch({ path: endpoint + '/thumbnails' })
        .then(
            ( imgSizes ) => {
                this.setState({
                    imgSizes
                });
            },
            ( error ) => {
                this.setState({
                    error,
                    imgSizes: null
                });
            }
        );
    }

    getTaxonomies()
    {
        const { attributes } = this.props;

        wp.apiFetch({ path: endpoint + '/taxonomies' })
        .then(
            ( taxonomies ) => {
                if ( taxonomies ) {
                    let tax = attributes.taxonomy.split(';'),
                        term_id = attributes.term_id.split(';'),
                        term_slug = attributes.term_slug.split(';');

                    if ( tax.length && (tax.length == term_id.length || tax.length == term_slug.length) ) {
                        let selected_taxonomies = {};

                        for( var t = 0; t < tax.length; t++ ) {
                            selected_taxonomies[tax[t]] = term_id[t];
                        }

                        for( const tax in taxonomies ) {
                            taxonomies[tax]._terms = 'undefined' != typeof selected_taxonomies[tax] ? selected_taxonomies[tax] : '';
                        }
                    }
                }

                this.setState({
                    taxonomies
                });
            },
            ( error ) => {
                this.setState({
                    error,
                    taxonomies: null
                });
            }
        );
    }

    getBlockControls()
    {
        const { setAttributes } = this.props;
        const _self = this;

        function onPreviewChange()
        {
            let editMode = ! _self.state.editMode;
            _self.setState({ editMode: editMode });
            setAttributes({ _editMode: editMode });
        }

        return (
            <BlockControls>
                <Toolbar label="{ __('Settings') }">
                    <ToolbarButton
                        label={ this.state.editMode ? __('Preview', 'recently') : __('Edit', 'recently') }
                        icon={ this.state.editMode ? "format-image" : "edit" }
                        onClick={onPreviewChange}
                    />
                </Toolbar>
            </BlockControls>
        );
    }

    getMainFields()
    {
        const { attributes, setAttributes } = this.props;

        function onTitleChange(value)
        {
            value = escape_html(unescape_html(value));
            setAttributes({ title: value });
        }

        function onLimitChange(value)
        {
            let limit = Number.isInteger(Number(value)) && Number(value) > 0 ? value : 10;
            setAttributes({ posts_per_page: Number(limit) });
        }

        function onOffsetChange(value)
        {
            let limit = Number.isInteger(Number(value)) && Number(value) >= 0 ? value : 10;
            setAttributes({ offset: Number(limit) });
        }

        return <Fragment>
            <TextControl
                label={__('Title', 'recently')}
                value={attributes.title}
                onChange={onTitleChange}
            />
            <TextControl
                label={__('Limit', 'recently')}
                help={__('Max. number of posts to show.', 'recently')}
                value={attributes.posts_per_page}
                onChange={onLimitChange}
            />
            <TextControl
                label={__('Offset', 'recently')}
                help={__('Number of posts to displace or pass over.', 'recently')}
                value={attributes.offset}
                onChange={onOffsetChange}
            />
        </Fragment>;
    }

    getFiltersFields()
    {
        const { attributes, setAttributes } = this.props;
        const _self = this;

        function onPostTypeChange(value)
        {
            let new_value = value.replace(/[^a-z0-9-_\,]+/gi, '');
            setAttributes({ post_type: new_value });
        }

        function onPostIDExcludeChange(value)
        {
            let new_value = value.replace(/[^0-9\,]/g, '');
            setAttributes({ post_id: new_value });
        }

        function onAuthorChange(value)
        {
            let new_value = value.replace(/[^0-9\,]/g, '');
            setAttributes({ author_id: new_value });
        }

        function onTaxChange(taxonomy_name, terms)
        {
            let taxonomies = _self.state.taxonomies;

            terms = terms.replace(/[^0-9-\,]/g, '');

            if ( taxonomies && 'undefined' != typeof taxonomies[taxonomy_name] ) {
                taxonomies[taxonomy_name]._terms = terms;
                _self.setState({ taxonomies: taxonomies });
            }
        }

        function onTaxBlur(taxonomy_name)
        {
            let taxonomies = _self.state.taxonomies;

            if ( taxonomies && 'undefined' != typeof taxonomies[taxonomy_name] ) {
                let terms_arr = taxonomies[taxonomy_name]._terms.split(',');

                // Remove invalid values
                if ( terms_arr.length )
                    terms_arr = terms_arr.map((term) => term.trim())
                        .filter((term) => '' != term && '-' != term);

                // Remove duplicates
                if ( terms_arr.length )
                    terms_arr = Array.from(new Set(terms_arr));

                taxonomies[taxonomy_name]._terms = terms_arr.join(',');

                _self.setState({ taxonomies });

                let tax = '',
                    term_id = '';

                for ( let key in _self.state.taxonomies ) {
                    if ( _self.state.taxonomies.hasOwnProperty(key) ) {

                        if ( ! _self.state.taxonomies[key]._terms.length )
                            continue;

                        tax += key + ';';
                        term_id += _self.state.taxonomies[key]._terms + ';';
                    }
                }

                // Remove trailing semicolon
                if ( tax && term_id ) {
                    tax = tax.replace(new RegExp(';$'), '');
                    term_id = term_id.replace(new RegExp(';$'), '');
                }

                setAttributes({ taxonomy: tax, term_id: term_id });
            }
        }

        let taxonomies = [];

        if ( this.state.taxonomies ) {
            for( const tax in this.state.taxonomies ) {
                taxonomies.push(
                    {
                        name: this.state.taxonomies[tax].name,
                        label: this.state.taxonomies[tax].labels.singular_name + ' (' + this.state.taxonomies[tax].name + ')',
                        terms: this.state.taxonomies[tax]._terms
                    }
                );
            }
        }

        return <Fragment>
            <p className='not-a-legend'><strong>{__('Filters', 'recently')}</strong></p>
            <TextControl
                label={__('Post Type(s)', 'recently')}
                help={__(' Post types, separated by comma.', 'recently')}
                value={attributes.post_type}
                onChange={onPostTypeChange}
            />
            <TextControl
                label={__('Post ID(s) to exclude', 'recently')}
                help={__(' Post / Page IDs, separated by comma.', 'recently')}
                value={attributes.post_id}
                onChange={onPostIDExcludeChange}
            />
            <TextControl
                label={__('Author ID(s)', 'recently')}
                help={__(' Author IDs, separated by comma (prefix a minus sign to exclude).', 'recently')}
                value={attributes.author_id}
                onChange={onAuthorChange}
            />
            { taxonomies && taxonomies.filter((tax) => 'post_format' != tax.name).map((tax) =>
                {
                    return (
                        <TextControl
                            label={tax.label}
                            help={__('Term IDs must be comma separated, prefix a minus sign to exclude.', 'recently')}
                            value={tax.terms}
                            onChange={(terms) => onTaxChange(tax.name, terms)}
                            onBlur={() => onTaxBlur(tax.name)}
                        />
                    );
                }
            )}
        </Fragment>;
    }

    getPostSettingsFields()
    {
        const { attributes, setAttributes } = this.props;
        const _self = this;

        function onShortenTitleChange(value) {
            if ( false == value ) 
                setAttributes({ title_length: 0, title_by_words: 0, shorten_title: value });
            else
                setAttributes({ shorten_title: value, title_length: 25 });
        }

        function onTitleLengthChange(value)
        {
            let length = Number.isInteger(Number(value)) && Number(value) >= 0 ? value : 0;
            setAttributes({ title_length: Number(length) });
        }

        function onDisplayExcerptChange(value) {
            if ( false == value )
                setAttributes({ excerpt_length: 0, excerpt_by_words: 0, display_post_excerpt: value, excerpt_format: false });
            else
                setAttributes({ display_post_excerpt: value, excerpt_length: 55 });
        }

        function onExcerptLengthChange(value)
        {
            let length = Number.isInteger(Number(value)) && Number(value) >= 0 ? value : 0;
            setAttributes({ excerpt_length: Number(length) });
        }

        function onDisplayThumbnailChange(value) {
            if ( false == value )
                setAttributes({ thumbnail_width: 0, thumbnail_height: 0, display_post_thumbnail: value, thumbnail_build: 'manual' });
            else
                setAttributes({ thumbnail_width: 75, thumbnail_height: 75, display_post_thumbnail: value });
        }

        function onThumbnailDimChange(dim, value)
        {
            let width = Number.isInteger(Number(value)) && Number(value) >= 0 ? value : 0;
            setAttributes(( 'width' == dim ? { thumbnail_width: Number(width) } : { thumbnail_height: Number(width) } ));
        }

        function onThumbnailBuildChange(value)
        {
            if ( 'predefined' == value ) {
                let fallback = 0;

                setAttributes({
                    thumbnail_width: _self.state.imgSizes[sizes[fallback].value].width,
                    thumbnail_height: _self.state.imgSizes[sizes[fallback].value].height,
                    thumbnail_size: sizes[fallback].value
                });
            } else {
                setAttributes({
                    thumbnail_width: 75,
                    thumbnail_height: 75,
                    thumbnail_size: ''
                });
            }
            setAttributes({ thumbnail_build: value });
        }

        function onThumbnailSizeChange(value) {
            setAttributes({
                thumbnail_width: _self.state.imgSizes[value].width,
                thumbnail_height: _self.state.imgSizes[value].height,
                thumbnail_size: value
            });
        }

        let sizes = [];

        if ( this.state.imgSizes ) {
            for( const size in this.state.imgSizes ) {
                sizes.push(
                    {
                        label: size,
                        value: size
                    },
                );
            }
        }

        return <Fragment>
            <p className='not-a-legend'><strong>{__('Posts settings', 'recently')}</strong></p>
            <CheckboxControl
                label={__('Shorten title', 'recently')}
                checked={attributes.shorten_title}
                onChange={onShortenTitleChange}
            />
            { attributes.shorten_title &&
                <div className='option-subset'>
                    <TextControl
                        label={__('Shorten title to', 'recently')}
                        value={attributes.title_length}
                        onChange={onTitleLengthChange}
                    />
                    <SelectControl
                        value={attributes.title_by_words}
                        options={[
                            { label: __('characters', 'recently'), value: 0 },
                            { label: __('words', 'recently'), value: 1 },
                        ]}
                        onChange={(value) => setAttributes({ title_by_words: Number(value) })}
                    />
                </div>
            }
            <CheckboxControl
                label={__('Display post excerpt', 'recently')}
                checked={attributes.display_post_excerpt}
                onChange={onDisplayExcerptChange}
            />
            { attributes.display_post_excerpt && 
                <div className='option-subset'>
                    <CheckboxControl
                        label={__('Keep text format and links', 'recently')}
                        checked={attributes.excerpt_format}
                        onChange={(value) => setAttributes({ excerpt_format: value })}
                    />
                    <TextControl
                        label={__('Excerpt length', 'recently')}
                        value={attributes.excerpt_length}
                        onChange={onExcerptLengthChange}
                    />
                    <SelectControl
                        value={attributes.excerpt_by_words}
                        options={[
                            { label: __('characters', 'recently'), value: 0 },
                            { label: __('words', 'recently'), value: 1 },
                        ]}
                        onChange={(value) => setAttributes({ excerpt_by_words: Number(value) })}
                    />
                </div>
            }
            <CheckboxControl
                label={__('Display post thumbnail', 'recently')}
                checked={attributes.display_post_thumbnail}
                onChange={onDisplayThumbnailChange}
            />
            { attributes.display_post_thumbnail && 
                <div className='option-subset'>
                    <SelectControl
                        value={attributes.thumbnail_build}
                        options={[
                            { label: __('Set size manually', 'recently'), value: 'manual' },
                            { label: __('Use predefined size', 'recently'), value: 'predefined' },
                        ]}
                        onChange={onThumbnailBuildChange}
                    />
                    { 'manual' == attributes.thumbnail_build &&
                        <Fragment>
                            <TextControl
                                label={__('Thumbnail width', 'recently')}
                                help={__('Size in px units (pixels)', 'recently')}
                                value={attributes.thumbnail_width}
                                onChange={(value) => onThumbnailDimChange('width', value)}
                            />
                            <TextControl
                                label={__('Thumbnail height', 'recently')}
                                help={__('Size in px units (pixels)', 'recently')}
                                value={attributes.thumbnail_height}
                                onChange={(value) => onThumbnailDimChange('height', value)}
                            />
                        </Fragment>
                    }
                    { 'predefined' == attributes.thumbnail_build &&
                        <Fragment>
                            <SelectControl
                                value={attributes.thumbnail_size}
                                options={sizes}
                                onChange={onThumbnailSizeChange}
                            />
                        </Fragment>
                    }
                </div>
            }
            { _recently.can_show_rating &&
                <CheckboxControl
                    label={__('Display post rating', 'recently')}
                    checked={attributes.rating}
                    onChange={(value) => setAttributes({ rating: value })}
                />
            }
        </Fragment>;
    }

    getStatsTagFields()
    {
        const { attributes, setAttributes } = this.props;

        let taxonomies = [];

        if ( this.state.taxonomies ) {
            for( const tax in this.state.taxonomies ) {
                if ( 'post_format' == this.state.taxonomies[tax].name )
                    continue;

                taxonomies.push(
                    {
                        label: this.state.taxonomies[tax].labels.singular_name + ' (' + this.state.taxonomies[tax].name + ')',
                        value: this.state.taxonomies[tax].name
                    },
                );
            }
        }

        return <Fragment>
            <p className='not-a-legend'><strong>{__('Stats Tag settings', 'recently')}</strong></p>
            <CheckboxControl
                label={__('Display comments count', 'recently')}
                checked={attributes.meta_comments}
                onChange={(value) => setAttributes({ meta_comments: value })}
            />
            { _recently.can_show_views &&
                <CheckboxControl
                    label={__('Display views', 'recently')}
                    checked={attributes.meta_views}
                    onChange={(value) => setAttributes({ meta_views: value })}
                />
            }
            <CheckboxControl
                label={__('Display author', 'recently')}
                checked={attributes.meta_author}
                onChange={(value) => setAttributes({ meta_author: value })}
            />
            <CheckboxControl
                label={__('Display date', 'recently')}
                checked={attributes.meta_date}
                onChange={(value) => setAttributes({ meta_date: value })}
            />
            { attributes.meta_date && 
                <div className='option-subset'>
                    <SelectControl
                        label={__('Date Format', 'recently')}
                        value={attributes.meta_date_format}
                        options={[
                            { label: __('Relative', 'recently'), value: 'relative' },
                            { label: __('Month Day, Year', 'recently'), value: 'F j, Y' },
                            { label: __('yyyy/mm/dd', 'recently'), value: 'Y/m/d' },
                            { label: __('mm/dd/yyyy', 'recently'), value: 'm/d/Y' },
                            { label: __('dd/mm/yyyy', 'recently'), value: 'd/m/Y' },
                            { label: __('WordPress Date Format', 'recently'), value: 'wp_date_format' },
                        ]}
                        onChange={(value) => setAttributes({ meta_date_format: value })}
                    />
                </div>
            }
            <CheckboxControl
                label={__('Display taxonomy', 'recently')}
                checked={attributes.meta_taxonomy}
                onChange={(value) => setAttributes({ meta_taxonomy: value })}
            />
            { attributes.meta_taxonomy && 
                <div className='option-subset'>
                    <SelectControl
                        multiple
                        label={__('Taxonomy', 'recently')}
                        value={attributes.meta_taxonomy_list}
                        options={taxonomies}
                        onChange={(value) => setAttributes({ meta_taxonomy_list: value })}
                    />
                </div>
            }
        </Fragment>;
    }

    getHTMLMarkupFields()
    {
        const { attributes, setAttributes } = this.props;
        const _self = this;

        function onThemeChange(value)
        {
            if ( 'undefined' != typeof _self.state.themes[value] ) {
                let config = _self.state.themes[value].json.config;

                setAttributes({
                    shorten_title: config.shorten_title.active,
                    title_length: config.shorten_title.length,
                    title_by_words: config.shorten_title.words ? 1 : 0,
                    display_post_excerpt: config['post-excerpt'].active,
                    excerpt_format: config['post-excerpt'].format,
                    excerpt_length: config['post-excerpt'].length,
                    excerpt_by_words: config['post-excerpt'].words ? 1 : 0,
                    display_post_thumbnail: config.thumbnail.active,
                    thumbnail_build: config.thumbnail.build,
                    thumbnail_width: config.thumbnail.width,
                    thumbnail_height: config.thumbnail.height,
                    meta_comments: config.meta_tag.comment_count,
                    meta_views: config.meta_tag.views,
                    meta_author: config.meta_tag.author,
                    meta_date: config.meta_tag.date.active,
                    meta_date_format: config.meta_tag.date.format,
                    meta_taxonomy: config.meta_tag.taxonomy.active,
                    taxonomy: config.meta_tag.taxonomy.name,
                    custom_html: true,
                    recently_start: config.markup['recently-start'],
                    recently_end: config.markup['recently-end'],
                    post_html: config.markup['post-html'],
                    theme: value
                });
            } else {
                setAttributes({ theme: value });
            }
        }

        let themes = [
            {
                label: __('None', 'recently'),
                value: ''
            },
        ];

        if ( this.state.themes ) {
            for( const theme in this.state.themes ) {
                themes.push(
                    {
                        label: this.state.themes[theme].json.name,
                        value: theme
                    },
                );
            }
        }

        return <Fragment>
            <p className='not-a-legend'><strong>{__('HTML Markup settings', 'recently')}</strong></p>
            <CheckboxControl
                label={__('Use custom HTML Markup', 'recently')}
                checked={attributes.custom_html}
                onChange={(value) => setAttributes({ custom_html: value })}
            />
            { attributes.custom_html &&
                <div className='option-subset'>
                    <TextareaControl
                        rows="1"
                        label={__('Before title', 'recently')}
                        value={attributes.header_start}
                        onChange={(value) => setAttributes({ header_start: value })}
                    />
                    <TextareaControl
                        rows="1"
                        label={__('After title', 'recently')}
                        value={attributes.header_end}
                        onChange={(value) => setAttributes({ header_end: value })}
                    />
                    <TextareaControl
                        rows="1"
                        label={__('Before recent posts', 'recently')}
                        value={attributes.recently_start}
                        onChange={(value) => setAttributes({ recently_start: value })}
                    />
                    <TextareaControl
                        rows="1"
                        label={__('After recent posts', 'recently')}
                        value={attributes.recently_end}
                        onChange={(value) => setAttributes({ recently_end: value })}
                    />
                    <TextareaControl
                        label={__('Post HTML markup', 'recently')}
                        value={attributes.post_html}
                        onChange={(value) => setAttributes({ post_html: value })}
                    />
                </div>
            }
            <SelectControl
                label={__('Theme', 'recently')}
                value={attributes.theme}
                options={themes}
                onChange={onThemeChange}
            />
        </Fragment>;
    }

    render()
    {
        if ( ! this.state.taxonomies || ! this.state.themes || ! this.state.imgSizes )
            return <Spinner />;

        const { isSelected, className, attributes } = this.props;

        let classes = className;
        classes += this.state.editMode ? ' in-edit-mode' : ' in-preview-mode';
        classes += isSelected ? ' is-selected' : '';

        return ([
            this.getBlockControls(),
            <div className={classes}>
                { this.state.editMode &&
                    <Fragment>
                        {this.getMainFields()}
                        {this.getFiltersFields()}
                        {this.getPostSettingsFields()}
                        {this.getStatsTagFields()}
                        {this.getHTMLMarkupFields()}
                    </Fragment>
                }
                { ! this.state.editMode &&
                    <Disabled>
                        <ServerSideRender
                            block={this.props.name}
                            className={className}
                            attributes={attributes}
                            urlQueryArgs={{isSelected: isSelected}}
                        />
                    </Disabled>
                }
            </div>
        ]);
    }
}
