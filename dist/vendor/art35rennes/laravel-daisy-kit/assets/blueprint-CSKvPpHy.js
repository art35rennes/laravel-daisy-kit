const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/lazy-editors-BLRR2-PY.js","assets/rolldown-runtime-DAXXjFlN.js","assets/app-B_LCWxYG.js","assets/vendor-_w-TbwAY.js","assets/trix-CLhQ41h9.js","assets/blueprint-layout-DzSZUrPQ.js","assets/blueprint-engine-By9Uz-pu.js","assets/blueprint-render-CQtWP0Vb.js"])))=>i.map(i=>d[i]);
import{n as e,r as t}from"./rolldown-runtime-DAXXjFlN.js";import{i as n,r}from"./app-B_LCWxYG.js";import{S as i,T as a,a as o,b as s,c,d as l,f as u,h as d,i as f,l as p,m as ee,n as m,o as te,p as ne,r as re,s as h,t as ie,u as ae,v as oe,w as g,x as _}from"./blueprint-engine-By9Uz-pu.js";import{n as v,r as y,t as se}from"./blueprint-render-CQtWP0Vb.js";function ce(e=[]){return L(e).map(e=>{let t=R(e),n=z(t.type),r=C(t.controls??t.fields);if(!n)return null;let i={type:n,label:z(t.label,n),category:z(t.category,`General`),description:z(t.description),theme:z(t.theme,`default`),display:x(t.display??t.variant),icon:z(t.icon??t.brandIcon),nameStrategy:S(t.nameStrategy??t.naming),previewFields:b(t.previewFields??t.displayFields??t.visibleFields??t.preview),inputs:O(t.inputs,!1),outputs:O(t.outputs,!0),controls:r,defaults:{...w(r),...R(t.defaults)}},a=z(t.colorField??t.color_field);return a&&(i.colorField=a),i}).filter(Boolean)}function b(e=[]){return L(e).map(e=>{if(typeof e==`string`)return{key:z(e),label:``};let t=R(e),n=z(t.key??t.name??t.id);return n?{key:n,label:z(t.label)}:null}).filter(Boolean)}function x(e){let t=z(e,`detailed`);return[`minimal`,`detailed`].includes(t)?t:`detailed`}function S(e={}){if(typeof e==`string`)return{mode:z(e,`free`)};let t=R(e),n=z(t.mode,`free`);return{mode:[`free`,`preset`,`auto`].includes(n)?n:`free`,prefix:z(t.prefix),value:z(t.value)}}function C(e=[]){return L(e).map(e=>{let t=R(e),n=z(t.key??t.name??t.id);if(!n)return null;let r=z(t.type,`text`),i={key:n,name:z(t.name,n),label:z(t.label,n),type:D(r),placeholder:z(t.placeholder),help:z(t.help??t.description),section:z(t.section??t.group??t.tab),required:!!t.required,pattern:z(t.pattern),minLength:t.minLength??t.minlength??null,maxLength:t.maxLength??t.maxlength??null,min:t.min??null,max:t.max??null,step:t.step??null,options:T(t.options),default:t.default??t.value??null};return[`code-editor`,`wysiwyg`].includes(i.type)&&(i.height=z(t.height)),i.type===`code-editor`&&(i.language=z(t.language,`javascript`)),i}).filter(Boolean)}function w(e=[]){return L(e).reduce((e,t)=>(t.default!==null&&t.default!==void 0&&(e[t.key]=t.default),e),{})}function T(e=[]){return L(e).map(e=>{if(typeof e==`string`||typeof e==`number`||typeof e==`boolean`)return{value:String(e),label:String(e)};let t=R(e),n=t.value??t.id??t.key;return n==null?null:{value:String(n),label:z(t.label,String(n)),color:E(t.color)}}).filter(Boolean)}function E(e){let t=z(e);if(!t)return``;let n=t.startsWith(`#`)?t.slice(1):t;return/^(?:[0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(n)?`#${n.toLowerCase()}`:``}function D(e){let t=z(e,`text`),n={boolean:`checkbox`,bool:`checkbox`,code:`code-editor`,codeeditor:`code-editor`,dropdown:`select`,email:`email`,integer:`number`,multiple:`multiselect`,multi_select:`multiselect`,string:`text`,toggle:`checkbox`,richtext:`wysiwyg`,wysiwyg_html:`wysiwyg`}[t]||t;return[`checkbox`,`code-editor`,`color`,`date`,`datetime-local`,`email`,`hidden`,`month`,`multiselect`,`number`,`password`,`radio`,`range`,`select`,`tel`,`text`,`textarea`,`time`,`url`,`week`,`wysiwyg`].includes(n)?n:`text`}function O(e=[],t=!1){return L(e).map(e=>{let n=R(e),r=z(n.key);return r?{key:r,label:z(n.label,r),kind:z(n.kind,`default`),type:z(n.type??n.dataType,`any`),multiple:n.multiple===void 0?t:!!n.multiple}:null}).filter(Boolean)}function le(e={},t=[]){let n=typeof e==`string`?N(e,I()):R(e),r=new Map(ce(t).map(e=>[e.type,e])),i=[],a=new Set;L(n.nodes).forEach((e,t)=>{let n=R(e),o=z(n.type,r.keys().next().value||`node`),s=r.get(o),c=z(n.id,`${o}-${t+1}`);a.has(c)||(a.add(c),i.push({id:c,type:o,label:z(n.label,s?.label||o),position:{x:B(n.position?.x,t*260),y:B(n.position?.y,0)},data:{...s?.defaults||{},...R(n.data)}}))});let o=new Map(i.map(e=>[e.id,e])),s=[],c=new Set;L(n.edges).forEach((e,t)=>{let n=R(e),i={id:z(n.id,`edge-${t+1}`),source:z(n.source),sourcePort:z(n.sourcePort),target:z(n.target),targetPort:z(n.targetPort),data:R(n.data)};!i.source||!i.target||!o.has(i.source)||!o.has(i.target)||c.has(i.id)||!ue(i,o,r)||(c.add(i.id),s.push(i))});let l=R(n.viewport);return{version:B(n.version,F),nodes:i,edges:s,viewport:{x:B(l.x,0),y:B(l.y,0),zoom:B(l.zoom??l.k,1)}}}function ue(e,t,n){let r=t instanceof Map?t:new Map(L(t).map(e=>[e.id,e])),i=n instanceof Map?n:new Map(ce(n).map(e=>[e.type,e])),a=r.get(e.source),o=r.get(e.target);if(!a||!o||a.id===o.id)return!1;let s=i.get(a.type),c=i.get(o.type);if(!s||!c)return!0;let l=s.outputs.find(t=>t.key===e.sourcePort),u=c.inputs.find(t=>t.key===e.targetPort);return!l||!u?!1:de(l,u)}function de(e,t){return k(e?.kind,t?.kind)&&k(e?.type,t?.type)}function k(e,t){let n=z(e,`any`),r=z(t,`any`);return n===`any`||r===`any`||n===r||n===`int`&&r===`float`}function fe(e=[],t={}){let n={};return L(e).forEach(e=>{let r=t[e.key],i=A(e,r);i.length&&(n[e.key]=i)}),{valid:Object.keys(n).length===0,errors:n}}function A(e,t){let n=[],r=t==null||t===``;if(e.required&&(r||e.type===`checkbox`&&t!==!0)&&n.push(`required`),r)return n;if([`number`,`range`].includes(e.type)){let r=Number(t);if(!Number.isFinite(r))return n.push(`number`),n;e.min!==null&&e.min!==void 0&&r<Number(e.min)&&n.push(`min`),e.max!==null&&e.max!==void 0&&r>Number(e.max)&&n.push(`max`),e.step!==null&&e.step!==void 0&&!me(r,e)&&n.push(`step`)}if([`select`,`radio`].includes(e.type)&&e.options?.length&&(e.options.map(e=>String(e.value)).includes(String(t))||n.push(`option`)),e.type===`email`&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(t))&&n.push(`email`),e.type===`url`&&!pe(t)&&n.push(`url`),e.minLength!==null&&e.minLength!==void 0&&String(t).length<Number(e.minLength)&&n.push(`minLength`),e.maxLength!==null&&e.maxLength!==void 0&&String(t).length>Number(e.maxLength)&&n.push(`maxLength`),e.pattern)try{new RegExp(e.pattern).test(String(t))||n.push(`pattern`)}catch{n.push(`pattern`)}return n}function pe(e){try{return new URL(String(e)),!0}catch{return!1}}function me(e,t){let n=Number(t.step);if(!Number.isFinite(n)||n<=0)return!0;let r=Number(t.min??0),i=(e-(Number.isFinite(r)?r:0))/n;return Math.abs(i-Math.round(i))<2**-52*100}function he(e,t){let n=e?.querySelector?.(`[data-blueprint-sync]`);n&&(n.value=JSON.stringify(t))}function j(e,t,n={}){e?.dispatchEvent?.(new CustomEvent(`daisy:blueprint:${t}`,{bubbles:!0,detail:n}))}function M(e,t,n=6){return!e||e.pointerId!==t.pointerId?!1:Math.hypot(t.clientX-e.x,t.clientY-e.y)<=n}function N(e,t={}){try{return JSON.parse(e)}catch{return t}}function P(e,t,n={}){let r=e?.querySelector?.(t);return r?N(r.content?.textContent||r.textContent||``,n):n}var F,I,L,R,z,B,V=e((()=>{F=1,I=()=>({version:F,nodes:[],edges:[],viewport:{x:0,y:0,zoom:1}}),L=e=>Array.isArray(e)?e:[],R=e=>e&&typeof e==`object`&&!Array.isArray(e)?e:{},z=(e,t=``)=>{if(e==null)return t;let n=String(e).trim();return n===``?t:n},B=(e,t=0)=>{let n=Number(e);return Number.isFinite(n)?n:t}}));function H(e){let t=String(e||`primary`).trim();return ge.includes(t)?t:_e[t]||`primary`}var ge,_e,ve=e((()=>{ge=[`primary`,`secondary`,`accent`,`neutral`,`info`,`success`,`warning`,`error`],_e={action:`success`,condition:`warning`,data:`accent`,default:`primary`,function:`info`,schema:`secondary`,trigger:`primary`}}));function ye(e){let t=new Map;return e.forEach(e=>{[...e.inputs,...e.outputs].forEach(e=>{t.has(e.kind)||t.set(e.kind,new g.Socket(e.kind))})}),t.size||t.set(`default`,new g.Socket(`default`)),{get(e=`default`){return t.has(e)||t.set(e,new g.Socket(e)),t.get(e)}}}function U(e,t,n,r){let i=t.find(t=>t.type===e.type)||t[0],a=new g.Node(e.label||i?.label||e.type),o=be(i,e.data||{}).length,s=(i?.inputs?.length||0)+(i?.outputs?.length||0),c=i?.display||`detailed`;return a.id=e.id,a.width=c===`minimal`?188:248,a.height=c===`minimal`?Math.max(74,52+Math.max(s,1)*18):Math.max(126,54+o*34,88+Math.max(s,1)*28),a.__blueprint={type:e.type,category:i?.category||``,description:i?.description||``,display:c,colorField:i?.colorField||``,icon:i?.icon||``,theme:H(i?.theme),nameStrategy:i?.nameStrategy||{mode:`free`},controls:i?.controls||[],previewFields:be(i,e.data||{}),data:{...e.data||{}}},(i?.inputs||[]).forEach(e=>{a.addInput(e.key,new g.Input(n.get(e.kind),e.label,!!e.multiple))}),(i?.outputs||[]).forEach(e=>{a.addOutput(e.key,new g.Output(n.get(e.kind),e.label,!!e.multiple))}),a}function be(e,t={}){if(e?.previewFields?.length)return e.previewFields;let n=(e?.controls||[]).filter(e=>t[e.key]!==null&&t[e.key]!==void 0&&typeof t[e.key]!=`object`).slice(0,3).map(e=>({key:e.key,label:e.label||e.key}));return n.length?n:Object.entries(t).filter(([,e])=>e!=null&&typeof e!=`object`).slice(0,3).map(([e])=>({key:e,label:e}))}function xe(e,t,n){let r=new g.Connection(t,e.sourcePort,n,e.targetPort);return r.id=e.id,r.data=e.data||{},r}function Se(e,t){return t.find(t=>t.type===e?.__blueprint?.type)||null}function Ce(e,t){return e.nodeViews.get(t?.id)?.position||{x:40,y:40}}function we(e,t=0){let n=e?.nameStrategy||{mode:`free`};return n.mode===`preset`&&n.value?n.value:n.mode===`auto`?`${n.prefix||e?.label||e?.type||`Node`} ${t+1}`:e?.label||e?.type||`Node`}function Te(e,t,n,r){let i=Se(e,r),a=Se(t,r);if(!i||!a)return null;let o=n.getConnections(),s=i.outputs.filter(t=>t.multiple?!0:!o.some(n=>n.source===e.id&&String(n.sourceOutput)===t.key)),c=a.inputs.filter(e=>!o.some(n=>n.target===t.id&&String(n.targetInput)===e.key)),l=s.flatMap(e=>c.filter(t=>de(e,t)).map(t=>({output:e,input:t})));return s.length!==1||c.length!==1||l.length!==1?null:l[0]}var Ee,De,W=e((()=>{a(),V(),ve(),Ee=[{type:`task`,label:`Task`,category:`Workflow`,theme:`success`,inputs:[{key:`in`,label:`In`,kind:`flow`}],outputs:[{key:`out`,label:`Out`,kind:`flow`,multiple:!0}],defaults:{}}],De=e=>typeof e==`number`?`number`:`text`}));function G(e,t){let n=e.querySelector(`[data-blueprint-details-panel]`);n?.classList.toggle(`hidden`,!t),n?.classList.toggle(`modal-open`,t&&n.classList.contains(`modal`)),e.querySelector(`[data-blueprint-details-backdrop]`)?.classList.toggle(`hidden`,!t)}function K(e,t,n,r=!1,i=null,a=null){let o=e.querySelector(`[data-blueprint-properties]`);if(!o)return;if(Ke(o),!t){o.append(X(`p`,{className:`text-sm text-base-content/70`,text:n.noSelection})),G(e,!1);return}let s=ke(t),c=i||t.__blueprint?.data||{},l=X(`div`,{className:`grid gap-3`}),u=X(`div`,{className:`flex items-start justify-between gap-3`}),d=X(`div`,{className:`min-w-0`});d.append(X(`p`,{className:`text-sm font-semibold`,text:t.label}),X(`p`,{className:`text-xs text-base-content/60`,text:t.__blueprint?.type||`node`})),u.append(d,X(`span`,{className:`badge badge-outline max-w-32 truncate`,text:t.__blueprint?.theme||t.id})),l.append(u),t.__blueprint?.description&&l.append(X(`p`,{className:`text-sm text-base-content/70`,text:t.__blueprint.description})),a&&l.append(Ne(a,n));let f=X(`div`,{className:`grid gap-3`});if(s.length?f.append(Pe(s,t.id,c,r)):f.append(X(`p`,{className:`rounded-box border border-dashed border-base-300 bg-base-200/60 p-3 text-sm text-base-content/70`,text:n.noProperties})),l.append(f),!r){let e=X(`div`,{className:`flex flex-wrap items-center justify-between gap-2 pt-1`});e.append(X(`button`,{className:`btn btn-primary btn-sm`,dataset:{blueprintApplyNode:``},text:n.applyNode||`Apply`,type:`button`}),X(`button`,{className:`btn btn-error btn-outline btn-xs`,dataset:{blueprintDeleteNode:``},text:n.deleteNode||`Delete`,type:`button`})),l.append(e)}o.append(l),G(e,!0),Ge(o)}function Oe(e,t){e.querySelectorAll(`[data-blueprint-details-tab]`).forEach(e=>{let n=e.dataset.blueprintDetailsTab===t;e.classList.toggle(`tab-active`,n),e.setAttribute(`aria-selected`,n?`true`:`false`)}),e.querySelectorAll(`[data-blueprint-details-section]`).forEach(e=>{e.classList.toggle(`hidden`,e.dataset.blueprintDetailsSection!==t)})}function ke(e){return e.__blueprint?.controls?.length?e.__blueprint.controls:Object.entries(e.__blueprint?.data||{}).map(([e,t])=>({key:e,label:e,type:De(t)}))}function Ae(e){if(e.classList?.contains(`code-editor`))return e.querySelector(`textarea[data-sync]`)?.value||``;if(e.classList?.contains(`trix-wrapper`))return e.querySelector(`input[type="hidden"]`)?.value||``;if(e.type===`checkbox`)return e.checked;if(e.tagName===`SELECT`&&e.multiple)return[...e.selectedOptions].map(e=>e.value);if(e.type===`number`||e.type===`range`){let t=Number(e.value);return Number.isFinite(t)?t:e.value}return e.value}function je(e,t={}){let n={...t};return e.querySelectorAll(`[data-blueprint-property-input]`).forEach(e=>{n[e.dataset.blueprintPropertyInput]=Ae(e)}),n}function Me(e,t){e.__blueprint.data={...t}}function Ne(e,t){if(e.valid){let e=X(`div`,{className:`alert alert-success py-2 text-sm`,dataset:{blueprintDetailsFeedback:``}});return e.append(X(`span`,{text:t.applySuccess||`Changes applied.`})),e}let n=X(`div`,{className:`alert alert-error grid gap-2 py-2 text-sm`,dataset:{blueprintDetailsFeedback:``}}),r=X(`details`,{className:`collapse collapse-arrow rounded-box bg-error-content/10`}),i=X(`div`,{className:`collapse-content grid gap-2 px-3 pb-3`}),a=X(`textarea`,{className:`textarea textarea-bordered textarea-xs min-h-24 font-mono text-xs`,dataset:{blueprintErrorDetails:``},readonly:!0});return a.value=JSON.stringify(e.errors,null,2),i.append(a,X(`button`,{className:`btn btn-xs justify-self-start`,dataset:{blueprintCopyError:``},text:t.copyError||`Copy details`,type:`button`})),r.append(X(`summary`,{className:`collapse-title min-h-0 px-3 py-2 text-xs font-semibold`,text:t.errorDetails||`Details`}),i),n.append(X(`span`,{text:t.applyError||`Some fields are invalid.`}),r),n}function Pe(e,t,n,r){let i=Fe(e);if(i.length<=1){let e=X(`div`,{className:`grid gap-3`});return i[0].controls.forEach(i=>e.append(Re(i,t,n,r))),e}let a=X(`div`,{className:`grid gap-4`}),o=X(`div`,{className:`tabs tabs-border overflow-x-auto`,role:`tablist`}),s=X(`div`,{className:`grid gap-4`});return i.forEach((e,i)=>{let a=i===0;o.append(X(`button`,{className:`tab ${a?`tab-active`:``}`,dataset:{blueprintDetailsTab:e.key},role:`tab`,type:`button`,text:e.label}));let c=X(`section`,{className:a?`grid gap-3`:`hidden grid gap-3`,dataset:{blueprintDetailsSection:e.key},role:`tabpanel`});e.controls.forEach(e=>c.append(Re(e,t,n,r))),s.append(c)}),a.append(o,s),a}function Fe(e){let t=[],n=new Map;return e.forEach(e=>{let r=Ie(e);n.has(r.key)||(n.set(r.key,{...r,controls:[]}),t.push(n.get(r.key))),n.get(r.key).controls.push(e)}),t}function Ie(e){let t=e.section||e.group||e.tab;if(t){let e=String(t);return{key:Le(e),label:e}}let n=String(e.key||``);return[`eligibility_rules`,`eligibility_rules_description`,`recommendation`].includes(n)||n.includes(`rule`)?{key:`rules`,label:`Règles`}:[`forwardable`,`backwardable`].includes(n)||n.includes(`transition`)?{key:`transitions`,label:`Transitions`}:n.includes(`availability`)||n===`readonly`?{key:`permissions`,label:`Droits`}:n.includes(`action`)||n.includes(`shortcut`)||n.includes(`generate`)?{key:`actions`,label:`Actions`}:{key:`general`,label:`Général`}}function Le(e){return String(e).toLowerCase().normalize(`NFD`).replace(/[\u0300-\u036f]/g,``).replace(/[^a-z0-9]+/g,`-`).replace(/^-|-$/g,``)||`section`}function Re(e,t,n,r){let i=e.key,a=n[i]??e.default??``;if(e.type===`textarea`){let t=X(`textarea`,{className:`textarea textarea-bordered textarea-sm min-h-20 w-full`,dataset:{blueprintPropertyInput:i},disabled:r,placeholder:e.placeholder||``,required:e.required});return t.value=String(a??``),Ue(t,e),J(e,i,t)}if(e.type===`code-editor`)return ze(e,i,a,r);if(e.type===`wysiwyg`)return Be(e,i,a,r);if(e.type===`select`){let t=X(`select`,{className:`select select-bordered select-sm w-full`,dataset:{blueprintPropertyInput:i},disabled:r,required:e.required});return e.options.forEach(e=>{t.append(X(`option`,{text:e.label,value:e.value,selected:String(a)===String(e.value)}))}),J(e,i,t)}if(e.type===`multiselect`){let t=Array.isArray(a)?a.map(e=>String(e)):String(a??``).split(`,`).map(e=>e.trim()).filter(Boolean),n=X(`select`,{className:`select select-bordered select-sm min-h-28 w-full`,dataset:{blueprintPropertyInput:i},disabled:r,multiple:!0,required:e.required,size:Math.min(Math.max(e.options.length,3),Number(e.size||8))});return e.options.forEach(e=>{n.append(X(`option`,{text:e.label,value:e.value,selected:t.includes(String(e.value))}))}),J(e,i,n)}if(e.type===`radio`){let n=X(`fieldset`,{className:`form-control grid w-full gap-2`,dataset:{blueprintProperty:i}});n.append(X(`legend`,{className:`label-text text-xs font-medium text-base-content/70`,text:e.label||i}));let o=X(`div`,{className:`grid gap-1`});return e.options.forEach(n=>{let s=X(`input`,{checked:String(a)===String(n.value),className:`radio radio-sm`,dataset:{blueprintPropertyInput:i},disabled:r,name:`${t}-${i}`,required:e.required,type:`radio`,value:n.value});o.append(X(`label`,{className:`flex items-center gap-2 text-sm`},[s,X(`span`,{text:n.label})]))}),n.append(o),He(n,e),n}if(e.type===`checkbox`){let t=X(`input`,{checked:!!a,className:`checkbox checkbox-sm mt-0.5`,dataset:{blueprintPropertyInput:i},disabled:r,required:e.required,type:`checkbox`}),n=X(`span`,{className:`grid gap-0.5`});return n.append(Ve(e,i)),He(n,e),X(`label`,{className:`flex items-start gap-2`,dataset:{blueprintProperty:i}},[t,n])}let o=e.type===`range`?`range`:[`color`,`date`,`datetime-local`,`email`,`hidden`,`month`,`number`,`password`,`tel`,`text`,`time`,`url`,`week`].includes(e.type)?e.type:`text`,s=X(`input`,{className:o===`range`?`range range-sm`:`input input-sm input-bordered w-full`,dataset:{blueprintPropertyInput:i},disabled:r,placeholder:e.placeholder||``,required:e.required,type:o,value:String(a??``)});return We(s,e),Ue(s,e),J(e,i,s)}function ze(e,t,n,r){let i=`blueprint-code-${t}-${Math.random().toString(36).slice(2)}`,a=X(`div`,{className:`code-editor bg-base-100 card-border rounded-box overflow-hidden`,dataset:{blueprintPropertyInput:t,module:`code-editor`,language:e.language||`javascript`,readonly:r?`true`:`false`,tabSize:2}}),o=X(`div`,{className:`flex items-center justify-between gap-2 border-b bg-base-200 px-2 py-1`}),s=X(`div`,{className:`flex items-center gap-1`});o.append(X(`div`,{className:`text-xs opacity-70`,text:String(e.language||`javascript`).toUpperCase()}),s),s.append(X(`button`,{className:`btn btn-xs`,dataset:{action:`fold-all`},text:`Tout plier`,type:`button`}),X(`button`,{className:`btn btn-xs`,dataset:{action:`unfold-all`},text:`Tout déplier`,type:`button`}),X(`button`,{className:`btn btn-xs`,dataset:{action:`format`},text:`Formater`,type:`button`}),X(`button`,{className:`btn btn-xs`,dataset:{action:`copy`},text:`Copier`,type:`button`}));let c=X(`div`,{className:`cm-host daisy-code-editor-height-px-180`});return e.height&&(c.style.height=e.height),a.append(o,c,X(`textarea`,{className:`hidden`,dataset:{sync:``}}),q(`options`,{}),q(`initial`,{value:String(n??``)}),q(`i18n`,{})),a.id=i,a.querySelector(`textarea[data-sync]`).value=String(n??``),J(e,t,a)}function Be(e,t,n,r){let i=`blueprint-trix-${t}-${Math.random().toString(36).slice(2)}`,a=X(`div`,{className:`trix-wrapper daisy-blueprint-wysiwyg`,dataset:{blueprintPropertyInput:t,module:`lazy-editors`,trixAttachments:`0`}}),o=X(`div`,{dataset:{trixContainer:``}}),s=X(`trix-toolbar`),c=X(`input`,{type:`hidden`,value:String(n??``)}),l=X(`trix-editor`,{className:`trix-content daisy-wysiwyg-min-height-rem-24`,disabled:r,placeholder:e.placeholder||``});return e.height&&(l.style.minHeight=e.height),s.id=`${i}-toolbar`,c.id=`${i}-input`,l.setAttribute(`input`,c.id),l.setAttribute(`toolbar`,s.id),o.append(s,c,l),a.append(o),J(e,t,a)}function q(e,t){let n=document.createElement(`script`);return n.type=`application/json`,n.dataset[e]=``,n.textContent=JSON.stringify(t),n}function J(e,t,n){let r=X(`label`,{className:`form-control grid w-full gap-1`,dataset:{blueprintProperty:t}});return r.append(Ve(e,t),n),He(r,e),r}function Ve(e,t){return X(`span`,{className:`label-text text-xs font-medium text-base-content/70`,text:e.label||t})}function He(e,t){t.help&&e.append(X(`span`,{className:`text-xs text-base-content/50`,text:t.help}))}function Ue(e,t){Y(e,`pattern`,t.pattern),Y(e,`minlength`,t.minLength),Y(e,`maxlength`,t.maxLength)}function We(e,t){Y(e,`min`,t.min),Y(e,`max`,t.max),Y(e,`step`,t.step)}function Y(e,t,n){n==null||n===``||e.setAttribute(t,String(n))}function Ge(e){e.querySelector(`.code-editor, .trix-wrapper`)&&(window.requestAnimationFrame||(e=>window.setTimeout(e,0)))(()=>{r(()=>import(`./lazy-editors-BLRR2-PY.js`).then(t=>t.initEditorsIn?.(e)),__vite__mapDeps([0,1,2,3,4])).catch(()=>{})})}function X(e,t={},n=[]){let r=document.createElement(e);return Object.entries(t.dataset||{}).forEach(([e,t])=>{r.dataset[e]=t}),t.className&&(r.className=t.className),t.text!==void 0&&(r.textContent=t.text),[`checked`,`disabled`,`required`,`selected`].forEach(e=>{t[e]&&(r[e]=!0)}),t.readonly&&(r.readOnly=!0,r.setAttribute(`readonly`,``)),[`id`,`name`,`placeholder`,`type`,`value`].forEach(e=>{t[e]!==void 0&&r.setAttribute(e,String(t[e]))}),n.forEach(e=>r.append(e)),r}function Ke(e){if(typeof e.replaceChildren==`function`){e.replaceChildren();return}for(;e.firstChild;)e.removeChild(e.firstChild)}var qe=e((()=>{W(),n()}));function Je(e,t,n){if(!e||e.dataset.blueprintClickBound===`true`)return!1;let r=null;return e.dataset.blueprintClickBound=`true`,e.addEventListener(`pointerdown`,e=>{if(e.button!==0||Ye(e)){r=null;return}r={pointerId:e.pointerId,x:e.clientX,y:e.clientY}}),e.addEventListener(`pointerup`,e=>{if(Ye(e)){r=null;return}M(r,e)&&n(t),r=null}),e.addEventListener(`pointercancel`,()=>{r=null}),!0}function Ye(e){return(e.composedPath?.()||[]).some(e=>e instanceof Element&&!!e.closest?.([`button`,`input`,`select`,`textarea`,`[contenteditable="true"]`,`rete-socket`,`rete-ref`,`.input-socket`,`.output-socket`].join(`,`)))}var Xe=e((()=>{V()}));function Ze(e,t,n,r){return()=>U({id:`${e.type}-${Date.now()}`,type:e.type,label:e.label,data:e.defaults},t,n,r)}function Qe(e,t,n){let r=[],i=e.reduce((i,a)=>{i.has(a.category)||i.set(a.category,[]);let o=Ze(a,e,t,n);return r.push(o),i.get(a.category).push([a.label,o]),i},new Map);return{nodeFactories:r,typeGroups:i,contextMenuItems:Array.from(i.entries())}}var $e=e((()=>{W()}));function Z(e,t,n){let r=t.nodeViews.get(n.id)?.element;if(!r)return;let i=H(n.__blueprint?.theme);r.dataset.blueprintNodeType=n.__blueprint?.type||`node`,r.dataset.blueprintDisplay=n.__blueprint?.display||`detailed`,r.dataset.blueprintIcon=n.__blueprint?.icon||``,r.dataset.blueprintLabel=n.label||``,r.dataset.blueprintTheme=i,tt(e,r,i,n),window.requestAnimationFrame?.(()=>tt(e,r,i,n)),window.setTimeout(()=>tt(e,r,i,n),50)}function et(e,t,n){n.getNodes().forEach(n=>Z(e,t,n))}function tt(e,t,n,r){t.querySelectorAll(`rete-node`).forEach(e=>{let i=ct(r);e.dataset.blueprintTheme=n,e.dataset.blueprintDisplay=t.dataset.blueprintDisplay||`detailed`,e.dataset.blueprintIcon=t.dataset.blueprintIcon||``,e.dataset.blueprintLabel=t.dataset.blueprintLabel||``,lt(e,i),nt(e,r)})}function nt(e,t){let n=e.shadowRoot;if(!n)return;ft(n,`node`,bt),rt(n);let r=n.querySelector(`.title`);r&&at(r,e),ot(n,t),n.querySelectorAll(`.input-socket rete-ref, .output-socket rete-ref`).forEach(t=>{dt(t,e.dataset.blueprintTheme,e.style.getPropertyValue(`--daisy-blueprint-node-theme`))})}function rt(e){it(e.querySelectorAll(`.input`)),it(e.querySelectorAll(`.output`))}function it(e){let t=e.length;e.forEach((e,n)=>{e.dataset.blueprintPortIndex=String(n),e.dataset.blueprintPortCount=String(Math.min(t,3))})}function at(e,t){let n=t.closest(`[data-blueprint-display]`),r=n?.dataset.blueprintIcon||t.dataset.blueprintIcon||``,i=n?.dataset.blueprintLabel||t.dataset.blueprintLabel||``;if(e.dataset.blueprintTitleDecorated!==`true`){e.dataset.blueprintTitleDecorated=`true`,e.textContent=``;let t=document.createElement(`span`);t.dataset.blueprintTitleLabel=`true`,t.textContent=i,e.append(t)}let a=e.querySelector(`[data-blueprint-title-label]`);if(a&&(a.textContent=i),e.querySelector(`[data-blueprint-title-icon]`)?.remove(),!r)return;let o=document.createElement(`span`);o.dataset.blueprintTitleIcon=`true`,o.textContent=r.slice(0,3),e.append(o)}function ot(e,t){if(e.querySelector(`.daisy-blueprint-preview`)?.remove(),!t||t.__blueprint?.display===`minimal`)return;let n=t.__blueprint?.previewFields||[];if(!n.length)return;let r=document.createElement(`div`);r.className=`daisy-blueprint-preview`,r.dataset.blueprintNodePreview=`true`,n.forEach(e=>{let n=t.__blueprint?.data?.[e.key];if(n==null||typeof n==`object`)return;let i=document.createElement(`div`),a=document.createElement(`span`),o=document.createElement(`span`);i.className=`daisy-blueprint-preview-row`,a.className=`daisy-blueprint-preview-label`,o.className=`daisy-blueprint-preview-value`,a.textContent=e.label||e.key,o.textContent=st(n,t,e),i.append(a,o),r.append(i)}),r.childElementCount&&e.querySelector(`.title`)?.after(r)}function st(e,t=null,n=null){let r=(t?.__blueprint?.controls?.find(e=>e.key===n?.key))?.options?.find(t=>String(t.value)===String(e));return r?.label?String(r.label):typeof e==`boolean`?e?`true`:`false`:String(e)}function ct(e){let t=e?.__blueprint?.colorField;if(!t)return``;let n=e?.__blueprint?.data?.[t];return(e?.__blueprint?.controls?.find(e=>e.key===t))?.options?.find(e=>String(e.value)===String(n))?.color||``}function lt(e,t){if(!t){e.style.removeProperty(`--daisy-blueprint-node-theme`),e.style.removeProperty(`--daisy-blueprint-node-theme-content`);return}e.style.setProperty(`--daisy-blueprint-node-theme`,t),e.style.setProperty(`--daisy-blueprint-node-theme-content`,ut(t))}function ut(e){let t=e.startsWith(`#`)?e.slice(1):e,n=t.length===3?t.split(``).map(e=>`${e}${e}`).join(``):t,r=Number.parseInt(n.slice(0,2),16),i=Number.parseInt(n.slice(2,4),16),a=Number.parseInt(n.slice(4,6),16);return(r*299+i*587+a*114)/1e3<128?`#ffffff`:`#030f40`}function dt(e,t,n=``){let r=e.querySelector?.(`rete-socket`),i=r?.shadowRoot;!r||!i||(r.dataset.blueprintTheme=t,n?r.style.setProperty(`--daisy-blueprint-node-theme`,n):r.style.removeProperty(`--daisy-blueprint-node-theme`),ft(i,`socket`,xt))}function ft(e,t,n){if(e.__daisyBlueprintStyleKeys?.has(t))return;let r=pt(t,n);r&&(e.adoptedStyleSheets=[...e.adoptedStyleSheets,r],e.__daisyBlueprintStyleKeys=e.__daisyBlueprintStyleKeys||new Set,e.__daisyBlueprintStyleKeys.add(t))}function pt(e,t){if(typeof CSSStyleSheet>`u`||typeof ShadowRoot>`u`||!(`adoptedStyleSheets`in ShadowRoot.prototype))return null;if(!Q.has(e)){let n=new CSSStyleSheet;n.replaceSync(t),Q.set(e,n)}return Q.get(e)}function mt(e,t){let n=[],r=e=>{e.querySelectorAll?.(t).forEach(e=>n.push(e)),e.querySelectorAll?.(`*`).forEach(e=>{e.shadowRoot&&r(e.shadowRoot)})};return r(e),n}function ht(e,t){let n=e?.data?.theme;return H(n||t.getNode(e?.source)?.__blueprint?.theme)}function gt(e,t){mt(e,`rete-connection`).forEach(e=>{e.dataset.blueprintConnectionTheme=t})}function _t(e,t,n,r){let i=t.connectionViews.get(r?.id)?.element;if(!i)return;let a=ht(r,n);i.dataset.blueprintConnectionTheme=a,gt(i,a),window.requestAnimationFrame?.(()=>gt(i,a)),window.setTimeout(()=>gt(i,a),50)}function vt(e,t,n){n.getConnections().forEach(r=>_t(e,t,n,r))}var yt,bt,xt,Q,St=e((()=>{ve(),yt=[`primary`,`secondary`,`accent`,`neutral`,`info`,`success`,`warning`,`error`].map(e=>`
  :host([data-blueprint-theme="${e}"]) {
    --daisy-blueprint-node-theme: var(--color-${e});
    --daisy-blueprint-node-theme-content: var(--color-${e}-content);
  }
`).join(``),bt=`
  ${yt}

  :host {
    --socket-size: 18px !important;
    --socket-margin: 0px !important;
    background: var(--color-base-100) !important;
    border-color: color-mix(in oklch, var(--daisy-blueprint-node-theme) 42%, var(--color-base-300)) !important;
    border-radius: 8px !important;
    box-shadow: 0 8px 18px color-mix(in oklch, var(--color-base-content) 8%, transparent) !important;
    box-sizing: border-box !important;
    overflow: visible !important;
  }

  .title {
    align-items: center !important;
    background: var(--daisy-blueprint-node-theme) !important;
    border-bottom: 1px solid color-mix(in oklch, var(--daisy-blueprint-node-theme) 42%, var(--color-base-300)) !important;
    border-radius: 6px 6px 0 0 !important;
    color: var(--daisy-blueprint-node-theme-content) !important;
    display: flex !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    gap: 8px !important;
    justify-content: space-between !important;
    line-height: 1.2 !important;
    min-height: 42px !important;
    padding: 8px 12px !important;
    text-shadow: none !important;
  }

  :host([data-blueprint-display="minimal"]) {
    width: 188px !important;
  }

  :host([data-blueprint-display="minimal"]) .title {
    border-radius: 6px !important;
    min-height: 46px !important;
    padding: 10px 10px 10px 12px !important;
  }

  [data-blueprint-title-label] {
    min-width: 0 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  [data-blueprint-title-icon] {
    align-items: center !important;
    background: color-mix(in oklch, var(--daisy-blueprint-node-theme-content) 18%, transparent) !important;
    border: 1px solid color-mix(in oklch, var(--daisy-blueprint-node-theme-content) 28%, transparent) !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    flex: 0 0 26px !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    height: 26px !important;
    justify-content: center !important;
    line-height: 1 !important;
    width: 26px !important;
  }

  .input,
  .output {
    align-items: center !important;
    background: transparent !important;
    border: 0 !important;
    box-sizing: border-box !important;
    display: flex !important;
    gap: 6px !important;
    height: 24px !important;
    min-height: 24px !important;
    padding: 0 !important;
    position: absolute !important;
    top: calc(50% - 12px) !important;
    width: 22px !important;
    z-index: 3 !important;
  }

  .input {
    left: -11px !important;
    justify-content: flex-start !important;
    text-align: left !important;
  }

  .output {
    right: -11px !important;
    justify-content: flex-end !important;
    text-align: right !important;
  }

  .input::after,
  .output::before {
    background: var(--daisy-blueprint-node-theme) !important;
    border-radius: 999px !important;
    content: "" !important;
    height: 3px !important;
    opacity: 0.82 !important;
    position: absolute !important;
    top: calc(50% - 1.5px) !important;
    width: 9px !important;
    z-index: 0 !important;
  }

  .input::after {
    left: 8px !important;
  }

  .output::before {
    right: 8px !important;
  }

  .input[data-blueprint-port-count="2"][data-blueprint-port-index="0"],
  .output[data-blueprint-port-count="2"][data-blueprint-port-index="0"] {
    top: calc(50% - 25px) !important;
  }

  .input[data-blueprint-port-count="2"][data-blueprint-port-index="1"],
  .output[data-blueprint-port-count="2"][data-blueprint-port-index="1"] {
    top: calc(50% + 1px) !important;
  }

  .input[data-blueprint-port-count="3"][data-blueprint-port-index="0"],
  .output[data-blueprint-port-count="3"][data-blueprint-port-index="0"] {
    top: calc(50% - 38px) !important;
  }

  .input[data-blueprint-port-count="3"][data-blueprint-port-index="1"],
  .output[data-blueprint-port-count="3"][data-blueprint-port-index="1"] {
    top: calc(50% - 12px) !important;
  }

  .input[data-blueprint-port-count="3"][data-blueprint-port-index="2"],
  .output[data-blueprint-port-count="3"][data-blueprint-port-index="2"] {
    top: calc(50% + 14px) !important;
  }

  :host([data-blueprint-display="minimal"]) .input,
  :host([data-blueprint-display="minimal"]) .output {
    top: calc(50% - 12px) !important;
  }

  .input-socket,
  .output-socket {
    display: inline-flex !important;
    flex: 0 0 14px !important;
    height: 14px !important;
    margin: 0 !important;
    position: relative !important;
    transform: none !important;
    width: 14px !important;
    z-index: 1 !important;
  }

  .input-title,
  .output-title {
    display: none !important;
  }

  :host([data-blueprint-display="minimal"]) .input-title,
  :host([data-blueprint-display="minimal"]) .output-title {
    font-size: 10px !important;
    opacity: 0.65 !important;
  }

  .control,
  .input-control {
    display: none !important;
  }

  .daisy-blueprint-preview:empty {
    display: none !important;
  }

  .daisy-blueprint-preview {
    background: var(--color-base-100) !important;
    border-radius: 0 0 7px 7px !important;
    border-top: 1px solid color-mix(in oklch, var(--color-base-300) 78%, transparent) !important;
    display: grid !important;
    gap: 0 !important;
    padding: 7px 0 !important;
  }

  .daisy-blueprint-preview-row {
    align-items: center !important;
    display: grid !important;
    gap: 10px !important;
    grid-template-columns: minmax(4.5rem, 0.72fr) minmax(0, 1fr) !important;
    min-height: 30px !important;
    padding: 4px 22px !important;
  }

  .daisy-blueprint-preview-row:nth-child(even) {
    background: color-mix(in oklch, var(--color-base-200) 54%, transparent) !important;
  }

  .daisy-blueprint-preview-label {
    color: var(--color-base-content) !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    opacity: 0.55 !important;
    overflow: hidden !important;
    text-transform: uppercase !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  .daisy-blueprint-preview-value {
    color: var(--color-base-content) !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    overflow: hidden !important;
    text-align: right !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  :host([data-blueprint-display="minimal"]) .daisy-blueprint-preview {
    display: none !important;
  }
`,xt=`
  ${yt}

  :host {
    --socket-size: 14px !important;
    --socket-margin: 0px !important;
    --socket-color: var(--daisy-blueprint-node-theme) !important;
    display: block !important;
    height: 14px !important;
    width: 14px !important;
  }

  .hoverable {
    border-radius: 999px !important;
    box-sizing: border-box !important;
    display: block !important;
    height: 14px !important;
    padding: 0 !important;
    width: 14px !important;
  }

  .styles {
    box-sizing: border-box !important;
    height: 14px !important;
    width: 14px !important;
  }
`,Q=new Map}));function Ct(e,t,n){return{version:1,nodes:e.getNodes().map((e,n)=>{let r=t.nodeViews.get(e.id);return{id:e.id,type:e.__blueprint?.type||`node`,label:e.label,position:{x:r?.position?.x??n*260,y:r?.position?.y??0},data:{...e.__blueprint?.data||{}}}}),edges:e.getConnections().map(e=>({id:e.id,source:e.source,sourcePort:String(e.sourceOutput),target:e.target,targetPort:String(e.targetInput),data:{...e.data||{}}})),viewport:{x:t.area?.transform?.x??n.x,y:t.area?.transform?.y??n.y,zoom:t.area?.transform?.k??n.zoom}}}var wt=e((()=>{}));async function Tt(e){if(e.__daisyBlueprint)return e.__daisyBlueprint;let t=e.querySelector(`[data-blueprint-canvas]`),n=ce(P(e,`[data-blueprint-node-types]`,Ee)),a=le(P(e,`[data-blueprint-value]`,{}),n),o=Et(e),u=$(e,`details`),d=$(e,`autoLink`),p=P(e,`[data-blueprint-i18n]`,{}),m=ye(n),h=new i,g=new oe(t),_=new ne,y=new se,b=$(e,`history`)?new ae({timing:200}):null,x=null,S=new te,C=$(e,`minimap`)?new c({boundViewport:!0}):null,w=$(e,`reroute`)?new ie:null,T=null,E=null,D={},O=!1;if(h.use(S.root),h.use(g),g.use(S.area),_.use(S.connection),g.use(_),g.use(y),_.addPreset(ee.classic.setup()),y.addPreset(v.classic.setup()),C&&(g.use(C),y.addPreset(v.minimap.setup())),w&&(_.use(w),y.addPreset(v.reroute.setup())),b&&(g.use(b),b.addPreset(l.classic.setup())),$(e,`autoArrange`)){let{AutoArrangePlugin:e,Presets:t}=await r(async()=>{let{AutoArrangePlugin:e,Presets:t}=await import(`./blueprint-layout-DzSZUrPQ.js`).then(e=>(e.t(),e.n));return{AutoArrangePlugin:e,Presets:t}},__vite__mapDeps([5,1,3,4,6,7]));x=new e,g.use(x),x.addPreset(t.classic.setup())}let{nodeFactories:de,contextMenuItems:k}=Qe(n,m,o);if(!o){let e=new re({items:f.classic.setup(k)});g.use(e),y.addPreset(v.contextMenu.setup())}if(!o&&$(e,`dock`,!1)){let{DockPlugin:e,DockPresets:t}=await r(async()=>{let{DockPlugin:e,DockPresets:t}=await import(`./blueprint-engine-By9Uz-pu.js`).then(e=>(e.g(),e._));return{DockPlugin:e,DockPresets:t}},__vite__mapDeps([6,1,3,4,7])),n=new e;g.use(n),n.addPreset(t.classic.setup({area:g,size:120,scale:.65})),de.forEach((e,t)=>n.add(e,t))}h.addPipe(t=>{if(t.type===`connectioncreate`){let r=Ct(h,g,a.viewport),i={source:t.data.source,sourcePort:String(t.data.sourceOutput),target:t.data.target,targetPort:String(t.data.targetInput)};if(!ue(i,r.nodes,n)){j(e,`error`,{message:p.invalidConnection||`Invalid connection`,edge:i});return}}return[`nodecreated`,`noderemoved`,`connectioncreated`,`connectionremoved`].includes(t.type)&&F(),t});function A(){E=null,D={},G(e,!1)}function pe(t){!u||!t||(E=t,D={...t.__blueprint?.data||{}},K(e,t,p,o,D))}function me(e){if(!(!e||O)&&(T=e,u)){if(E?.id===e.id){A();return}pe(e)}}g.addPipe(t=>{if(t.type===`nodepicked`&&(T=h.getNode(t.data.id),j(e,`select`,{node:T})),t.type===`rendered`&&t.data?.type===`node`){let n=h.getNode(t.data.payload.id);n&&(Z(e,g,n),Je(g.nodeViews.get(n.id)?.element,n,me))}return t.type===`rendered`&&t.data?.type===`connection`&&_t(e,g,h,t.data.payload),[`nodetranslated`,`zoomed`,`translated`].includes(t.type)&&F(),t});for(let t of a.nodes){let r=U(t,n,m,o);await h.addNode(r),await g.translate(r.id,t.position),Z(e,g,r)}for(let e of a.edges){let t=h.getNode(e.source),n=h.getNode(e.target);t&&n&&await h.addConnection(xe(e,t,n))}s.simpleNodesOrder(g),a.nodes.length&&$(e,`fitOnInit`)&&await s.zoomAt(g,h.getNodes()),o&&S.enable(),et(e,g,h),vt(e,g,h);function M(){return le(Ct(h,g,a.viewport),n)}function N(){let t=M();he(e,t),j(e,`change`,{graph:t})}function F(){(window.requestAnimationFrame||(e=>window.setTimeout(e,0)))(N)}async function I(t){if(o)return null;O=!0,A();let r=n.find(e=>e.type===t)||n[0],i=T?Ce(g,T):{x:40,y:40},a=T?{x:i.x+300,y:i.y}:{x:40,y:40},s=h.getNodes().filter(e=>e.__blueprint?.type===r.type).length,c=U({id:`${r.type}-${Date.now()}`,type:r.type,label:we(r,s),data:r.defaults,position:a},n,m,o),l=T;if(await h.addNode(c),await g.translate(c.id,a),Z(e,g,c),d&&l){let t=Te(l,c,h,n);t&&(await h.addConnection(xe({id:`edge-${Date.now()}`,sourcePort:t.output.key,targetPort:t.input.key,data:{}},l,c)),vt(e,g,h))}return T=c,N(),F(),window.setTimeout(()=>{O=!1,N()},0),c}async function L(e=T){if(o||!e)return!1;let t=h.getConnections().filter(t=>t.source===e.id||t.target===e.id);for(let e of t)await h.removeConnection(e.id);return await h.removeNode(e.id),T=null,E?.id===e.id&&A(),F(),!0}async function R(){let t=E||T;if(o||!t)return!1;let n=ke(t);D=je(e,D);let r=fe(n,D);return r.valid?(Me(t,D),await g.update(`node`,t.id),Z(e,g,t),K(e,t,p,o,D,r),F(),!0):(K(e,t,p,o,D,r),j(e,`error`,{message:p.applyError||`Some fields are invalid.`,node:t,errors:r.errors}),!1)}async function z(){if(e.classList.contains(`daisy-blueprint-fullscreen-fallback`)){e.classList.remove(`daisy-blueprint-fullscreen`,`daisy-blueprint-fullscreen-fallback`);return}if(document.fullscreenElement===e){await document.exitFullscreen?.(),e.classList.remove(`daisy-blueprint-fullscreen`);return}e.classList.add(`daisy-blueprint-fullscreen`);try{await e.requestFullscreen?.()}catch{e.classList.add(`daisy-blueprint-fullscreen-fallback`)}}let B={editor:h,area:g,history:b,arrange:x,getGraph:M,addNode:I,removeNode:L,async undo(){o||(await b?.undo(),F())},async redo(){o||(await b?.redo(),F())},async arrange(){await x?.layout(),F()},async fit(){let e=h.getNodes();e.length&&await s.zoomAt(g,e)},async fullscreen(){await z()},destroy(){g.destroy()}};e.querySelectorAll(`[data-blueprint-add-node]`).forEach(e=>{e.addEventListener(`pointerdown`,()=>{O=!0,A()})}),e.querySelectorAll(`[data-blueprint-palette-menu]`).forEach(e=>{e.addEventListener(`pointerdown`,()=>{O=!0,A(),window.setTimeout(()=>{O=!1},0)})}),e.querySelectorAll(`[data-blueprint-action]`).forEach(e=>{e.addEventListener(`click`,()=>{if(o&&![`fit`,`arrange`,`fullscreen`].includes(e.dataset.blueprintAction))return;let t=e.dataset.blueprintAction;t===`undo`&&B.undo(),t===`redo`&&B.redo(),t===`arrange`&&B.arrange(),t===`fit`&&B.fit(),t===`fullscreen`&&B.fullscreen()})}),e.addEventListener(`click`,t=>{let n=t.target.closest?.(`[data-blueprint-add-node]`);if(n&&e.contains(n)){I(n.dataset.blueprintAddNode).finally(()=>{window.setTimeout(()=>{O=!1},0)});return}let r=t.target.closest?.(`[data-blueprint-details-tab]`);if(r&&e.contains(r)){Oe(e,r.dataset.blueprintDetailsTab);return}if(t.target.closest?.(`[data-blueprint-details-close], [data-blueprint-details-backdrop]`)){A();return}if(t.target.closest?.(`[data-blueprint-apply-node]`)){R();return}if(t.target.closest?.(`[data-blueprint-copy-error]`)){let t=e.querySelector(`[data-blueprint-error-details]`)?.value||``;navigator.clipboard?.writeText(t);return}t.target.closest?.(`[data-blueprint-delete-node]`)&&L()});let V=e=>{let t=e.target.closest?.(`[data-blueprint-property-input]`);!t||!E||o||(D[t.dataset.blueprintPropertyInput]=Ae(t))};return e.addEventListener(`input`,V),e.addEventListener(`change`,V),e.addEventListener(`code:change`,V),e.addEventListener(`trix-change`,V),document.addEventListener(`fullscreenchange`,()=>{document.fullscreenElement!==e&&e.classList.remove(`daisy-blueprint-fullscreen`,`daisy-blueprint-fullscreen-fallback`)}),u&&K(e,null,p,o),he(e,M()),j(e,`init`,{graph:M(),readonly:o}),e.__daisyBlueprint=B,B}var Et,$,Dt=e((()=>{a(),_(),d(),y(),u(),p(),h(),o(),m(),V(),qe(),Xe(),W(),$e(),St(),wt(),n(),Et=e=>e.dataset.readonly===`true`||e.dataset.mode===`view`,$=(e,t,n=!0)=>{let r=e.dataset[t];return r===void 0||r===``?n:r===`true`}})),Ot=t({default:()=>Tt}),kt=e((()=>{Dt()}));export{kt as n,Ot as t};