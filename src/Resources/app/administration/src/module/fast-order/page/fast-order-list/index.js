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
        },
        itemCriteria() {
            return new Criteria()
                .addAssociation('product.parent')
        },
        itemName() {
            if(this.parent) {
                return this.parent.name;
            }
            return this.name;
        }
    },

    created() {
        const criteria = new Criteria();
        criteria.setPage(1);
        criteria.setLimit(10);

        this.repository = this.repositoryFactory.create('elio_fast_order_line_item');
        criteria.getAssociation('product.parent')

        this.repository.search(criteria, Shopware.Context.api).then((result) => {
            this.items = result;
        });
    },

    methods: {
        getColumns() {
            return [{
                property: 'product.name',
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
                property: 'dateTime',
                label: this.$tc('fast-order.list.columnDateTime'),
                allowResize: true
            }, {
                property: 'comment',
                label: this.$tc('fast-order.list.columnComment'),
                inlineEdit: 'string',
                allowResize: true
            }];
        }
    }
});
