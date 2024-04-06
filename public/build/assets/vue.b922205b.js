import{R as d,S as a,T as u,U as E,V as T,W as g,X as f}from"./@vue.04713aa0.js";/**
* vue v3.4.15
* (c) 2018-present Yuxi (Evan) You and Vue contributors
* @license MIT
**/const l=new WeakMap;function h(e){let n=l.get(e!=null?e:f);return n||(n=Object.create(null),l.set(e!=null?e:f,n)),n}function C(e,n){if(!a(e))if(e.nodeType)e=e.innerHTML;else return u;const o=e,t=h(n),s=t[o];if(s)return s;if(e[0]==="#"){const c=document.querySelector(e);e=c?c.innerHTML:""}const r=E({hoistStatic:!0,onError:void 0,onWarn:u},n);!r.isCustomElement&&typeof customElements!="undefined"&&(r.isCustomElement=c=>!!customElements.get(c));const{code:m}=T(e,r),i=new Function("Vue",m)(g);return i._rc=!0,t[o]=i}d(C);
