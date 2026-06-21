const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/blueprint-layout-BOog3gkz.js","assets/rolldown-runtime-CMxvf4Kt.js","assets/vendor-vcnHIpS1.js","assets/trix-DxQbyuLj.js","assets/blueprint-engine-Dv6sC_C3.js","assets/blueprint-render-BU1sXyxw.js"])))=>i.map(i=>d[i]);
import{n as e,r as t}from"./rolldown-runtime-CMxvf4Kt.js";import{i as n,r}from"./app-P8cweapc.js";import{S as i,T as a,a as o,b as s,c,d as l,f as u,h as d,i as f,l as p,m as ee,n as m,o as te,p as ne,r as re,s as h,t as ie,u as ae,v as oe,w as g,x as _}from"./blueprint-engine-Dv6sC_C3.js";import{n as v,r as y,t as se}from"./blueprint-render-BU1sXyxw.js";function b(e=[]){return P(e).map(e=>{let t=F(e),n=I(t.type),r=C(t.controls??t.fields);return n?{type:n,label:I(t.label,n),category:I(t.category,`General`),description:I(t.description),theme:I(t.theme,`default`),display:x(t.display??t.variant),icon:I(t.icon??t.brandIcon),nameStrategy:S(t.nameStrategy??t.naming),inputs:T(t.inputs,!1),outputs:T(t.outputs,!0),controls:r,defaults:{...ce(r),...F(t.defaults)}}:null}).filter(Boolean)}function x(e){let t=I(e,`detailed`);return[`minimal`,`detailed`].includes(t)?t:`detailed`}function S(e={}){if(typeof e==`string`)return{mode:I(e,`free`)};let t=F(e),n=I(t.mode,`free`);return{mode:[`free`,`preset`,`auto`].includes(n)?n:`free`,prefix:I(t.prefix),value:I(t.value)}}function C(e=[]){return P(e).map(e=>{let t=F(e),n=I(t.key??t.name??t.id);if(!n)return null;let r=I(t.type,`text`);return{key:n,name:I(t.name,n),label:I(t.label,n),type:w(r),placeholder:I(t.placeholder),help:I(t.help??t.description),required:!!t.required,pattern:I(t.pattern),minLength:t.minLength??t.minlength??null,maxLength:t.maxLength??t.maxlength??null,min:t.min??null,max:t.max??null,step:t.step??null,options:le(t.options),default:t.default??t.value??null}}).filter(Boolean)}function ce(e=[]){return P(e).reduce((e,t)=>(t.default!==null&&t.default!==void 0&&(e[t.key]=t.default),e),{})}function le(e=[]){return P(e).map(e=>{if(typeof e==`string`||typeof e==`number`||typeof e==`boolean`)return{value:String(e),label:String(e)};let t=F(e),n=t.value??t.id??t.key;return n==null?null:{value:String(n),label:I(t.label,String(n))}}).filter(Boolean)}function w(e){let t=I(e,`text`),n={boolean:`checkbox`,bool:`checkbox`,dropdown:`select`,email:`email`,integer:`number`,string:`text`,toggle:`checkbox`}[t]||t;return[`checkbox`,`color`,`date`,`datetime-local`,`email`,`hidden`,`month`,`number`,`password`,`radio`,`range`,`select`,`tel`,`text`,`textarea`,`time`,`url`,`week`].includes(n)?n:`text`}function T(e=[],t=!1){return P(e).map(e=>{let n=F(e),r=I(n.key);return r?{key:r,label:I(n.label,r),kind:I(n.kind,`default`),type:I(n.type??n.dataType,`any`),multiple:n.multiple===void 0?t:!!n.multiple}:null}).filter(Boolean)}function ue(e={},t=[]){let n=typeof e==`string`?A(e,N()):F(e),r=new Map(b(t).map(e=>[e.type,e])),i=[],a=new Set;P(n.nodes).forEach((e,t)=>{let n=F(e),o=I(n.type,r.keys().next().value||`node`),s=r.get(o),c=I(n.id,`${o}-${t+1}`);a.has(c)||(a.add(c),i.push({id:c,type:o,label:I(n.label,s?.label||o),position:{x:L(n.position?.x,t*260),y:L(n.position?.y,0)},data:{...s?.defaults||{},...F(n.data)}}))});let o=new Map(i.map(e=>[e.id,e])),s=[],c=new Set;P(n.edges).forEach((e,t)=>{let n=F(e),i={id:I(n.id,`edge-${t+1}`),source:I(n.source),sourcePort:I(n.sourcePort),target:I(n.target),targetPort:I(n.targetPort),data:F(n.data)};!i.source||!i.target||!o.has(i.source)||!o.has(i.target)||c.has(i.id)||!de(i,o,r)||(c.add(i.id),s.push(i))});let l=F(n.viewport);return{version:L(n.version,M),nodes:i,edges:s,viewport:{x:L(l.x,0),y:L(l.y,0),zoom:L(l.zoom??l.k,1)}}}function de(e,t,n){let r=t instanceof Map?t:new Map(P(t).map(e=>[e.id,e])),i=n instanceof Map?n:new Map(b(n).map(e=>[e.type,e])),a=r.get(e.source),o=r.get(e.target);if(!a||!o||a.id===o.id)return!1;let s=i.get(a.type),c=i.get(o.type);if(!s||!c)return!0;let l=s.outputs.find(t=>t.key===e.sourcePort),u=c.inputs.find(t=>t.key===e.targetPort);return!l||!u?!1:E(l,u)}function E(e,t){return D(e?.kind,t?.kind)&&D(e?.type,t?.type)}function D(e,t){let n=I(e,`any`),r=I(t,`any`);return n===`any`||r===`any`||n===r?!0:n===`int`&&r===`float`}function fe(e=[],t={}){let n={};return P(e).forEach(e=>{let r=t[e.key],i=pe(e,r);i.length&&(n[e.key]=i)}),{valid:Object.keys(n).length===0,errors:n}}function pe(e,t){let n=[],r=t==null||t===``;if(e.required&&(r||e.type===`checkbox`&&t!==!0)&&n.push(`required`),r)return n;if([`number`,`range`].includes(e.type)){let r=Number(t);if(!Number.isFinite(r))return n.push(`number`),n;e.min!==null&&e.min!==void 0&&r<Number(e.min)&&n.push(`min`),e.max!==null&&e.max!==void 0&&r>Number(e.max)&&n.push(`max`),e.step!==null&&e.step!==void 0&&!O(r,e)&&n.push(`step`)}if([`select`,`radio`].includes(e.type)&&e.options?.length&&(e.options.map(e=>String(e.value)).includes(String(t))||n.push(`option`)),e.type===`email`&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(t))&&n.push(`email`),e.type===`url`&&!me(t)&&n.push(`url`),e.minLength!==null&&e.minLength!==void 0&&String(t).length<Number(e.minLength)&&n.push(`minLength`),e.maxLength!==null&&e.maxLength!==void 0&&String(t).length>Number(e.maxLength)&&n.push(`maxLength`),e.pattern)try{new RegExp(e.pattern).test(String(t))||n.push(`pattern`)}catch{n.push(`pattern`)}return n}function me(e){try{return new URL(String(e)),!0}catch{return!1}}function O(e,t){let n=Number(t.step);if(!Number.isFinite(n)||n<=0)return!0;let r=Number(t.min??0),i=(e-(Number.isFinite(r)?r:0))/n;return Math.abs(i-Math.round(i))<2**-52*100}function he(e,t){let n=e?.querySelector?.(`[data-blueprint-sync]`);n&&(n.value=JSON.stringify(t))}function k(e,t,n={}){e?.dispatchEvent?.(new CustomEvent(`daisy:blueprint:${t}`,{bubbles:!0,detail:n}))}function ge(e,t,n=6){return!e||e.pointerId!==t.pointerId?!1:Math.hypot(t.clientX-e.x,t.clientY-e.y)<=n}function A(e,t={}){try{return JSON.parse(e)}catch{return t}}function j(e,t,n={}){let r=e?.querySelector?.(t);return r?A(r.content?.textContent||r.textContent||``,n):n}var M,N,P,F,I,L,R=e((()=>{M=1,N=()=>({version:M,nodes:[],edges:[],viewport:{x:0,y:0,zoom:1}}),P=e=>Array.isArray(e)?e:[],F=e=>e&&typeof e==`object`&&!Array.isArray(e)?e:{},I=(e,t=``)=>{if(e==null)return t;let n=String(e).trim();return n===``?t:n},L=(e,t=0)=>{let n=Number(e);return Number.isFinite(n)?n:t}}));function z(e){let t=String(e||`primary`).trim();return _e.includes(t)?t:ve[t]||`primary`}var _e,ve,ye=e((()=>{_e=[`primary`,`secondary`,`accent`,`neutral`,`info`,`success`,`warning`,`error`],ve={action:`success`,condition:`warning`,data:`accent`,default:`primary`,function:`info`,schema:`secondary`,trigger:`primary`}}));function be(e){let t=new Map;return e.forEach(e=>{[...e.inputs,...e.outputs].forEach(e=>{t.has(e.kind)||t.set(e.kind,new g.Socket(e.kind))})}),t.size||t.set(`default`,new g.Socket(`default`)),{get(e=`default`){return t.has(e)||t.set(e,new g.Socket(e)),t.get(e)}}}function xe(e,t=0){let n=V(e),r=new g.InputControl(n,{readonly:!0,initial:e});return r.index=t,r}function Se(e){Object.entries(e.__blueprint?.data||{}).forEach(([t,n],r)=>{if(n==null||typeof n==`object`){e.controls?.[t]&&e.removeControl(t);return}e.controls?.[t]&&e.removeControl(t),e.addControl(t,xe(n,r))})}function B(e,t,n,r){let i=t.find(t=>t.type===e.type)||t[0],a=new g.Node(e.label||i?.label||e.type),o=Object.values(e.data||{}).filter(e=>e!=null&&typeof e!=`object`).length,s=(i?.inputs?.length||0)+(i?.outputs?.length||0),c=i?.display||`detailed`;return a.id=e.id,a.width=c===`minimal`?188:220,a.height=c===`minimal`?Math.max(74,52+s*24):Math.max(132,46+s*34+o*42),a.__blueprint={type:e.type,category:i?.category||``,description:i?.description||``,display:c,icon:i?.icon||``,theme:z(i?.theme),nameStrategy:i?.nameStrategy||{mode:`free`},controls:i?.controls||[],data:{...e.data||{}}},(i?.inputs||[]).forEach(e=>{a.addInput(e.key,new g.Input(n.get(e.kind),e.label,!!e.multiple))}),(i?.outputs||[]).forEach(e=>{a.addOutput(e.key,new g.Output(n.get(e.kind),e.label,!!e.multiple))}),Se(a),a}function Ce(e,t,n){let r=new g.Connection(t,e.sourcePort,n,e.targetPort);return r.id=e.id,r.data=e.data||{},r}function we(e,t){return t.find(t=>t.type===e?.__blueprint?.type)||null}function Te(e,t){return e.nodeViews.get(t?.id)?.position||{x:40,y:40}}function Ee(e,t=0){let n=e?.nameStrategy||{mode:`free`};return n.mode===`preset`&&n.value?n.value:n.mode===`auto`?`${n.prefix||e?.label||e?.type||`Node`} ${t+1}`:e?.label||e?.type||`Node`}function De(e,t,n,r){let i=we(e,r),a=we(t,r);if(!i||!a)return null;let o=n.getConnections(),s=i.outputs.filter(t=>t.multiple?!0:!o.some(n=>n.source===e.id&&String(n.sourceOutput)===t.key)),c=a.inputs.filter(e=>!o.some(n=>n.target===t.id&&String(n.targetInput)===e.key)),l=s.flatMap(e=>c.filter(t=>E(e,t)).map(t=>({output:e,input:t})));return s.length!==1||c.length!==1||l.length!==1?null:l[0]}var Oe,V,H=e((()=>{a(),R(),ye(),Oe=[{type:`task`,label:`Task`,category:`Workflow`,theme:`success`,inputs:[{key:`in`,label:`In`,kind:`flow`}],outputs:[{key:`out`,label:`Out`,kind:`flow`,multiple:!0}],defaults:{}}],V=e=>typeof e==`number`?`number`:`text`}));function U(e,t){e.querySelector(`[data-blueprint-details-panel]`)?.classList.toggle(`hidden`,!t),e.querySelector(`[data-blueprint-details-backdrop]`)?.classList.toggle(`hidden`,!t)}function W(e,t,n,r=!1,i=null,a=null){let o=e.querySelector(`[data-blueprint-properties]`);if(!o)return;if(Re(o),!t){o.append(J(`p`,{className:`text-sm text-base-content/70`,text:n.noSelection})),U(e,!1);return}let s=ke(t),c=i||t.__blueprint?.data||{},l=J(`div`,{className:`grid gap-3`}),u=J(`div`,{className:`flex items-start justify-between gap-3`}),d=J(`div`,{className:`min-w-0`});d.append(J(`p`,{className:`text-sm font-semibold`,text:t.label}),J(`p`,{className:`text-xs text-base-content/60`,text:t.__blueprint?.type||`node`})),u.append(d,J(`span`,{className:`badge badge-outline max-w-32 truncate`,text:t.__blueprint?.theme||t.id})),l.append(u),t.__blueprint?.description&&l.append(J(`p`,{className:`text-sm text-base-content/70`,text:t.__blueprint.description})),a&&l.append(Ne(a,n));let f=J(`div`,{className:`grid gap-2`});if(s.length?s.forEach(e=>f.append(Pe(e,t.id,c,r))):f.append(J(`p`,{className:`rounded-box border border-dashed border-base-300 bg-base-200/60 p-3 text-sm text-base-content/70`,text:n.noProperties})),l.append(f),!r){let e=J(`div`,{className:`flex flex-wrap items-center justify-between gap-2 pt-1`});e.append(J(`button`,{className:`btn btn-primary btn-sm`,dataset:{blueprintApplyNode:``},text:n.applyNode||`Apply`,type:`button`}),J(`button`,{className:`btn btn-error btn-outline btn-xs`,dataset:{blueprintDeleteNode:``},text:n.deleteNode||`Delete`,type:`button`})),l.append(e)}o.append(l),U(e,!0)}function ke(e){return e.__blueprint?.controls?.length?e.__blueprint.controls:Object.entries(e.__blueprint?.data||{}).map(([e,t])=>({key:e,label:e,type:V(t)}))}function Ae(e){if(e.type===`checkbox`)return e.checked;if(e.type===`number`||e.type===`range`){let t=Number(e.value);return Number.isFinite(t)?t:e.value}return e.value}function je(e,t={}){let n={...t};return e.querySelectorAll(`[data-blueprint-property-input]`).forEach(e=>{n[e.dataset.blueprintPropertyInput]=Ae(e)}),n}function Me(e,t){e.__blueprint.data={...t},Se(e)}function Ne(e,t){if(e.valid){let e=J(`div`,{className:`alert alert-success py-2 text-sm`,dataset:{blueprintDetailsFeedback:``}});return e.append(J(`span`,{text:t.applySuccess||`Changes applied.`})),e}let n=J(`div`,{className:`alert alert-error grid gap-2 py-2 text-sm`,dataset:{blueprintDetailsFeedback:``}}),r=J(`details`,{className:`collapse collapse-arrow rounded-box bg-error-content/10`}),i=J(`div`,{className:`collapse-content grid gap-2 px-3 pb-3`}),a=J(`textarea`,{className:`textarea textarea-bordered textarea-xs min-h-24 font-mono text-xs`,dataset:{blueprintErrorDetails:``},readonly:!0});return a.value=JSON.stringify(e.errors,null,2),i.append(a,J(`button`,{className:`btn btn-xs justify-self-start`,dataset:{blueprintCopyError:``},text:t.copyError||`Copy details`,type:`button`})),r.append(J(`summary`,{className:`collapse-title min-h-0 px-3 py-2 text-xs font-semibold`,text:t.errorDetails||`Details`}),i),n.append(J(`span`,{text:t.applyError||`Some fields are invalid.`}),r),n}function Pe(e,t,n,r){let i=e.key,a=n[i]??e.default??``;if(e.type===`textarea`){let t=J(`textarea`,{className:`textarea textarea-bordered textarea-sm min-h-20 w-full`,dataset:{blueprintPropertyInput:i},disabled:r,placeholder:e.placeholder||``,required:e.required});return t.value=String(a??``),Ie(t,e),G(e,i,t)}if(e.type===`select`){let t=J(`select`,{className:`select select-bordered select-sm w-full`,dataset:{blueprintPropertyInput:i},disabled:r,required:e.required});return e.options.forEach(e=>{t.append(J(`option`,{text:e.label,value:e.value,selected:String(a)===String(e.value)}))}),G(e,i,t)}if(e.type===`radio`){let n=J(`fieldset`,{className:`form-control grid w-full gap-2`,dataset:{blueprintProperty:i}});n.append(J(`legend`,{className:`label-text text-xs font-medium text-base-content/70`,text:e.label||i}));let o=J(`div`,{className:`grid gap-1`});return e.options.forEach(n=>{let s=J(`input`,{checked:String(a)===String(n.value),className:`radio radio-sm`,dataset:{blueprintPropertyInput:i},disabled:r,name:`${t}-${i}`,required:e.required,type:`radio`,value:n.value});o.append(J(`label`,{className:`flex items-center gap-2 text-sm`},[s,J(`span`,{text:n.label})]))}),n.append(o),K(n,e),n}if(e.type===`checkbox`){let t=J(`input`,{checked:!!a,className:`checkbox checkbox-sm mt-0.5`,dataset:{blueprintPropertyInput:i},disabled:r,required:e.required,type:`checkbox`}),n=J(`span`,{className:`grid gap-0.5`});return n.append(Fe(e,i)),K(n,e),J(`label`,{className:`flex items-start gap-2`,dataset:{blueprintProperty:i}},[t,n])}let o=e.type===`range`?`range`:[`color`,`date`,`datetime-local`,`email`,`hidden`,`month`,`number`,`password`,`tel`,`text`,`time`,`url`,`week`].includes(e.type)?e.type:`text`,s=J(`input`,{className:o===`range`?`range range-sm`:`input input-sm input-bordered w-full`,dataset:{blueprintPropertyInput:i},disabled:r,placeholder:e.placeholder||``,required:e.required,type:o,value:String(a??``)});return Le(s,e),Ie(s,e),G(e,i,s)}function G(e,t,n){let r=J(`label`,{className:`form-control grid w-full gap-1`,dataset:{blueprintProperty:t}});return r.append(Fe(e,t),n),K(r,e),r}function Fe(e,t){return J(`span`,{className:`label-text text-xs font-medium text-base-content/70`,text:e.label||t})}function K(e,t){t.help&&e.append(J(`span`,{className:`text-xs text-base-content/50`,text:t.help}))}function Ie(e,t){q(e,`pattern`,t.pattern),q(e,`minlength`,t.minLength),q(e,`maxlength`,t.maxLength)}function Le(e,t){q(e,`min`,t.min),q(e,`max`,t.max),q(e,`step`,t.step)}function q(e,t,n){n==null||n===``||e.setAttribute(t,String(n))}function J(e,t={},n=[]){let r=document.createElement(e);return Object.entries(t.dataset||{}).forEach(([e,t])=>{r.dataset[e]=t}),t.className&&(r.className=t.className),t.text!==void 0&&(r.textContent=t.text),[`checked`,`disabled`,`required`,`selected`].forEach(e=>{t[e]&&(r[e]=!0)}),t.readonly&&(r.readOnly=!0,r.setAttribute(`readonly`,``)),[`name`,`placeholder`,`type`,`value`].forEach(e=>{t[e]!==void 0&&r.setAttribute(e,String(t[e]))}),n.forEach(e=>r.append(e)),r}function Re(e){if(typeof e.replaceChildren==`function`){e.replaceChildren();return}for(;e.firstChild;)e.removeChild(e.firstChild)}var ze=e((()=>{H()}));function Be(e,t,n){if(!e||e.dataset.blueprintClickBound===`true`)return!1;let r=null;return e.dataset.blueprintClickBound=`true`,e.addEventListener(`pointerdown`,e=>{if(e.button!==0||Ve(e)){r=null;return}r={pointerId:e.pointerId,x:e.clientX,y:e.clientY}}),e.addEventListener(`pointerup`,e=>{if(Ve(e)){r=null;return}ge(r,e)&&n(t),r=null}),e.addEventListener(`pointercancel`,()=>{r=null}),!0}function Ve(e){return(e.composedPath?.()||[]).some(e=>e instanceof Element?!!e.closest?.([`button`,`input`,`select`,`textarea`,`[contenteditable="true"]`,`rete-socket`,`rete-ref`,`.input-socket`,`.output-socket`].join(`,`)):!1)}var He=e((()=>{R()}));function Ue(e,t,n,r){return()=>B({id:`${e.type}-${Date.now()}`,type:e.type,label:e.label,data:e.defaults},t,n,r)}function We(e,t,n){let r=[],i=e.reduce((i,a)=>{i.has(a.category)||i.set(a.category,[]);let o=Ue(a,e,t,n);return r.push(o),i.get(a.category).push([a.label,o]),i},new Map);return{nodeFactories:r,typeGroups:i,contextMenuItems:Array.from(i.entries())}}var Ge=e((()=>{H()}));function Y(e,t,n){let r=t.nodeViews.get(n.id)?.element;if(!r)return;let i=z(n.__blueprint?.theme);r.dataset.blueprintNodeType=n.__blueprint?.type||`node`,r.dataset.blueprintDisplay=n.__blueprint?.display||`detailed`,r.dataset.blueprintIcon=n.__blueprint?.icon||``,r.dataset.blueprintLabel=n.label||``,r.dataset.blueprintTheme=i,X(e,r,i),window.requestAnimationFrame?.(()=>X(e,r,i)),window.setTimeout(()=>X(e,r,i),50)}function Ke(e,t,n){n.getNodes().forEach(n=>Y(e,t,n))}function X(e,t,n){t.querySelectorAll(`rete-node`).forEach(e=>{e.dataset.blueprintTheme=n,e.dataset.blueprintDisplay=t.dataset.blueprintDisplay||`detailed`,qe(e)})}function qe(e){let t=e.shadowRoot;if(!t)return;Xe(t,`node`,rt);let n=t.querySelector(`.title`);n&&Je(n,e),t.querySelectorAll(`.input-socket rete-ref, .output-socket rete-ref`).forEach(t=>{Ye(t,e.dataset.blueprintTheme)})}function Je(e,t){let n=t.closest(`[data-blueprint-display]`),r=n?.dataset.blueprintIcon||``;if(e.dataset.blueprintTitleDecorated!==`true`){e.dataset.blueprintTitleDecorated=`true`,e.textContent=``;let t=document.createElement(`span`);t.dataset.blueprintTitleLabel=`true`,t.textContent=n?.dataset.blueprintLabel||``,e.append(t)}if(e.querySelector(`[data-blueprint-title-icon]`)?.remove(),!r)return;let i=document.createElement(`span`);i.dataset.blueprintTitleIcon=`true`,i.textContent=r.slice(0,3),e.append(i)}function Ye(e,t){let n=e.querySelector?.(`rete-socket`),r=n?.shadowRoot;!n||!r||(n.dataset.blueprintTheme=t,Xe(r,`socket`,it))}function Xe(e,t,n){if(e.__daisyBlueprintStyleKeys?.has(t))return;let r=Ze(t,n);r&&(e.adoptedStyleSheets=[...e.adoptedStyleSheets,r],e.__daisyBlueprintStyleKeys=e.__daisyBlueprintStyleKeys||new Set,e.__daisyBlueprintStyleKeys.add(t))}function Ze(e,t){if(typeof CSSStyleSheet>`u`||typeof ShadowRoot>`u`||!(`adoptedStyleSheets`in ShadowRoot.prototype))return null;if(!Q.has(e)){let n=new CSSStyleSheet;n.replaceSync(t),Q.set(e,n)}return Q.get(e)}function Qe(e,t){let n=[],r=e=>{e.querySelectorAll?.(t).forEach(e=>n.push(e)),e.querySelectorAll?.(`*`).forEach(e=>{e.shadowRoot&&r(e.shadowRoot)})};return r(e),n}function $e(e,t){let n=e?.data?.theme;return z(n||t.getNode(e?.source)?.__blueprint?.theme)}function Z(e,t){Qe(e,`rete-connection`).forEach(e=>{e.dataset.blueprintConnectionTheme=t})}function et(e,t,n,r){let i=t.connectionViews.get(r?.id)?.element;if(!i)return;let a=$e(r,n);i.dataset.blueprintConnectionTheme=a,Z(i,a),window.requestAnimationFrame?.(()=>Z(i,a)),window.setTimeout(()=>Z(i,a),50)}function tt(e,t,n){n.getConnections().forEach(r=>et(e,t,n,r))}var nt,rt,it,Q,at=e((()=>{ye(),nt=[`primary`,`secondary`,`accent`,`neutral`,`info`,`success`,`warning`,`error`].map(e=>`
  :host([data-blueprint-theme="${e}"]) {
    --daisy-blueprint-node-theme: var(--color-${e});
    --daisy-blueprint-node-theme-content: var(--color-${e}-content);
  }
`).join(``),rt=`
  ${nt}

  :host {
    --socket-size: 18px !important;
    --socket-margin: 0px !important;
    background: var(--color-base-100) !important;
    border-color: color-mix(in oklch, var(--daisy-blueprint-node-theme) 42%, var(--color-base-300)) !important;
    border-radius: 8px !important;
    box-shadow: 0 8px 18px color-mix(in oklch, var(--color-base-content) 8%, transparent) !important;
    box-sizing: border-box !important;
  }

  .title {
    align-items: center !important;
    background: var(--daisy-blueprint-node-theme) !important;
    border-bottom: 1px solid color-mix(in oklch, var(--daisy-blueprint-node-theme) 42%, var(--color-base-300)) !important;
    border-radius: 6px 6px 0 0 !important;
    color: var(--daisy-blueprint-node-theme-content) !important;
    display: flex !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    gap: 8px !important;
    justify-content: space-between !important;
    line-height: 1.2 !important;
    padding: 9px 12px !important;
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
    border-top: 1px solid var(--color-base-300) !important;
    box-sizing: border-box !important;
    display: flex !important;
    gap: 8px !important;
    min-height: 34px !important;
    padding: 5px 16px !important;
    width: 100% !important;
  }

  .input:nth-of-type(even),
  .output:nth-of-type(even) {
    background: color-mix(in oklch, var(--color-base-200) 72%, var(--color-base-100) 28%) !important;
  }

  .input {
    justify-content: flex-start !important;
    text-align: left !important;
  }

  .output {
    justify-content: flex-end !important;
    text-align: right !important;
  }

  :host([data-blueprint-display="minimal"]) .input,
  :host([data-blueprint-display="minimal"]) .output {
    background: var(--color-base-100) !important;
    border-top: 0 !important;
    min-height: 24px !important;
    padding: 1px 10px !important;
  }

  .input-socket,
  .output-socket {
    display: inline-flex !important;
    flex: 0 0 18px !important;
    height: 18px !important;
    margin: 0 !important;
    position: relative !important;
    transform: none !important;
    width: 18px !important;
  }

  .input-title,
  .output-title {
    color: var(--color-base-content) !important;
    flex: 0 1 auto !important;
    font-size: 12px !important;
    font-weight: 650 !important;
    line-height: 18px !important;
    margin: 0 !important;
    min-width: 0 !important;
    opacity: 0.82 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  :host([data-blueprint-display="minimal"]) .input-title,
  :host([data-blueprint-display="minimal"]) .output-title {
    font-size: 10px !important;
    opacity: 0.65 !important;
  }

  .control,
  .input-control {
    background: var(--color-base-100) !important;
    border-top: 1px solid var(--color-base-300) !important;
    box-sizing: border-box !important;
    padding: 8px 12px !important;
    width: 100% !important;
  }

  :host([data-blueprint-display="minimal"]) .control,
  :host([data-blueprint-display="minimal"]) .input-control {
    display: none !important;
  }
`,it=`
  ${nt}

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
`,Q=new Map}));function ot(e,t,n){return{version:1,nodes:e.getNodes().map((e,n)=>{let r=t.nodeViews.get(e.id);return{id:e.id,type:e.__blueprint?.type||`node`,label:e.label,position:{x:r?.position?.x??n*260,y:r?.position?.y??0},data:{...e.__blueprint?.data||{}}}}),edges:e.getConnections().map(e=>({id:e.id,source:e.source,sourcePort:String(e.sourceOutput),target:e.target,targetPort:String(e.targetInput),data:{...e.data||{}}})),viewport:{x:t.area?.transform?.x??n.x,y:t.area?.transform?.y??n.y,zoom:t.area?.transform?.k??n.zoom}}}var st=e((()=>{}));async function ct(e){if(e.__daisyBlueprint)return e.__daisyBlueprint;let t=e.querySelector(`[data-blueprint-canvas]`),n=b(j(e,`[data-blueprint-node-types]`,Oe)),a=ue(j(e,`[data-blueprint-value]`,{}),n),o=lt(e),u=$(e,`details`),d=$(e,`autoLink`),p=j(e,`[data-blueprint-i18n]`,{}),m=be(n),h=new i,g=new oe(t),_=new ne,y=new se,x=$(e,`history`)?new ae({timing:200}):null,S=null,C=new te,ce=$(e,`minimap`)?new c({boundViewport:!0}):null,le=$(e,`reroute`)?new ie:null,w=null,T=null,E={},D=!1;if(h.use(C.root),h.use(g),g.use(C.area),_.use(C.connection),g.use(_),g.use(y),_.addPreset(ee.classic.setup()),y.addPreset(v.classic.setup()),ce&&(g.use(ce),y.addPreset(v.minimap.setup())),le&&(_.use(le),y.addPreset(v.reroute.setup())),x&&(g.use(x),x.addPreset(l.classic.setup())),$(e,`autoArrange`)){let{AutoArrangePlugin:e,Presets:t}=await r(async()=>{let{AutoArrangePlugin:e,Presets:t}=await import(`./blueprint-layout-BOog3gkz.js`).then(e=>(e.t(),e.n));return{AutoArrangePlugin:e,Presets:t}},__vite__mapDeps([0,1,2,3,4,5]));S=new e,g.use(S),S.addPreset(t.classic.setup())}let{nodeFactories:pe,contextMenuItems:me}=We(n,m,o);if(!o){let e=new re({items:f.classic.setup(me)});g.use(e),y.addPreset(v.contextMenu.setup())}if(!o&&$(e,`dock`,!1)){let{DockPlugin:e,DockPresets:t}=await r(async()=>{let{DockPlugin:e,DockPresets:t}=await import(`./blueprint-engine-Dv6sC_C3.js`).then(e=>(e.g(),e._));return{DockPlugin:e,DockPresets:t}},__vite__mapDeps([4,1,2,3,5])),n=new e;g.use(n),n.addPreset(t.classic.setup({area:g,size:120,scale:.65})),pe.forEach((e,t)=>n.add(e,t))}h.addPipe(t=>{if(t.type===`connectioncreate`){let r=ot(h,g,a.viewport),i={source:t.data.source,sourcePort:String(t.data.sourceOutput),target:t.data.target,targetPort:String(t.data.targetInput)};if(!de(i,r.nodes,n)){k(e,`error`,{message:p.invalidConnection||`Invalid connection`,edge:i});return}}return[`nodecreated`,`noderemoved`,`connectioncreated`,`connectionremoved`].includes(t.type)&&P(),t});function O(){T=null,E={},U(e,!1)}function ge(t){!u||!t||(T=t,E={...t.__blueprint?.data||{}},W(e,t,p,o,E))}function A(e){if(!(!e||D)&&(w=e,u)){if(T?.id===e.id){O();return}ge(e)}}g.addPipe(t=>{if(t.type===`nodepicked`&&(w=h.getNode(t.data.id),k(e,`select`,{node:w})),t.type===`rendered`&&t.data?.type===`node`){let n=h.getNode(t.data.payload.id);n&&(Y(e,g,n),Be(g.nodeViews.get(n.id)?.element,n,A))}return t.type===`rendered`&&t.data?.type===`connection`&&et(e,g,h,t.data.payload),[`nodetranslated`,`zoomed`,`translated`].includes(t.type)&&P(),t});for(let t of a.nodes){let r=B(t,n,m,o);await h.addNode(r),await g.translate(r.id,t.position),Y(e,g,r)}for(let e of a.edges){let t=h.getNode(e.source),n=h.getNode(e.target);t&&n&&await h.addConnection(Ce(e,t,n))}s.simpleNodesOrder(g),a.nodes.length&&$(e,`fitOnInit`)&&await s.zoomAt(g,h.getNodes()),o&&C.enable(),Ke(e,g,h),tt(e,g,h);function M(){return ue(ot(h,g,a.viewport),n)}function N(){let t=M();he(e,t),k(e,`change`,{graph:t})}function P(){(window.requestAnimationFrame||(e=>window.setTimeout(e,0)))(N)}async function F(t){if(o)return null;D=!0,O();let r=n.find(e=>e.type===t)||n[0],i=w?Te(g,w):{x:40,y:40},a=w?{x:i.x+300,y:i.y}:{x:40,y:40},s=h.getNodes().filter(e=>e.__blueprint?.type===r.type).length,c=B({id:`${r.type}-${Date.now()}`,type:r.type,label:Ee(r,s),data:r.defaults,position:a},n,m,o),l=w;if(await h.addNode(c),await g.translate(c.id,a),Y(e,g,c),d&&l){let t=De(l,c,h,n);t&&(await h.addConnection(Ce({id:`edge-${Date.now()}`,sourcePort:t.output.key,targetPort:t.input.key,data:{}},l,c)),tt(e,g,h))}return w=c,N(),P(),window.setTimeout(()=>{D=!1,N()},0),c}async function I(e=w){if(o||!e)return!1;let t=h.getConnections().filter(t=>t.source===e.id||t.target===e.id);for(let e of t)await h.removeConnection(e.id);return await h.removeNode(e.id),w=null,T?.id===e.id&&O(),P(),!0}async function L(){let t=T||w;if(o||!t)return!1;let n=ke(t);E=je(e,E);let r=fe(n,E);return r.valid?(Me(t,E),await g.update(`node`,t.id),Y(e,g,t),W(e,t,p,o,E,r),P(),!0):(W(e,t,p,o,E,r),k(e,`error`,{message:p.applyError||`Some fields are invalid.`,node:t,errors:r.errors}),!1)}let R={editor:h,area:g,history:x,arrange:S,getGraph:M,addNode:F,removeNode:I,async undo(){o||(await x?.undo(),P())},async redo(){o||(await x?.redo(),P())},async arrange(){await S?.layout(),P()},async fit(){let e=h.getNodes();e.length&&await s.zoomAt(g,e)},destroy(){g.destroy()}};e.querySelectorAll(`[data-blueprint-add-node]`).forEach(e=>{e.addEventListener(`pointerdown`,()=>{D=!0,O()})}),e.querySelectorAll(`[data-blueprint-action]`).forEach(e=>{e.addEventListener(`click`,()=>{if(o&&e.dataset.blueprintAction!==`fit`&&e.dataset.blueprintAction!==`arrange`)return;let t=e.dataset.blueprintAction;t===`undo`&&R.undo(),t===`redo`&&R.redo(),t===`arrange`&&R.arrange(),t===`fit`&&R.fit()})}),e.addEventListener(`click`,t=>{let n=t.target.closest?.(`[data-blueprint-add-node]`);if(n&&e.contains(n)){F(n.dataset.blueprintAddNode).finally(()=>{window.setTimeout(()=>{D=!1},0)});return}if(t.target.closest?.(`[data-blueprint-details-close], [data-blueprint-details-backdrop]`)){O();return}if(t.target.closest?.(`[data-blueprint-apply-node]`)){L();return}if(t.target.closest?.(`[data-blueprint-copy-error]`)){let t=e.querySelector(`[data-blueprint-error-details]`)?.value||``;navigator.clipboard?.writeText(t);return}t.target.closest?.(`[data-blueprint-delete-node]`)&&I()});let z=e=>{let t=e.target.closest?.(`[data-blueprint-property-input]`);!t||!T||o||(E[t.dataset.blueprintPropertyInput]=Ae(t))};return e.addEventListener(`input`,z),e.addEventListener(`change`,z),u&&W(e,null,p,o),he(e,M()),k(e,`init`,{graph:M(),readonly:o}),e.__daisyBlueprint=R,R}var lt,$,ut=e((()=>{a(),_(),d(),y(),u(),p(),h(),o(),m(),R(),ze(),He(),H(),Ge(),at(),st(),n(),lt=e=>e.dataset.readonly===`true`||e.dataset.mode===`view`,$=(e,t,n=!0)=>{let r=e.dataset[t];return r===void 0||r===``?n:r===`true`}})),dt=t({default:()=>ct}),ft=e((()=>{ut()}));export{ft as n,dt as t};