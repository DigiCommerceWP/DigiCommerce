(()=>{document.addEventListener("DOMContentLoaded",()=>{let c=document.querySelectorAll('input[name="price_variation"]'),u=document.getElementById("variation-name"),m=document.getElementById("variation-price"),a=document.querySelector("#add-to-cart-button");if(!a)return;if(c.length){let n=e=>{let t=e.value,o=e.dataset.name,i=e.dataset.formattedPrice;m.value=t,u.value=o||"",a.innerHTML=`${digicommerceVars.i18n.purchase_for} <span class="button-price">${i}</span>`,a.classList.remove("button-disabled"),a.disabled=!1},r=Array.from(c).find(e=>e.checked);r?n(r):(a.innerHTML=digicommerceVars.i18n.select_option,a.classList.add("button-disabled"),a.disabled=!0),c.forEach(e=>{e.addEventListener("change",t=>n(t.target))})}let d=document.querySelector(".digicommerce-add-to-cart");d&&d.addEventListener("submit",async function(n){n.preventDefault();let r=new FormData(d);try{let t=await(await fetch(digicommerceVars.ajaxurl,{method:"POST",body:new URLSearchParams({action:"digicommerce_add_to_cart",product_id:r.get("product_id"),product_price:r.get("product_price")||"",variation_name:r.get("variation_name")||"",variation_price:r.get("variation_price")||"",nonce:r.get("cart_nonce")}),headers:{"Content-Type":"application/x-www-form-urlencoded"}})).json(),o=()=>!digicommerceVars.proVersion||!digicommerceVars.enableSideCart||!digicommerceVars.autoOpen;if(t.success){if(digicommerceVars.proVersion&&digicommerceVars.enableSideCart){let i=new CustomEvent("digicommerce_cart_updated",{detail:{source:"add_to_cart"}});document.dispatchEvent(i)}o()&&(t.data.redirect?window.location.href=t.data.redirect:alert("Product added to cart successfully!"))}else alert(t.data.message||"Failed to add product to cart.")}catch(e){console.error("Error:",e),alert("An error occurred. Please try again.")}}),document.querySelectorAll(".share-link").forEach(n=>{n.addEventListener("click",r=>{r.preventDefault();let e=n.href,t=window.innerWidth,o=window.innerHeight,i=Math.min(600,t*.9),s=Math.min(400,o*.8),l=t/2-i/2,p=o/2-s/2;window.open(e,"shareWindow",`width=${i},height=${s},top=${p},left=${l},resizable=yes,scrollbars=yes`)})})});})();
