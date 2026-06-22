const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/blueprint-layout-BOog3gkz.js","assets/rolldown-runtime-CMxvf4Kt.js","assets/vendor-vcnHIpS1.js","assets/trix-DxQbyuLj.js","assets/blueprint-engine-Dv6sC_C3.js","assets/blueprint-render-BU1sXyxw.js"])))=>i.map(i=>d[i]);
import{n as e,r as t}from"./rolldown-runtime-CMxvf4Kt.js";import{i as n,r}from"./app-BTgXGENF.js";import{S as i,T as a,a as o,b as s,c,d as l,f as u,h as d,i as f,l as p,m as ee,n as m,o as te,p as ne,r as re,s as h,t as ie,u as ae,v as oe,w as g,x as _}from"./blueprint-engine-Dv6sC_C3.js";import{n as v,r as y,t as se}from"./blueprint-render-BU1sXyxw.js";function b(e=[]){return I(e).map(e=>{let t=L(e),n=R(t.type),r=w(t.controls??t.fields);return n?{type:n,label:R(t.label,n),category:R(t.category,`General`),description:R(t.description),theme:R(t.theme,`default`),display:S(t.display??t.variant),icon:R(t.icon??t.brandIcon),nameStrategy:C(t.nameStrategy??t.naming),previewFields:x(t.previewFields??t.displayFields??t.visibleFields??t.preview),inputs:O(t.inputs,!1),outputs:O(t.outputs,!0),controls:r,defaults:{...T(r),...L(t.defaults)}}:null}).filter(Boolean)}function x(e=[]){return I(e).map(e=>{if(typeof e==`string`)return{key:R(e),label:``};let t=L(e),n=R(t.key??t.name??t.id);return n?{key:n,label:R(t.label)}:null}).filter(Boolean)}function S(e){let t=R(e,`detailed`);return[`minimal`,`detailed`].includes(t)?t:`detailed`}function C(e={}){if(typeof e==`string`)return{mode:R(e,`free`)};let t=L(e),n=R(t.mode,`free`);return{mode:[`free`,`preset`,`auto`].includes(n)?n:`free`,prefix:R(t.prefix),value:R(t.value)}}function w(e=[]){return I(e).map(e=>{let t=L(e),n=R(t.key??t.name??t.id);if(!n)return null;let r=R(t.type,`text`);return{key:n,name:R(t.name,n),label:R(t.label,n),type:D(r),placeholder:R(t.placeholder),help:R(t.help??t.description),required:!!t.required,pattern:R(t.pattern),minLength:t.minLength??t.minlength??null,maxLength:t.maxLength??t.maxlength??null,min:t.min??null,max:t.max??null,step:t.step??null,options:E(t.options),default:t.default??t.value??null}}).filter(Boolean)}function T(e=[]){return I(e).reduce((e,t)=>(t.default!==null&&t.default!==void 0&&(e[t.key]=t.default),e),{})}function E(e=[]){return I(e).map(e=>{if(typeof e==`string`||typeof e==`number`||typeof e==`boolean`)return{value:String(e),label:String(e)};let t=L(e),n=t.value??t.id??t.key;return n==null?null:{value:String(n),label:R(t.label,String(n))}}).filter(Boolean)}function D(e){let t=R(e,`text`),n={boolean:`checkbox`,bool:`checkbox`,dropdown:`select`,email:`email`,integer:`number`,string:`text`,toggle:`checkbox`}[t]||t;return[`checkbox`,`color`,`date`,`datetime-local`,`email`,`hidden`,`month`,`number`,`password`,`radio`,`range`,`select`,`tel`,`text`,`textarea`,`time`,`url`,`week`].includes(n)?n:`text`}function O(e=[],t=!1){return I(e).map(e=>{let n=L(e),r=R(n.key);return r?{key:r,label:R(n.label,r),kind:R(n.kind,`default`),type:R(n.type??n.dataType,`any`),multiple:n.multiple===void 0?t:!!n.multiple}:null}).filter(Boolean)}function ce(e={},t=[]){let n=typeof e==`string`?M(e,F()):L(e),r=new Map(b(t).map(e=>[e.type,e])),i=[],a=new Set;I(n.nodes).forEach((e,t)=>{let n=L(e),o=R(n.type,r.keys().next().value||`node`),s=r.get(o),c=R(n.id,`${o}-${t+1}`);a.has(c)||(a.add(c),i.push({id:c,type:o,label:R(n.label,s?.label||o),position:{x:z(n.position?.x,t*260),y:z(n.position?.y,0)},data:{...s?.defaults||{},...L(n.data)}}))});let o=new Map(i.map(e=>[e.id,e])),s=[],c=new Set;I(n.edges).forEach((e,t)=>{let n=L(e),i={id:R(n.id,`edge-${t+1}`),source:R(n.source),sourcePort:R(n.sourcePort),target:R(n.target),targetPort:R(n.targetPort),data:L(n.data)};!i.source||!i.target||!o.has(i.source)||!o.has(i.target)||c.has(i.id)||!le(i,o,r)||(c.add(i.id),s.push(i))});let l=L(n.viewport);return{version:z(n.version,P),nodes:i,edges:s,viewport:{x:z(l.x,0),y:z(l.y,0),zoom:z(l.zoom??l.k,1)}}}function le(e,t,n){let r=t instanceof Map?t:new Map(I(t).map(e=>[e.id,e])),i=n instanceof Map?n:new Map(b(n).map(e=>[e.type,e])),a=r.get(e.source),o=r.get(e.target);if(!a||!o||a.id===o.id)return!1;let s=i.get(a.type),c=i.get(o.type);if(!s||!c)return!0;let l=s.outputs.find(t=>t.key===e.sourcePort),u=c.inputs.find(t=>t.key===e.targetPort);return!l||!u?!1:k(l,u)}function k(e,t){return ue(e?.kind,t?.kind)&&ue(e?.type,t?.type)}function ue(e,t){let n=R(e,`any`),r=R(t,`any`);return n===`any`||r===`any`||n===r?!0:n===`int`&&r===`float`}function de(e=[],t={}){let n={};return I(e).forEach(e=>{let r=t[e.key],i=fe(e,r);i.length&&(n[e.key]=i)}),{valid:Object.keys(n).length===0,errors:n}}function fe(e,t){let n=[],r=t==null||t===``;if(e.required&&(r||e.type===`checkbox`&&t!==!0)&&n.push(`required`),r)return n;if([`number`,`range`].includes(e.type)){let r=Number(t);if(!Number.isFinite(r))return n.push(`number`),n;e.min!==null&&e.min!==void 0&&r<Number(e.min)&&n.push(`min`),e.max!==null&&e.max!==void 0&&r>Number(e.max)&&n.push(`max`),e.step!==null&&e.step!==void 0&&!pe(r,e)&&n.push(`step`)}if([`select`,`radio`].includes(e.type)&&e.options?.length&&(e.options.map(e=>String(e.value)).includes(String(t))||n.push(`option`)),e.type===`email`&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(t))&&n.push(`email`),e.type===`url`&&!A(t)&&n.push(`url`),e.minLength!==null&&e.minLength!==void 0&&String(t).length<Number(e.minLength)&&n.push(`minLength`),e.maxLength!==null&&e.maxLength!==void 0&&String(t).length>Number(e.maxLength)&&n.push(`maxLength`),e.pattern)try{new RegExp(e.pattern).test(String(t))||n.push(`pattern`)}catch{n.push(`pattern`)}return n}function A(e){try{return new URL(String(e)),!0}catch{return!1}}function pe(e,t){let n=Number(t.step);if(!Number.isFinite(n)||n<=0)return!0;let r=Number(t.min??0),i=(e-(Number.isFinite(r)?r:0))/n;return Math.abs(i-Math.round(i))<2**-52*100}function me(e,t){let n=e?.querySelector?.(`[data-blueprint-sync]`);n&&(n.value=JSON.stringify(t))}function j(e,t,n={}){e?.dispatchEvent?.(new CustomEvent(`daisy:blueprint:${t}`,{bubbles:!0,detail:n}))}function he(e,t,n=6){return!e||e.pointerId!==t.pointerId?!1:Math.hypot(t.clientX-e.x,t.clientY-e.y)<=n}function M(e,t={}){try{return JSON.parse(e)}catch{return t}}function N(e,t,n={}){let r=e?.querySelector?.(t);return r?M(r.content?.textContent||r.textContent||``,n):n}var P,F,I,L,R,z,B=e((()=>{P=1,F=()=>({version:P,nodes:[],edges:[],viewport:{x:0,y:0,zoom:1}}),I=e=>Array.isArray(e)?e:[],L=e=>e&&typeof e==`object`&&!Array.isArray(e)?e:{},R=(e,t=``)=>{if(e==null)return t;let n=String(e).trim();return n===``?t:n},z=(e,t=0)=>{let n=Number(e);return Number.isFinite(n)?n:t}}));function V(e){let t=String(e||`primary`).trim();return ge.includes(t)?t:_e[t]||`primary`}var ge,_e,ve=e((()=>{ge=[`primary`,`secondary`,`accent`,`neutral`,`info`,`success`,`warning`,`error`],_e={action:`success`,condition:`warning`,data:`accent`,default:`primary`,function:`info`,schema:`secondary`,trigger:`primary`}}));function ye(e){let t=new Map;return e.forEach(e=>{[...e.inputs,...e.outputs].forEach(e=>{t.has(e.kind)||t.set(e.kind,new g.Socket(e.kind))})}),t.size||t.set(`default`,new g.Socket(`default`)),{get(e=`default`){return t.has(e)||t.set(e,new g.Socket(e)),t.get(e)}}}function H(e,t,n,r){let i=t.find(t=>t.type===e.type)||t[0],a=new g.Node(e.label||i?.label||e.type),o=be(i,e.data||{}).length,s=(i?.inputs?.length||0)+(i?.outputs?.length||0),c=i?.display||`detailed`;return a.id=e.id,a.width=c===`minimal`?188:248,a.height=c===`minimal`?Math.max(74,52+Math.max(s,1)*18):Math.max(126,54+o*34,88+Math.max(s,1)*28),a.__blueprint={type:e.type,category:i?.category||``,description:i?.description||``,display:c,icon:i?.icon||``,theme:V(i?.theme),nameStrategy:i?.nameStrategy||{mode:`free`},controls:i?.controls||[],previewFields:be(i,e.data||{}),data:{...e.data||{}}},(i?.inputs||[]).forEach(e=>{a.addInput(e.key,new g.Input(n.get(e.kind),e.label,!!e.multiple))}),(i?.outputs||[]).forEach(e=>{a.addOutput(e.key,new g.Output(n.get(e.kind),e.label,!!e.multiple))}),a}function be(e,t={}){if(e?.previewFields?.length)return e.previewFields;let n=(e?.controls||[]).filter(e=>t[e.key]!==null&&t[e.key]!==void 0&&typeof t[e.key]!=`object`).slice(0,3).map(e=>({key:e.key,label:e.label||e.key}));return n.length?n:Object.entries(t).filter(([,e])=>e!=null&&typeof e!=`object`).slice(0,3).map(([e])=>({key:e,label:e}))}function xe(e,t,n){let r=new g.Connection(t,e.sourcePort,n,e.targetPort);return r.id=e.id,r.data=e.data||{},r}function Se(e,t){return t.find(t=>t.type===e?.__blueprint?.type)||null}function Ce(e,t){return e.nodeViews.get(t?.id)?.position||{x:40,y:40}}function we(e,t=0){let n=e?.nameStrategy||{mode:`free`};return n.mode===`preset`&&n.value?n.value:n.mode===`auto`?`${n.prefix||e?.label||e?.type||`Node`} ${t+1}`:e?.label||e?.type||`Node`}function Te(e,t,n,r){let i=Se(e,r),a=Se(t,r);if(!i||!a)return null;let o=n.getConnections(),s=i.outputs.filter(t=>t.multiple?!0:!o.some(n=>n.source===e.id&&String(n.sourceOutput)===t.key)),c=a.inputs.filter(e=>!o.some(n=>n.target===t.id&&String(n.targetInput)===e.key)),l=s.flatMap(e=>c.filter(t=>k(e,t)).map(t=>({output:e,input:t})));return s.length!==1||c.length!==1||l.length!==1?null:l[0]}var Ee,De,U=e((()=>{a(),B(),ve(),Ee=[{type:`task`,label:`Task`,category:`Workflow`,theme:`success`,inputs:[{key:`in`,label:`In`,kind:`flow`}],outputs:[{key:`out`,label:`Out`,kind:`flow`,multiple:!0}],defaults:{}}],De=e=>typeof e==`number`?`number`:`text`}));function W(e,t){e.querySelector(`[data-blueprint-details-panel]`)?.classList.toggle(`hidden`,!t),e.querySelector(`[data-blueprint-details-backdrop]`)?.classList.toggle(`hidden`,!t)}function G(e,t,n,r=!1,i=null,a=null){let o=e.querySelector(`[data-blueprint-properties]`);if(!o)return;if(Le(o),!t){o.append(Y(`p`,{className:`text-sm text-base-content/70`,text:n.noSelection})),W(e,!1);return}let s=Oe(t),c=i||t.__blueprint?.data||{},l=Y(`div`,{className:`grid gap-3`}),u=Y(`div`,{className:`flex items-start justify-between gap-3`}),d=Y(`div`,{className:`min-w-0`});d.append(Y(`p`,{className:`text-sm font-semibold`,text:t.label}),Y(`p`,{className:`text-xs text-base-content/60`,text:t.__blueprint?.type||`node`})),u.append(d,Y(`span`,{className:`badge badge-outline max-w-32 truncate`,text:t.__blueprint?.theme||t.id})),l.append(u),t.__blueprint?.description&&l.append(Y(`p`,{className:`text-sm text-base-content/70`,text:t.__blueprint.description})),a&&l.append(Me(a,n));let f=Y(`div`,{className:`grid gap-2`});if(s.length?s.forEach(e=>f.append(Ne(e,t.id,c,r))):f.append(Y(`p`,{className:`rounded-box border border-dashed border-base-300 bg-base-200/60 p-3 text-sm text-base-content/70`,text:n.noProperties})),l.append(f),!r){let e=Y(`div`,{className:`flex flex-wrap items-center justify-between gap-2 pt-1`});e.append(Y(`button`,{className:`btn btn-primary btn-sm`,dataset:{blueprintApplyNode:``},text:n.applyNode||`Apply`,type:`button`}),Y(`button`,{className:`btn btn-error btn-outline btn-xs`,dataset:{blueprintDeleteNode:``},text:n.deleteNode||`Delete`,type:`button`})),l.append(e)}o.append(l),W(e,!0)}function Oe(e){return e.__blueprint?.controls?.length?e.__blueprint.controls:Object.entries(e.__blueprint?.data||{}).map(([e,t])=>({key:e,label:e,type:De(t)}))}function ke(e){if(e.type===`checkbox`)return e.checked;if(e.type===`number`||e.type===`range`){let t=Number(e.value);return Number.isFinite(t)?t:e.value}return e.value}function Ae(e,t={}){let n={...t};return e.querySelectorAll(`[data-blueprint-property-input]`).forEach(e=>{n[e.dataset.blueprintPropertyInput]=ke(e)}),n}function je(e,t){e.__blueprint.data={...t}}function Me(e,t){if(e.valid){let e=Y(`div`,{className:`alert alert-success py-2 text-sm`,dataset:{blueprintDetailsFeedback:``}});return e.append(Y(`span`,{text:t.applySuccess||`Changes applied.`})),e}let n=Y(`div`,{className:`alert alert-error grid gap-2 py-2 text-sm`,dataset:{blueprintDetailsFeedback:``}}),r=Y(`details`,{className:`collapse collapse-arrow rounded-box bg-error-content/10`}),i=Y(`div`,{className:`collapse-content grid gap-2 px-3 pb-3`}),a=Y(`textarea`,{className:`textarea textarea-bordered textarea-xs min-h-24 font-mono text-xs`,dataset:{blueprintErrorDetails:``},readonly:!0});return a.value=JSON.stringify(e.errors,null,2),i.append(a,Y(`button`,{className:`btn btn-xs justify-self-start`,dataset:{blueprintCopyError:``},text:t.copyError||`Copy details`,type:`button`})),r.append(Y(`summary`,{className:`collapse-title min-h-0 px-3 py-2 text-xs font-semibold`,text:t.errorDetails||`Details`}),i),n.append(Y(`span`,{text:t.applyError||`Some fields are invalid.`}),r),n}function Ne(e,t,n,r){let i=e.key,a=n[i]??e.default??``;if(e.type===`textarea`){let t=Y(`textarea`,{className:`textarea textarea-bordered textarea-sm min-h-20 w-full`,dataset:{blueprintPropertyInput:i},disabled:r,placeholder:e.placeholder||``,required:e.required});return t.value=String(a??``),Fe(t,e),K(e,i,t)}if(e.type===`select`){let t=Y(`select`,{className:`select select-bordered select-sm w-full`,dataset:{blueprintPropertyInput:i},disabled:r,required:e.required});return e.options.forEach(e=>{t.append(Y(`option`,{text:e.label,value:e.value,selected:String(a)===String(e.value)}))}),K(e,i,t)}if(e.type===`radio`){let n=Y(`fieldset`,{className:`form-control grid w-full gap-2`,dataset:{blueprintProperty:i}});n.append(Y(`legend`,{className:`label-text text-xs font-medium text-base-content/70`,text:e.label||i}));let o=Y(`div`,{className:`grid gap-1`});return e.options.forEach(n=>{let s=Y(`input`,{checked:String(a)===String(n.value),className:`radio radio-sm`,dataset:{blueprintPropertyInput:i},disabled:r,name:`${t}-${i}`,required:e.required,type:`radio`,value:n.value});o.append(Y(`label`,{className:`flex items-center gap-2 text-sm`},[s,Y(`span`,{text:n.label})]))}),n.append(o),q(n,e),n}if(e.type===`checkbox`){let t=Y(`input`,{checked:!!a,className:`checkbox checkbox-sm mt-0.5`,dataset:{blueprintPropertyInput:i},disabled:r,required:e.required,type:`checkbox`}),n=Y(`span`,{className:`grid gap-0.5`});return n.append(Pe(e,i)),q(n,e),Y(`label`,{className:`flex items-start gap-2`,dataset:{blueprintProperty:i}},[t,n])}let o=e.type===`range`?`range`:[`color`,`date`,`datetime-local`,`email`,`hidden`,`month`,`number`,`password`,`tel`,`text`,`time`,`url`,`week`].includes(e.type)?e.type:`text`,s=Y(`input`,{className:o===`range`?`range range-sm`:`input input-sm input-bordered w-full`,dataset:{blueprintPropertyInput:i},disabled:r,placeholder:e.placeholder||``,required:e.required,type:o,value:String(a??``)});return Ie(s,e),Fe(s,e),K(e,i,s)}function K(e,t,n){let r=Y(`label`,{className:`form-control grid w-full gap-1`,dataset:{blueprintProperty:t}});return r.append(Pe(e,t),n),q(r,e),r}function Pe(e,t){return Y(`span`,{className:`label-text text-xs font-medium text-base-content/70`,text:e.label||t})}function q(e,t){t.help&&e.append(Y(`span`,{className:`text-xs text-base-content/50`,text:t.help}))}function Fe(e,t){J(e,`pattern`,t.pattern),J(e,`minlength`,t.minLength),J(e,`maxlength`,t.maxLength)}function Ie(e,t){J(e,`min`,t.min),J(e,`max`,t.max),J(e,`step`,t.step)}function J(e,t,n){n==null||n===``||e.setAttribute(t,String(n))}function Y(e,t={},n=[]){let r=document.createElement(e);return Object.entries(t.dataset||{}).forEach(([e,t])=>{r.dataset[e]=t}),t.className&&(r.className=t.className),t.text!==void 0&&(r.textContent=t.text),[`checked`,`disabled`,`required`,`selected`].forEach(e=>{t[e]&&(r[e]=!0)}),t.readonly&&(r.readOnly=!0,r.setAttribute(`readonly`,``)),[`name`,`placeholder`,`type`,`value`].forEach(e=>{t[e]!==void 0&&r.setAttribute(e,String(t[e]))}),n.forEach(e=>r.append(e)),r}function Le(e){if(typeof e.replaceChildren==`function`){e.replaceChildren();return}for(;e.firstChild;)e.removeChild(e.firstChild)}var Re=e((()=>{U()}));function ze(e,t,n){if(!e||e.dataset.blueprintClickBound===`true`)return!1;let r=null;return e.dataset.blueprintClickBound=`true`,e.addEventListener(`pointerdown`,e=>{if(e.button!==0||Be(e)){r=null;return}r={pointerId:e.pointerId,x:e.clientX,y:e.clientY}}),e.addEventListener(`pointerup`,e=>{if(Be(e)){r=null;return}he(r,e)&&n(t),r=null}),e.addEventListener(`pointercancel`,()=>{r=null}),!0}function Be(e){return(e.composedPath?.()||[]).some(e=>e instanceof Element?!!e.closest?.([`button`,`input`,`select`,`textarea`,`[contenteditable="true"]`,`rete-socket`,`rete-ref`,`.input-socket`,`.output-socket`].join(`,`)):!1)}var Ve=e((()=>{B()}));function He(e,t,n,r){return()=>H({id:`${e.type}-${Date.now()}`,type:e.type,label:e.label,data:e.defaults},t,n,r)}function Ue(e,t,n){let r=[],i=e.reduce((i,a)=>{i.has(a.category)||i.set(a.category,[]);let o=He(a,e,t,n);return r.push(o),i.get(a.category).push([a.label,o]),i},new Map);return{nodeFactories:r,typeGroups:i,contextMenuItems:Array.from(i.entries())}}var We=e((()=>{U()}));function X(e,t,n){let r=t.nodeViews.get(n.id)?.element;if(!r)return;let i=V(n.__blueprint?.theme);r.dataset.blueprintNodeType=n.__blueprint?.type||`node`,r.dataset.blueprintDisplay=n.__blueprint?.display||`detailed`,r.dataset.blueprintIcon=n.__blueprint?.icon||``,r.dataset.blueprintLabel=n.label||``,r.dataset.blueprintTheme=i,Ke(e,r,i,n),window.requestAnimationFrame?.(()=>Ke(e,r,i,n)),window.setTimeout(()=>Ke(e,r,i,n),50)}function Ge(e,t,n){n.getNodes().forEach(n=>X(e,t,n))}function Ke(e,t,n,r){t.querySelectorAll(`rete-node`).forEach(e=>{e.dataset.blueprintTheme=n,e.dataset.blueprintDisplay=t.dataset.blueprintDisplay||`detailed`,e.dataset.blueprintIcon=t.dataset.blueprintIcon||``,e.dataset.blueprintLabel=t.dataset.blueprintLabel||``,qe(e,r)})}function qe(e,t){let n=e.shadowRoot;if(!n)return;et(n,`node`,st),Je(n);let r=n.querySelector(`.title`);r&&Xe(r,e),Ze(n,t),n.querySelectorAll(`.input-socket rete-ref, .output-socket rete-ref`).forEach(t=>{$e(t,e.dataset.blueprintTheme)})}function Je(e){Ye(e.querySelectorAll(`.input`)),Ye(e.querySelectorAll(`.output`))}function Ye(e){let t=e.length;e.forEach((e,n)=>{e.dataset.blueprintPortIndex=String(n),e.dataset.blueprintPortCount=String(Math.min(t,3))})}function Xe(e,t){let n=t.closest(`[data-blueprint-display]`),r=n?.dataset.blueprintIcon||t.dataset.blueprintIcon||``,i=n?.dataset.blueprintLabel||t.dataset.blueprintLabel||``;if(e.dataset.blueprintTitleDecorated!==`true`){e.dataset.blueprintTitleDecorated=`true`,e.textContent=``;let t=document.createElement(`span`);t.dataset.blueprintTitleLabel=`true`,t.textContent=i,e.append(t)}let a=e.querySelector(`[data-blueprint-title-label]`);if(a&&(a.textContent=i),e.querySelector(`[data-blueprint-title-icon]`)?.remove(),!r)return;let o=document.createElement(`span`);o.dataset.blueprintTitleIcon=`true`,o.textContent=r.slice(0,3),e.append(o)}function Ze(e,t){if(e.querySelector(`.daisy-blueprint-preview`)?.remove(),!t||t.__blueprint?.display===`minimal`)return;let n=t.__blueprint?.previewFields||[];if(!n.length)return;let r=document.createElement(`div`);r.className=`daisy-blueprint-preview`,r.dataset.blueprintNodePreview=`true`,n.forEach(e=>{let n=t.__blueprint?.data?.[e.key];if(n==null||typeof n==`object`)return;let i=document.createElement(`div`),a=document.createElement(`span`),o=document.createElement(`span`);i.className=`daisy-blueprint-preview-row`,a.className=`daisy-blueprint-preview-label`,o.className=`daisy-blueprint-preview-value`,a.textContent=e.label||e.key,o.textContent=Qe(n),i.append(a,o),r.append(i)}),r.childElementCount&&e.querySelector(`.title`)?.after(r)}function Qe(e){return typeof e==`boolean`?e?`true`:`false`:String(e)}function $e(e,t){let n=e.querySelector?.(`rete-socket`),r=n?.shadowRoot;!n||!r||(n.dataset.blueprintTheme=t,et(r,`socket`,ct))}function et(e,t,n){if(e.__daisyBlueprintStyleKeys?.has(t))return;let r=tt(t,n);r&&(e.adoptedStyleSheets=[...e.adoptedStyleSheets,r],e.__daisyBlueprintStyleKeys=e.__daisyBlueprintStyleKeys||new Set,e.__daisyBlueprintStyleKeys.add(t))}function tt(e,t){if(typeof CSSStyleSheet>`u`||typeof ShadowRoot>`u`||!(`adoptedStyleSheets`in ShadowRoot.prototype))return null;if(!Q.has(e)){let n=new CSSStyleSheet;n.replaceSync(t),Q.set(e,n)}return Q.get(e)}function nt(e,t){let n=[],r=e=>{e.querySelectorAll?.(t).forEach(e=>n.push(e)),e.querySelectorAll?.(`*`).forEach(e=>{e.shadowRoot&&r(e.shadowRoot)})};return r(e),n}function rt(e,t){let n=e?.data?.theme;return V(n||t.getNode(e?.source)?.__blueprint?.theme)}function Z(e,t){nt(e,`rete-connection`).forEach(e=>{e.dataset.blueprintConnectionTheme=t})}function it(e,t,n,r){let i=t.connectionViews.get(r?.id)?.element;if(!i)return;let a=rt(r,n);i.dataset.blueprintConnectionTheme=a,Z(i,a),window.requestAnimationFrame?.(()=>Z(i,a)),window.setTimeout(()=>Z(i,a),50)}function at(e,t,n){n.getConnections().forEach(r=>it(e,t,n,r))}var ot,st,ct,Q,lt=e((()=>{ve(),ot=[`primary`,`secondary`,`accent`,`neutral`,`info`,`success`,`warning`,`error`].map(e=>`
  :host([data-blueprint-theme="${e}"]) {
    --daisy-blueprint-node-theme: var(--color-${e});
    --daisy-blueprint-node-theme-content: var(--color-${e}-content);
  }
`).join(``),st=`
  ${ot}

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
`,ct=`
  ${ot}

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
`,Q=new Map}));function ut(e,t,n){return{version:1,nodes:e.getNodes().map((e,n)=>{let r=t.nodeViews.get(e.id);return{id:e.id,type:e.__blueprint?.type||`node`,label:e.label,position:{x:r?.position?.x??n*260,y:r?.position?.y??0},data:{...e.__blueprint?.data||{}}}}),edges:e.getConnections().map(e=>({id:e.id,source:e.source,sourcePort:String(e.sourceOutput),target:e.target,targetPort:String(e.targetInput),data:{...e.data||{}}})),viewport:{x:t.area?.transform?.x??n.x,y:t.area?.transform?.y??n.y,zoom:t.area?.transform?.k??n.zoom}}}var dt=e((()=>{}));async function ft(e){if(e.__daisyBlueprint)return e.__daisyBlueprint;let t=e.querySelector(`[data-blueprint-canvas]`),n=b(N(e,`[data-blueprint-node-types]`,Ee)),a=ce(N(e,`[data-blueprint-value]`,{}),n),o=pt(e),u=$(e,`details`),d=$(e,`autoLink`),p=N(e,`[data-blueprint-i18n]`,{}),m=ye(n),h=new i,g=new oe(t),_=new ne,y=new se,x=$(e,`history`)?new ae({timing:200}):null,S=null,C=new te,w=$(e,`minimap`)?new c({boundViewport:!0}):null,T=$(e,`reroute`)?new ie:null,E=null,D=null,O={},k=!1;if(h.use(C.root),h.use(g),g.use(C.area),_.use(C.connection),g.use(_),g.use(y),_.addPreset(ee.classic.setup()),y.addPreset(v.classic.setup()),w&&(g.use(w),y.addPreset(v.minimap.setup())),T&&(_.use(T),y.addPreset(v.reroute.setup())),x&&(g.use(x),x.addPreset(l.classic.setup())),$(e,`autoArrange`)){let{AutoArrangePlugin:e,Presets:t}=await r(async()=>{let{AutoArrangePlugin:e,Presets:t}=await import(`./blueprint-layout-BOog3gkz.js`).then(e=>(e.t(),e.n));return{AutoArrangePlugin:e,Presets:t}},__vite__mapDeps([0,1,2,3,4,5]));S=new e,g.use(S),S.addPreset(t.classic.setup())}let{nodeFactories:ue,contextMenuItems:fe}=Ue(n,m,o);if(!o){let e=new re({items:f.classic.setup(fe)});g.use(e),y.addPreset(v.contextMenu.setup())}if(!o&&$(e,`dock`,!1)){let{DockPlugin:e,DockPresets:t}=await r(async()=>{let{DockPlugin:e,DockPresets:t}=await import(`./blueprint-engine-Dv6sC_C3.js`).then(e=>(e.g(),e._));return{DockPlugin:e,DockPresets:t}},__vite__mapDeps([4,1,2,3,5])),n=new e;g.use(n),n.addPreset(t.classic.setup({area:g,size:120,scale:.65})),ue.forEach((e,t)=>n.add(e,t))}h.addPipe(t=>{if(t.type===`connectioncreate`){let r=ut(h,g,a.viewport),i={source:t.data.source,sourcePort:String(t.data.sourceOutput),target:t.data.target,targetPort:String(t.data.targetInput)};if(!le(i,r.nodes,n)){j(e,`error`,{message:p.invalidConnection||`Invalid connection`,edge:i});return}}return[`nodecreated`,`noderemoved`,`connectioncreated`,`connectionremoved`].includes(t.type)&&F(),t});function A(){D=null,O={},W(e,!1)}function pe(t){!u||!t||(D=t,O={...t.__blueprint?.data||{}},G(e,t,p,o,O))}function he(e){if(!(!e||k)&&(E=e,u)){if(D?.id===e.id){A();return}pe(e)}}g.addPipe(t=>{if(t.type===`nodepicked`&&(E=h.getNode(t.data.id),j(e,`select`,{node:E})),t.type===`rendered`&&t.data?.type===`node`){let n=h.getNode(t.data.payload.id);n&&(X(e,g,n),ze(g.nodeViews.get(n.id)?.element,n,he))}return t.type===`rendered`&&t.data?.type===`connection`&&it(e,g,h,t.data.payload),[`nodetranslated`,`zoomed`,`translated`].includes(t.type)&&F(),t});for(let t of a.nodes){let r=H(t,n,m,o);await h.addNode(r),await g.translate(r.id,t.position),X(e,g,r)}for(let e of a.edges){let t=h.getNode(e.source),n=h.getNode(e.target);t&&n&&await h.addConnection(xe(e,t,n))}s.simpleNodesOrder(g),a.nodes.length&&$(e,`fitOnInit`)&&await s.zoomAt(g,h.getNodes()),o&&C.enable(),Ge(e,g,h),at(e,g,h);function M(){return ce(ut(h,g,a.viewport),n)}function P(){let t=M();me(e,t),j(e,`change`,{graph:t})}function F(){(window.requestAnimationFrame||(e=>window.setTimeout(e,0)))(P)}async function I(t){if(o)return null;k=!0,A();let r=n.find(e=>e.type===t)||n[0],i=E?Ce(g,E):{x:40,y:40},a=E?{x:i.x+300,y:i.y}:{x:40,y:40},s=h.getNodes().filter(e=>e.__blueprint?.type===r.type).length,c=H({id:`${r.type}-${Date.now()}`,type:r.type,label:we(r,s),data:r.defaults,position:a},n,m,o),l=E;if(await h.addNode(c),await g.translate(c.id,a),X(e,g,c),d&&l){let t=Te(l,c,h,n);t&&(await h.addConnection(xe({id:`edge-${Date.now()}`,sourcePort:t.output.key,targetPort:t.input.key,data:{}},l,c)),at(e,g,h))}return E=c,P(),F(),window.setTimeout(()=>{k=!1,P()},0),c}async function L(e=E){if(o||!e)return!1;let t=h.getConnections().filter(t=>t.source===e.id||t.target===e.id);for(let e of t)await h.removeConnection(e.id);return await h.removeNode(e.id),E=null,D?.id===e.id&&A(),F(),!0}async function R(){let t=D||E;if(o||!t)return!1;let n=Oe(t);O=Ae(e,O);let r=de(n,O);return r.valid?(je(t,O),await g.update(`node`,t.id),X(e,g,t),G(e,t,p,o,O,r),F(),!0):(G(e,t,p,o,O,r),j(e,`error`,{message:p.applyError||`Some fields are invalid.`,node:t,errors:r.errors}),!1)}let z={editor:h,area:g,history:x,arrange:S,getGraph:M,addNode:I,removeNode:L,async undo(){o||(await x?.undo(),F())},async redo(){o||(await x?.redo(),F())},async arrange(){await S?.layout(),F()},async fit(){let e=h.getNodes();e.length&&await s.zoomAt(g,e)},destroy(){g.destroy()}};e.querySelectorAll(`[data-blueprint-add-node]`).forEach(e=>{e.addEventListener(`pointerdown`,()=>{k=!0,A()})}),e.querySelectorAll(`[data-blueprint-palette-menu]`).forEach(e=>{e.addEventListener(`pointerdown`,()=>{k=!0,A(),window.setTimeout(()=>{k=!1},0)})}),e.querySelectorAll(`[data-blueprint-action]`).forEach(e=>{e.addEventListener(`click`,()=>{if(o&&e.dataset.blueprintAction!==`fit`&&e.dataset.blueprintAction!==`arrange`)return;let t=e.dataset.blueprintAction;t===`undo`&&z.undo(),t===`redo`&&z.redo(),t===`arrange`&&z.arrange(),t===`fit`&&z.fit()})}),e.addEventListener(`click`,t=>{let n=t.target.closest?.(`[data-blueprint-add-node]`);if(n&&e.contains(n)){I(n.dataset.blueprintAddNode).finally(()=>{window.setTimeout(()=>{k=!1},0)});return}if(t.target.closest?.(`[data-blueprint-details-close], [data-blueprint-details-backdrop]`)){A();return}if(t.target.closest?.(`[data-blueprint-apply-node]`)){R();return}if(t.target.closest?.(`[data-blueprint-copy-error]`)){let t=e.querySelector(`[data-blueprint-error-details]`)?.value||``;navigator.clipboard?.writeText(t);return}t.target.closest?.(`[data-blueprint-delete-node]`)&&L()});let B=e=>{let t=e.target.closest?.(`[data-blueprint-property-input]`);!t||!D||o||(O[t.dataset.blueprintPropertyInput]=ke(t))};return e.addEventListener(`input`,B),e.addEventListener(`change`,B),u&&G(e,null,p,o),me(e,M()),j(e,`init`,{graph:M(),readonly:o}),e.__daisyBlueprint=z,z}var pt,$,mt=e((()=>{a(),_(),d(),y(),u(),p(),h(),o(),m(),B(),Re(),Ve(),U(),We(),lt(),dt(),n(),pt=e=>e.dataset.readonly===`true`||e.dataset.mode===`view`,$=(e,t,n=!0)=>{let r=e.dataset[t];return r===void 0||r===``?n:r===`true`}})),ht=t({default:()=>ft}),gt=e((()=>{mt()}));export{gt as n,ht as t};