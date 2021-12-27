(window.__wcAdmin_webpackJsonp=window.__wcAdmin_webpackJsonp||[]).push([[5],{512:function(e,t,c){"use strict";c.d(t,"a",(function(){return N})),c.d(t,"b",(function(){return O}));var a=c(0),n=c(6),o=c.n(n),s=c(61),i=c.n(s),r=c(9),l=c.n(r),m=c(1),d=c.n(m),u=c(21),b=c(3),_=(c(522),c(4));class p extends a.Component{render(){const{className:e,hasAction:t,hasDate:c,hasSubtitle:n,lines:s}=this.props,i=o()("woocommerce-activity-card is-loading",e);return Object(a.createElement)("div",{className:i,"aria-hidden":!0},Object(a.createElement)("span",{className:"woocommerce-activity-card__icon"},Object(a.createElement)("span",{className:"is-placeholder"})),Object(a.createElement)("div",{className:"woocommerce-activity-card__header"},Object(a.createElement)("div",{className:"woocommerce-activity-card__title is-placeholder"}),n&&Object(a.createElement)("div",{className:"woocommerce-activity-card__subtitle is-placeholder"}),c&&Object(a.createElement)("div",{className:"woocommerce-activity-card__date"},Object(a.createElement)("span",{className:"is-placeholder"}))),Object(a.createElement)("div",{className:"woocommerce-activity-card__body"},Object(_.range)(s).map(e=>Object(a.createElement)("span",{className:"is-placeholder",key:e}))),t&&Object(a.createElement)("div",{className:"woocommerce-activity-card__actions"},Object(a.createElement)("span",{className:"is-placeholder"})))}}p.propTypes={className:d.a.string,hasAction:d.a.bool,hasDate:d.a.bool,hasSubtitle:d.a.bool,lines:d.a.number},p.defaultProps={hasAction:!1,hasDate:!1,hasSubtitle:!1,lines:1};var O=p;class N extends a.Component{getCard(){const{actions:e,className:t,children:c,date:n,icon:s,subtitle:i,title:r,unread:m}=this.props,d=o()("woocommerce-activity-card",t),b=Array.isArray(e)?e:[e],_=/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/.test(n)?l.a.utc(n).fromNow():n;return Object(a.createElement)("section",{className:d},m&&Object(a.createElement)("span",{className:"woocommerce-activity-card__unread"}),s&&Object(a.createElement)("span",{className:"woocommerce-activity-card__icon","aria-hidden":!0},s),r&&Object(a.createElement)("header",{className:"woocommerce-activity-card__header"},Object(a.createElement)(u.H,{className:"woocommerce-activity-card__title"},r),i&&Object(a.createElement)("div",{className:"woocommerce-activity-card__subtitle"},i),_&&Object(a.createElement)("span",{className:"woocommerce-activity-card__date"},_)),c&&Object(a.createElement)(u.Section,{className:"woocommerce-activity-card__body"},c),e&&Object(a.createElement)("footer",{className:"woocommerce-activity-card__actions"},b.map((e,t)=>Object(a.cloneElement)(e,{key:t}))))}render(){const{onClick:e}=this.props;return e?Object(a.createElement)(b.Button,{className:"woocommerce-activity-card__button",onClick:e},this.getCard()):this.getCard()}}N.propTypes={actions:d.a.oneOfType([d.a.arrayOf(d.a.element),d.a.element]),onClick:d.a.func,className:d.a.string,children:d.a.node,date:d.a.string,icon:d.a.node,subtitle:d.a.node,title:d.a.oneOfType([d.a.string,d.a.node]),unread:d.a.bool},N.defaultProps={icon:Object(a.createElement)(i.a,{size:48}),unread:!1}},517:function(e,t,c){"use strict";function a(e){return e?e.substr(1).split("&").reduce((e,t)=>{const c=t.split("="),a=c[0];let n=decodeURIComponent(c[1]);return n=isNaN(Number(n))?n:Number(n),e[a]=n,e},{}):{}}function n(){let e="";const{page:t,path:c,post_type:n}=a(window.location.search);if(t){const a="wc-admin"===t?"home_screen":t;e=c?c.replace(/\//g,"_").substring(1):a}else n&&(e=n);return e}c.d(t,"b",(function(){return a})),c.d(t,"a",(function(){return n}))},522:function(e,t,c){},523:function(e,t,c){"use strict";var a=c(0),n=c(2),o=c(21),s=c(11),i=c(7),r=c(16),l=c(172),m=c(169),d=c(20),u=c(512),b=c(164),_=c(517);c(524);const p=(e,t)=>{Object(r.recordEvent)("inbox_action_click",{note_name:e.name,note_title:e.title,note_content_inner_link:t})},O=({hasNotes:e,isBatchUpdating:t,lastRead:c,notes:o,onDismiss:s,onNoteActionClick:i})=>{if(t)return;if(!e)return Object(a.createElement)(u.a,{className:"woocommerce-empty-activity-card",title:Object(n.__)("Your inbox is empty",'woocommerce'),icon:!1},Object(n.__)("As things begin to happen in your store your inbox will start to fill up. You'll see things like achievements, new feature announcements, extension recommendations and more!",'woocommerce'));const b=Object(_.a)(),O=e=>{Object(r.recordEvent)("inbox_note_view",{note_content:e.content,note_name:e.name,note_title:e.title,note_type:e.type,screen:b})},N=Object.keys(o).map(e=>o[e]);return Object(a.createElement)(l.a,{role:"menu"},N.map(e=>{const{id:t,is_deleted:n}=e;return n?null:Object(a.createElement)(m.a,{key:t,timeout:500,classNames:"woocommerce-inbox-message"},Object(a.createElement)(d.InboxNoteCard,{key:t,note:e,lastRead:c,onDismiss:s,onNoteActionClick:i,onBodyLinkClick:p,onNoteVisible:O}))}))},N={page:1,per_page:s.QUERY_DEFAULTS.pageSize,status:"unactioned",type:s.QUERY_DEFAULTS.noteTypes,orderby:"date",order:"desc",_fields:["id","name","title","content","type","status","actions","date_created","date_created_gmt","layout","image","is_deleted"]};t.a=()=>{const{createNotice:e}=Object(i.useDispatch)("core/notices"),{batchUpdateNotes:t,removeAllNotes:c,removeNote:l,updateNote:m,triggerNoteAction:u}=Object(i.useDispatch)(s.NOTES_STORE_NAME),{isError:p,isResolvingNotes:h,isBatchUpdating:j,notes:v}=Object(i.useSelect)(e=>{const{getNotes:t,getNotesError:c,isResolving:a,isNotesRequesting:n}=e(s.NOTES_STORE_NAME);return{notes:t(N),isError:Boolean(c("getNotes",[N])),isResolvingNotes:a("getNotes",[N]),isBatchUpdating:n("batchUpdateNotes")}}),{updateUserPreferences:w,...y}=Object(s.useUserPreferences)(),[g]=Object(a.useState)(y.activity_panel_inbox_last_read),[E,f]=Object(a.useState)();Object(a.useEffect)(()=>{const e=Date.now();w({activity_panel_inbox_last_read:e})},[]);const C=async(a=!1)=>{const o="all"===E.type,s=Object(_.a)();if(Object(r.recordEvent)("inbox_action_dismiss",{note_name:E.note.name,note_title:E.note.title,note_name_dismiss_all:o,note_name_dismiss_confirmation:a,screen:s}),a){const a=E.note.id,s=!a||o;try{let o=[];if(s)o=await c({status:N.status});else{const e=await l(a);o=[e]}f(void 0),e("success",o.length>1?Object(n.__)("All messages dismissed",'woocommerce'):Object(n.__)("Message dismissed",'woocommerce'),{actions:[{label:Object(n.__)("Undo",'woocommerce'),onClick:()=>{o.length>1?t(o.map(e=>e.id),{is_deleted:0}):m(a,{is_deleted:0})}}]})}catch(t){const c=s?v.length:1;e("error",Object(n._n)("Message could not be dismissed","Messages could not be dismissed",c,'woocommerce')),f(void 0)}}else f(void 0)};if(p){const e=Object(n.__)("There was an error getting your inbox. Please try again.",'woocommerce'),t=Object(n.__)("Reload",'woocommerce'),c=()=>{window.location.reload()};return Object(a.createElement)(o.EmptyContent,{title:e,actionLabel:t,actionURL:null,actionCallback:c})}const k=Object(b.b)(v);return Object(a.createElement)(a.Fragment,null,Object(a.createElement)("div",{className:"woocommerce-homepage-notes-wrapper"},(h||j)&&Object(a.createElement)(o.Section,null,Object(a.createElement)(d.InboxNotePlaceholder,{className:"banner message-is-unread"})),Object(a.createElement)(o.Section,null,!h&&!j&&O({hasNotes:k,isBatchUpdating:j,lastRead:g,notes:v,onDismiss:(e,t)=>{f({note:e,type:t})},onNoteActionClick:(e,t)=>{u(e.id,t.id)}})),E&&Object(a.createElement)(d.InboxDismissConfirmationModal,{onClose:C,onDismiss:()=>C(!0)})))}},524:function(e,t,c){},585:function(e,t,c){},608:function(e,t,c){"use strict";c.r(t),c.d(t,"InboxPanel",(function(){return s}));var a=c(0),n=(c(585),c(523)),o=c(254);const s=({hasAbbreviatedNotifications:e,thingsToDoNextCount:t})=>Object(a.createElement)("div",{className:"woocommerce-notification-panels"},e&&Object(a.createElement)(o.b,{thingsToDoNextCount:t}),Object(a.createElement)(n.a,null));t.default=s},61:function(e,t,c){"use strict";var a=Object.assign||function(e){for(var t,c=1;c<arguments.length;c++)for(var a in t=arguments[c])Object.prototype.hasOwnProperty.call(t,a)&&(e[a]=t[a]);return e};Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(e){var t,c=e.size,n=void 0===c?24:c,o=e.onClick,i=(e.icon,e.className),r=function(e,t){var c={};for(var a in e)0<=t.indexOf(a)||Object.prototype.hasOwnProperty.call(e,a)&&(c[a]=e[a]);return c}(e,["size","onClick","icon","className"]),l=["gridicon","gridicons-notice-outline",i,(t=n,!(0!=t%18)&&"needs-offset"),!1,!1].filter(Boolean).join(" ");return s.default.createElement("svg",a({className:l,height:n,width:n,onClick:o},r,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"}),s.default.createElement("g",null,s.default.createElement("path",{d:"M12 4c4.41 0 8 3.59 8 8s-3.59 8-8 8-8-3.59-8-8 3.59-8 8-8m0-2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 13h-2v2h2v-2zm-2-2h2l.5-6h-3l.5 6z"})))};var n,o=c(5),s=(n=o)&&n.__esModule?n:{default:n};e.exports=t.default}}]);