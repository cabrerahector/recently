/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/Block/Widget/edit.js":
/*!**********************************!*\
  !*** ./src/Block/Widget/edit.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "RecentlyWidgetBlockEdit": () => (/* binding */ RecentlyWidgetBlockEdit)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils */ "./src/Block/utils.js");


const {
  serverSideRender: ServerSideRender
} = wp;
const {
  Component,
  Fragment
} = wp.element;
const {
  BlockControls
} = wp.blockEditor;
const {
  CheckboxControl,
  Disabled,
  SelectControl,
  Spinner,
  TextareaControl,
  TextControl,
  Toolbar,
  ToolbarButton
} = wp.components;
const {
  __
} = wp.i18n;
const endpoint = 'recently/v1';
class RecentlyWidgetBlockEdit extends Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      editMode: true,
      themes: null,
      imgSizes: null,
      taxonomies: null
    };
    const {
      attributes,
      setAttributes,
      clientId
    } = this.props;
    const {
      blockID
    } = attributes;

    if (!blockID) {
      setAttributes({
        blockID: clientId
      });
    }
  }

  componentDidMount() {
    const {
      attributes
    } = this.props;
    this.getThemes();
    this.getImageSizes();
    this.getTaxonomies();
    this.setState({
      editMode: attributes._editMode
    });
  }

  getThemes() {
    wp.apiFetch({
      path: endpoint + '/themes'
    }).then(themes => {
      this.setState({
        themes
      });
    }, error => {
      this.setState({
        error,
        themes: null
      });
    });
  }

  getImageSizes() {
    wp.apiFetch({
      path: endpoint + '/thumbnails'
    }).then(imgSizes => {
      this.setState({
        imgSizes
      });
    }, error => {
      this.setState({
        error,
        imgSizes: null
      });
    });
  }

  getTaxonomies() {
    const {
      attributes
    } = this.props;
    wp.apiFetch({
      path: endpoint + '/taxonomies'
    }).then(taxonomies => {
      if (taxonomies) {
        let tax = attributes.taxonomy.split(';'),
            term_id = attributes.term_id.split(';'),
            term_slug = attributes.term_slug.split(';');

        if (tax.length && (tax.length == term_id.length || tax.length == term_slug.length)) {
          let selected_taxonomies = {};

          for (var t = 0; t < tax.length; t++) {
            selected_taxonomies[tax[t]] = term_id[t];
          }

          for (const tax in taxonomies) {
            taxonomies[tax]._terms = 'undefined' != typeof selected_taxonomies[tax] ? selected_taxonomies[tax] : '';
          }
        }
      }

      this.setState({
        taxonomies
      });
    }, error => {
      this.setState({
        error,
        taxonomies: null
      });
    });
  }

  getBlockControls() {
    const {
      setAttributes
    } = this.props;

    const _self = this;

    function onPreviewChange() {
      let editMode = !_self.state.editMode;

      _self.setState({
        editMode: editMode
      });

      setAttributes({
        _editMode: editMode
      });
    }

    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlockControls, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Toolbar, {
      label: "{ __('Settings') }"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ToolbarButton, {
      label: this.state.editMode ? __('Preview', 'recently') : __('Edit', 'recently'),
      icon: this.state.editMode ? "format-image" : "edit",
      onClick: onPreviewChange
    })));
  }

  getMainFields() {
    const {
      attributes,
      setAttributes
    } = this.props;

    function onTitleChange(value) {
      value = (0,_utils__WEBPACK_IMPORTED_MODULE_1__.escape_html)((0,_utils__WEBPACK_IMPORTED_MODULE_1__.unescape_html)(value));
      setAttributes({
        title: value
      });
    }

    function onLimitChange(value) {
      let limit = Number.isInteger(Number(value)) && Number(value) > 0 ? value : 10;
      setAttributes({
        posts_per_page: Number(limit)
      });
    }

    function onOffsetChange(value) {
      let limit = Number.isInteger(Number(value)) && Number(value) >= 0 ? value : 10;
      setAttributes({
        offset: Number(limit)
      });
    }

    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Title', 'recently'),
      value: attributes.title,
      onChange: onTitleChange
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Limit', 'recently'),
      help: __('Max. number of posts to show.', 'recently'),
      value: attributes.posts_per_page,
      onChange: onLimitChange
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Offset', 'recently'),
      help: __('Number of posts to displace or pass over.', 'recently'),
      value: attributes.offset,
      onChange: onOffsetChange
    }));
  }

  getFiltersFields() {
    const {
      attributes,
      setAttributes
    } = this.props;

    const _self = this;

    function onPostTypeChange(value) {
      let new_value = value.replace(/[^a-z0-9-_\,]+/gi, '');
      setAttributes({
        post_type: new_value
      });
    }

    function onPostIDExcludeChange(value) {
      let new_value = value.replace(/[^0-9\,]/g, '');
      setAttributes({
        post_id: new_value
      });
    }

    function onAuthorChange(value) {
      let new_value = value.replace(/[^0-9\,]/g, '');
      setAttributes({
        author_id: new_value
      });
    }

    function onTaxChange(taxonomy_name, terms) {
      let taxonomies = _self.state.taxonomies;
      terms = terms.replace(/[^0-9-\,]/g, '');

      if (taxonomies && 'undefined' != typeof taxonomies[taxonomy_name]) {
        taxonomies[taxonomy_name]._terms = terms;

        _self.setState({
          taxonomies: taxonomies
        });
      }
    }

    function onTaxBlur(taxonomy_name) {
      let taxonomies = _self.state.taxonomies;

      if (taxonomies && 'undefined' != typeof taxonomies[taxonomy_name]) {
        let terms_arr = taxonomies[taxonomy_name]._terms.split(','); // Remove invalid values


        if (terms_arr.length) terms_arr = terms_arr.map(term => term.trim()).filter(term => '' != term && '-' != term); // Remove duplicates

        if (terms_arr.length) terms_arr = Array.from(new Set(terms_arr));
        taxonomies[taxonomy_name]._terms = terms_arr.join(',');

        _self.setState({
          taxonomies
        });

        let tax = '',
            term_id = '';

        for (let key in _self.state.taxonomies) {
          if (_self.state.taxonomies.hasOwnProperty(key)) {
            if (!_self.state.taxonomies[key]._terms.length) continue;
            tax += key + ';';
            term_id += _self.state.taxonomies[key]._terms + ';';
          }
        } // Remove trailing semicolon


        if (tax && term_id) {
          tax = tax.replace(new RegExp(';$'), '');
          term_id = term_id.replace(new RegExp(';$'), '');
        }

        setAttributes({
          taxonomy: tax,
          term_id: term_id
        });
      }
    }

    let taxonomies = [];

    if (this.state.taxonomies) {
      for (const tax in this.state.taxonomies) {
        taxonomies.push({
          name: this.state.taxonomies[tax].name,
          label: this.state.taxonomies[tax].labels.singular_name + ' (' + this.state.taxonomies[tax].name + ')',
          terms: this.state.taxonomies[tax]._terms
        });
      }
    }

    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      className: "not-a-legend"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", null, __('Filters', 'recently'))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Post Type(s)', 'recently'),
      help: __(' Post types, separated by comma.', 'recently'),
      value: attributes.post_type,
      onChange: onPostTypeChange
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Post ID(s) to exclude', 'recently'),
      help: __(' Post / Page IDs, separated by comma.', 'recently'),
      value: attributes.post_id,
      onChange: onPostIDExcludeChange
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Author ID(s)', 'recently'),
      help: __(' Author IDs, separated by comma (prefix a minus sign to exclude).', 'recently'),
      value: attributes.author_id,
      onChange: onAuthorChange
    }), taxonomies && taxonomies.filter(tax => 'post_format' != tax.name).map(tax => {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
        label: tax.label,
        help: __('Term IDs must be comma separated, prefix a minus sign to exclude.', 'recently'),
        value: tax.terms,
        onChange: terms => onTaxChange(tax.name, terms),
        onBlur: () => onTaxBlur(tax.name)
      });
    }));
  }

  getPostSettingsFields() {
    const {
      attributes,
      setAttributes
    } = this.props;

    const _self = this;

    function onShortenTitleChange(value) {
      if (false == value) setAttributes({
        title_length: 0,
        title_by_words: 0,
        shorten_title: value
      });else setAttributes({
        shorten_title: value,
        title_length: 25
      });
    }

    function onTitleLengthChange(value) {
      let length = Number.isInteger(Number(value)) && Number(value) >= 0 ? value : 0;
      setAttributes({
        title_length: Number(length)
      });
    }

    function onDisplayExcerptChange(value) {
      if (false == value) setAttributes({
        excerpt_length: 0,
        excerpt_by_words: 0,
        display_post_excerpt: value,
        excerpt_format: false
      });else setAttributes({
        display_post_excerpt: value,
        excerpt_length: 55
      });
    }

    function onExcerptLengthChange(value) {
      let length = Number.isInteger(Number(value)) && Number(value) >= 0 ? value : 0;
      setAttributes({
        excerpt_length: Number(length)
      });
    }

    function onDisplayThumbnailChange(value) {
      if (false == value) setAttributes({
        thumbnail_width: 0,
        thumbnail_height: 0,
        display_post_thumbnail: value,
        thumbnail_build: 'manual'
      });else setAttributes({
        thumbnail_width: 75,
        thumbnail_height: 75,
        display_post_thumbnail: value
      });
    }

    function onThumbnailDimChange(dim, value) {
      let width = Number.isInteger(Number(value)) && Number(value) >= 0 ? value : 0;
      setAttributes('width' == dim ? {
        thumbnail_width: Number(width)
      } : {
        thumbnail_height: Number(width)
      });
    }

    function onThumbnailBuildChange(value) {
      if ('predefined' == value) {
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

      setAttributes({
        thumbnail_build: value
      });
    }

    function onThumbnailSizeChange(value) {
      setAttributes({
        thumbnail_width: _self.state.imgSizes[value].width,
        thumbnail_height: _self.state.imgSizes[value].height,
        thumbnail_size: value
      });
    }

    let sizes = [];

    if (this.state.imgSizes) {
      for (const size in this.state.imgSizes) {
        sizes.push({
          label: size,
          value: size
        });
      }
    }

    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      className: "not-a-legend"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", null, __('Posts settings', 'recently'))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Shorten title', 'recently'),
      checked: attributes.shorten_title,
      onChange: onShortenTitleChange
    }), attributes.shorten_title && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "option-subset"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Shorten title to', 'recently'),
      value: attributes.title_length,
      onChange: onTitleLengthChange
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      value: attributes.title_by_words,
      options: [{
        label: __('characters', 'recently'),
        value: 0
      }, {
        label: __('words', 'recently'),
        value: 1
      }],
      onChange: value => setAttributes({
        title_by_words: Number(value)
      })
    })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Display post excerpt', 'recently'),
      checked: attributes.display_post_excerpt,
      onChange: onDisplayExcerptChange
    }), attributes.display_post_excerpt && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "option-subset"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Keep text format and links', 'recently'),
      checked: attributes.excerpt_format,
      onChange: value => setAttributes({
        excerpt_format: value
      })
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Excerpt length', 'recently'),
      value: attributes.excerpt_length,
      onChange: onExcerptLengthChange
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      value: attributes.excerpt_by_words,
      options: [{
        label: __('characters', 'recently'),
        value: 0
      }, {
        label: __('words', 'recently'),
        value: 1
      }],
      onChange: value => setAttributes({
        excerpt_by_words: Number(value)
      })
    })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Display post thumbnail', 'recently'),
      checked: attributes.display_post_thumbnail,
      onChange: onDisplayThumbnailChange
    }), attributes.display_post_thumbnail && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "option-subset"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      value: attributes.thumbnail_build,
      options: [{
        label: __('Set size manually', 'recently'),
        value: 'manual'
      }, {
        label: __('Use predefined size', 'recently'),
        value: 'predefined'
      }],
      onChange: onThumbnailBuildChange
    }), 'manual' == attributes.thumbnail_build && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Thumbnail width', 'recently'),
      help: __('Size in px units (pixels)', 'recently'),
      value: attributes.thumbnail_width,
      onChange: value => onThumbnailDimChange('width', value)
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Thumbnail height', 'recently'),
      help: __('Size in px units (pixels)', 'recently'),
      value: attributes.thumbnail_height,
      onChange: value => onThumbnailDimChange('height', value)
    })), 'predefined' == attributes.thumbnail_build && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      value: attributes.thumbnail_size,
      options: sizes,
      onChange: onThumbnailSizeChange
    }))));
  }

  getStatsTagFields() {
    const {
      attributes,
      setAttributes
    } = this.props;
    let taxonomies = [];

    if (this.state.taxonomies) {
      for (const tax in this.state.taxonomies) {
        if ('post_format' == this.state.taxonomies[tax].name) continue;
        taxonomies.push({
          label: this.state.taxonomies[tax].labels.singular_name + ' (' + this.state.taxonomies[tax].name + ')',
          value: this.state.taxonomies[tax].name
        });
      }
    }

    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      className: "not-a-legend"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", null, __('Stats Tag settings', 'recently'))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Display comments count', 'recently'),
      checked: attributes.meta_comments,
      onChange: value => setAttributes({
        meta_comments: value
      })
    }), _recently.can_show_views && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Display views', 'recently'),
      checked: attributes.meta_views,
      onChange: value => setAttributes({
        meta_views: value
      })
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Display author', 'recently'),
      checked: attributes.meta_author,
      onChange: value => setAttributes({
        meta_author: value
      })
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Display date', 'recently'),
      checked: attributes.meta_date,
      onChange: value => setAttributes({
        meta_date: value
      })
    }), attributes.meta_date && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "option-subset"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      label: __('Date Format', 'recently'),
      value: attributes.meta_date_format,
      options: [{
        label: __('Relative', 'recently'),
        value: 'relative'
      }, {
        label: __('Month Day, Year', 'recently'),
        value: 'F j, Y'
      }, {
        label: __('yyyy/mm/dd', 'recently'),
        value: 'Y/m/d'
      }, {
        label: __('mm/dd/yyyy', 'recently'),
        value: 'm/d/Y'
      }, {
        label: __('dd/mm/yyyy', 'recently'),
        value: 'd/m/Y'
      }, {
        label: __('WordPress Date Format', 'recently'),
        value: 'wp_date_format'
      }],
      onChange: value => setAttributes({
        meta_date_format: value
      })
    })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Display taxonomy', 'recently'),
      checked: attributes.meta_taxonomy,
      onChange: value => setAttributes({
        meta_taxonomy: value
      })
    }), attributes.meta_taxonomy && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "option-subset"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      multiple: true,
      label: __('Taxonomy', 'recently'),
      value: attributes.meta_taxonomy_list,
      options: taxonomies,
      onChange: value => setAttributes({
        meta_taxonomy_list: value
      })
    })));
  }

  getHTMLMarkupFields() {
    const {
      attributes,
      setAttributes
    } = this.props;

    const _self = this;

    function onThemeChange(value) {
      if ('undefined' != typeof _self.state.themes[value]) {
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
        setAttributes({
          theme: value
        });
      }
    }

    let themes = [{
      label: __('None', 'recently'),
      value: ''
    }];

    if (this.state.themes) {
      for (const theme in this.state.themes) {
        themes.push({
          label: this.state.themes[theme].json.name,
          value: theme
        });
      }
    }

    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      className: "not-a-legend"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", null, __('HTML Markup settings', 'recently'))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      label: __('Use custom HTML Markup', 'recently'),
      checked: attributes.custom_html,
      onChange: value => setAttributes({
        custom_html: value
      })
    }), attributes.custom_html && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "option-subset"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextareaControl, {
      rows: "1",
      label: __('Before title', 'recently'),
      value: attributes.header_start,
      onChange: value => setAttributes({
        header_start: value
      })
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextareaControl, {
      rows: "1",
      label: __('After title', 'recently'),
      value: attributes.header_end,
      onChange: value => setAttributes({
        header_end: value
      })
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextareaControl, {
      rows: "1",
      label: __('Before recent posts', 'recently'),
      value: attributes.recently_start,
      onChange: value => setAttributes({
        recently_start: value
      })
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextareaControl, {
      rows: "1",
      label: __('After recent posts', 'recently'),
      value: attributes.recently_end,
      onChange: value => setAttributes({
        recently_end: value
      })
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextareaControl, {
      label: __('Post HTML markup', 'recently'),
      value: attributes.post_html,
      onChange: value => setAttributes({
        post_html: value
      })
    })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      label: __('Theme', 'recently'),
      value: attributes.theme,
      options: themes,
      onChange: onThemeChange
    }));
  }

  render() {
    if (!this.state.taxonomies || !this.state.themes || !this.state.imgSizes) return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Spinner, null);
    const {
      isSelected,
      className,
      attributes
    } = this.props;
    let classes = className;
    classes += this.state.editMode ? ' in-edit-mode' : ' in-preview-mode';
    classes += isSelected ? ' is-selected' : '';
    return [this.getBlockControls(), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: classes
    }, this.state.editMode && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, this.getMainFields(), this.getFiltersFields(), this.getPostSettingsFields(), this.getStatsTagFields(), this.getHTMLMarkupFields()), !this.state.editMode && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Disabled, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ServerSideRender, {
      block: this.props.name,
      className: className,
      attributes: attributes,
      urlQueryArgs: {
        isSelected: isSelected
      }
    })))];
  }

}

/***/ }),

/***/ "./src/Block/icons.js":
/*!****************************!*\
  !*** ./src/Block/icons.js ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);

const icons = {};
icons.recently = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  xmlnsXlink: "http://www.w3.org/1999/xlink",
  enableBackground: "new 0 0 595.3 841.9",
  viewBox: "0 0 595.3 841.9"
}, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("defs", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  id: "a",
  d: "M83.8 205h388v388h-388z"
})), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("clipPath", {
  id: "b"
}, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("use", {
  overflow: "visible",
  xlinkHref: "#a"
})), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  fill: "#56b078",
  d: "M217 188.9C136.7 229.6 90.1 310.9 89.7 396h68.6c.8-60.8 33.5-117.4 89.7-145.9 80.5-40.7 179-8.4 219.7 72.1 40.7 80.5 8.4 179-72.1 219.7-61.5 31.1-133.5 19.5-182.3-23.6l92.9-90.4h-217l-5.5 216.8 80.4-78.4c69.2 63.9 173.6 81.7 262.4 36.8 114.2-57.8 160.1-197.7 102.3-311.9C471.2 177 331.2 131.1 217 188.9zm-83.1 284.7h59.9l-61.4 59.9 1.5-59.9z",
  clipPath: "url(#b)"
}));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (icons);

/***/ }),

/***/ "./src/Block/utils.js":
/*!****************************!*\
  !*** ./src/Block/utils.js ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "escape_html": () => (/* binding */ escape_html),
/* harmony export */   "unescape_html": () => (/* binding */ unescape_html)
/* harmony export */ });
function escape_html(value) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    "/": '&#x2F;',
    '`': '&grave;'
  };
  const reg = /[&<>"'/]/ig;
  return value.replace(reg, match => map[match]);
}
function unescape_html(value) {
  var div = document.createElement('div');
  div.innerHTML = value;
  var child = div.childNodes[0];
  return child ? child.nodeValue : '';
}

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!************************************!*\
  !*** ./src/Block/Widget/widget.js ***!
  \************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../icons */ "./src/Block/icons.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./src/Block/Widget/edit.js");


const {
  registerBlockType
} = wp.blocks;
const {
  __
} = wp.i18n;
registerBlockType('recently/widget', {
  title: 'Recently',
  category: 'widgets',
  icon: _icons__WEBPACK_IMPORTED_MODULE_0__["default"].recently,
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
      type: 'string'
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
      default: '<li>{thumb} {title} {meta}</li>'
    },
    theme: {
      type: 'string',
      default: ''
    }
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
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__.RecentlyWidgetBlockEdit,
  save: () => {
    return null;
  }
});
})();

/******/ })()
;
//# sourceMappingURL=block-recently-widget.js.map