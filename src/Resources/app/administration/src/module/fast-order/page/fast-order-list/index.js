import template from './fast-order-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('fast-order-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            repository: null,
            items: null
        };
    },

    computed: {
        columns() {
            return this.getColumns();
        }
    },

    created() {
        const criteria = new Criteria();
        criteria.setPage(1);
        criteria.setLimit(10);

        this.repository = this.repositoryFactory.create('elio_fast_order_line_item');
        criteria.getAssociation('product')

        this.repository.search(criteria, Shopware.Context.api).then((result) => {
            this.items = result;

            this.items.forEach((item) => {
                let date = new Date(item.dateTime);
                item.formattedDateTime = date.toLocaleString();
            });
        });
    },

    methods: {
        getColumns() {
            return [{
                property: 'name',
                label: this.$tc('fast-order.list.columnName'),
                routerLink: 'fast.order.detail',
                allowResize: true,
                primary: true
            }, {
                property: 'product.id',
                label: this.$tc('fast-order.list.columnId'),
                allowResize: true
            }, {
                property: 'quantity',
                label: this.$tc('fast-order.list.columnQuantity'),
                allowResize: true
            }, {
                property: 'sessionId',
                label: this.$tc('fast-order.list.columnSessionId'),
                allowResize: true
            }, {
                property: 'formattedDateTime',
                label: this.$tc('fast-order.list.columnDateTime'),
                allowResize: true
            }, {
                property: 'comment',
                label: this.$tc('fast-order.list.columnComment'),
                allowResize: true
            }];
        }
    }
});
