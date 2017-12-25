var $ = require('jquery')
require('bootstrap-sass')
require('moment')
require('eonasdan-bootstrap-datetimepicker')
require('tinymce')
require('chart.js')
/* TinyMCE dependencies */
require('tinymce/themes/modern/theme')
require('tinymce/plugins/advlist')
require('tinymce/plugins/autolink')
require('tinymce/plugins/link')
require('tinymce/plugins/lists')
require('tinymce/plugins/image')
require('tinymce/plugins/charmap')
require('tinymce/plugins/print')
require('tinymce/plugins/preview')
require('tinymce/plugins/anchor')
require('tinymce/plugins/hr')
require('tinymce/plugins/pagebreak')
require('tinymce/plugins/searchreplace')
require('tinymce/plugins/wordcount')
require('tinymce/plugins/visualblocks')
require('tinymce/plugins/insertdatetime')
require('tinymce/plugins/media')
require('tinymce/plugins/nonbreaking')
require('tinymce/plugins/visualchars')
require('tinymce/plugins/table')
require('tinymce/plugins/emoticons')
require('tinymce/plugins/directionality')
require('tinymce/plugins/textcolor')
require('tinymce/plugins/paste')
require('tinymce/plugins/contextmenu')
require('tinymce/plugins/code')
require('../dist/plugins/tinymce/responsivefilemanager/plugin.min')
require('../dist/plugins/tinymce/filemanager/plugin.min')
/* End TinyMCE dependencies */
require('bootstrap-tagsinput')
require('../dist/js/app')
require('../dist/js/engine')

$(function () {
    var dreamstone = new engine();
    dreamstone.enableStatusButtons();
    dreamstone.enableDateTimePicker();
    dreamstone.enableTinyMCE();
    dreamstone.enableTagsInput();
    dreamstone.enableCharts();
});
