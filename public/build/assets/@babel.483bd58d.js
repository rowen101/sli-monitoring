function l(t){return l=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},l(t)}function s(t,e){(e==null||e>t.length)&&(e=t.length);for(var r=0,n=new Array(e);r<e;r++)n[r]=t[r];return n}function _(t,e){if(!!t){if(typeof t=="string")return s(t,e);var r=Object.prototype.toString.call(t).slice(8,-1);if(r==="Object"&&t.constructor&&(r=t.constructor.name),r==="Map"||r==="Set")return Array.from(t);if(r==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return s(t,e)}}function O(t,e){var r=typeof Symbol!="undefined"&&t[Symbol.iterator]||t["@@iterator"];if(!r){if(Array.isArray(t)||(r=_(t))||e&&t&&typeof t.length=="number"){r&&(t=r);var n=0,o=function(){};return{s:o,n:function(){return n>=t.length?{done:!0}:{done:!1,value:t[n++]}},e:function(c){throw c},f:o}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}var u=!0,f=!1,a;return{s:function(){r=r.call(t)},n:function(){var c=r.next();return u=c.done,c},e:function(c){f=!0,a=c},f:function(){try{!u&&r.return!=null&&r.return()}finally{if(f)throw a}}}}function d(t){if(t===void 0)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}function p(t,e){return p=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(n,o){return n.__proto__=o,n},p(t,e)}function P(t,e){if(typeof e!="function"&&e!==null)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),Object.defineProperty(t,"prototype",{writable:!1}),e&&p(t,e)}function y(t){return y=Object.setPrototypeOf?Object.getPrototypeOf.bind():function(r){return r.__proto__||Object.getPrototypeOf(r)},y(t)}function m(){try{var t=!Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],function(){}))}catch{}return(m=function(){return!!t})()}function g(t,e){if(e&&(l(e)==="object"||typeof e=="function"))return e;if(e!==void 0)throw new TypeError("Derived constructors may only return object or undefined");return d(t)}function S(t){var e=m();return function(){var n=y(t),o;if(e){var u=y(this).constructor;o=Reflect.construct(n,arguments,u)}else o=n.apply(this,arguments);return g(this,o)}}function j(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function w(t,e){if(l(t)!="object"||!t)return t;var r=t[Symbol.toPrimitive];if(r!==void 0){var n=r.call(t,e||"default");if(l(n)!="object")return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(t)}function h(t){var e=w(t,"string");return l(e)=="symbol"?e:String(e)}function b(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,h(n.key),n)}}function T(t,e,r){return e&&b(t.prototype,e),r&&b(t,r),Object.defineProperty(t,"prototype",{writable:!1}),t}function A(t,e,r){return e=h(e),e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}function v(t,e,r,n,o,u,f){try{var a=t[u](f),i=a.value}catch(c){r(c);return}a.done?e(i):Promise.resolve(i).then(n,o)}function E(t){return function(){var e=this,r=arguments;return new Promise(function(n,o){var u=t.apply(e,r);function f(i){v(u,n,o,f,a,"next",i)}function a(i){v(u,n,o,f,a,"throw",i)}f(void 0)})}}export{E as _,A as a,l as b,P as c,S as d,j as e,d as f,T as g,O as h};