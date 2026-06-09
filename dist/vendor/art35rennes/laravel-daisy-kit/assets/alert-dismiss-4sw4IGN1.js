function s(t){t.addEventListener("click",a=>{const i=a.target.closest("[data-alert-dismiss]");!i||!t.contains(i)||(a.preventDefault(),t.remove())})}export{s as default};
