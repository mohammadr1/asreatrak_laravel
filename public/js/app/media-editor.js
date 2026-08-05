console.log('MEDIA EDITOR FILE LOADED');

import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

console.log('MEDIA FILE');

Alpine.data('imageEditor', () => ({

    init() {
        console.log('INIT');
    },

    save() {
        console.log('SAVE');
    },

}));