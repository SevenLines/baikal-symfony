/**
 * Created by m on 11.02.17.
 */
(function () {
    Vue.component("product-row", {
        template: "#product-row",
        props: ['title', 'id', 'category_id', 'price', 'unit', 'in_basket'],
        computed: {
            isInBasket: function (product_id) {
                return this.in_basket;
            },
        },
        methods: {
            toggleBasket: function (product_id) {
                basket = Cookies.getJSON('basket') || {};
                if (_.has(basket, product_id)) {
                    delete basket[product_id];
                } else {
                    basket[product_id] = 1;
                }

                this.in_basket = _.has(basket, product_id);

                Cookies.set('basket', basket);
            },

        },
    });

    window.ProductsController = function ($data, $element) {
        new Vue({
            el: $element,
            data: function () {
                data = $data;
                var categories = _.map(data, function (item) {
                    return item;
                });
                categories.unshift({
                    title: 'Все категории',
                    id: -1,
                });

                basket = Cookies.getJSON('basket');

                return {
                    categories: _.sortBy(categories, function (o) {
                        if (o.id == -1) {
                            return '!'
                        }
                        return o.title;
                    }),
                    products: _.flatten(_.map(data, function (category) {
                        return _.map(category.products, function (item) {
                            item.category_id = category.id;
                            item.in_basket = _.has(basket, item.id);
                            return item;
                        });
                    })),
                    productQuery: '',
                    activeCategories: [],
                    realCategories: []
                }
            },
            watch: {
                productQuery: function (newProductQuery) {
                    if (newProductQuery == "") {
                        this.activeCategories = this.realCategories.slice();
                    }
                }
            },
            computed: {
                filteredProducts: function () {
                    var filterKey = this.productQuery && this.productQuery.toLowerCase();
                    var that = this;
                    products = this.products;

                    if (filterKey != "") {
                        var real_active_categories = this.realCategories.slice();
                        products = _.filter(this.products, function (item) {
                            var result = _.includes(item.title.toLowerCase(), filterKey);
                            if (that.realCategories.length > 0 && that.realCategories.indexOf(item.category_id) == -1) {
                                result = false;
                            }
                            if (result) {
                                real_active_categories.push(item.category_id);
                            }
                            return result;
                        });
                        that.activeCategories = _.uniqBy(real_active_categories);
                    } else if (this.activeCategories.length > 0) {
                        products = _.filter(this.products, function (item) {
                            return that.activeCategories.indexOf(item.category_id) != -1;
                        })
                    }

                    basket = Cookies.getJSON('basket');

                    _.forEach(products, function (o) {
                        o.in_basket = _.has(basket, o.id);
                    });

                    products = _.sortBy(products, function (o) {
                        return [o.in_basket == false, o.title]
                    });

                    return products;
                }
            },
            methods: {
                sortBy: function (key) {

                },
                isActive: function (category) {
                    if (category.id == -1 && this.activeCategories.length == 0) {
                        return true;
                    }
                    return this.activeCategories.indexOf(category.id) != -1;
                },
                toggleCategory: function (category_id, event) {
                    if (category_id == -1) {
                        this.activeCategories = [];
                        this.realCategories = []
                    } else if (event.shiftKey) {
                        this.activeCategories = _.xor(this.activeCategories, [category_id]);
                        this.realCategories = _.xor(this.realCategories, [category_id]);
                    } else {
                        if (this.activeCategories.length == 1 && this.activeCategories.indexOf(category_id) != -1) {
                            this.activeCategories = [];
                            this.realCategories = [];
                        } else {
                            this.activeCategories = [category_id];
                            this.realCategories = [category_id];
                        }
                    }
                }
            }
        })
    }
})();
