import Plugin from 'src/plugin-system/plugin.class';

export default class FastOrderPlugin extends Plugin {
    init() {
        document.querySelector("#fast-order-add-line-btn").addEventListener('click', this.onAddLineBtnClick.bind(this));
    }

    onAddLineBtnClick() {
        let newRow = document.querySelector("#fast-order-row-template").content.cloneNode(true);
        let itemRowContainer = document.querySelector("#fast-order-form-body");
        itemRowContainer.appendChild(newRow);
    }
}
