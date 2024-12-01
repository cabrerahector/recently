(()=>{"use strict";var e={};e.recently=React.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",xmlnsXlink:"http://www.w3.org/1999/xlink",enableBackground:"new 0 0 595.3 841.9",viewBox:"0 0 595.3 841.9"},React.createElement("defs",null,React.createElement("path",{id:"a",d:"M83.8 205h388v388h-388z"})),React.createElement("clipPath",{id:"b"},React.createElement("use",{overflow:"visible",xlinkHref:"#a"})),React.createElement("path",{fill:"#56b078",d:"M217 188.9C136.7 229.6 90.1 310.9 89.7 396h68.6c.8-60.8 33.5-117.4 89.7-145.9 80.5-40.7 179-8.4 219.7 72.1 40.7 80.5 8.4 179-72.1 219.7-61.5 31.1-133.5 19.5-182.3-23.6l92.9-90.4h-217l-5.5 216.8 80.4-78.4c69.2 63.9 173.6 81.7 262.4 36.8 114.2-57.8 160.1-197.7 102.3-311.9C471.2 177 331.2 131.1 217 188.9zm-83.1 284.7h59.9l-61.4 59.9 1.5-59.9z",clipPath:"url(#b)"}));const t=e;function a(e){return a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},a(e)}function n(e,t){for(var a=0;a<t.length;a++){var n=t[a];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,l(n.key),n)}}function l(e){var t=function(e){if("object"!=a(e)||!e)return e;var t=e[Symbol.toPrimitive];if(void 0!==t){var n=t.call(e,"string");if("object"!=a(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"==a(t)?t:t+""}function r(){try{var e=!Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){})))}catch(e){}return(r=function(){return!!e})()}function i(e){return i=Object.setPrototypeOf?Object.getPrototypeOf.bind():function(e){return e.__proto__||Object.getPrototypeOf(e)},i(e)}function o(e,t){return o=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e},o(e,t)}var s=wp.serverSideRender,c=wp.element,u=c.Component,m=c.Fragment,h=wp.blockEditor.BlockControls,p=wp.components,d=p.CheckboxControl,y=p.Disabled,b=p.SelectControl,_=p.Spinner,f=p.TextareaControl,g=p.TextControl,v=p.Toolbar,x=p.ToolbarButton,__=wp.i18n.__,w="recently/v1",E=function(e){function t(e){var n;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),(n=function(e,t,n){return t=i(t),function(e,t){if(t&&("object"==a(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e)}(e,r()?Reflect.construct(t,n||[],i(e).constructor):t.apply(e,n))}(this,t,[e])).state={error:null,editMode:!0,themes:null,imgSizes:null,taxonomies:null};var l=n.props,o=l.attributes,s=l.setAttributes,c=l.clientId;return o.blockID||s({blockID:c}),n}return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),Object.defineProperty(e,"prototype",{writable:!1}),t&&o(e,t)}(t,e),l=t,c=[{key:"componentDidMount",value:function(){var e=this.props.attributes;this.getThemes(),this.getImageSizes(),this.getTaxonomies(),this.setState({editMode:e._editMode})}},{key:"getThemes",value:function(){var e=this;wp.apiFetch({path:w+"/themes"}).then((function(t){e.setState({themes:t})}),(function(t){e.setState({error:t,themes:null})}))}},{key:"getImageSizes",value:function(){var e=this;wp.apiFetch({path:w+"/thumbnails"}).then((function(t){e.setState({imgSizes:t})}),(function(t){e.setState({error:t,imgSizes:null})}))}},{key:"getTaxonomies",value:function(){var e=this,t=this.props.attributes;wp.apiFetch({path:w+"/taxonomies"}).then((function(a){if(a){var n=t.taxonomy.split(";"),l=t.term_id.split(";"),r=t.term_slug.split(";");if(n.length&&(n.length==l.length||n.length==r.length)){for(var i={},o=0;o<n.length;o++)i[n[o]]=l[o];for(var s in a)a[s]._terms=void 0!==i[s]?i[s]:""}}e.setState({taxonomies:a})}),(function(t){e.setState({error:t,taxonomies:null})}))}},{key:"getBlockControls",value:function(){var e=this.props.setAttributes,t=this;return React.createElement(h,null,React.createElement(v,{label:"{ __('Settings') }"},React.createElement(x,{label:this.state.editMode?__("Preview","recently"):__("Edit","recently"),icon:this.state.editMode?"format-image":"edit",onClick:function(){var a=!t.state.editMode;t.setState({editMode:a}),e({_editMode:a})}})))}},{key:"getMainFields",value:function(){var e=this.props,t=e.attributes,a=e.setAttributes;return React.createElement(m,null,React.createElement(g,{label:__("Title","recently"),value:t.title,onChange:function(e){e=function(e){var t={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","/":"&#x2F;","`":"&grave;"};return e.replace(/[&<>"'/]/gi,(function(e){return t[e]}))}(function(e){var t=document.createElement("div");t.innerHTML=e;var a=t.childNodes[0];return a?a.nodeValue:""}(e)),a({title:e})}}),React.createElement(g,{label:__("Limit","recently"),help:__("Max. number of posts to show.","recently"),value:t.posts_per_page,onChange:function(e){var t=Number.isInteger(Number(e))&&Number(e)>0?e:10;a({posts_per_page:Number(t)})}}),React.createElement(g,{label:__("Offset","recently"),help:__("Number of posts to displace or pass over.","recently"),value:t.offset,onChange:function(e){var t=Number.isInteger(Number(e))&&Number(e)>=0?e:10;a({offset:Number(t)})}}))}},{key:"getFiltersFields",value:function(){var e=this.props,t=e.attributes,a=e.setAttributes,n=this,l=[];if(this.state.taxonomies)for(var r in this.state.taxonomies)l.push({name:this.state.taxonomies[r].name,label:this.state.taxonomies[r].labels.singular_name+" ("+this.state.taxonomies[r].name+")",terms:this.state.taxonomies[r]._terms});return React.createElement(m,null,React.createElement("p",{className:"not-a-legend"},React.createElement("strong",null,__("Filters","recently"))),React.createElement(g,{label:__("Post Type(s)","recently"),help:__(" Post types, separated by comma.","recently"),value:t.post_type,onChange:function(e){var t=e.replace(/[^a-z0-9-_\,]+/gi,"");a({post_type:t})}}),React.createElement(g,{label:__("Post ID(s) to exclude","recently"),help:__(" Post / Page IDs, separated by comma.","recently"),value:t.post_id,onChange:function(e){var t=e.replace(/[^0-9\,]/g,"");a({post_id:t})}}),React.createElement(g,{label:__("Author ID(s)","recently"),help:__(" Author IDs, separated by comma (prefix a minus sign to exclude).","recently"),value:t.author_id,onChange:function(e){var t=e.replace(/[^0-9\,]/g,"");a({author_id:t})}}),l&&l.filter((function(e){return"post_format"!=e.name})).map((function(e){return React.createElement(g,{label:e.label,help:__("Term IDs must be comma separated, prefix a minus sign to exclude.","recently"),value:e.terms,onChange:function(t){return function(e,t){var a=n.state.taxonomies;t=t.replace(/[^0-9-\,]/g,""),a&&void 0!==a[e]&&(a[e]._terms=t,n.setState({taxonomies:a}))}(e.name,t)},onBlur:function(){return function(e){var t=n.state.taxonomies;if(t&&void 0!==t[e]){var l=t[e]._terms.split(",");l.length&&(l=l.map((function(e){return e.trim()})).filter((function(e){return""!=e&&"-"!=e}))),l.length&&(l=Array.from(new Set(l))),t[e]._terms=l.join(","),n.setState({taxonomies:t});var r="",i="";for(var o in n.state.taxonomies)if(n.state.taxonomies.hasOwnProperty(o)){if(!n.state.taxonomies[o]._terms.length)continue;r+=o+";",i+=n.state.taxonomies[o]._terms+";"}r&&i&&(r=r.replace(new RegExp(";$"),""),i=i.replace(new RegExp(";$"),"")),a({taxonomy:r,term_id:i})}}(e.name)}})})))}},{key:"getPostSettingsFields",value:function(){var e=this.props,t=e.attributes,a=e.setAttributes,n=this;function l(e,t){var n=Number.isInteger(Number(t))&&Number(t)>=0?t:0;a("width"==e?{thumbnail_width:Number(n)}:{thumbnail_height:Number(n)})}var r=[];if(this.state.imgSizes)for(var i in this.state.imgSizes)r.push({label:i,value:i});return React.createElement(m,null,React.createElement("p",{className:"not-a-legend"},React.createElement("strong",null,__("Posts settings","recently"))),React.createElement(d,{label:__("Shorten title","recently"),checked:t.shorten_title,onChange:function(e){a(0==e?{title_length:0,title_by_words:0,shorten_title:e}:{shorten_title:e,title_length:25})}}),t.shorten_title&&React.createElement("div",{className:"option-subset"},React.createElement(g,{label:__("Shorten title to","recently"),value:t.title_length,onChange:function(e){var t=Number.isInteger(Number(e))&&Number(e)>=0?e:0;a({title_length:Number(t)})}}),React.createElement(b,{value:t.title_by_words,options:[{label:__("characters","recently"),value:0},{label:__("words","recently"),value:1}],onChange:function(e){return a({title_by_words:Number(e)})}})),React.createElement(d,{label:__("Display post excerpt","recently"),checked:t.display_post_excerpt,onChange:function(e){a(0==e?{excerpt_length:0,excerpt_by_words:0,display_post_excerpt:e,excerpt_format:!1}:{display_post_excerpt:e,excerpt_length:55})}}),t.display_post_excerpt&&React.createElement("div",{className:"option-subset"},React.createElement(d,{label:__("Keep text format and links","recently"),checked:t.excerpt_format,onChange:function(e){return a({excerpt_format:e})}}),React.createElement(g,{label:__("Excerpt length","recently"),value:t.excerpt_length,onChange:function(e){var t=Number.isInteger(Number(e))&&Number(e)>=0?e:0;a({excerpt_length:Number(t)})}}),React.createElement(b,{value:t.excerpt_by_words,options:[{label:__("characters","recently"),value:0},{label:__("words","recently"),value:1}],onChange:function(e){return a({excerpt_by_words:Number(e)})}})),React.createElement(d,{label:__("Display post thumbnail","recently"),checked:t.display_post_thumbnail,onChange:function(e){a(0==e?{thumbnail_width:0,thumbnail_height:0,display_post_thumbnail:e,thumbnail_build:"manual"}:{thumbnail_width:75,thumbnail_height:75,display_post_thumbnail:e})}}),t.display_post_thumbnail&&React.createElement("div",{className:"option-subset"},React.createElement(b,{value:t.thumbnail_build,options:[{label:__("Set size manually","recently"),value:"manual"},{label:__("Use predefined size","recently"),value:"predefined"}],onChange:function(e){a("predefined"==e?{thumbnail_width:n.state.imgSizes[r[0].value].width,thumbnail_height:n.state.imgSizes[r[0].value].height,thumbnail_size:r[0].value}:{thumbnail_width:75,thumbnail_height:75,thumbnail_size:""}),a({thumbnail_build:e})}}),"manual"==t.thumbnail_build&&React.createElement(m,null,React.createElement(g,{label:__("Thumbnail width","recently"),help:__("Size in px units (pixels)","recently"),value:t.thumbnail_width,onChange:function(e){return l("width",e)}}),React.createElement(g,{label:__("Thumbnail height","recently"),help:__("Size in px units (pixels)","recently"),value:t.thumbnail_height,onChange:function(e){return l("height",e)}})),"predefined"==t.thumbnail_build&&React.createElement(m,null,React.createElement(b,{value:t.thumbnail_size,options:r,onChange:function(e){a({thumbnail_width:n.state.imgSizes[e].width,thumbnail_height:n.state.imgSizes[e].height,thumbnail_size:e})}}))),_recently.can_show_rating&&React.createElement(d,{label:__("Display post rating","recently"),checked:t.rating,onChange:function(e){return a({rating:e})}}))}},{key:"getStatsTagFields",value:function(){var e=this.props,t=e.attributes,a=e.setAttributes,n=[];if(this.state.taxonomies)for(var l in this.state.taxonomies)"post_format"!=this.state.taxonomies[l].name&&n.push({label:this.state.taxonomies[l].labels.singular_name+" ("+this.state.taxonomies[l].name+")",value:this.state.taxonomies[l].name});return React.createElement(m,null,React.createElement("p",{className:"not-a-legend"},React.createElement("strong",null,__("Stats Tag settings","recently"))),React.createElement(d,{label:__("Display comments count","recently"),checked:t.meta_comments,onChange:function(e){return a({meta_comments:e})}}),_recently.can_show_views&&React.createElement(d,{label:__("Display views","recently"),checked:t.meta_views,onChange:function(e){return a({meta_views:e})}}),React.createElement(d,{label:__("Display author","recently"),checked:t.meta_author,onChange:function(e){return a({meta_author:e})}}),React.createElement(d,{label:__("Display date","recently"),checked:t.meta_date,onChange:function(e){return a({meta_date:e})}}),t.meta_date&&React.createElement("div",{className:"option-subset"},React.createElement(b,{label:__("Date Format","recently"),value:t.meta_date_format,options:[{label:__("Relative","recently"),value:"relative"},{label:__("Month Day, Year","recently"),value:"F j, Y"},{label:__("yyyy/mm/dd","recently"),value:"Y/m/d"},{label:__("mm/dd/yyyy","recently"),value:"m/d/Y"},{label:__("dd/mm/yyyy","recently"),value:"d/m/Y"},{label:__("WordPress Date Format","recently"),value:"wp_date_format"}],onChange:function(e){return a({meta_date_format:e})}})),React.createElement(d,{label:__("Display taxonomy","recently"),checked:t.meta_taxonomy,onChange:function(e){return a({meta_taxonomy:e})}}),t.meta_taxonomy&&React.createElement("div",{className:"option-subset"},React.createElement(b,{multiple:!0,label:__("Taxonomy","recently"),value:t.meta_taxonomy_list,options:n,onChange:function(e){return a({meta_taxonomy_list:e})}})))}},{key:"getHTMLMarkupFields",value:function(){var e=this.props,t=e.attributes,a=e.setAttributes,n=this,l=[{label:__("None","recently"),value:""}];if(this.state.themes)for(var r in this.state.themes)l.push({label:this.state.themes[r].json.name,value:r});return React.createElement(m,null,React.createElement("p",{className:"not-a-legend"},React.createElement("strong",null,__("HTML Markup settings","recently"))),React.createElement(d,{label:__("Use custom HTML Markup","recently"),checked:t.custom_html,onChange:function(e){return a({custom_html:e})}}),t.custom_html&&React.createElement("div",{className:"option-subset"},React.createElement(f,{rows:"1",label:__("Before title","recently"),value:t.header_start,onChange:function(e){return a({header_start:e})}}),React.createElement(f,{rows:"1",label:__("After title","recently"),value:t.header_end,onChange:function(e){return a({header_end:e})}}),React.createElement(f,{rows:"1",label:__("Before recent posts","recently"),value:t.recently_start,onChange:function(e){return a({recently_start:e})}}),React.createElement(f,{rows:"1",label:__("After recent posts","recently"),value:t.recently_end,onChange:function(e){return a({recently_end:e})}}),React.createElement(f,{label:__("Post HTML markup","recently"),value:t.post_html,onChange:function(e){return a({post_html:e})}})),React.createElement(b,{label:__("Theme","recently"),value:t.theme,options:l,onChange:function(e){if(void 0!==n.state.themes[e]){var t=n.state.themes[e].json.config;a({shorten_title:t.shorten_title.active,title_length:t.shorten_title.length,title_by_words:t.shorten_title.words?1:0,display_post_excerpt:t["post-excerpt"].active,excerpt_format:t["post-excerpt"].format,excerpt_length:t["post-excerpt"].length,excerpt_by_words:t["post-excerpt"].words?1:0,display_post_thumbnail:t.thumbnail.active,thumbnail_build:t.thumbnail.build,thumbnail_width:t.thumbnail.width,thumbnail_height:t.thumbnail.height,meta_comments:t.meta_tag.comment_count,meta_views:t.meta_tag.views,meta_author:t.meta_tag.author,meta_date:t.meta_tag.date.active,meta_date_format:t.meta_tag.date.format,meta_taxonomy:t.meta_tag.taxonomy.active,taxonomy:t.meta_tag.taxonomy.name,custom_html:!0,recently_start:t.markup["recently-start"],recently_end:t.markup["recently-end"],post_html:t.markup["post-html"],theme:e})}else a({theme:e})}}))}},{key:"render",value:function(){if(!this.state.taxonomies||!this.state.themes||!this.state.imgSizes)return React.createElement(_,null);var e=this.props,t=e.isSelected,a=e.className,n=e.attributes,l=a;return l+=this.state.editMode?" in-edit-mode":" in-preview-mode",l+=t?" is-selected":"",[this.getBlockControls(),React.createElement("div",{className:l},this.state.editMode&&React.createElement(m,null,this.getMainFields(),this.getFiltersFields(),this.getPostSettingsFields(),this.getStatsTagFields(),this.getHTMLMarkupFields()),!this.state.editMode&&React.createElement(y,null,React.createElement(s,{block:this.props.name,className:a,attributes:n,urlQueryArgs:{isSelected:t}})))]}}],c&&n(l.prototype,c),Object.defineProperty(l,"prototype",{writable:!1}),l;var l,c}(u),R=wp.blocks.registerBlockType,k=wp.i18n.__;R("recently/widget",{title:"Recently",category:"widgets",icon:t.recently,description:k("A highly customizable block that displays your most recent posts.","recently"),keywords:["recent","posts","recently"],attributes:{_editMode:{type:"boolean",default:!0},_isSelected:{type:"boolean",default:!1},blockID:{type:"string"},title:{type:"string"},posts_per_page:{type:"number",default:10},offset:{type:"number",default:0},post_type:{type:"string",default:"post"},post_id:{type:"string",default:""},author_id:{type:"string",default:""},taxonomy:{type:"string",default:""},term_id:{type:"string",default:""},term_slug:{type:"string",default:""},shorten_title:{type:"boolean",default:!1},title_length:{type:"number",default:0},title_by_words:{type:"number",default:0},display_post_excerpt:{type:"boolean",default:!1},excerpt_format:{type:"boolean",default:!1},excerpt_length:{type:"number",default:0},excerpt_by_words:{type:"number",default:0},display_post_thumbnail:{type:"boolean",default:!1},thumbnail_width:{type:"number",default:0},thumbnail_height:{type:"number",default:0},thumbnail_build:{type:"string",default:"manual"},thumbnail_size:{type:"string",default:""},rating:{type:"boolean",default:!1},meta_comments:{type:"boolean",default:!0},meta_views:{type:"boolean",default:!1},meta_author:{type:"boolean",default:!1},meta_date:{type:"boolean",default:!1},meta_date_format:{type:"string",default:"F j, Y"},meta_taxonomy:{type:"boolean",default:!1},meta_taxonomy_list:{type:"array",default:["category"]},custom_html:{type:"boolean",default:!1},header_start:{type:"string",default:"<h2>"},header_end:{type:"string",default:"</h2>"},recently_start:{type:"string",default:'<ul class="recently-list">'},recently_end:{type:"string",default:"</ul>"},post_html:{type:"string",default:"<li>{thumb} {title} {meta}</li>"},theme:{type:"string",default:""}},supports:{anchor:!0,align:!0,html:!1},example:{attributes:{_editMode:!1,title:"Recent Posts",limit:3,display_post_excerpt:!0,excerpt_length:75,display_post_thumbnail:!0,thumbnail_width:75,thumbnail_height:75,meta_comments:!1,meta_taxonomy:!0,custom_html:!0,recently_start:'<ul class="recently-list recently-cards">',post_html:'<li>{thumb_img} <div class="recently-item-data"><div class="taxonomies">{taxonomy}</div>{title} <p class="wpp-excerpt">{excerpt}</p></div></li>',theme:"cards"}},edit:E,save:function(){return null}})})();