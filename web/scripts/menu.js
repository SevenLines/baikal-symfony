/**
 * Created by m on 22.02.17.
 */
(function () {
    window.MenuInterface = function (element_id) {

        Vue.directive('click-outside', {
            bind: function (el, binding, vnode) {
                this.event = function (event) {
                    if (!el.contains(event.target)) {
                        vnode.context[binding.expression](event);
                    }
                };
                document.body.addEventListener('click', this.event)
            },
            unbind: function (el) {
                document.body.removeEventListener('click', this.event)
            },
        });

        new Vue({
            el: element_id,
            data: function () {
                return {
                    'active': false
                }
            },
            mounted: function() {
                var mc = new Hammer(document.body);
                var me = this;
                mc.on("panright", function(ev) {
                    if (ev.deltaX > 50) {
                        me.active = false;
                    }
                });
                mc.on("panleft", function(ev) {
                    if (ev.deltaX < -50) {
                        me.active = true;
                    }
                });
            },
            methods: {
                showMenu: function () {
                    this.active = true;
                },
                hideMenu: function () {
                    this.active = false;
                }
            },
            computed: {
                activeClass: function () {
                    if (this.active) {
                        return "active"
                    }
                    return ""
                }
            },
        })
    }
})();