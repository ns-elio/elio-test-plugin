import './page/fast-order-list';
import './page/fast-order-detail';

import enGB from './snippet/en-GB.json';

Shopware.Module.register('fast-order', {
    type: 'plugin',
    name: 'fast-order',
    title: 'fast-order.general.mainMenuItemGeneral',
    description: 'fast-order.general.descriptionTextModule',
    color: '#591555',
    icon: 'default-shopping-paper-bag-product',

    snippets: {
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'fast-order-list',
            path: 'list'
        },
        detail: {
            component: 'fast-order-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'fast.order.list'
            }
        }
    },

    navigation: [{
        label: 'fast-order.general.mainMenuItemGeneral',
        color: '#591555',
        path: 'fast.order.list',
        icon: 'default-shopping-paper-bag-product',
        parent: 'sw-order',
        position: 100
    }]
});