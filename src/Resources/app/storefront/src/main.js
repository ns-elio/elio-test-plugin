import FastOrderPlugin from './fast-order-plugin/fast-order-plugin.plugin';

const PluginManager = window.PluginManager;
PluginManager.register('FastOrderPlugin', FastOrderPlugin, '#fast-order-form-body');
