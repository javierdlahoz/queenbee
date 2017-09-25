(function() {
    tinymce.create('tinymce.plugins.Cupg', {
        
        init : function(editor, url) {
            editor.addButton('cupg_btn', {
                title: 'Content Upgrades',
                image: url + '/../assets/images/menu_icon_modal.png',
                cmd: 'cupg_click'
            });
 
            editor.addCommand('cupg_click', function() {
                editor.windowManager.open({
                    title: 'Content Upgrades',
                    url: editor.editorManager.documentBaseURL + 'admin.php?page=content-upgrades-modal',
                    width: 650,
                    height: 500,
                    close_previous: true
                }, {
                    editor: editor
                });
            });
        }
        
    });
    
    // Register plugin
    tinymce.PluginManager.add('cupg', tinymce.plugins.Cupg);
})();