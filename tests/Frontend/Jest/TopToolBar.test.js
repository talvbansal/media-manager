import { shallow } from "@vue/test-utils"
import TopToolBar from './../../../resources/assets/js/components/subcomponents/TopToolBar.vue';

describe('TopToolBar', () => {
    const wrapper = shallow(TopToolBar);

    it('it has the correct buttons', () => {
        const toolBarAsText = wrapper.text();
        expect(toolBarAsText).toContain('Upload');
        expect(toolBarAsText).toContain('Add folder');

        expect(toolBarAsText).toContain('Refresh');

        expect(toolBarAsText).toContain('Move');
        expect(toolBarAsText).toContain('Delete');
        expect(toolBarAsText).toContain('Rename');
    })

    it('emits the open-modal-create-folder event when add folder button is clicked', () => {
        const reloadButton = wrapper.find('button[title="Add folder"]');
        reloadButton.trigger('click');
        expect(wrapper.emitted('open-modal-create-folder')).toBeTruthy();
    })

    it('emits the reload-folder event when reload button is clicked', () => {
        const reloadButton = wrapper.find('button[title="Refresh"]');
        reloadButton.trigger('click');
        expect(wrapper.emitted('reload-folder')).toBeTruthy();
    })

    it('emits the open-modal-move-item event when move button is clicked', () => {
        wrapper.setProps({
            currentFile : {
                name: '/test.jpg',
            }
        })

        const reloadButton = wrapper.find('button[title="Move"]');
        reloadButton.trigger('click');
        expect(wrapper.emitted('open-modal-move-item')).toBeTruthy();
    })

    it('emits the open-modal-delete-item event when delete button is clicked', () => {
        wrapper.setProps({
            currentFile : {
                name: '/test.jpg',
            }
        })

        const reloadButton = wrapper.find('button[title="Delete"]');
        reloadButton.trigger('click');
        expect(wrapper.emitted('open-modal-delete-item')).toBeTruthy();
    })

    it('emits the open-modal-rename-item event when rename button is clicked', () => {
        wrapper.setProps({
            currentFile : {
                name: '/test.jpg',
            }
        })

        const reloadButton = wrapper.find('button[title="Rename"]');
        reloadButton.trigger('click');
        expect(wrapper.emitted('open-modal-rename-item')).toBeTruthy();
    })

});