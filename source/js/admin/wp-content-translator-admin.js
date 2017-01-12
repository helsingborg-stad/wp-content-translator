var ContentTranslateAdmin = {};

ContentTranslateAdmin.AddLang = (function ($) {

    var _rowTemplate;
    var _rowNum = 0;

    /**
     * Constructor
     * Should be named as the class itself
     */
    function AddLang() {
        $(document).ready(function () {
            _rowTemplate = $('.wp-content-translator-admin__table__add-lang tr[data-template]').clone();
            $('.wp-content-translator-admin__table__add-lang tr[data-template]').remove();
        });

        $('[data-action="wp-content-translator-new-language"]').on('click', function (e) {
            this.addLangRow();
        }.bind(this));

        $(document).on('change', '.wp-content-translator-admin__table__add-lang select', function (e) {
            this.populateIdentifier(e.target);
        }.bind(this));

        $(document).on('click', '[data-action="wp-content-translator-remove-row"]', function (e) {
            this.removeRow(e.target);
        }.bind(this));
    }

    AddLang.prototype.addLangRow = function() {
        _rowNum++;

        var $newRow = _rowTemplate.clone();
        $newRow.removeAttr('data-template');

        var html = $newRow.html();
        html = html.replace(/{num}/g, _rowNum);

        $newRow.html(html);

        $newRow.appendTo('.wp-content-translator-admin__table__add-lang tbody');
    };

    AddLang.prototype.populateIdentifier = function(element) {
        var $select = $(element).closest('select');
        var $row = $select.parents('tr');

        $row.find('[data-placeholder="identifier"]').text($select.val());
    };

    AddLang.prototype.removeRow = function(element) {
        $(element).parents('tr').remove();
    };

    return new AddLang();

})(jQuery);

