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
                el.addEventListener('click', this.stopProp);
                document.body.addEventListener('click', this.event)
            },
            unbind: function (el) {
                el.removeEventListener('click', this.stopProp);
                document.body.removeEventListener('click', this.event)
            },
            stopProp: function (event) {
                event.stopPropagation()
            }
        });

        new Vue({
            el: element_id,
            data: function () {
                return {
                    'active': false
                }
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