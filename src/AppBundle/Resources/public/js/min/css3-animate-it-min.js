!function($){function a(){i=!1;for(var a=0;a<t.length;a++){var e=$(t[a]).filter(function(){return $(this).is(":appeared")});if(e.trigger("appear",[e]),r){var n=r.not(e);n.trigger("disappear",[n])}r=e}}var t=[],e=!1,i=!1,n={interval:250,force_process:!1},d=$(window),r;$.expr[":"].appeared=function(a){var t=$(a);if(!t.is(":visible"))return!1;var e=d.scrollLeft(),i=d.scrollTop(),n=t.offset(),r=n.left,o=n.top;return o+t.height()>=i&&o-(t.data("appear-top-offset")||0)<=i+d.height()&&r+t.width()>=e&&r-(t.data("appear-left-offset")||0)<=e+d.width()?!0:!1},$.fn.extend({appear:function(d){var r=$.extend({},n,d||{}),o=this.selector||this;if(!e){var s=function(){i||(i=!0,setTimeout(a,r.interval))};$(window).scroll(s).resize(s),e=!0}return r.force_process&&setTimeout(a,r.interval),t.push(o),$(o)}}),$.extend({force_appear:function(){return e?(a(),!0):!1}})}(jQuery),function($){"$:nomunge";function a(a){function e(){a?r.removeData(a):u&&delete t[u]}function n(){o.id=setTimeout(function(){o.fn()},c)}var d=this,r,o={},s=a?$.fn:$,f=arguments,l=4,u=f[1],c=f[2],p=f[3];if("string"!=typeof u&&(l--,u=a=0,c=f[1],p=f[2]),a?(r=d.eq(0),r.data(a,o=r.data(a)||{})):u&&(o=t[u]||(t[u]={})),o.id&&clearTimeout(o.id),delete o.id,p)o.fn=function(a){"string"==typeof p&&(p=s[p]),p.apply(d,i.call(f,l))!==!0||a?e():n()},n();else{if(o.fn)return void 0===c?e():o.fn(c===!1),!0;e()}}var t={},e="doTimeout",i=Array.prototype.slice;$[e]=function(){return a.apply(window,[0].concat(i.call(arguments)))},$.fn[e]=function(){var t=i.call(arguments),n=a.apply(this,[e+t[0]].concat(t));return"number"==typeof t[0]||"number"==typeof t[1]?this:n}}(jQuery),$(".animatedParent").appear(),$(".animatedClick").click(function(){var a=$(this).attr("data-target");if(void 0!=$(this).attr("data-sequence")){var t=$("."+a+":first").attr("data-id"),e=$("."+a+":last").attr("data-id"),i=t;$("."+a+"[data-id="+i+"]").hasClass("go")?($("."+a+"[data-id="+i+"]").addClass("goAway"),$("."+a+"[data-id="+i+"]").removeClass("go")):($("."+a+"[data-id="+i+"]").addClass("go"),$("."+a+"[data-id="+i+"]").removeClass("goAway")),i++,delay=Number($(this).attr("data-sequence")),$.doTimeout(delay,function(){return console.log(e),$("."+a+"[data-id="+i+"]").hasClass("go")?($("."+a+"[data-id="+i+"]").addClass("goAway"),$("."+a+"[data-id="+i+"]").removeClass("go")):($("."+a+"[data-id="+i+"]").addClass("go"),$("."+a+"[data-id="+i+"]").removeClass("goAway")),++i,e>=i?!0:void 0})}else $("."+a).hasClass("go")?($("."+a).addClass("goAway"),$("."+a).removeClass("go")):($("."+a).addClass("go"),$("."+a).removeClass("goAway"))}),$(document.body).on("appear",".animatedParent",function(a,t){var e=$(this).find(".animated"),i=$(this);if(void 0!=i.attr("data-sequence")){var n=$(this).find(".animated:first").attr("data-id"),d=n,r=$(this).find(".animated:last").attr("data-id");$(i).find(".animated[data-id="+d+"]").addClass("go"),d++,delay=Number(i.attr("data-sequence")),$.doTimeout(delay,function(){return $(i).find(".animated[data-id="+d+"]").addClass("go"),++d,r>=d?!0:void 0})}else e.addClass("go")}),$(document.body).on("disappear",".animatedParent",function(a,t){$(this).hasClass("animateOnce")||$(this).find(".animated").removeClass("go")}),$(window).load(function(){$.force_appear()});