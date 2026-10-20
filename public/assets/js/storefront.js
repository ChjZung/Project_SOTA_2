/* Standalone Laravel storefront behaviour. No WordPress endpoints required. */
document.addEventListener('DOMContentLoaded', () => {
  const $ = (s, root = document) => root.querySelector(s);
  const $$ = (s, root = document) => [...root.querySelectorAll(s)];
  const money = n => '$' + Number(n).toFixed(2);
  const toast = text => { $('.store-toast').textContent = text; clearTimeout(toast.timer); toast.timer = setTimeout(() => $('.store-toast').textContent = '', 5000); };
  const json = async (url, body) => {
    const response = await fetch(url, {method: body ? 'POST' : 'GET', headers: {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':$('meta[name=csrf-token]').content}, ...(body ? {body:JSON.stringify(body)} : {})});
    const data = await response.json(); if (!response.ok || !data.success) throw Error(data.message || 'Please try again.'); return data;
  };
  let lastFocus;
  const closePanels = () => { $$('.store-overlay').forEach(e => {e.classList.remove('is-open');e.hidden = true;});document.body.style.overflow = '';lastFocus?.focus(); };
  const openPanel = id => { const trigger = document.activeElement;closePanels();lastFocus = trigger;const panel = $(id);panel.hidden = false;panel.classList.add('is-open');document.body.style.overflow = 'hidden';$('.store-close',panel).focus(); };
  $$('.store-overlay').forEach(e => e.addEventListener('click', event => {if(event.target === e || event.target.closest('.store-close')) closePanels();}));
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closePanels();
    const panel = $('.store-overlay.is-open'); if (!panel || event.key !== 'Tab') return;
    const items = $$('a,button,input,summary,select',panel).filter(e=>e.getClientRects().length && !e.disabled);
    if(event.shiftKey && document.activeElement === items[0]){event.preventDefault();items.at(-1).focus();}
    else if(!event.shiftKey && document.activeElement === items.at(-1)){event.preventDefault();items[0].focus();}
  });
  $$('[data-bg-url],[data-bg]').forEach(e => {const bg = e.dataset.bgUrl || e.dataset.bg;if(bg)e.style.backgroundImage = (e.dataset.bgGradient ? e.dataset.bgGradient + ', ' : '') + `url("${bg}")`;});
  $$('img[data-orig-src]').forEach(e => {e.src = e.dataset.origSrc;if(e.dataset.srcset)e.srcset=e.dataset.srcset;e.classList.remove('lazyload','lazyloading');e.classList.add('lazyloaded');});
  $$('.awb-background-slider').forEach(slider => {const slides = $$('.swiper-slide',slider);let index=0;slides[0]?.classList.add('is-active');if(slides.length>1 && !matchMedia('(prefers-reduced-motion: reduce)').matches)setInterval(()=>{slides[index].classList.remove('is-active');index=(index+1)%slides.length;slides[index].classList.add('is-active');},5000);});
  if (window.Swiper) $$('.fusion-post-cards.awb-swiper-carousel').forEach(el => {
    new Swiper(el, {slidesPerView:1,slidesPerGroup:1,spaceBetween:24,speed:500,watchOverflow:true,keyboard:{enabled:true,onlyInViewport:true},a11y:{enabled:true},navigation:{prevEl:$('.awb-swiper-button-prev',el),nextEl:$('.awb-swiper-button-next',el)},pagination:{el:$('.swiper-pagination',el),clickable:true},breakpoints:{640:{slidesPerView:2,slidesPerGroup:2},1024:{slidesPerView:Number(el.dataset.columns)||3,slidesPerGroup:Number(el.dataset.columns)||3}},on:{init(){this.update();}}});
  });
  $$('.awb-menu').forEach(menu => {menu.classList.remove('loading','mega-menu-loading');if(!menu.closest('.fusion-tb-header'))menu.classList.add('awb-menu_desktop');});
  $$('a[href="#"]').forEach(a => {if(a.closest('.awb-menu__main-li')?.querySelector('.awb-menu__sub-ul,.fusion-megamenu-wrapper,.awb-menu__mega-wrap')){a.setAttribute('aria-expanded','false');a.addEventListener('click',e=>{e.preventDefault();const li=a.closest('.awb-menu__main-li');const state=li.classList.toggle('store-open');a.setAttribute('aria-expanded',state);});}});
  $$('a[href*="awb-open-oc__2892"],.awb-vegan-storemenu').forEach(e=>{e.setAttribute('aria-label','Open navigation');e.addEventListener('click',event=>{event.preventDefault();openPanel('#store-menu');});});
  const deals = $('.fusion-builder-row-10');if(deals)deals.id='flash-deals';
  $$('.fusion-countdown').forEach(el => {
    // One stable deadline, configurable through STOREFRONT_DEAL_ENDS_AT.
    const end = new Date(el.dataset.endsAt || '2026-12-31T23:59:59+07:00').getTime();
    const tick = () => {let diff=Math.max(0,Math.floor((end-Date.now())/1000));const values=[Math.floor(diff/86400),Math.floor(diff%86400/3600),Math.floor(diff%3600/60),diff%60];['days','hours','minutes','seconds'].forEach((key,i)=>{const target=$('.fusion-dash-'+key+' .fusion-digit-wrapper',el);if(target)target.textContent=String(values[i]).padStart(2,'0');});};tick();setInterval(tick,1000);
  });
  $$('form.searchform').forEach(form=>{form.action='/search';form.method='GET';const input=$('input[type=search],input[name=s]',form);if(input){input.name='q';input.setAttribute('aria-label','Search products');}});
  $$('[data-gallery-image]').forEach(button=>button.addEventListener('click',()=>{const link=$('.product-main-image');if(link){link.href=button.dataset.galleryImage;$('img',link).src=button.dataset.galleryImage;}}));
  $$('[data-place-order]').forEach(form=>form.addEventListener('submit',()=>{const button=$('button[type=submit]',form);if(button){button.disabled=true;button.textContent='Placing your order…';}}));
  $$('form[action*="wp-login"]').forEach(form=>{form.addEventListener('submit',e=>{e.preventDefault();location.href='/my-account';});});
  $$('form.variations_form').forEach(form => {
    const selects=$$('select[name^=attribute]',form);
    const update = () => {
      const ready=selects.every(s=>s.value);const button=$('.single_add_to_cart_button,.add_to_cart_button',form);
      if(button){button.classList.toggle('disabled',!ready);button.classList.toggle('wc-variation-selection-needed',!ready);button.setAttribute('aria-disabled',String(!ready));}
      let variations=[];try{variations=JSON.parse(form.dataset.product_variations||'[]');}catch{}
      const chosen=ready?variations.find(v=>selects.every(s=>v.attributes[s.name]===s.value)):null;
      let price=$('.store-option-price',form);if(!price){price=document.createElement('span');price.className='store-option-price';price.setAttribute('aria-live','polite');form.append(price);}price.textContent=chosen?money(chosen.display_price):'';price.hidden=!chosen;
      $$('[data-value]',form).forEach(e=>e.classList.toggle('store-swatch-selected',selects.some(s=>s.value===e.dataset.value)));
    };
    selects.forEach(s=>s.addEventListener('change',update));
    $$('[data-value]',form).forEach(swatch=>{swatch.setAttribute('tabindex','0');swatch.setAttribute('role','button');swatch.setAttribute('aria-label',swatch.getAttribute('title')||swatch.dataset.value);const select=()=>{const s=selects.find(s=>[...s.options].some(o=>o.value===swatch.dataset.value));if(s){s.value=swatch.dataset.value;s.dispatchEvent(new Event('change',{bubbles:true}));}};swatch.addEventListener('click',e=>{e.preventDefault();select();});swatch.addEventListener('keydown',e=>{if(['Enter',' '].includes(e.key)){e.preventDefault();select();}});});
    $('.reset_variations',form)?.addEventListener('click',e=>{e.preventDefault();selects.forEach(s=>s.value='');update();});update();
  });
  let cartBusy=false;
  const renderCart = data => {
    $$('.fusion-widget-cart-number,.awb-menu__cart-counter').forEach(e=>e.textContent=data.cart_count);
    const list=$('#store-cart-items');list.replaceChildren();
    (data.items || Object.values(data.cart||{})).forEach(item=>{
      const row=document.createElement('div');row.className='store-cart-item';
      const image=document.createElement('img');image.src=item.image;image.alt=item.name;
      const info=document.createElement('div');const title=document.createElement('h3');title.textContent=item.name;info.append(title);
      if(item.options && Object.keys(item.options).length){const options=document.createElement('small');options.textContent=Object.values(item.options).join(' / ');info.append(options);}
      const price=document.createElement('span');price.textContent=money(item.price);info.append(price);
      const qty=document.createElement('input');qty.type='number';qty.min='1';qty.max='99';qty.value=item.quantity;qty.setAttribute('aria-label','Quantity for '+item.name);
      qty.addEventListener('change',async()=>{qty.disabled=true;try{await json('/cart/update',{product_id:item.key||item.id,quantity:Number(qty.value)});await refreshCart();}catch(e){toast(e.message);}finally{qty.disabled=false;}});info.append(document.createElement('br'),qty);
      const remove=document.createElement('button');remove.textContent='×';remove.className='store-remove';remove.setAttribute('aria-label','Remove '+item.name);remove.addEventListener('click',async()=>{remove.disabled=true;try{await json('/cart/remove',{product_id:item.key||item.id});await refreshCart();}catch(e){toast(e.message);remove.disabled=false;}});
      row.append(image,info,remove);list.append(row);
    });
    if(!list.children.length)list.textContent='No products in the cart.';$('#store-cart-total').textContent=data.formatted_total;
  };
  const refreshCart=async()=>renderCart(await json('/cart/mini-cart'));
  $$('a[href="/cart"],a[href="/cart/"]').filter(a=>a.closest('.fusion-tb-header')).forEach(a=>a.addEventListener('click',async e=>{e.preventDefault();openPanel('#store-cart');try{await refreshCart();}catch(err){toast(err.message);}}));
  const addProduct=async (button,form) => {
    if(cartBusy)return;const selects=form?$$('select[name^=attribute]',form):[];
    if(selects.some(s=>!s.value)){toast('Please select your product options.');selects.find(s=>!s.value)?.focus();return;}
    const id=form?.dataset.product_id || button.dataset.product_id || $('input[name=product_id]',form||button.parentElement)?.value;
    const card=button.closest('.post-card,.store-product');const link=card?.querySelector('h5 a,h2 a');const slug=form?.dataset.slug || link?.getAttribute('href')?.split('/product/')[1]?.split('/')[0];
    cartBusy=true;button.setAttribute('aria-busy','true');
    try{const data=await json('/cart/add',{product_id:id,slug,quantity:Number($('input[name=quantity]',form||card||document)?.value||1),options:Object.fromEntries(selects.map(s=>[s.name,s.value]))});renderCart(data);openPanel('#store-cart');}catch(e){toast(e.message);}finally{cartBusy=false;button.removeAttribute('aria-busy');}
  };
  document.addEventListener('click',e=>{const button=e.target.closest('.add_to_cart_button,.single_add_to_cart_button,[data-store-add]');if(!button)return;e.preventDefault();addProduct(button,button.closest('form'));});
  $$('form.variations_form').forEach(form=>form.addEventListener('submit',e=>{e.preventDefault();addProduct($('.add_to_cart_button,.single_add_to_cart_button,[data-store-add]',form),form);}));
  refreshCart().catch(()=>{});
  let saved=[];try{saved=JSON.parse(localStorage.getItem('vegan-wishlist')||'[]');if(!Array.isArray(saved))saved=[];}catch{}
  // Product photos and titles lead to the same product detail page.
  $$('.post-card').forEach(card => {
    const title = $('h5 a,h2 a',card);
    const image = $('.fusion-woo-product-image img',card);
    if (!title || !image || image.closest('a')) return;
    const link = document.createElement('a');
    link.href = title.getAttribute('href');
    link.setAttribute('aria-label','View '+title.textContent.trim());
    image.replaceWith(link);
    link.append(image);
  });
  const renderWishlist=()=>{const container=$('#store-wishlist-items');container.replaceChildren();saved.forEach(p=>{const row=document.createElement('div');row.className='store-cart-item';const image=document.createElement('img');image.src=p.image;image.alt=p.name;const link=document.createElement('a');link.href=p.href;link.textContent=p.name;const remove=document.createElement('button');remove.className='store-remove';remove.textContent='×';remove.setAttribute('aria-label','Remove '+p.name+' from wishlist');remove.onclick=()=>{saved=saved.filter(x=>x.href!==p.href);saveWishlist();renderWishlist();};row.append(image,link,remove);container.append(row);});if(!saved.length)container.textContent='Your wishlist is empty.';};
  const saveWishlist=()=>{try{localStorage.setItem('vegan-wishlist',JSON.stringify(saved));}catch{toast('Wishlist storage is unavailable in this browser.');}$$('.store-wishlist').forEach(b=>b.setAttribute('aria-pressed',String(saved.some(p=>p.href===b.dataset.href))));};
  $$('.post-card,.db-product-card').forEach(card=>{const a=$('h5 a,h2 a',card),image=$('img',card);if(!a||!image)return;const button=document.createElement('button');button.className='store-wishlist';button.textContent='♡';button.type='button';button.dataset.href=a.getAttribute('href');button.setAttribute('aria-label','Save '+a.textContent+' to wishlist');button.setAttribute('aria-pressed',String(saved.some(p=>p.href===button.dataset.href)));const container=$('.fusion-column-wrapper',card)||card;container.style.position='relative';container.append(button);button.addEventListener('click',()=>{const p={href:button.dataset.href,name:a.textContent,image:image.src};saved=saved.some(x=>x.href===p.href)?saved.filter(x=>x.href!==p.href):[...saved,p];saveWishlist();toast('Wishlist updated.');});});
  const header=$('.fusion-tb-header .awb-menu__main-ul');if(header){const li=document.createElement('li');li.className='awb-menu__li';const button=document.createElement('button');button.type='button';button.textContent='♡';button.style.cssText='border:0;background:none;font-size:25px;cursor:pointer;color:inherit';button.setAttribute('aria-label','Open wishlist');button.onclick=()=>{renderWishlist();openPanel('#store-wishlist');};li.append(button);header.prepend(li);}
  $$('footer form,.fusion-tb-footer form').forEach(form=>{if(!$('input[type=email]',form))return;form.method='POST';form.addEventListener('submit',e=>{e.preventDefault();if(!form.reportValidity())return;const email=$('input[type=email]',form).value;try{localStorage.setItem('vegan-newsletter-email',email);}catch{}let status=$('.store-message',form);if(!status){status=document.createElement('p');status.className='store-message';status.setAttribute('role','status');form.append(status);}status.textContent='Email saved on this device. Mailing service is not connected yet.';});});
  $('#toTop')?.addEventListener('click',e=>{e.preventDefault();scrollTo({top:0,behavior:'smooth'});});
});
