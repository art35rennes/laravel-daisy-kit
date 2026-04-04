function r(){return typeof window<"u"&&window.isSecureContext&&navigator?.clipboard&&(typeof navigator.clipboard.writeText=="function"||typeof navigator.clipboard.write=="function")}async function g(){if(!r())return!1;try{return(await navigator.permissions.query({name:"clipboard-write"})).state!=="denied"}catch{return!0}}async function x(){if(!r())return!1;try{return await navigator.clipboard.writeText(""),!0}catch(e){return console.warn("[Copyable] Permission refusée pour le presse-papier:",e),!1}}function h(e){const t=document.createElement("div");return t.innerHTML=e,t.textContent||t.innerText||""}function C(e){if(!e)return"";const t=e.cloneNode(!0);return t.querySelectorAll(".copyable-icon, .copyable-feedback, [data-copyable-ignore]").forEach(i=>i.remove()),t.textContent?.trim()||t.innerText?.trim()||""}function L(e){if(!e)return"";const t=e.cloneNode(!0);return t.querySelectorAll(".copyable-icon, .copyable-feedback, [data-copyable-ignore]").forEach(i=>i.remove()),t.innerHTML.trim()}function A(e="sm"){const t={xs:{className:"w-3 h-3",dimension:"0.75rem"},sm:{className:"w-4 h-4",dimension:"1rem"},md:{className:"w-5 h-5",dimension:"1.25rem"},lg:{className:"w-6 h-6",dimension:"1.5rem"},xl:{className:"w-8 h-8",dimension:"2rem"}},i=t[e]||t.sm,a=document.createElement("span"),l=`copyable-icon inline-flex items-center justify-center ${i.className} text-base-content/60`;a.className=l,a.setAttribute("aria-hidden","true"),a.setAttribute("data-copyable-ignore","true"),a.dataset.iconSize=e,a.dataset.iconDimension=i.dimension;const d=`
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" class="${i.className}">
            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
        </svg>
    `;return a.innerHTML=d,a}function E(e="Copié!"){const t=document.createElement("span");return t.className="copyable-feedback hidden absolute -top-8 left-1/2 -translate-x-1/2 bg-base-content text-base-100 px-2 py-1 rounded text-xs whitespace-nowrap z-50",t.setAttribute("data-copyable-ignore","true"),t.textContent=e,t}async function w(e){if(navigator.clipboard?.writeText)try{return await navigator.clipboard.writeText(e),!0}catch(t){console.error("[Copyable] Erreur lors de la copie:",t)}return k(e)}async function T(e){if(typeof ClipboardItem<"u"&&navigator.clipboard?.write)try{const t=new ClipboardItem({"text/html":new Blob([e],{type:"text/html"}),"text/plain":new Blob([h(e)],{type:"text/plain"})});return await navigator.clipboard.write([t]),!0}catch(t){console.warn("[Copyable] Impossible de copier le HTML, fallback sur texte brut:",t)}return await w(h(e))}function k(e){try{const t=document.createElement("textarea");t.value=e,t.setAttribute("readonly",""),t.style.position="absolute",t.style.left="-9999px",document.body.appendChild(t),t.select();const i=document.execCommand("copy");return document.body.removeChild(t),i}catch(t){return console.error("[Copyable] Fallback copy failed:",t),!1}}function p(e,t,i=!1){const a=e.querySelector(".copyable-feedback");a&&(t&&(a.textContent=t),i?(a.classList.add("bg-error","text-error-content"),a.classList.remove("bg-base-content","text-base-100")):(a.classList.remove("bg-error","text-error-content"),a.classList.add("bg-base-content","text-base-100")),a.classList.remove("hidden"),a.classList.add("show"),setTimeout(()=>{a.classList.remove("show"),setTimeout(()=>{a.classList.add("hidden")},2e3)},2e3))}function b(e){if(e.dataset.copyableInitialized==="true")return;e.dataset.copyableInitialized="true",e.setAttribute("role","button"),e.setAttribute("tabindex","0"),e.getAttribute("aria-label")||e.setAttribute("aria-label","Copier dans le presse-papier"),e.classList.contains("copyable-underline")&&e.classList.add("border-b","border-dashed","border-base-content/30","hover:border-base-content/60","transition-colors");const t=e.dataset.copyValue,i=e.dataset.copyHtml==="true",a=e.dataset.iconSize||"sm",l=e.dataset.iconPosition||"right",d=e.dataset.successMessage,u=e.dataset.errorMessage;e.classList.contains("inline-flex")||e.classList.add("inline-flex"),e.classList.contains("items-center")||e.classList.add("items-center"),e.classList.contains("flex-wrap")||e.classList.add("flex-wrap"),e.classList.contains("flex-wrap")||e.classList.add("flex-wrap");const s=A(a);e.style.getPropertyValue("--copyable-icon-size")||e.style.setProperty("--copyable-icon-size",s.dataset.iconDimension||"1rem"),l==="left"?(e.classList.add("relative","copyable-has-icon-left"),e.insertBefore(s,e.firstChild),s.classList.add("copyable-icon-left","absolute","top-1/2","-translate-y-1/2","left-0")):l==="inline"?(e.appendChild(s),s.classList.add("ml-1","copyable-inline-icon")):(e.classList.add("relative","copyable-has-icon-right"),e.appendChild(s),s.classList.add("copyable-icon-right","absolute","top-1/2","-translate-y-1/2","right-0"));const v=E(d||"Copié!");if(e.appendChild(v),!document.getElementById("copyable-styles")){const o=document.createElement("style");o.id="copyable-styles",o.textContent=`
            .copyable-has-icon-right,
            .copyable-has-icon-left {
                transition: padding 150ms ease;
            }
            .copyable-has-icon-right .copyable-icon,
            .copyable-has-icon-left .copyable-icon {
                opacity: 0;
                pointer-events: none;
                transition: opacity 150ms ease;
            }
            .copyable:focus-visible {
                outline: 2px solid hsl(var(--p));
                outline-offset: 2px;
            }
            .copyable-feedback.show {
                display: block;
                animation: copyableFadeInOut 2s ease-in-out;
            }
            .copyable-icon-left {
                left: 0;
            }
            .copyable-icon-right {
                right: 0;
            }
            .copyable-has-icon-right:is(:hover, :focus-visible) {
                padding-right: calc(var(--copyable-icon-size, 1rem) + 0.3rem);
            }
            .copyable-has-icon-left:is(:hover, :focus-visible) {
                padding-left: calc(var(--copyable-icon-size, 1rem) + 0.3rem);
            }
            .copyable-has-icon-right:is(:hover, :focus-visible) .copyable-icon,
            .copyable-has-icon-left:is(:hover, :focus-visible) .copyable-icon {
                opacity: 1;
            }
            .copyable-inline-icon {
                opacity: 0;
                pointer-events: none;
                transition: opacity 150ms ease;
            }
            .copyable:hover .copyable-inline-icon,
            .copyable:focus-visible .copyable-inline-icon {
                opacity: 1;
            }
            @keyframes copyableFadeInOut {
                0%, 100% { opacity: 0; transform: translate(-50%, -4px); }
                10%, 90% { opacity: 1; transform: translate(-50%, 0); }
            }
        `,document.head.appendChild(o)}const f=async o=>{if(o.target.closest("[data-copyable-ignore]")&&(o.preventDefault(),o.stopPropagation()),r()&&!await g()&&!await x()){p(e,u||"Permission refusée pour accéder au presse-papier",!0);return}let n=t;if(n||(i?n=L(e):n=C(e)),!n){p(e,u||"Aucun contenu à copier",!0);return}let y=!1;i&&!t?y=await T(n):y=await w(n),y?p(e,d||"Copié!"):p(e,u||"Erreur lors de la copie",!0)};e.addEventListener("click",f),e.addEventListener("keydown",o=>{(o.key==="Enter"||o.key===" ")&&(o.preventDefault(),f(o))})}function c(e=document){e.querySelectorAll('.copyable:not([data-copyable-initialized="true"])').forEach(b)}function N(e,t={}){if(e.classList.contains("copyable")){b(e);return}c(e)}async function m(){if(!(document.querySelectorAll(".copyable").length>0))return;if(!r()){console.warn("[Copyable] Clipboard API indisponible (page non sécurisée ?). Passage en fallback.");return}try{const i=await navigator.permissions.query({name:"clipboard-write"});if(i.state==="granted"||i.state==="prompt"){if(i.state==="prompt")try{await navigator.clipboard.writeText("")}catch{}return}if(i.state==="denied")return}catch{if(r())try{await navigator.clipboard.writeText("")}catch{}}}document.readyState==="loading"?document.addEventListener("DOMContentLoaded",async()=>{await m(),c()}):m().then(()=>{c()});typeof MutationObserver<"u"&&new MutationObserver(t=>{t.forEach(i=>{i.addedNodes.forEach(a=>{a.nodeType===Node.ELEMENT_NODE&&(a.classList&&a.classList.contains("copyable")&&b(a),c(a))})})}).observe(document.body,{childList:!0,subtree:!0});typeof window<"u"&&(window.DaisyKit=window.DaisyKit||{},window.DaisyKit.initCopyable=b,window.DaisyKit.initAllCopyables=c);export{N as default};
