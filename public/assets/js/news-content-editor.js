(() => {
    if (window.newsContentEditorInstalled) return;
    window.newsContentEditorInstalled = true;
    let range = null;
    let currentEditor = null;
    const findEditor = () => document.getElementById('news-content');
    document.addEventListener('trix-selection-change', (event) => {
        if (event.target.id !== 'news-content') return;
        currentEditor = event.target;
        range = event.target.editor.getSelectedRange().slice();
    });
    document.addEventListener('trix-initialize', (event) => {
        if (event.target.id === 'news-content') {
            currentEditor = event.target;
            range = null;
        }
    });
    const insert = (html) => {
        const element = findEditor();
        if (!element?.editor) return;
        const editor = element.editor;
        // Keep the cursor position while a Filament dialog has focus.
        if (currentEditor === element && range) editor.setSelectedRange(range);
        else editor.setSelectedRange(editor.getDocument().getLength() - 1);
        editor.recordUndoEntry('درج رسانه');
        editor.insertHTML(html);
        range = editor.getSelectedRange().slice();
        currentEditor = element;
        // Filament's modal focus trap is released after its close transition.
        setTimeout(() => { if (element.isConnected) element.focus(); }, 350);
    };
    window.addEventListener('news-editor-image', (event) => {
        let url;
        try { url = new URL(event.detail.url, window.location.origin); } catch { return; }
        if (!['https:', 'http:'].includes(url.protocol)) return;
        const image = document.createElement('img');
        image.src = url.href;
        image.alt = event.detail.alt || 'تصویر خبر';
        insert(image.outerHTML + '<p><br></p>');
    });
    window.addEventListener('news-editor-video', (event) => {
        const id = event.detail.id;
        if (typeof id !== 'string' || !/^[a-zA-Z0-9]{1,100}$/.test(id)) return;
        const link = document.createElement('a');
        link.href = `https://www.aparat.com/v/${id}`;
        link.textContent = `ویدیوی آپارات: ${id}`;
        insert(`<p>${link.outerHTML}</p><p><br></p>`);
    });
})();
