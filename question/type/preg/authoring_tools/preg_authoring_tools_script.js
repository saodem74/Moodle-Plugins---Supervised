/**
 * Script for button "Check", "Back" and push in interactive tree
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Pahomov Dmitry, Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

/**
 * This object extends M.poasquestion_text_and_button with onfirstpresscallback()
 * function and oneachpresscallback()
 */
M.preg_authoring_tools_script = (function ($) {

    var self = {

    TREE_KEY : 'tree',

    TREE_MAP_KEY : 'map',

    GRAPH_KEY : 'graph',

    DESCRIPTION_KEY : 'description',

    STRINGS_KEY : 'regex_test',

    TREE_MAP_ID : '#qtype_preg_graph',

    /** @var string with moodle root url (smth like 'http://moodle.site.ru/') */
    www_root : null,

    /** @var {string} name of qtype_preg_textbutton parent object */
    textbutton_widget : null,

    /** @var {Object} reference to the regex textarea */
    regex_input : null,

    matching_options : ['engine', 'notation', 'exactmatch', 'usecase'],

    /** @var {Object} cache of content; dimensions are: 1) tool name, 2) concatenated options, selection borders, etc. */
    cache : {
        tree : {},
        graph : {},
        description : {},
        regex_test : {}
    },

    /**
     * setups module
     * @param {Object} Y NOT USED! It's needed because moodle passes this object anyway
     * @param {string} _www_root string with www host of moodle
     * (smth like 'http://moodle.site.ru/')
     * @param {string} poasquestion_text_and_button_objname name of qtype_preg_textbutton parent object
     */
    init : function (Y, _www_root, poasquestion_text_and_button_objname) {
        this.www_root = _www_root;
        this.textbutton_widget = M.poasquestion_text_and_button;
        this.setup_parent_object();
    },

    /**
     * Sets up options of M.poasquestion_text_and_button object
     * This method defines onfirstpresscallback method, that calls on very first
     * press on button, right afted dialog generation
     * oneachpresscallback calls on second and following pressings on button
     */
    setup_parent_object : function () {
        var options = {

            // Function called on the very first form opening.
            onfirstpresscallback : function () {
                $.ajax({
                    url: self.www_root + '/question/type/preg/authoring_tools/preg_authoring.php',
                    type: "GET",
                    dataType: "text"
                }).done(function( responseText, textStatus, jqXHR ) {
                    var tmpM = M;
                    $(self.textbutton_widget.dialog).html($.parseHTML(responseText, document, true));
                    M = $.extend(M, tmpM);

                    // Remove the "skip to main content" link.
                    $(self.textbutton_widget.dialog).find('.skiplinks').remove();

                    // Create a clone of the textarea.
                    var textarea = $('<textarea style="margin:0;padding:0;border:none;resize:both;outline:none;overflow:hidden;width:100%;height:100%"></textarea>');
                    $('#id_regex_text').each(function() {
                        $.each(this.attributes, function() {
                            if (this.specified) {
                                textarea.attr(this.name, this.value);
                            }
                        });
                    });

                    // Replace the textarea with an iframe.
                    var iframeMarkup = '<div id="id_regex_resizable" style="border:1px solid black;padding:0;overflow:hidden;width:50%;height:20px">' +
                                         '<iframe id="id_regex_text_replacement" style="border:none;resize:none;width:100%;height:100%"/>' +
                                       '</div>';

                    $('#id_regex_text').replaceWith(iframeMarkup);
                    $('#id_regex_resizable').resizable();

                    // Deal with iframe.
                    var iframe = $('#id_regex_text_replacement');

                    setTimeout(function() {
                        var innerDoc = iframe[0].contentWindow.document,
                            innerBody = $('body', innerDoc);
                        innerBody.css('margin', '0').css('padding', '0')
                                 .append(textarea);
                    }, 1);

                    // Add handlers for the buttons.
                    $('#id_regex_show').click(self.btn_show_clicked);
                    $('#id_regex_save').click(self.btn_save_clicked);
                    $('#id_regex_cancel').click(self.btn_cancel_clicked);
                    $('#id_regex_check_strings').click(self.btn_check_strings_clicked);

                    // Add handlers for the radiobuttons.
                    $('#fgroup_id_tree_orientation_radioset input').change(self.rbtn_changed);
                    $('#fgroup_id_charset_process_radioset input').change(self.rbtn_changed);

                    // Add handlers for the regex textarea.
                    self.regex_input = textarea;
                    self.regex_input.keyup(self.textbutton_widget.fix_textarea_rows);

                    // Add handlers for the regex testing textarea.
                    $('#id_regex_match_text').keyup(self.textbutton_widget.fix_textarea_rows);

                    // Add some question options.
                    var header = $('#id_regex_matching_options_header').find('.fcontainer');
                    $.each(self.matching_options, function(i, option) {
                        var fitem_id = 'fitem_id_' + option,
                            old_id = 'id_' + option,
                            new_id = old_id + '_auth',
                            clone = $('#' + fitem_id).clone();

                        // Change id and append to the DOM.
                        clone.find('#' + old_id).attr('id', new_id);
                        header.append(clone);

                        // When an option is changed, update all the tools.
                        $('#' + new_id).change(function() {
                            $('#id_regex_show').click();
                            $('#id_regex_check_strings').click();
                        });
                    });

                    options.oneachpresscallback();
                });
            },

            // Function called on non-first form openings.
            oneachpresscallback : function () {
                self.regex_input.val(self.textbutton_widget.data).trigger('keyup');

                // Put the testing data into ui.
                $('#id_regex_match_text').val($('input[name=\'regextests[' + $(self.textbutton_widget.currentlinput).attr('id').split("id_answer_")[1] + ']\']').val())
                                         .trigger('keyup');
                $.each(self.matching_options, function(i, option) {
                    var old_id = '#id_' + option,
                        new_id = old_id + '_auth';
                    $(new_id).val($(old_id).val());
                });
                $('#id_regex_show').click();
            },

            onsaveclicked : function () {
                $('#id_regex_save').click();
            },

            oncancelclicked : function () {
                $('#id_regex_cancel').click();
            }
        };

        self.textbutton_widget.setup(options);
    },

    btn_show_clicked : function (e) {
        e.preventDefault();
        var sel = self.get_selection();
        self.load_content(sel.indfirst, sel.indlast);
        self.load_strings(sel.indfirst, sel.indlast);
    },

    btn_save_clicked : function (e) {
        e.preventDefault();
        self.textbutton_widget.data = self.regex_input.val();
        $.each(self.matching_options, function(i, option) {
            var old_id = '#id_' + option,
                new_id = old_id + '_auth';
            $(old_id).val($(new_id).val());
        });
        self.textbutton_widget.close_and_set_new_data();
        M.form.updateFormState("mform1");
    },

    btn_cancel_clicked : function (e) {
        e.preventDefault();
        self.textbutton_widget.dialog.dialog("close");
        $('#id_test_regex').html('');
    },

    btn_check_strings_clicked : function (e) {
        e.preventDefault();
        var sel = self.get_selection();
        self.load_strings(sel.indfirst, sel.indlast);
    },

    rbtn_changed : function (e) {
        e.preventDefault();
        var sel = self.get_selection();
        self.load_content(sel.indfirst, sel.indlast);
    },

    tree_node_clicked : function (e) {
        e.preventDefault();
        var tmp = e.target.id.split(','),
            indfirst = tmp[1],
            indlast = tmp[2];
        self.load_content(indfirst, indlast);
        self.load_strings(indfirst, indlast);
    },

    tree_node_misclicked : function (e) {
        e.preventDefault();
        self.load_content();
        self.load_strings();
    },

    cache_key_for_explaining_tools : function (indfirst, indlast) {
        return '' +
               self.regex_input.val() +
               $('#id_notation_auth').val() +
               $('#id_exactmatch_auth').val() +
               $('#id_usecase_auth').val() +
               self.get_orientation() +
               self.get_displayas() +
               indfirst + ',' + indlast;
    },

    cache_key_for_testing_tool : function (indfirst, indlast) {
        return '' +
               self.regex_input.val() +
               $('#id_engine_auth').val() +
               $('#id_notation_auth').val() +
               $('#id_exactmatch_auth').val() +
               $('#id_usecase_auth').val() +
               $('#id_regex_match_text').val() +
               indfirst + ',' + indlast;
    },

    upd_content_success : function (data, textStatus, jqXHR) {
        var json = JSON.parse(data),
            regex = json['regex'],
            //engine = json['engine'],
            notation = json['notation'],
            exactmatch = json['exactmatch'],
            usecase = json['usecase'],
            treeorientation = json['treeorientation'],
            displayas = json['displayas'],
            indfirst = json['indfirst'],
            indlast = json['indlast'],
            t = json[self.TREE_KEY]
            g = json[self.GRAPH_KEY],
            d = json[self.DESCRIPTION_KEY],
            k = '' + regex + notation + exactmatch + usecase + treeorientation + displayas + indfirst + ',' + indlast;

        // Highlight description.
        var desc = $($.parseHTML(d));
        desc.find('.description_node_' + json['id']).css('background', '#FFFF00');
        d = desc.html();

        // Cache the content.
        self.cache[self.TREE_KEY][k] = t;
        self.cache[self.GRAPH_KEY][k] = g;
        self.cache[self.DESCRIPTION_KEY][k] = d;

        // Display the content.
        self.display_content(t, g, d, indfirst, indlast);
    },

    upd_strings_success : function (data, textStatus, jqXHR) {
        var json = JSON.parse(data),
            regex = json['regex'],
            engine = json['engine'],
            notation = json['notation'],
            exactmatch = json['exactmatch'],
            usecase = json['usecase'],
            treeorientation = json['treeorientation'],
            displayas = json['displayas'],
            indfirst = json['indfirst'],
            indlast = json['indlast'],
            strings = json['strings'],
            s = json[self.STRINGS_KEY],
            k = '' + regex + engine + notation + exactmatch + usecase + strings + indfirst + ',' + indlast;

        // Cache the strings.
        self.cache[self.STRINGS_KEY][k] = s;

        // Display the strings.
        self.display_strings(s);
    },

    // Displays given images and description
    display_content : function (t, g, d, indfirst, indlast) {
        var scroll = $(window).scrollTop(),
            tree_err = $('#tree_err'),
            tree_img = $('#tree_img'),
            tree_map = $('#tree_map'),
            graph_err = $('#graph_err'),
            graph_img = $('#graph_img'),
            desc_hnd = $('#description_handler');

        tree_err.html('');
        tree_map.html('');
        tree_img.removeAttr('src').css('visibility', 'hidden');
        if (typeof t != 'undefined' && t.img && t.map) {
            tree_img.attr('src', t.img).css('visibility', 'visible');
            tree_map.html(t.map);
            tree_img.click(self.tree_node_misclicked);
            $(self.TREE_MAP_ID + ' > area').click(self.tree_node_clicked);
        } else if (typeof t != 'undefined') {
            tree_err.html(t);
        }

        graph_err.html('');
        graph_img.removeAttr('src').css('visibility', 'hidden');
        if (typeof g != 'undefined' && g.substring(0, 4) == 'data') {
            graph_img.attr('src', g).css('visibility', 'visible');
        } else if (typeof g != 'undefined') {
            graph_err.html(g);
        }

        desc_hnd.html('');
        if (typeof d != 'undefined') {
            desc_hnd.html(d);
        }

        var length =  indlast - indfirst + 1;
        if (indfirst < 0) {
            indfirst = 0;
        }
        if (indlast < 0) {
            length = 0;
        }
        self.regex_input.textrange('set', indfirst, length);
        $(window).scrollTop(scroll);
    },

    display_strings : function (s) {
        $('#id_test_regex').html(s);
    },

    /** Checks for cached data and if it doesn't exist, sends a request to the server */
    load_content : function (indfirst, indlast) {
        if (typeof indfirst == "undefined" || typeof indlast == "undefined") {
            indfirst = indlast = -2;
        }

        // Unbind tree handlers so nothing is clickable till the response is received.
        $('#tree_img').unbind();
        $(self.TREE_MAP_ID + ' > area').unbind();

        // Check the cache.
        var k = self.cache_key_for_explaining_tools(indfirst, indlast);
        cached = self.cache[self.TREE_KEY][k];
        if (cached) {
            self.display_content(self.cache[self.TREE_KEY][k], self.cache[self.GRAPH_KEY][k], self.cache[self.DESCRIPTION_KEY][k], indfirst, indlast);
            return;
        }

        $.ajax({
            type: 'GET',
            url: self.www_root + '/question/type/preg/authoring_tools/preg_authoring_tools_loader.php',
            data: {
                regex: self.regex_input.val(),
                engine: $('#id_engine_auth :selected').val(),
                notation: $('#id_notation_auth :selected').val(),
                exactmatch: $('#id_exactmatch_auth :selected').val(),
                usecase: $('#id_usecase_auth :selected').val(),
                indfirst: indfirst,
                indlast: indlast,
                treeorientation: self.get_orientation(),
                displayas: self.get_displayas(),
                ajax: true
            },
            success: self.upd_content_success
        });
    },

    load_strings : function (indfirst, indlast) {
        if (typeof indfirst == "undefined" || typeof indlast == "undefined") {
            indfirst = indlast = -2;
        }

        // Check the cache.
        var k = self.cache_key_for_testing_tool(indfirst, indlast);
        cached = self.cache[self.STRINGS_KEY][k];
        if (cached) {
            self.display_strings(cached);
            return;
        }

        $.ajax({
            type: 'GET',
            url: self.www_root + '/question/type/preg/authoring_tools/preg_regex_testing_tool_loader.php',
            data: {
                regex: self.regex_input.val(),
                engine: $('#id_engine_auth :selected').val(),
                notation: $('#id_notation_auth :selected').val(),
                exactmatch: $('#id_exactmatch_auth :selected').val(),
                usecase: $('#id_usecase_auth :selected').val(),
                indfirst: indfirst,
                indlast: indlast,
                strings: $('#id_regex_match_text').val(),
                ajax: true
            },
            success: self.upd_strings_success
        });
    },

    get_selection : function () {
        var scroll = $(window).scrollTop(),
            selection = self.regex_input.textrange('get'),
            indfirst = selection.start,
            indlast = selection.end - 1;
        if (indfirst > indlast) {
            indfirst = indlast = -2;
        }
        $(window).scrollTop(scroll);
        return {
            indfirst : indfirst,
            indlast : indlast
        };
    },

    get_orientation : function () {
        return $('#fgroup_id_tree_orientation_radioset input:checked').val();
    },

    get_displayas : function () {
        return $('#fgroup_id_charset_process_radioset input:checked').val();
    }
};

return self;

})(jQuery);
