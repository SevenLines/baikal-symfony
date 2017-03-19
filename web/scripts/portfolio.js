/**
 * Created by m on 16.03.17.
 */

window.PortfolioMixin = {
    data: {
        imagePreview: '',
        visible: false
    },
    methods: {
        showImage: function (url) {
            this.imagePreview = url;
            this.visible = true;
        },
        hideImagePreview: function () {
            this.imagePreview = "";
            this.visible = false;
        },
    }
};
