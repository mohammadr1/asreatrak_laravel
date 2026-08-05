import Alpine from 'alpinejs'

console.log('MEDIA EDITOR LOADED')

document.addEventListener('alpine:init', () => {

    console.log('REGISTER IMAGE EDITOR')

    Alpine.data('imageEditor', () => ({

        message: 'hello',

        init() {
            console.log('IMAGE EDITOR INIT')
        }

    }))

})