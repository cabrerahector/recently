!function(){"use strict";var e=window.wp.element;const t={};t.recently=(0,e.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",xmlnsXlink:"http://www.w3.org/1999/xlink",enableBackground:"new 0 0 595.3 841.9",viewBox:"0 0 595.3 841.9"},(0,e.createElement)("defs",null,(0,e.createElement)("path",{id:"a",d:"M83.8 205h388v388h-388z"})),(0,e.createElement)("clipPath",{id:"b"},(0,e.createElement)("use",{overflow:"visible",xlinkHref:"#a"})),(0,e.createElement)("path",{fill:"#56b078",d:"M217 188.9C136.7 229.6 90.1 310.9 89.7 396h68.6c.8-60.8 33.5-117.4 89.7-145.9 80.5-40.7 179-8.4 219.7 72.1 40.7 80.5 8.4 179-72.1 219.7-61.5 31.1-133.5 19.5-182.3-23.6l92.9-90.4h-217l-5.5 216.8 80.4-78.4c69.2 63.9 173.6 81.7 262.4 36.8 114.2-57.8 160.1-197.7 102.3-311.9C471.2 177 331.2 131.1 217 188.9zm-83.1 284.7h59.9l-61.4 59.9 1.5-59.9z",clipPath:"url(#b)"}));var l=t;const{serverSideRender:a}=wp,{Component:n,Fragment:s}=wp.element,{BlockControls:i}=wp.blockEditor,{CheckboxControl:r,Disabled:o,SelectControl:m,Spinner:c,TextareaControl:h,TextControl:u,Toolbar:p,ToolbarButton:d}=wp.components,{__:__}=wp.i18n,_="recently/v1",{registerBlockType:b}=wp.blocks,{__:y}=wp.i18n;b("recently/widget",{title:"Recently",category:"widgets",icon:l.recently,description:y("A highly customizable block that displays your most recent posts.","recently"),keywords:["recent","posts","recently"],attributes:{_editMode:{type:"boolean",default:!0},_isSelected:{type:"boolean",default:!1},blockID:{type:"string"},title:{type:"string"},posts_per_page:{type:"number",default:10},offset:{type:"number",default:0},post_type:{type:"string",default:"post"},post_id:{type:"string",default:""},author_id:{type:"string",default:""},taxonomy:{type:"string",default:""},term_id:{type:"string",default:""},term_slug:{type:"string",default:""},shorten_title:{type:"boolean",default:!1},title_length:{type:"number",default:0},title_by_words:{type:"number",default:0},display_post_excerpt:{type:"boolean",default:!1},excerpt_format:{type:"boolean",default:!1},excerpt_length:{type:"number",default:0},excerpt_by_words:{type:"number",default:0},display_post_thumbnail:{type:"boolean",default:!1},thumbnail_width:{type:"number",default:0},thumbnail_height:{type:"number",default:0},thumbnail_build:{type:"string",default:"manual"},thumbnail_size:{type:"string",default:""},rating:{type:"boolean",default:!1},meta_comments:{type:"boolean",default:!0},meta_views:{type:"boolean",default:!1},meta_author:{type:"boolean",default:!1},meta_date:{type:"boolean",default:!1},meta_date_format:{type:"string",default:"F j, Y"},meta_taxonomy:{type:"boolean",default:!1},meta_taxonomy_list:{type:"array",default:["category"]},custom_html:{type:"boolean",default:!1},header_start:{type:"string",default:"<h2>"},header_end:{type:"string",default:"</h2>"},recently_start:{type:"string",default:'<ul class="recently-list">'},recently_end:{type:"string",default:"</ul>"},post_html:{type:"string",default:"<li>{thumb} {title} {meta}</li>"},theme:{type:"string",default:""}},supports:{anchor:!0,align:!0,html:!1},example:{attributes:{_editMode:!1,title:"Recent Posts",limit:3,display_post_excerpt:!0,excerpt_length:75,display_post_thumbnail:!0,thumbnail_width:75,thumbnail_height:75,meta_comments:!1,meta_taxonomy:!0,custom_html:!0,recently_start:'<ul class="recently-list recently-cards">',post_html:'<li>{thumb_img} <div class="recently-item-data"><div class="taxonomies">{taxonomy}</div>{title} <p class="wpp-excerpt">{excerpt}</p></div></li>',theme:"cards"}},edit:class extends n{constructor(e){super(e),this.state={error:null,editMode:!0,themes:null,imgSizes:null,taxonomies:null};const{attributes:t,setAttributes:l,clientId:a}=this.props,{blockID:n}=t;n||l({blockID:a})}componentDidMount(){const{attributes:e}=this.props;this.getThemes(),this.getImageSizes(),this.getTaxonomies(),this.setState({editMode:e._editMode})}getThemes(){wp.apiFetch({path:_+"/themes"}).then((e=>{this.setState({themes:e})}),(e=>{this.setState({error:e,themes:null})}))}getImageSizes(){wp.apiFetch({path:_+"/thumbnails"}).then((e=>{this.setState({imgSizes:e})}),(e=>{this.setState({error:e,imgSizes:null})}))}getTaxonomies(){const{attributes:e}=this.props;wp.apiFetch({path:_+"/taxonomies"}).then((t=>{if(t){let a=e.taxonomy.split(";"),n=e.term_id.split(";"),s=e.term_slug.split(";");if(a.length&&(a.length==n.length||a.length==s.length)){let e={};for(var l=0;l<a.length;l++)e[a[l]]=n[l];for(const l in t)t[l]._terms=void 0!==e[l]?e[l]:""}}this.setState({taxonomies:t})}),(e=>{this.setState({error:e,taxonomies:null})}))}getBlockControls(){const{setAttributes:t}=this.props,l=this;return(0,e.createElement)(i,null,(0,e.createElement)(p,{label:"{ __('Settings') }"},(0,e.createElement)(d,{label:this.state.editMode?__("Preview","recently"):__("Edit","recently"),icon:this.state.editMode?"format-image":"edit",onClick:function(){let e=!l.state.editMode;l.setState({editMode:e}),t({_editMode:e})}})))}getMainFields(){const{attributes:t,setAttributes:l}=this.props;return(0,e.createElement)(s,null,(0,e.createElement)(u,{label:__("Title","recently"),value:t.title,onChange:function(e){e=function(e){const t={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","/":"&#x2F;","`":"&grave;"};return e.replace(/[&<>"'/]/gi,(e=>t[e]))}(function(e){var t=document.createElement("div");t.innerHTML=e;var l=t.childNodes[0];return l?l.nodeValue:""}(e)),l({title:e})}}),(0,e.createElement)(u,{label:__("Limit","recently"),help:__("Max. number of posts to show.","recently"),value:t.posts_per_page,onChange:function(e){let t=Number.isInteger(Number(e))&&Number(e)>0?e:10;l({posts_per_page:Number(t)})}}),(0,e.createElement)(u,{label:__("Offset","recently"),help:__("Number of posts to displace or pass over.","recently"),value:t.offset,onChange:function(e){let t=Number.isInteger(Number(e))&&Number(e)>=0?e:10;l({offset:Number(t)})}}))}getFiltersFields(){const{attributes:t,setAttributes:l}=this.props,a=this;let n=[];if(this.state.taxonomies)for(const e in this.state.taxonomies)n.push({name:this.state.taxonomies[e].name,label:this.state.taxonomies[e].labels.singular_name+" ("+this.state.taxonomies[e].name+")",terms:this.state.taxonomies[e]._terms});return(0,e.createElement)(s,null,(0,e.createElement)("p",{className:"not-a-legend"},(0,e.createElement)("strong",null,__("Filters","recently"))),(0,e.createElement)(u,{label:__("Post Type(s)","recently"),help:__(" Post types, separated by comma.","recently"),value:t.post_type,onChange:function(e){let t=e.replace(/[^a-z0-9-_\,]+/gi,"");l({post_type:t})}}),(0,e.createElement)(u,{label:__("Post ID(s) to exclude","recently"),help:__(" Post / Page IDs, separated by comma.","recently"),value:t.post_id,onChange:function(e){let t=e.replace(/[^0-9\,]/g,"");l({post_id:t})}}),(0,e.createElement)(u,{label:__("Author ID(s)","recently"),help:__(" Author IDs, separated by comma (prefix a minus sign to exclude).","recently"),value:t.author_id,onChange:function(e){let t=e.replace(/[^0-9\,]/g,"");l({author_id:t})}}),n&&n.filter((e=>"post_format"!=e.name)).map((t=>(0,e.createElement)(u,{label:t.label,help:__("Term IDs must be comma separated, prefix a minus sign to exclude.","recently"),value:t.terms,onChange:e=>function(e,t){let l=a.state.taxonomies;t=t.replace(/[^0-9-\,]/g,""),l&&void 0!==l[e]&&(l[e]._terms=t,a.setState({taxonomies:l}))}(t.name,e),onBlur:()=>function(e){let t=a.state.taxonomies;if(t&&void 0!==t[e]){let n=t[e]._terms.split(",");n.length&&(n=n.map((e=>e.trim())).filter((e=>""!=e&&"-"!=e))),n.length&&(n=Array.from(new Set(n))),t[e]._terms=n.join(","),a.setState({taxonomies:t});let s="",i="";for(let e in a.state.taxonomies)if(a.state.taxonomies.hasOwnProperty(e)){if(!a.state.taxonomies[e]._terms.length)continue;s+=e+";",i+=a.state.taxonomies[e]._terms+";"}s&&i&&(s=s.replace(new RegExp(";$"),""),i=i.replace(new RegExp(";$"),"")),l({taxonomy:s,term_id:i})}}(t.name)}))))}getPostSettingsFields(){const{attributes:t,setAttributes:l}=this.props,a=this;function n(e,t){let a=Number.isInteger(Number(t))&&Number(t)>=0?t:0;l("width"==e?{thumbnail_width:Number(a)}:{thumbnail_height:Number(a)})}let i=[];if(this.state.imgSizes)for(const e in this.state.imgSizes)i.push({label:e,value:e});return(0,e.createElement)(s,null,(0,e.createElement)("p",{className:"not-a-legend"},(0,e.createElement)("strong",null,__("Posts settings","recently"))),(0,e.createElement)(r,{label:__("Shorten title","recently"),checked:t.shorten_title,onChange:function(e){l(0==e?{title_length:0,title_by_words:0,shorten_title:e}:{shorten_title:e,title_length:25})}}),t.shorten_title&&(0,e.createElement)("div",{className:"option-subset"},(0,e.createElement)(u,{label:__("Shorten title to","recently"),value:t.title_length,onChange:function(e){let t=Number.isInteger(Number(e))&&Number(e)>=0?e:0;l({title_length:Number(t)})}}),(0,e.createElement)(m,{value:t.title_by_words,options:[{label:__("characters","recently"),value:0},{label:__("words","recently"),value:1}],onChange:e=>l({title_by_words:Number(e)})})),(0,e.createElement)(r,{label:__("Display post excerpt","recently"),checked:t.display_post_excerpt,onChange:function(e){l(0==e?{excerpt_length:0,excerpt_by_words:0,display_post_excerpt:e,excerpt_format:!1}:{display_post_excerpt:e,excerpt_length:55})}}),t.display_post_excerpt&&(0,e.createElement)("div",{className:"option-subset"},(0,e.createElement)(r,{label:__("Keep text format and links","recently"),checked:t.excerpt_format,onChange:e=>l({excerpt_format:e})}),(0,e.createElement)(u,{label:__("Excerpt length","recently"),value:t.excerpt_length,onChange:function(e){let t=Number.isInteger(Number(e))&&Number(e)>=0?e:0;l({excerpt_length:Number(t)})}}),(0,e.createElement)(m,{value:t.excerpt_by_words,options:[{label:__("characters","recently"),value:0},{label:__("words","recently"),value:1}],onChange:e=>l({excerpt_by_words:Number(e)})})),(0,e.createElement)(r,{label:__("Display post thumbnail","recently"),checked:t.display_post_thumbnail,onChange:function(e){l(0==e?{thumbnail_width:0,thumbnail_height:0,display_post_thumbnail:e,thumbnail_build:"manual"}:{thumbnail_width:75,thumbnail_height:75,display_post_thumbnail:e})}}),t.display_post_thumbnail&&(0,e.createElement)("div",{className:"option-subset"},(0,e.createElement)(m,{value:t.thumbnail_build,options:[{label:__("Set size manually","recently"),value:"manual"},{label:__("Use predefined size","recently"),value:"predefined"}],onChange:function(e){if("predefined"==e){let e=0;l({thumbnail_width:a.state.imgSizes[i[e].value].width,thumbnail_height:a.state.imgSizes[i[e].value].height,thumbnail_size:i[e].value})}else l({thumbnail_width:75,thumbnail_height:75,thumbnail_size:""});l({thumbnail_build:e})}}),"manual"==t.thumbnail_build&&(0,e.createElement)(s,null,(0,e.createElement)(u,{label:__("Thumbnail width","recently"),help:__("Size in px units (pixels)","recently"),value:t.thumbnail_width,onChange:e=>n("width",e)}),(0,e.createElement)(u,{label:__("Thumbnail height","recently"),help:__("Size in px units (pixels)","recently"),value:t.thumbnail_height,onChange:e=>n("height",e)})),"predefined"==t.thumbnail_build&&(0,e.createElement)(s,null,(0,e.createElement)(m,{value:t.thumbnail_size,options:i,onChange:function(e){l({thumbnail_width:a.state.imgSizes[e].width,thumbnail_height:a.state.imgSizes[e].height,thumbnail_size:e})}}))),_recently.can_show_rating&&(0,e.createElement)(r,{label:__("Display post rating","recently"),checked:t.rating,onChange:e=>l({rating:e})}))}getStatsTagFields(){const{attributes:t,setAttributes:l}=this.props;let a=[];if(this.state.taxonomies)for(const e in this.state.taxonomies)"post_format"!=this.state.taxonomies[e].name&&a.push({label:this.state.taxonomies[e].labels.singular_name+" ("+this.state.taxonomies[e].name+")",value:this.state.taxonomies[e].name});return(0,e.createElement)(s,null,(0,e.createElement)("p",{className:"not-a-legend"},(0,e.createElement)("strong",null,__("Stats Tag settings","recently"))),(0,e.createElement)(r,{label:__("Display comments count","recently"),checked:t.meta_comments,onChange:e=>l({meta_comments:e})}),_recently.can_show_views&&(0,e.createElement)(r,{label:__("Display views","recently"),checked:t.meta_views,onChange:e=>l({meta_views:e})}),(0,e.createElement)(r,{label:__("Display author","recently"),checked:t.meta_author,onChange:e=>l({meta_author:e})}),(0,e.createElement)(r,{label:__("Display date","recently"),checked:t.meta_date,onChange:e=>l({meta_date:e})}),t.meta_date&&(0,e.createElement)("div",{className:"option-subset"},(0,e.createElement)(m,{label:__("Date Format","recently"),value:t.meta_date_format,options:[{label:__("Relative","recently"),value:"relative"},{label:__("Month Day, Year","recently"),value:"F j, Y"},{label:__("yyyy/mm/dd","recently"),value:"Y/m/d"},{label:__("mm/dd/yyyy","recently"),value:"m/d/Y"},{label:__("dd/mm/yyyy","recently"),value:"d/m/Y"},{label:__("WordPress Date Format","recently"),value:"wp_date_format"}],onChange:e=>l({meta_date_format:e})})),(0,e.createElement)(r,{label:__("Display taxonomy","recently"),checked:t.meta_taxonomy,onChange:e=>l({meta_taxonomy:e})}),t.meta_taxonomy&&(0,e.createElement)("div",{className:"option-subset"},(0,e.createElement)(m,{multiple:!0,label:__("Taxonomy","recently"),value:t.meta_taxonomy_list,options:a,onChange:e=>l({meta_taxonomy_list:e})})))}getHTMLMarkupFields(){const{attributes:t,setAttributes:l}=this.props,a=this;let n=[{label:__("None","recently"),value:""}];if(this.state.themes)for(const e in this.state.themes)n.push({label:this.state.themes[e].json.name,value:e});return(0,e.createElement)(s,null,(0,e.createElement)("p",{className:"not-a-legend"},(0,e.createElement)("strong",null,__("HTML Markup settings","recently"))),(0,e.createElement)(r,{label:__("Use custom HTML Markup","recently"),checked:t.custom_html,onChange:e=>l({custom_html:e})}),t.custom_html&&(0,e.createElement)("div",{className:"option-subset"},(0,e.createElement)(h,{rows:"1",label:__("Before title","recently"),value:t.header_start,onChange:e=>l({header_start:e})}),(0,e.createElement)(h,{rows:"1",label:__("After title","recently"),value:t.header_end,onChange:e=>l({header_end:e})}),(0,e.createElement)(h,{rows:"1",label:__("Before recent posts","recently"),value:t.recently_start,onChange:e=>l({recently_start:e})}),(0,e.createElement)(h,{rows:"1",label:__("After recent posts","recently"),value:t.recently_end,onChange:e=>l({recently_end:e})}),(0,e.createElement)(h,{label:__("Post HTML markup","recently"),value:t.post_html,onChange:e=>l({post_html:e})})),(0,e.createElement)(m,{label:__("Theme","recently"),value:t.theme,options:n,onChange:function(e){if(void 0!==a.state.themes[e]){let t=a.state.themes[e].json.config;l({shorten_title:t.shorten_title.active,title_length:t.shorten_title.length,title_by_words:t.shorten_title.words?1:0,display_post_excerpt:t["post-excerpt"].active,excerpt_format:t["post-excerpt"].format,excerpt_length:t["post-excerpt"].length,excerpt_by_words:t["post-excerpt"].words?1:0,display_post_thumbnail:t.thumbnail.active,thumbnail_build:t.thumbnail.build,thumbnail_width:t.thumbnail.width,thumbnail_height:t.thumbnail.height,meta_comments:t.meta_tag.comment_count,meta_views:t.meta_tag.views,meta_author:t.meta_tag.author,meta_date:t.meta_tag.date.active,meta_date_format:t.meta_tag.date.format,meta_taxonomy:t.meta_tag.taxonomy.active,taxonomy:t.meta_tag.taxonomy.name,custom_html:!0,recently_start:t.markup["recently-start"],recently_end:t.markup["recently-end"],post_html:t.markup["post-html"],theme:e})}else l({theme:e})}}))}render(){if(!this.state.taxonomies||!this.state.themes||!this.state.imgSizes)return(0,e.createElement)(c,null);const{isSelected:t,className:l,attributes:n}=this.props;let i=l;return i+=this.state.editMode?" in-edit-mode":" in-preview-mode",i+=t?" is-selected":"",[this.getBlockControls(),(0,e.createElement)("div",{className:i},this.state.editMode&&(0,e.createElement)(s,null,this.getMainFields(),this.getFiltersFields(),this.getPostSettingsFields(),this.getStatsTagFields(),this.getHTMLMarkupFields()),!this.state.editMode&&(0,e.createElement)(o,null,(0,e.createElement)(a,{block:this.props.name,className:l,attributes:n,urlQueryArgs:{isSelected:t}})))]}},save:()=>null})}();