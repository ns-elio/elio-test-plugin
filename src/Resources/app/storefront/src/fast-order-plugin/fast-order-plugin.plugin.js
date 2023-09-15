import Plugin from 'src/plugin-system/plugin.class';

export default class FastOrderPlugin extends Plugin {
    init() {
        document.querySelector("#fast-order-add-line-btn").addEventListener('click', this.onAddLineBtnClick.bind(this));
    }

    onAddLineBtnClick() {
        let itemRowContainer = document.querySelector("#fast-order-form-body");
        let newRow = itemRowContainer.children[0].cloneNode(true);

        newRow.querySelectorAll('input').forEach(n => n.value = '');

        itemRowContainer.appendChild(newRow);
    }
}
