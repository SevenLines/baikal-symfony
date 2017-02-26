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

            if (product_id in this.state.products) {
                this.removeFromBasket(product_id, this.state.products)
            } else {
                this.addToBasket(product_id, 1, this.state.products)
            }

            return _.has(this.state.products, product_id);
        },
        addToBasket: function (product_id, count, basket) {
            if (basket === null) {
                basket = Cookies.getJSON('basket') || {};
                this.state.products = basket;
            }
            if (count === null) {
                count = 1
            }
            basket[product_id] = count;

            Cookies.set('basket', basket)
        },
        removeFromBasket: function (product_id, basket) {
            if (basket === null) {
                basket = Cookies.getJSON('basket') || {};
                this.state.products = basket;
            }
            delete basket[product_id];
            Cookies.set('basket', basket)
        },
        update: function () {
            this.state.products = Cookies.getJSON('basket') || {}
        }
    };

    Vue.component("product-row", {
        template: "#product-row",
        props: ['title', 'id', 'category_id', 'price', 'unit', 'in_basket'],
        data: function() {
            return {
                count: 1
            }
        },
        watch: {
            count: function (val, oldValue) {
                if (val == 0) {
                    sharedBasketStore.removeFromBasket(this.id, null);
                    this.count = 1;
                    this.in_basket = false;
                } else if (oldValue != 0) {
                    sharedBasketStore.addToBasket(this.id, val, null);
                    this.in_basket = true;
                }
                sharedBasketStore.update();
            }
        },
        computed: {
            isInBasket: function (product_id) {
                return this.in_basket;
            },
        },
        methods: {
            toggleBasket: function (product_id) {
                this.in_basket = sharedBasketStore.toggleToBasket(product_id);
            },
        },
    });

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