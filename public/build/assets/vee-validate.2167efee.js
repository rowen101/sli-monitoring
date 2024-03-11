import{J as gn,K as ge,L as bn,M as On,I as j,r as ue,C as Me,u as M,n as ce,o as Vn,D as Pe,w as Oe,N as pn,O as A,a as $n,P as Wn,Q as qn,s as Hn,B as Gn,c as Ne}from"./@vue.4ae0f184.js";/**
  * vee-validate v4.12.5
  * (c) 2024 Abdelrahman Awad
  * @license MIT
  */function $(e){return typeof e=="function"}function Sn(e){return e==null}const Ve=e=>e!==null&&!!e&&typeof e=="object"&&!Array.isArray(e);function Xe(e){return Number(e)>=0}function Kn(e){const n=parseFloat(e);return isNaN(n)?e:n}function Yn(e){return typeof e=="object"&&e!==null}function Jn(e){return e==null?e===void 0?"[object Undefined]":"[object Null]":Object.prototype.toString.call(e)}function un(e){if(!Yn(e)||Jn(e)!=="[object Object]")return!1;if(Object.getPrototypeOf(e)===null)return!0;let n=e;for(;Object.getPrototypeOf(n)!==null;)n=Object.getPrototypeOf(n);return Object.getPrototypeOf(e)===n}function je(e,n){return Object.keys(n).forEach(t=>{if(un(n[t])&&un(e[t])){e[t]||(e[t]={}),je(e[t],n[t]);return}e[t]=n[t]}),e}function Ae(e){const n=e.split(".");if(!n.length)return"";let t=String(n[0]);for(let l=1;l<n.length;l++){if(Xe(n[l])){t+=`[${n[l]}]`;continue}t+=`.${n[l]}`}return t}const Qn={};function Xn(e){return Qn[e]}function on(e,n,t){typeof t.value=="object"&&(t.value=F(t.value)),!t.enumerable||t.get||t.set||!t.configurable||!t.writable||n==="__proto__"?Object.defineProperty(e,n,t):e[n]=t.value}function F(e){if(typeof e!="object")return e;var n=0,t,l,a,u=Object.prototype.toString.call(e);if(u==="[object Object]"?a=Object.create(e.__proto__||null):u==="[object Array]"?a=Array(e.length):u==="[object Set]"?(a=new Set,e.forEach(function(d){a.add(F(d))})):u==="[object Map]"?(a=new Map,e.forEach(function(d,v){a.set(F(v),F(d))})):u==="[object Date]"?a=new Date(+e):u==="[object RegExp]"?a=new RegExp(e.source,e.flags):u==="[object DataView]"?a=new e.constructor(F(e.buffer)):u==="[object ArrayBuffer]"?a=e.slice(0):u.slice(-6)==="Array]"&&(a=new e.constructor(e)),a){for(l=Object.getOwnPropertySymbols(e);n<l.length;n++)on(a,l[n],Object.getOwnPropertyDescriptor(e,l[n]));for(n=0,l=Object.getOwnPropertyNames(e);n<l.length;n++)Object.hasOwnProperty.call(a,t=l[n])&&a[t]===e[t]||on(a,t,Object.getOwnPropertyDescriptor(e,t))}return a||e}const xe=Symbol("vee-validate-form"),xn=Symbol("vee-validate-field-instance"),we=Symbol("Default empty value"),Zn=typeof window!="undefined";function Ke(e){return $(e)&&!!e.__locatorRef}function oe(e){return!!e&&$(e.parse)&&e.__type==="VVTypedSchema"}function Be(e){return!!e&&$(e.validate)}function Ee(e){return e==="checkbox"||e==="radio"}function et(e){return Ve(e)||Array.isArray(e)}function nt(e){return Array.isArray(e)?e.length===0:Ve(e)&&Object.keys(e).length===0}function Te(e){return/^\[.+\]$/i.test(e)}function tt(e){return _n(e)&&e.multiple}function _n(e){return e.tagName==="SELECT"}function rt(e,n){const t=![!1,null,void 0,0].includes(n.multiple)&&!Number.isNaN(n.multiple);return e==="select"&&"multiple"in n&&t}function it(e,n){return!rt(e,n)&&n.type!=="file"&&!Ee(n.type)}function An(e){return Ze(e)&&e.target&&"submit"in e.target}function Ze(e){return e?!!(typeof Event!="undefined"&&$(Event)&&e instanceof Event||e&&e.srcElement):!1}function sn(e,n){return n in e&&e[n]!==we}function G(e,n){if(e===n)return!0;if(e&&n&&typeof e=="object"&&typeof n=="object"){if(e.constructor!==n.constructor)return!1;var t,l,a;if(Array.isArray(e)){if(t=e.length,t!=n.length)return!1;for(l=t;l--!==0;)if(!G(e[l],n[l]))return!1;return!0}if(e instanceof Map&&n instanceof Map){if(e.size!==n.size)return!1;for(l of e.entries())if(!n.has(l[0]))return!1;for(l of e.entries())if(!G(l[1],n.get(l[0])))return!1;return!0}if(dn(e)&&dn(n))return!(e.size!==n.size||e.name!==n.name||e.lastModified!==n.lastModified||e.type!==n.type);if(e instanceof Set&&n instanceof Set){if(e.size!==n.size)return!1;for(l of e.entries())if(!n.has(l[0]))return!1;return!0}if(ArrayBuffer.isView(e)&&ArrayBuffer.isView(n)){if(t=e.length,t!=n.length)return!1;for(l=t;l--!==0;)if(e[l]!==n[l])return!1;return!0}if(e.constructor===RegExp)return e.source===n.source&&e.flags===n.flags;if(e.valueOf!==Object.prototype.valueOf)return e.valueOf()===n.valueOf();if(e.toString!==Object.prototype.toString)return e.toString()===n.toString();for(a=Object.keys(e),t=a.length,l=t;l--!==0;){var u=a[l];if(!G(e[u],n[u]))return!1}return!0}return e!==e&&n!==n}function dn(e){return Zn?e instanceof File:!1}function en(e){return Te(e)?e.replace(/\[|\]/gi,""):e}function Q(e,n,t){return e?Te(n)?e[en(n)]:(n||"").split(/\.|\[(\d+)\]/).filter(Boolean).reduce((a,u)=>et(a)&&u in a?a[u]:t,e):t}function me(e,n,t){if(Te(n)){e[en(n)]=t;return}const l=n.split(/\.|\[(\d+)\]/).filter(Boolean);let a=e;for(let u=0;u<l.length;u++){if(u===l.length-1){a[l[u]]=t;return}(!(l[u]in a)||Sn(a[l[u]]))&&(a[l[u]]=Xe(l[u+1])?[]:{}),a=a[l[u]]}}function He(e,n){if(Array.isArray(e)&&Xe(n)){e.splice(Number(n),1);return}Ve(e)&&delete e[n]}function cn(e,n){if(Te(n)){delete e[en(n)];return}const t=n.split(/\.|\[(\d+)\]/).filter(Boolean);let l=e;for(let u=0;u<t.length;u++){if(u===t.length-1){He(l,t[u]);break}if(!(t[u]in l)||Sn(l[t[u]]))break;l=l[t[u]]}const a=t.map((u,d)=>Q(e,t.slice(0,d).join(".")));for(let u=a.length-1;u>=0;u--)if(!!nt(a[u])){if(u===0){He(e,t[0]);continue}He(a[u-1],t[u-1])}}function ie(e){return Object.keys(e)}function jn(e,n=void 0){const t=Ne();return(t==null?void 0:t.provides[e])||Gn(e,n)}function fn(e,n,t){if(Array.isArray(e)){const l=[...e],a=l.findIndex(u=>G(u,n));return a>=0?l.splice(a,1):l.push(n),l}return G(e,n)?t:n}function vn(e,n=0){let t=null,l=[];return function(...a){return t&&clearTimeout(t),t=setTimeout(()=>{const u=e(...a);l.forEach(d=>d(u)),l=[]},n),new Promise(u=>l.push(u))}}function lt(e,n){return Ve(n)&&n.number?Kn(e):e}function Ye(e,n){let t;return async function(...a){const u=e(...a);t=u;const d=await u;return u!==t?d:(t=void 0,n(d,a))}}function Je(e){return Array.isArray(e)?e:e?[e]:[]}function Ie(e,n){const t={};for(const l in e)n.includes(l)||(t[l]=e[l]);return t}function at(e){let n=null,t=[];return function(...l){const a=ce(()=>{if(n!==a)return;const u=e(...l);t.forEach(d=>d(u)),t=[],n=null});return n=a,new Promise(u=>t.push(u))}}function En(e,n,t){return n.slots.default?typeof e=="string"||!e?n.slots.default(t()):{default:()=>{var l,a;return(a=(l=n.slots).default)===null||a===void 0?void 0:a.call(l,t())}}:n.slots.default}function Ge(e){if(Fn(e))return e._value}function Fn(e){return"_value"in e}function ut(e){return e.type==="number"||e.type==="range"?Number.isNaN(e.valueAsNumber)?e.value:e.valueAsNumber:e.value}function ke(e){if(!Ze(e))return e;const n=e.target;if(Ee(n.type)&&Fn(n))return Ge(n);if(n.type==="file"&&n.files){const t=Array.from(n.files);return n.multiple?t:t[0]}if(tt(n))return Array.from(n.options).filter(t=>t.selected&&!t.disabled).map(Ge);if(_n(n)){const t=Array.from(n.options).find(l=>l.selected);return t?Ge(t):n.value}return ut(n)}function In(e){const n={};return Object.defineProperty(n,"_$$isNormalized",{value:!0,writable:!1,enumerable:!1,configurable:!1}),e?Ve(e)&&e._$$isNormalized?e:Ve(e)?Object.keys(e).reduce((t,l)=>{const a=ot(e[l]);return e[l]!==!1&&(t[l]=hn(a)),t},n):typeof e!="string"?n:e.split("|").reduce((t,l)=>{const a=st(l);return a.name&&(t[a.name]=hn(a.params)),t},n):n}function ot(e){return e===!0?[]:Array.isArray(e)||Ve(e)?e:[e]}function hn(e){const n=t=>typeof t=="string"&&t[0]==="@"?dt(t.slice(1)):t;return Array.isArray(e)?e.map(n):e instanceof RegExp?[e]:Object.keys(e).reduce((t,l)=>(t[l]=n(e[l]),t),{})}const st=e=>{let n=[];const t=e.split(":")[0];return e.includes(":")&&(n=e.split(":").slice(1).join(":").split(",")),{name:t,params:n}};function dt(e){const n=t=>Q(t,e)||t[e];return n.__locatorRef=e,n}function ct(e){return Array.isArray(e)?e.filter(Ke):ie(e).filter(n=>Ke(e[n])).map(n=>e[n])}const ft={generateMessage:({field:e})=>`${e} is not valid.`,bails:!0,validateOnBlur:!0,validateOnChange:!0,validateOnInput:!1,validateOnModelUpdate:!0};let vt=Object.assign({},ft);const be=()=>vt;async function Cn(e,n,t={}){const l=t==null?void 0:t.bails,a={name:(t==null?void 0:t.name)||"{field}",rules:n,label:t==null?void 0:t.label,bails:l!=null?l:!0,formData:(t==null?void 0:t.values)||{}},d=(await ht(a,e)).errors;return{errors:d,valid:!d.length}}async function ht(e,n){if(oe(e.rules)||Be(e.rules))return yt(n,e.rules);if($(e.rules)||Array.isArray(e.rules)){const d={field:e.label||e.name,name:e.name,label:e.label,form:e.formData,value:n},v=Array.isArray(e.rules)?e.rules:[e.rules],s=v.length,c=[];for(let p=0;p<s;p++){const g=v[p],_=await g(n,d);if(!(typeof _!="string"&&!Array.isArray(_)&&_)){if(Array.isArray(_))c.push(..._);else{const P=typeof _=="string"?_:Pn(d);c.push(P)}if(e.bails)return{errors:c}}}return{errors:c}}const t=Object.assign(Object.assign({},e),{rules:In(e.rules)}),l=[],a=Object.keys(t.rules),u=a.length;for(let d=0;d<u;d++){const v=a[d],s=await gt(t,n,{name:v,params:t.rules[v]});if(s.error&&(l.push(s.error),e.bails))return{errors:l}}return{errors:l}}function mt(e){return!!e&&e.name==="ValidationError"}function Mn(e){return{__type:"VVTypedSchema",async parse(t){var l;try{return{output:await e.validate(t,{abortEarly:!1}),errors:[]}}catch(a){if(!mt(a))throw a;if(!(!((l=a.inner)===null||l===void 0)&&l.length)&&a.errors.length)return{errors:[{path:a.path,errors:a.errors}]};const u=a.inner.reduce((d,v)=>{const s=v.path||"";return d[s]||(d[s]={errors:[],path:s}),d[s].errors.push(...v.errors),d},{});return{errors:Object.values(u)}}}}}async function yt(e,n){const l=await(oe(n)?n:Mn(n)).parse(e),a=[];for(const u of l.errors)u.errors.length&&a.push(...u.errors);return{errors:a}}async function gt(e,n,t){const l=Xn(t.name);if(!l)throw new Error(`No such validator '${t.name}' exists.`);const a=bt(t.params,e.formData),u={field:e.label||e.name,name:e.name,label:e.label,value:n,form:e.formData,rule:Object.assign(Object.assign({},t),{params:a})},d=await l(n,a,u);return typeof d=="string"?{error:d}:{error:d?void 0:Pn(u)}}function Pn(e){const n=be().generateMessage;return n?n(e):"Field is invalid"}function bt(e,n){const t=l=>Ke(l)?l(n):l;return Array.isArray(e)?e.map(t):Object.keys(e).reduce((l,a)=>(l[a]=t(e[a]),l),{})}async function Ot(e,n){const l=await(oe(e)?e:Mn(e)).parse(F(n)),a={},u={};for(const d of l.errors){const v=d.errors,s=(d.path||"").replace(/\["(\d+)"\]/g,(c,p)=>`[${p}]`);a[s]={valid:!v.length,errors:v},v.length&&(u[s]=v[0])}return{valid:!l.errors.length,results:a,errors:u,values:l.value}}async function Vt(e,n,t){const a=ie(e).map(async c=>{var p,g,_;const O=(p=t==null?void 0:t.names)===null||p===void 0?void 0:p[c],P=await Cn(Q(n,c),e[c],{name:(O==null?void 0:O.name)||c,label:O==null?void 0:O.label,values:n,bails:(_=(g=t==null?void 0:t.bailsMap)===null||g===void 0?void 0:g[c])!==null&&_!==void 0?_:!0});return Object.assign(Object.assign({},P),{path:c})});let u=!0;const d=await Promise.all(a),v={},s={};for(const c of d)v[c.path]={valid:c.valid,errors:c.errors},c.valid||(u=!1,s[c.path]=c.errors[0]);return{valid:u,results:v,errors:s}}let mn=0;function pt(e,n){const{value:t,initialValue:l,setInitialValue:a}=St(e,n.modelValue,n.form);if(!n.form){let _=function(O){var P;"value"in O&&(t.value=O.value),"errors"in O&&c(O.errors),"touched"in O&&(g.touched=(P=O.touched)!==null&&P!==void 0?P:g.touched),"initialValue"in O&&a(O.initialValue)};const{errors:s,setErrors:c}=jt(),p=mn>=Number.MAX_SAFE_INTEGER?0:++mn,g=At(t,l,s,n.schema);return{id:p,path:e,value:t,initialValue:l,meta:g,flags:{pendingUnmount:{[p]:!1},pendingReset:!1},errors:s,setState:_}}const u=n.form.createPathState(e,{bails:n.bails,label:n.label,type:n.type,validate:n.validate,schema:n.schema}),d=j(()=>u.errors);function v(s){var c,p,g;"value"in s&&(t.value=s.value),"errors"in s&&((c=n.form)===null||c===void 0||c.setFieldError(M(e),s.errors)),"touched"in s&&((p=n.form)===null||p===void 0||p.setFieldTouched(M(e),(g=s.touched)!==null&&g!==void 0?g:!1)),"initialValue"in s&&a(s.initialValue)}return{id:Array.isArray(u.id)?u.id[u.id.length-1]:u.id,path:e,value:t,errors:d,meta:u,initialValue:l,flags:u.__flags,setState:v}}function St(e,n,t){const l=ue(M(n));function a(){return t?Q(t.initialValues.value,M(e),M(l)):M(l)}function u(c){if(!t){l.value=c;return}t.setFieldInitialValue(M(e),c,!0)}const d=j(a);if(!t)return{value:ue(a()),initialValue:d,setInitialValue:u};const v=_t(n,t,d,e);return t.stageInitialValue(M(e),v,!0),{value:j({get(){return Q(t.values,M(e))},set(c){t.setFieldValue(M(e),c,!1)}}),initialValue:d,setInitialValue:u}}function _t(e,n,t,l){return Pe(e)?M(e):e!==void 0?e:Q(n.values,M(l),M(t))}function At(e,n,t,l){var a,u;const d=(u=(a=l==null?void 0:l.describe)===null||a===void 0?void 0:a.call(l).required)!==null&&u!==void 0?u:!1,v=Me({touched:!1,pending:!1,valid:!0,required:d,validated:!!M(t).length,initialValue:j(()=>M(n)),dirty:j(()=>!G(M(e),M(n)))});return Oe(t,s=>{v.valid=!s.length},{immediate:!0,flush:"sync"}),v}function jt(){const e=ue([]);return{errors:e,setErrors:n=>{e.value=Je(n)}}}function Et(e,n,t){return Ee(t==null?void 0:t.type)?It(e,n,t):wn(e,n,t)}function wn(e,n,t){const{initialValue:l,validateOnMount:a,bails:u,type:d,checkedValue:v,label:s,validateOnValueUpdate:c,uncheckedValue:p,controlled:g,keepValueOnUnmount:_,syncVModel:O,form:P}=Ft(t),X=g?jn(xe):void 0,b=P||X,D=j(()=>Ae(A(e))),R=j(()=>{if(A(b==null?void 0:b.schema))return;const V=M(n);return Be(V)||oe(V)||$(V)||Array.isArray(V)?V:In(V)}),{id:W,value:x,initialValue:Z,meta:B,setState:le,errors:k,flags:U}=pt(D,{modelValue:l,form:b,bails:u,label:s,type:d,validate:R.value?ne:void 0,schema:oe(n)?n:void 0}),T=j(()=>k.value[0]);O&&Ct({value:x,prop:O,handleChange:S,shouldValidate:()=>c&&!U.pendingReset});const se=(h,V=!1)=>{B.touched=!0,V&&ee()};async function fe(h){var V,C;if(b!=null&&b.validateSchema){const{results:I}=await b.validateSchema(h);return(V=I[A(D)])!==null&&V!==void 0?V:{valid:!0,errors:[]}}return R.value?Cn(x.value,R.value,{name:A(D),label:A(s),values:(C=b==null?void 0:b.values)!==null&&C!==void 0?C:{},bails:u}):{valid:!0,errors:[]}}const ee=Ye(async()=>(B.pending=!0,B.validated=!0,fe("validated-only")),h=>(U.pendingUnmount[q.id]||(le({errors:h.errors}),B.pending=!1,B.valid=h.valid),h)),K=Ye(async()=>fe("silent"),h=>(B.valid=h.valid,h));function ne(h){return(h==null?void 0:h.mode)==="silent"?K():ee()}function S(h,V=!0){const C=ke(h);Se(C,V)}Vn(()=>{if(a)return ee();(!b||!b.validateSchema)&&K()});function Y(h){B.touched=h}function te(h){var V;const C=h&&"value"in h?h.value:Z.value;le({value:F(C),initialValue:F(C),touched:(V=h==null?void 0:h.touched)!==null&&V!==void 0?V:!1,errors:(h==null?void 0:h.errors)||[]}),B.pending=!1,B.validated=!1,K()}const he=Ne();function Se(h,V=!0){x.value=he&&O?lt(h,he.props.modelModifiers):h,(V?ee:K)()}function Fe(h){le({errors:Array.isArray(h)?h:[h]})}const nn=j({get(){return x.value},set(h){Se(h,c)}}),q={id:W,name:D,label:s,value:nn,meta:B,errors:k,errorMessage:T,type:d,checkedValue:v,uncheckedValue:p,bails:u,keepValueOnUnmount:_,resetField:te,handleReset:()=>te(),validate:ne,handleChange:S,handleBlur:se,setState:le,setTouched:Y,setErrors:Fe,setValue:Se};if(pn(xn,q),Pe(n)&&typeof M(n)!="function"&&Oe(n,(h,V)=>{G(h,V)||(B.validated?ee():K())},{deep:!0}),!b)return q;const Re=j(()=>{const h=R.value;return!h||$(h)||Be(h)||oe(h)||Array.isArray(h)?{}:Object.keys(h).reduce((V,C)=>{const I=ct(h[C]).map(ve=>ve.__locatorRef).reduce((ve,de)=>{const re=Q(b.values,de)||b.values[de];return re!==void 0&&(ve[de]=re),ve},{});return Object.assign(V,I),V},{})});return Oe(Re,(h,V)=>{if(!Object.keys(h).length)return;!G(h,V)&&(B.validated?ee():K())}),qn(()=>{var h;const V=(h=A(q.keepValueOnUnmount))!==null&&h!==void 0?h:A(b.keepValuesOnUnmount),C=A(D);if(V||!b||U.pendingUnmount[q.id]){b==null||b.removePathState(C,W);return}U.pendingUnmount[q.id]=!0;const I=b.getPathState(C);if(!!(Array.isArray(I==null?void 0:I.id)&&(I==null?void 0:I.multiple)?I==null?void 0:I.id.includes(q.id):(I==null?void 0:I.id)===q.id)){if((I==null?void 0:I.multiple)&&Array.isArray(I.value)){const de=I.value.findIndex(re=>G(re,A(q.checkedValue)));if(de>-1){const re=[...I.value];re.splice(de,1),b.setFieldValue(C,re)}Array.isArray(I.id)&&I.id.splice(I.id.indexOf(q.id),1)}else b.unsetPathValue(A(D));b.removePathState(C,W)}}),q}function Ft(e){const n=()=>({initialValue:void 0,validateOnMount:!1,bails:!0,label:void 0,validateOnValueUpdate:!0,keepValueOnUnmount:void 0,syncVModel:!1,controlled:!0}),t=!!(e!=null&&e.syncVModel),l=typeof(e==null?void 0:e.syncVModel)=="string"?e.syncVModel:(e==null?void 0:e.modelPropName)||"modelValue",a=t&&!("initialValue"in(e||{}))?Qe(Ne(),l):e==null?void 0:e.initialValue;if(!e)return Object.assign(Object.assign({},n()),{initialValue:a});const u="valueProp"in e?e.valueProp:e.checkedValue,d="standalone"in e?!e.standalone:e.controlled,v=(e==null?void 0:e.modelPropName)||(e==null?void 0:e.syncVModel)||!1;return Object.assign(Object.assign(Object.assign({},n()),e||{}),{initialValue:a,controlled:d!=null?d:!0,checkedValue:u,syncVModel:v})}function It(e,n,t){const l=t!=null&&t.standalone?void 0:jn(xe),a=t==null?void 0:t.checkedValue,u=t==null?void 0:t.uncheckedValue;function d(v){const s=v.handleChange,c=j(()=>{const g=A(v.value),_=A(a);return Array.isArray(g)?g.findIndex(O=>G(O,_))>=0:G(_,g)});function p(g,_=!0){var O,P;if(c.value===((O=g==null?void 0:g.target)===null||O===void 0?void 0:O.checked)){_&&v.validate();return}const X=A(e),b=l==null?void 0:l.getPathState(X),D=ke(g);let R=(P=A(a))!==null&&P!==void 0?P:D;l&&(b==null?void 0:b.multiple)&&b.type==="checkbox"?R=fn(Q(l.values,X)||[],R,void 0):(t==null?void 0:t.type)==="checkbox"&&(R=fn(A(v.value),R,A(u))),s(R,_)}return Object.assign(Object.assign({},v),{checked:c,checkedValue:a,uncheckedValue:u,handleChange:p})}return d(wn(e,n,t))}function Ct({prop:e,value:n,handleChange:t,shouldValidate:l}){const a=Ne();if(!a||!e)return;const u=typeof e=="string"?e:"modelValue",d=`update:${u}`;u in a.props&&(Oe(n,v=>{G(v,Qe(a,u))||a.emit(d,v)}),Oe(()=>Qe(a,u),v=>{if(v===we&&n.value===void 0)return;const s=v===we?void 0:v;G(s,n.value)||t(s,l())}))}function Qe(e,n){if(!!e)return e.props[n]}const Mt=gn({name:"Field",inheritAttrs:!1,props:{as:{type:[String,Object],default:void 0},name:{type:String,required:!0},rules:{type:[Object,String,Function],default:void 0},validateOnMount:{type:Boolean,default:!1},validateOnBlur:{type:Boolean,default:void 0},validateOnChange:{type:Boolean,default:void 0},validateOnInput:{type:Boolean,default:void 0},validateOnModelUpdate:{type:Boolean,default:void 0},bails:{type:Boolean,default:()=>be().bails},label:{type:String,default:void 0},uncheckedValue:{type:null,default:void 0},modelValue:{type:null,default:we},modelModifiers:{type:null,default:()=>({})},"onUpdate:modelValue":{type:null,default:void 0},standalone:{type:Boolean,default:!1},keepValue:{type:Boolean,default:void 0}},setup(e,n){const t=ge(e,"rules"),l=ge(e,"name"),a=ge(e,"label"),u=ge(e,"uncheckedValue"),d=ge(e,"keepValue"),{errors:v,value:s,errorMessage:c,validate:p,handleChange:g,handleBlur:_,setTouched:O,resetField:P,handleReset:X,meta:b,checked:D,setErrors:R}=Et(l,t,{validateOnMount:e.validateOnMount,bails:e.bails,standalone:e.standalone,type:n.attrs.type,initialValue:wt(e,n),checkedValue:n.attrs.value,uncheckedValue:u,label:a,validateOnValueUpdate:e.validateOnModelUpdate,keepValueOnUnmount:d,syncVModel:!0}),W=function(U,T=!0){g(U,T)},x=j(()=>{const{validateOnInput:k,validateOnChange:U,validateOnBlur:T,validateOnModelUpdate:se}=Pt(e);function fe(S){_(S,T),$(n.attrs.onBlur)&&n.attrs.onBlur(S)}function ee(S){W(S,k),$(n.attrs.onInput)&&n.attrs.onInput(S)}function K(S){W(S,U),$(n.attrs.onChange)&&n.attrs.onChange(S)}const ne={name:e.name,onBlur:fe,onInput:ee,onChange:K};return ne["onUpdate:modelValue"]=S=>W(S,se),ne}),Z=j(()=>{const k=Object.assign({},x.value);Ee(n.attrs.type)&&D&&(k.checked=D.value);const U=yn(e,n);return it(U,n.attrs)&&(k.value=s.value),k}),B=j(()=>Object.assign(Object.assign({},x.value),{modelValue:s.value}));function le(){return{field:Z.value,componentField:B.value,value:s.value,meta:b,errors:v.value,errorMessage:c.value,validate:p,resetField:P,handleChange:W,handleInput:k=>W(k,!1),handleReset:X,handleBlur:x.value.onBlur,setTouched:O,setErrors:R}}return n.expose({value:s,meta:b,errors:v,errorMessage:c,setErrors:R,setTouched:O,reset:P,validate:p,handleChange:g}),()=>{const k=bn(yn(e,n)),U=En(k,n,le);return k?On(k,Object.assign(Object.assign({},n.attrs),Z.value),U):U}}});function yn(e,n){let t=e.as||"";return!e.as&&!n.slots.default&&(t="input"),t}function Pt(e){var n,t,l,a;const{validateOnInput:u,validateOnChange:d,validateOnBlur:v,validateOnModelUpdate:s}=be();return{validateOnInput:(n=e.validateOnInput)!==null&&n!==void 0?n:u,validateOnChange:(t=e.validateOnChange)!==null&&t!==void 0?t:d,validateOnBlur:(l=e.validateOnBlur)!==null&&l!==void 0?l:v,validateOnModelUpdate:(a=e.validateOnModelUpdate)!==null&&a!==void 0?a:s}}function wt(e,n){return Ee(n.attrs.type)?sn(e,"modelValue")?e.modelValue:void 0:sn(e,"modelValue")?e.modelValue:n.attrs.value}const zt=Mt;let Bt=0;const Ce=["bails","fieldsCount","id","multiple","type","validate"];function Bn(e){const n=Object.assign({},A((e==null?void 0:e.initialValues)||{})),t=M(e==null?void 0:e.validationSchema);return t&&oe(t)&&$(t.cast)?F(t.cast(n)||{}):F(n)}function kt(e){var n;const t=Bt++;let l=0;const a=ue(!1),u=ue(!1),d=ue(0),v=[],s=Me(Bn(e)),c=ue([]),p=ue({}),g=ue({}),_=at(()=>{g.value=c.value.reduce((i,r)=>(i[Ae(A(r.path))]=r,i),{})});function O(i,r){const o=S(i);if(!o){typeof i=="string"&&(p.value[Ae(i)]=Je(r));return}if(typeof i=="string"){const f=Ae(i);p.value[f]&&delete p.value[f]}o.errors=Je(r),o.valid=!o.errors.length}function P(i){ie(i).forEach(r=>{O(r,i[r])})}e!=null&&e.initialErrors&&P(e.initialErrors);const X=j(()=>{const i=c.value.reduce((r,o)=>(o.errors.length&&(r[o.path]=o.errors),r),{});return Object.assign(Object.assign({},p.value),i)}),b=j(()=>ie(X.value).reduce((i,r)=>{const o=X.value[r];return o!=null&&o.length&&(i[r]=o[0]),i},{})),D=j(()=>c.value.reduce((i,r)=>(i[r.path]={name:r.path||"",label:r.label||""},i),{})),R=j(()=>c.value.reduce((i,r)=>{var o;return i[r.path]=(o=r.bails)!==null&&o!==void 0?o:!0,i},{})),W=Object.assign({},(e==null?void 0:e.initialErrors)||{}),x=(n=e==null?void 0:e.keepValuesOnUnmount)!==null&&n!==void 0?n:!1,{initialValues:Z,originalInitialValues:B,setInitialValues:le}=Tt(c,s,e),k=Nt(c,s,B,b),U=j(()=>c.value.reduce((i,r)=>{const o=Q(s,r.path);return me(i,r.path,o),i},{})),T=e==null?void 0:e.validationSchema;function se(i,r){var o,f;const y=j(()=>Q(Z.value,A(i))),m=g.value[A(i)],E=(r==null?void 0:r.type)==="checkbox"||(r==null?void 0:r.type)==="radio";if(m&&E){m.multiple=!0;const ae=l++;return Array.isArray(m.id)?m.id.push(ae):m.id=[m.id,ae],m.fieldsCount++,m.__flags.pendingUnmount[ae]=!1,m}const z=j(()=>Q(s,A(i))),N=A(i),H=te.findIndex(ae=>ae===N);H!==-1&&te.splice(H,1);const w=j(()=>{var ae,_e,$e,an,We,qe;return oe(T)?($e=(_e=(ae=T).describe)===null||_e===void 0?void 0:_e.call(ae,A(i)).required)!==null&&$e!==void 0?$e:!1:oe(r==null?void 0:r.schema)&&(qe=(We=(an=r==null?void 0:r.schema).describe)===null||We===void 0?void 0:We.call(an).required)!==null&&qe!==void 0?qe:!1}),L=l++,J=Me({id:L,path:i,touched:!1,pending:!1,valid:!0,validated:!!(!((o=W[N])===null||o===void 0)&&o.length),required:w,initialValue:y,errors:Hn([]),bails:(f=r==null?void 0:r.bails)!==null&&f!==void 0?f:!1,label:r==null?void 0:r.label,type:(r==null?void 0:r.type)||"default",value:z,multiple:!1,__flags:{pendingUnmount:{[L]:!1},pendingReset:!1},fieldsCount:1,validate:r==null?void 0:r.validate,dirty:j(()=>!G(M(z),M(y)))});return c.value.push(J),g.value[N]=J,_(),b.value[N]&&!W[N]&&ce(()=>{ye(N,{mode:"silent"})}),Pe(i)&&Oe(i,ae=>{_();const _e=F(z.value);g.value[ae]=J,ce(()=>{me(s,ae,_e)})}),J}const fe=vn(ln,5),ee=vn(ln,5),K=Ye(async i=>await(i==="silent"?fe():ee()),(i,[r])=>{const o=ie(V.errorBag.value),y=[...new Set([...ie(i.results),...c.value.map(m=>m.path),...o])].sort().reduce((m,E)=>{var z;const N=E,H=S(N)||Y(N),w=((z=i.results[N])===null||z===void 0?void 0:z.errors)||[],L=A(H==null?void 0:H.path)||N,J=Rt({errors:w,valid:!w.length},m.results[L]);return m.results[L]=J,J.valid||(m.errors[L]=J.errors[0]),H&&p.value[L]&&delete p.value[L],H?(H.valid=J.valid,r==="silent"||r==="validated-only"&&!H.validated||O(H,J.errors),m):(O(L,w),m)},{valid:i.valid,results:{},errors:{}});return i.values&&(y.values=i.values),y});function ne(i){c.value.forEach(i)}function S(i){const r=typeof i=="string"?Ae(i):i;return typeof r=="string"?g.value[r]:r}function Y(i){return c.value.filter(o=>i.startsWith(o.path)).reduce((o,f)=>o?f.path.length>o.path.length?f:o:f,void 0)}let te=[],he;function Se(i){return te.push(i),he||(he=ce(()=>{[...te].sort().reverse().forEach(o=>{cn(s,o)}),te=[],he=null})),he}function Fe(i){return function(o,f){return function(m){return m instanceof Event&&(m.preventDefault(),m.stopPropagation()),ne(E=>E.touched=!0),a.value=!0,d.value++,pe().then(E=>{const z=F(s);if(E.valid&&typeof o=="function"){const N=F(U.value);let H=i?N:z;return E.values&&(H=E.values),o(H,{evt:m,controlledValues:N,setErrors:P,setFieldError:O,setTouched:Ue,setFieldTouched:re,setValues:ve,setFieldValue:C,resetForm:De,resetField:tn})}!E.valid&&typeof f=="function"&&f({values:z,evt:m,errors:E.errors,results:E.results})}).then(E=>(a.value=!1,E),E=>{throw a.value=!1,E})}}}const q=Fe(!1);q.withControlled=Fe(!0);function Re(i,r){const o=c.value.findIndex(y=>y.path===i&&(Array.isArray(y.id)?y.id.includes(r):y.id===r)),f=c.value[o];if(!(o===-1||!f)){if(ce(()=>{ye(i,{mode:"silent",warn:!1})}),f.multiple&&f.fieldsCount&&f.fieldsCount--,Array.isArray(f.id)){const y=f.id.indexOf(r);y>=0&&f.id.splice(y,1),delete f.__flags.pendingUnmount[r]}(!f.multiple||f.fieldsCount<=0)&&(c.value.splice(o,1),rn(i),_(),delete g.value[i])}}function h(i){ie(g.value).forEach(r=>{r.startsWith(i)&&delete g.value[r]}),c.value=c.value.filter(r=>!r.path.startsWith(i)),ce(()=>{_()})}const V={formId:t,values:s,controlledValues:U,errorBag:X,errors:b,schema:T,submitCount:d,meta:k,isSubmitting:a,isValidating:u,fieldArrays:v,keepValuesOnUnmount:x,validateSchema:M(T)?K:void 0,validate:pe,setFieldError:O,validateField:ye,setFieldValue:C,setValues:ve,setErrors:P,setFieldTouched:re,setTouched:Ue,resetForm:De,resetField:tn,handleSubmit:q,useFieldModel:Dn,defineInputBinds:zn,defineComponentBinds:Ln,defineField:Le,stageInitialValue:Rn,unsetInitialValue:rn,setFieldInitialValue:ze,createPathState:se,getPathState:S,unsetPathValue:Se,removePathState:Re,initialValues:Z,getAllPathStates:()=>c.value,destroyPath:h,isFieldTouched:kn,isFieldDirty:Nn,isFieldValid:Tn};function C(i,r,o=!0){const f=F(r),y=typeof i=="string"?i:i.path;S(y)||se(y),me(s,y,f),o&&ye(y)}function I(i,r=!0){ie(s).forEach(o=>{delete s[o]}),ie(i).forEach(o=>{C(o,i[o],!1)}),r&&pe()}function ve(i,r=!0){je(s,i),v.forEach(o=>o&&o.reset()),r&&pe()}function de(i,r){const o=S(A(i))||se(i);return j({get(){return o.value},set(f){var y;const m=A(i);C(m,f,(y=A(r))!==null&&y!==void 0?y:!1)}})}function re(i,r){const o=S(i);o&&(o.touched=r)}function kn(i){const r=S(i);return r?r.touched:c.value.filter(o=>o.path.startsWith(i)).some(o=>o.touched)}function Nn(i){const r=S(i);return r?r.dirty:c.value.filter(o=>o.path.startsWith(i)).some(o=>o.dirty)}function Tn(i){const r=S(i);return r?r.valid:c.value.filter(o=>o.path.startsWith(i)).every(o=>o.valid)}function Ue(i){if(typeof i=="boolean"){ne(r=>{r.touched=i});return}ie(i).forEach(r=>{re(r,!!i[r])})}function tn(i,r){var o;const f=r&&"value"in r?r.value:Q(Z.value,i),y=S(i);y&&(y.__flags.pendingReset=!0),ze(i,F(f),!0),C(i,f,!1),re(i,(o=r==null?void 0:r.touched)!==null&&o!==void 0?o:!1),O(i,(r==null?void 0:r.errors)||[]),ce(()=>{y&&(y.__flags.pendingReset=!1)})}function De(i,r){let o=F(i!=null&&i.values?i.values:B.value);o=r!=null&&r.force?o:je(B.value,o),o=oe(T)&&$(T.cast)?T.cast(o):o,le(o),ne(f=>{var y;f.__flags.pendingReset=!0,f.validated=!1,f.touched=((y=i==null?void 0:i.touched)===null||y===void 0?void 0:y[f.path])||!1,C(f.path,Q(o,f.path),!1),O(f.path,void 0)}),r!=null&&r.force?I(o,!1):ve(o,!1),P((i==null?void 0:i.errors)||{}),d.value=(i==null?void 0:i.submitCount)||0,ce(()=>{pe({mode:"silent"}),ne(f=>{f.__flags.pendingReset=!1})})}async function pe(i){const r=(i==null?void 0:i.mode)||"force";if(r==="force"&&ne(m=>m.validated=!0),V.validateSchema)return V.validateSchema(r);u.value=!0;const o=await Promise.all(c.value.map(m=>m.validate?m.validate(i).then(E=>({key:m.path,valid:E.valid,errors:E.errors})):Promise.resolve({key:m.path,valid:!0,errors:[]})));u.value=!1;const f={},y={};for(const m of o)f[m.key]={valid:m.valid,errors:m.errors},m.errors.length&&(y[m.key]=m.errors[0]);return{valid:o.every(m=>m.valid),results:f,errors:y}}async function ye(i,r){var o;const f=S(i);if(f&&(r==null?void 0:r.mode)!=="silent"&&(f.validated=!0),T){const{results:y}=await K((r==null?void 0:r.mode)||"validated-only");return y[i]||{errors:[],valid:!0}}return f!=null&&f.validate?f.validate(r):(!f&&(o=r==null?void 0:r.warn),Promise.resolve({errors:[],valid:!0}))}function rn(i){cn(Z.value,i)}function Rn(i,r,o=!1){ze(i,r),me(s,i,r),o&&!(e!=null&&e.initialValues)&&me(B.value,i,F(r))}function ze(i,r,o=!1){me(Z.value,i,F(r)),o&&me(B.value,i,F(r))}async function ln(){const i=M(T);if(!i)return{valid:!0,results:{},errors:{}};u.value=!0;const r=Be(i)||oe(i)?await Ot(i,s):await Vt(i,s,{names:D.value,bailsMap:R.value});return u.value=!1,r}const Un=q((i,{evt:r})=>{An(r)&&r.target.submit()});Vn(()=>{if(e!=null&&e.initialErrors&&P(e.initialErrors),e!=null&&e.initialTouched&&Ue(e.initialTouched),e!=null&&e.validateOnMount){pe();return}V.validateSchema&&V.validateSchema("silent")}),Pe(T)&&Oe(T,()=>{var i;(i=V.validateSchema)===null||i===void 0||i.call(V,"validated-only")}),pn(xe,V);function Le(i,r){const o=$(r)||r==null?void 0:r.label,f=S(A(i))||se(i,{label:o}),y=()=>$(r)?r(Ie(f,Ce)):r||{};function m(){var w;f.touched=!0,((w=y().validateOnBlur)!==null&&w!==void 0?w:be().validateOnBlur)&&ye(f.path)}function E(){var w;((w=y().validateOnInput)!==null&&w!==void 0?w:be().validateOnInput)&&ce(()=>{ye(f.path)})}function z(){var w;((w=y().validateOnChange)!==null&&w!==void 0?w:be().validateOnChange)&&ce(()=>{ye(f.path)})}const N=j(()=>{const w={onChange:z,onInput:E,onBlur:m};return $(r)?Object.assign(Object.assign({},w),r(Ie(f,Ce)).props||{}):r!=null&&r.props?Object.assign(Object.assign({},w),r.props(Ie(f,Ce))):w});return[de(i,()=>{var w,L,J;return(J=(w=y().validateOnModelUpdate)!==null&&w!==void 0?w:(L=be())===null||L===void 0?void 0:L.validateOnModelUpdate)!==null&&J!==void 0?J:!0}),N]}function Dn(i){return Array.isArray(i)?i.map(r=>de(r,!0)):de(i)}function zn(i,r){const[o,f]=Le(i,r);function y(){f.value.onBlur()}function m(z){const N=ke(z);C(A(i),N,!1),f.value.onInput()}function E(z){const N=ke(z);C(A(i),N,!1),f.value.onChange()}return j(()=>Object.assign(Object.assign({},f.value),{onBlur:y,onInput:m,onChange:E,value:o.value}))}function Ln(i,r){const[o,f]=Le(i,r),y=S(A(i));function m(E){o.value=E}return j(()=>{const E=$(r)?r(Ie(y,Ce)):r||{};return Object.assign({[E.model||"modelValue"]:o.value,[`onUpdate:${E.model||"modelValue"}`]:m},f.value)})}return Object.assign(Object.assign({},V),{values:$n(s),handleReset:()=>De(),submitForm:Un})}function Nt(e,n,t,l){const a={touched:"some",pending:"some",valid:"every"},u=j(()=>!G(n,M(t)));function d(){const s=e.value;return ie(a).reduce((c,p)=>{const g=a[p];return c[p]=s[g](_=>_[p]),c},{})}const v=Me(d());return Wn(()=>{const s=d();v.touched=s.touched,v.valid=s.valid,v.pending=s.pending}),j(()=>Object.assign(Object.assign({initialValues:M(t)},v),{valid:v.valid&&!ie(l.value).length,dirty:u.value}))}function Tt(e,n,t){const l=Bn(t),a=ue(l),u=ue(F(l));function d(v,s=!1){a.value=je(F(a.value)||{},F(v)),u.value=je(F(u.value)||{},F(v)),s&&e.value.forEach(c=>{if(c.touched)return;const g=Q(a.value,c.path);me(n,c.path,F(g))})}return{initialValues:a,originalInitialValues:u,setInitialValues:d}}function Rt(e,n){return n?{valid:e.valid&&n.valid,errors:[...e.errors,...n.errors]}:e}const Ut=gn({name:"Form",inheritAttrs:!1,props:{as:{type:null,default:"form"},validationSchema:{type:Object,default:void 0},initialValues:{type:Object,default:void 0},initialErrors:{type:Object,default:void 0},initialTouched:{type:Object,default:void 0},validateOnMount:{type:Boolean,default:!1},onSubmit:{type:Function,default:void 0},onInvalidSubmit:{type:Function,default:void 0},keepValues:{type:Boolean,default:!1}},setup(e,n){const t=ge(e,"validationSchema"),l=ge(e,"keepValues"),{errors:a,errorBag:u,values:d,meta:v,isSubmitting:s,isValidating:c,submitCount:p,controlledValues:g,validate:_,validateField:O,handleReset:P,resetForm:X,handleSubmit:b,setErrors:D,setFieldError:R,setFieldValue:W,setValues:x,setFieldTouched:Z,setTouched:B,resetField:le}=kt({validationSchema:t.value?t:void 0,initialValues:e.initialValues,initialErrors:e.initialErrors,initialTouched:e.initialTouched,validateOnMount:e.validateOnMount,keepValuesOnUnmount:l}),k=b((S,{evt:Y})=>{An(Y)&&Y.target.submit()},e.onInvalidSubmit),U=e.onSubmit?b(e.onSubmit,e.onInvalidSubmit):k;function T(S){Ze(S)&&S.preventDefault(),P(),typeof n.attrs.onReset=="function"&&n.attrs.onReset()}function se(S,Y){return b(typeof S=="function"&&!Y?S:Y,e.onInvalidSubmit)(S)}function fe(){return F(d)}function ee(){return F(v.value)}function K(){return F(a.value)}function ne(){return{meta:v.value,errors:a.value,errorBag:u.value,values:d,isSubmitting:s.value,isValidating:c.value,submitCount:p.value,controlledValues:g.value,validate:_,validateField:O,handleSubmit:se,handleReset:P,submitForm:k,setErrors:D,setFieldError:R,setFieldValue:W,setValues:x,setFieldTouched:Z,setTouched:B,resetForm:X,resetField:le,getValues:fe,getMeta:ee,getErrors:K}}return n.expose({setFieldError:R,setErrors:D,setFieldValue:W,setValues:x,setFieldTouched:Z,setTouched:B,resetForm:X,validate:_,validateField:O,resetField:le,getValues:fe,getMeta:ee,getErrors:K,values:d,meta:v,errors:a}),function(){const Y=e.as==="form"?e.as:e.as?bn(e.as):null,te=En(Y,n,ne);return Y?On(Y,Object.assign(Object.assign(Object.assign({},Y==="form"?{novalidate:!0}:{}),n.attrs),{onSubmit:U,onReset:T}),te):te}}}),Lt=Ut;export{Lt as F,zt as a};
