/**
 * Created by m on 23.02.17.
 */


(function () {
    var sharedBasketStore = {
        state: {
            products: {}
        },
        toggleToBasket: function (product_id) {
            this.state.products = Cookies.getJSON('basket') || {};

            if (_.has(this.state.products, product_id)) {
                this.removeFromBasket(this.state.products, product_id)
            } else {
                this.addToBasket(this.state.products, product_id)
            }
            Cookies.set('basket', this.state.products);

            return _.has(this.state.products, product_id);
        },
        addToBasket: function (basket, product_id) {
            basket[product_id] = 1;

        },
        removeFromBasket: function (basket, product_id) {
            delete basket[product_id];
        },
        update: function () {
            this.state.products = Cookies.getJSON('basket') || {}
        }
    };

    Vue.component("basket", {
        template: "#menu-basket-template",
        props: ['urls'],
        data: function () {
            return {
                updateBasket: false,
                sumMin: 0,
                sumMax: 0,
                realState: sharedBasketStore.state,
            }
        },
        created: function () {
            sharedBasketStore.update();
        },
        computed: {
            count: function () {
                return _.keys(this.realState.products).length
            },
            count_verbose: function () {
                count = this.count;

                var re_teen = /(11|12|13|14)$/;
                var re_decimal = /(2|3|4)$/;

                if (re_teen.test(String(count)))
                    value = "товаров";
                else if (re_decimal.test(String(count)))
                    value = "товара";
                else if (String(count).endsWith('1'))
                    value = "товар";
                else
                    value = "товаров";

                return value
            }
        },
        methods: {
            updateBasketAction: _.debounce(function (val, oldValue) {
                var me = this;
                me.updateBasket = true;
                this.$http.get(this.urls['basket_calc']).then(function (data) {
                    me.sumMin = data.body['sum_min'];
                    me.sumMax = data.body['sum_max'];
                    me.updateBasket = false
                }, function () {
                    me.updateBasket = false
                })
            }, 1000)
        },
        watch: {
            realState: {
                handler: function (val, oldValue) {
                    this.updateBasketAction(val, oldValue)
                },
                deep: true
            }
        }
    });

    window.sharedBasketStore = sharedBasketStore;
})();