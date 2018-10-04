import { shallow } from "@vue/test-utils";
import PreviewSideBar from "./../../../resources/assets/js/components/subcomponents/PreviewSideBar.vue";
import dayjs from "dayjs";

describe("Preview Sidebar", () => {
    const wrapper = shallow(PreviewSideBar);

    it("it shows a file summary", () => {
        wrapper.setProps({
            currentFile:{
                name: "TestFile.jpg",
                size: (1024 * 1024 * 32),
                mimeType: "image/jpg",
                webPath: "https://localhost/TestFile.jpg",
                relativePath: "/TestFile.jpg",
                modified: {
                    date: dayjs("2018-03-19")
                }
            }
        });

        expect(wrapper.html().indexOf(
            `<img id="media-manager-preview-image" src="https://localhost/TestFile.jpg" class="img-responsive center-block">`
        ) !== -1).toBe(true);

        expect(wrapper.html().indexOf(
            `<td class="description">Name</td> <td class="file-value">TestFile.jpg</td>`
        ) !== -1).toBe(true);

        expect(wrapper.html().indexOf(
            `<td class="description">Size</td> <td class="file-value">32 MB</td>`
        ) !== -1).toBe(true);

        expect(wrapper.html().indexOf(
            `<td class="description">URL</td> <td class="file-value"><a href="https://localhost/TestFile.jpg" target="_blank" rel="noopener">/TestFile.jpg</a></td>`
        ) !== -1).toBe(true);

        expect(wrapper.html().indexOf(
            `<td class="description">Uploaded On</td> <td class="file-value">19/03/2018 12:00:00 AM</td>`
        ) !== -1).toBe(true);
    })

});