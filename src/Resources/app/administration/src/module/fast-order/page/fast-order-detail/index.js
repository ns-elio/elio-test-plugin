import template from './fast-order-detail.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('fast-order-detail', {
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
            item: null,
            isLoading: false,
            processSuccess: false,
            repository: null
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            const criteria = new Criteria();

            this.repository = this.repositoryFactory.create('elio_fast_order_line_item');
            criteria.getAssociation('product')

            this.repository.get(this.$route.params.id, Shopware.Context.api, criteria).then((entity) => {
                this.item = entity;
            });
        },

        onClickSave() {
            this.isLoading = true;

            this.repository.save(this.item, Shopware.Context.api).then(() => {
                this.isLoading = false;
                this.processSuccess = true;
            }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$tc('fast-order.detail.errorTitle'),
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
