var $ = require("jquery");
var Markdown = require("markdown");

module.exports = function(element) {
    $(element).find('.tab-preview').on('shown.bs.tab', function(e) {
        var $parent = $(this).closest('[data-module=markdown-edit-tabbar]');
        var markdown = $parent.find('textarea').val();
        var html = Markdown.toHTML(markdown);
        $parent.find('.tab-pane-preview').html(html);
    });
};
