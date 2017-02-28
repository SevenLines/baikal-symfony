/**
 * Created by m on 11.02.17.
 */
(function () {
    window.ProductsController = function ($productCategories, $element, $products) {
        new Vue({
            el: $element,
            data: function () {
                var basket = Cookies.getJSON('basket');

                if ($productCategories !== null) {
                    data = $productCategories;
                    var categories = data;
                    categories.unshift({
                        title: 'Все категории',
                        id: -1,
                    });

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
                                item.count = item.in_basket ? basket[item.id] : 1;
                                return item;
                            });
                        })),
                        productQuery: '',
                        activeCategories: [],
                        realCategories: [],
                        loading: true,
                    }
                }
                if ($products !== null) {
                    return {
                        categories: [],
                        products: _.map($products, function(item) {
                            var it = item['product'];
                            it.in_basket = _.has(basket, it.id);
                            it.count = it.in_basket ? basket[it.id] : 1;
                            return it;
                        }),
                        productQuery: '',
                        activeCategories: [],
                        realCategories: [],
                        loading: true,
                    }
                }
            },
            mounted: function () {
                this.loading = false;
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

                    window.scrollTo(0, 0);

                    return products;
                }
            },
            methods: {
                categorySelectChange: function (event) {
                    category_id = parseInt(event.currentTarget.value);
                    if (category_id == -1) {
                        this.activeCategories = [];
                        this.realCategories = []
                    } else {
                        this.activeCategories = [category_id];
                        this.realCategories = [category_id];
                    }
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
