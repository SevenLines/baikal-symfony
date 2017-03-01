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
            data: {
                active: false,
            },
            mounted: function() {
                var me = this;
                [document.body, document.getElementById("menu")].forEach(function(element) {
                    var mc = new Hammer(element, {
                        cssProps: {userSelect: 'text'}
                    });
                    mc.on("swiperight", function(ev) {
                        me.active = false;
                    });
                    mc.on("swipeleft", function(ev) {
                        me.active = true;
                    });
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