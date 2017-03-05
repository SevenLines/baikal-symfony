/**
 * Created by m on 23.02.17.
 */
(function () {
    var updateBasketActionCompleteEvent = document.createEvent('Event');
    var updateBasketActionCompleteFailEvent = document.createEvent('Event');
    var beforeUpdateBasketActionCompleteEvent = document.createEvent('Event');
    updateBasketActionCompleteEvent.initEvent('updateBasketActionComplete', true, true);
    updateBasketActionCompleteFailEvent.initEvent('updateBasketActionCompleteFail', true, true);
    beforeUpdateBasketActionCompleteEvent.initEvent('beforeUpdateBasketActionComplete', true, true);

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
        },
        updateBasketAction: _.debounce(function (url, val, oldValue) {
            var me = this;
            document.dispatchEvent(beforeUpdateBasketActionCompleteEvent);
            Vue.http.get(url).then(function (data) {
                me.updateBasketActionData = data.body;
                document.dispatchEvent(updateBasketActionCompleteEvent);
            }, function () {
                document.dispatchEvent(updateBasketActionCompleteFailEvent);
            })
        }, 1000),
    };

    Vue.component("product-row", {
        template: "#product-row",
        props: ['title', 'id', 'category_id', 'price', 'unit', 'in_basket_init', 'count_init'],
        data: function () {
            return {
                in_basket: this.in_basket_init,
                count: this.count_init,
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

    var menuBasketMixin = {
        mounted: function () {
            var me = this;
            document.addEventListener("updateBasketActionComplete", function (e) {
                me.sumMin = sharedBasketStore.updateBasketActionData['sum_min'];
                me.sumMax = sharedBasketStore.updateBasketActionData['sum_max'];
                me.updateBasket = false
            });
            document.addEventListener("updateBasketActionCompleteFail", function (e) {
                me.updateBasket = false;
            });
            document.addEventListener("beforeUpdateBasketActionComplete", function (e) {
                me.updateBasket = true;
            })
        },
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
                return _.filter(_.keys(this.realState.products), function (key) {
                    return String(key) === String(parseInt(key, 10))
                }).length
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
        watch: {
            realState: {
                handler: function (val, oldValue) {
                    sharedBasketStore.updateBasketAction(commonUrls['basket_calc'], val, oldValue)
                },
                deep: true
            }
        }
    };

    Vue.component("menu-basket", {
        template: "#menu-basket-template",
        mixins: [menuBasketMixin]
    });

    Vue.component("basket-top-bar", {
        template: "#basket-top-bar-template",
        mixins: [menuBasketMixin],
        methods: {
            placeOrder: function ($event) {
                this.$http.post(
                    commonUrls['basket_place_oder'],
                    sharedBasketStore.state.products
                ).then(function (data) {
                    document.location.href = commonUrls['basket_order'];
                }, function () {

                });
            }
        }
    });

    window.sharedBasketStore = sharedBasketStore;
})();