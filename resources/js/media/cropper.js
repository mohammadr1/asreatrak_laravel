console.log('cropper loaded');

import Cropper from "cropperjs";
import "cropperjs/dist/cropper.css";

// window.imageEditor = function () {

//     return {

//         cropper: null,

//         init() {

//             this.cropper = new Cropper(
//                 document.getElementById("cropper-image"),
//                 {
//                     viewMode: 1,
//                     aspectRatio: 16 / 9,
//                     cropBoxResizable: false,
//                     cropBoxMovable: true,
//                     autoCropArea: 1,
//                     responsive: true,
//                     movable: true,
//                     zoomable: true,
//                     rotatable: true,
//                     scalable: true,
//                 }
//             );

//         },

//         rotate(angle) {

//             this.cropper.rotate(angle);

//         },

//         zoom(value) {

//             this.cropper.zoom(value);

//         },

//         save() {

//             const data = this.cropper.getData(true);

//             Livewire.dispatch(
//                 "saveCrop",
//                 {
//                     crop: data
//                 }
//             );

//         }

//     }

// }