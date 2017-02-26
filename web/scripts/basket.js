/**
 * Created by m on 26.02.17.
 */
(function() {

    window.BasketController = function (data, element_id) {
        new Vue({
            el: element_id,
            data: function () {
                var products = data['products'];
                return {
                    products: products
                }
            }
        })
    }

})();