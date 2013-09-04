<?php

/**
 * Language strings for the Preg question type.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['answersinstruct'] = '<p>Введите регулярные выражения (как минимум одно) в выбранной нотации в качестве ответов. Если дан корректный ответ, он должен совпадать минимум с одним 100% ответом.</p><p>Вы можете использовать конструкцию вида {$0} в отзывах для того, чтобы вставить захваченные части ответа студента. {$0} будет заменено совпадением в целом, {$1} - совпадением с первым подвыражением и т.д. Если выбранный движок поиска не поддерживает подвыражения, вы должны использовать только {$0}.</p>';
$string['answerno'] = 'Ответ {$a}';
$string['charhintpenalty'] = 'Штраф за подсказку следующего символа';
$string['charhintpenalty_help'] = 'Штраф за подсказку следующего символа. Обычно должен быть больше, чем штраф, даваемый за каждую новую попытку ответа на вопрос без подсказки. Эти штрафы взаимоисключающие.';
$string['lexemhintpenalty'] = 'Штраф за подсказку следующей лексемы';
$string['lexemhintpenalty_help'] = 'Штраф за подсказку следующей лексемы. Обычно должен быть больше, чем штраф, даваемый за каждую новую попытку ответа на вопрос без подсказки. Эти штрафы взаимоисключающие.';
$string['correctanswer'] = 'Правильный ответ';
$string['correctanswer_help'] = 'Введите правильный ответ (не регулярное выражение) для показа студентам. Если вы оставите его пустым, preg попытается его сгенерировать сам, пытаясь сделать его наиболее похожим на ответ студента. На данный момент генерировать ответы может только НКА движок.';
$string['debugheading'] = 'Отладочные настройки';
$string['defaultenginedescription'] = 'Движок поиска совпадений, используемый по умолчанию при создании нового вопроса';
$string['defaultenginelabel'] = 'Движок поиска совпадений, используемый по умолчанию';
$string['defaultlangdescription'] = 'Язык, используемый по умолчанию при создании нового вопроса';
$string['defaultlanglabel'] = 'Язык, используемый по умолчанию';
$string['defaultnotationdescription'] = 'Нотация, используемая по умолчанию при создании нового вопроса';
$string['defaultnotationlabel'] = 'Нотация, используемая по умолчанию';
$string['description_tool'] = 'Описание';
$string['description_tool_help'] = 'Здесь вы можете увидеть описание регулярных выражений. Нажатие на узле дерева выделится соответствующий подграф и соответствующая часть описания желтым цветом.';
$string['dfa_matcher'] = 'Детерминированные конечные автоматы (ДКА)';
$string['engine'] = 'Движок поиска совпадений';
$string['engine_help'] = '<p>Не существует лучшего движка поиска совпадений, поэтому вы можете выбирать тот, который подходит для конкретного вопроса.</p><p>Стандартный движок <b>PHP</b> работает через функцию preg_match() языка PHP. В нем, скорее всего, нет ошибок, он полностью поддерживает синтаксис PCRE, но не поддерживает частичные совпадения и генерацию подсказок.</p><p>Движки <b>НКА</b> и <b>ДКА</b> написаны самостоятельно; они поддерживают частичные совпадения и генерацию подсказок, но не поддерживают сложные утверждения (вы будете уведомлены, если попытаетесь сохранить вопрос с неподдерживаемыми возможностями) и могут содержать ошибки.</p><p>Если вам трудно понять разницу между движками поиска совпадений, попробуйте их все и проверьте, насколько они вам подходят. Если один движок не подходит, возможно, подойдет другой.</p><p>Движок НКА, скорее всего, является наилучшим выбором, если вы не используете сложные утверждения.</p><p>Не рекомендуется использовать движок ДКА в новых вопросах, т.к. он устрел, имеет наибольшее количество ошибок и плохо поддерживается в последнее время. Используйте его только если вы не можете добиться нужных результатов с помощью других движков.</p>';
$string['exactmatch'] = 'Точное совпадение';
$string['exactmatch_help'] = '<p>По умолчанию поиск совпадений с регулярным выражением возвращает истину, если в ответе есть хотя бы одно совпадение. Точное совпадение означает, что совпадать должна строка целиком.</p><p>Установите значение Да, если вы пишете регулярное выражение для ответа целиком. Установка значения Нет дает дополнительную гибкость: вы можете указать ответ с низкой (или нулевой) оценкой, чтобы "отловить" частые ошибки студентов и дать на них отзыв. Вы также можете указывть режим точного совпадения, начиная регулярное выражение символом ^ и заканчивая его символом $.</p>';
$string['explaining_graph_tool'] = 'Объясняющий граф';
$string['explaining_graph_tool_help'] = 'Здесь вы можете увидеть объясняющий граф. Нажатие на узле дерева выделит соответствующий подграф темно-зеленым прямоугольником.';
$string['hintcolouredstring'] = ' совпавшую часть ответа';
$string['hintgradeborder'] = 'Граница показа подсказок';
$string['hintgradeborder_help'] = 'Ответы с оценкой ниже границы показа подсказок не будут использоваться для дачи подсказок.';
$string['hintnextchar'] = 'cледующий правильный символ';
$string['hintnextlexem'] = 'пока не закончится {$a}';
$string['langselect'] = 'Язык';
$string['langselect_help'] = 'Для подсказки лексем вам нужно выбрать язык, который разбивает ответ на лексемы. Каждый язык имеет свои правила. Языки определяются с помощью \'Блока формальных языков\'';
$string['largefa'] = 'Слишком большой конечный автомат';
$string['lexemusername'] = 'Синоним слова "лексема", отображаемый студентам';
$string['lexemusername_help'] = 'Возможно, студенты не знают, что атомарная часть языка называется <b>лексемой</b>. Они могут называть ее "словом", "цифрой" или чем-то другим. Вы можете задать слово, которое студенты будут видеть на кнопке подсказки лексемы.';
$string['maxerrorsshowndescription'] = 'Максимальное число показываемых ошибок для каждого регулярного выражения в форме редактирования';
$string['maxerrorsshownlabel'] = 'Максимальное число показываемых ошибок';
$string['nfa_matcher'] = 'Недетерминированные конечные автоматы (НКА)';
$string['nocorrectanswermatch'] = 'Не указано ни одного 100%-правильного ответа';
$string['nohintgradeborderpass'] = 'Не указано ни одного с оценкой выше границы подсказок. Это отключает подсказки.';
$string['notation'] = 'Нотация регулярных выражений';
$string['notation_help'] = '<p>Вы можете указать нотацию регулярных выражений. Если вы хотите просто использовать регулярные выражения, используйте нотацию <b>Регулярное выражение</b>.</p><p>Нотация <b>Регулярное выражение (расширенная)</b> удобнее для больших регулярных выражений. Она эквивалентна опции PCRE_EXTENDED (модификатор "x" в PHP). Игнорирует неэкранированные пробелы, не находящиеся внутри символных классов и считает комментарием все от неэкранированного знака # до конца строки.</p><p>Нотация <b>Moodle shortanswer</b> позволяет использовать preg как обычный вопрос Moodle shortanswer, но с поддержкой подсказок - вам не нужно понимать регулярные выражения. Просто скопируйте ваши ответы из shortanswer вопросов. Поддерживается \'*\'.</p>';
$string['notation_native'] = 'Регулярное выражение';
$string['notation_mdlshortanswer'] = 'Короткий вопрос Moodle';
$string['notation_pcreextended'] = 'Регулярное выражение (расширенная)';
$string['nosubexprcapturing'] = 'Движок {$a} не поддерживает захват подвыражений. Пожалуйста, удалите конструкции вида {$1...9} (кроме {$0}) из отзывов, или выберите другой движок поиска совпадений';
$string['objectname'] = 'вопроса';
$string['pathtodotempty'] = 'Невозможно отрисовать {$a->name}: путь к dot пакета graphviz пуст. Пожалуйста, обратитесь к администратору для установки <a href="http://www.graphviz.com">graphviz</a> и укажите путь к нему с помощью \'pathtodot\' опции Администрирование > Сервер > Системные пути';
$string['pathtodotincorrect'] = 'Невозможно отрисовать {$a->name}: путь к dot пакета graphviz не правильный или dot не может быть запущен. Пожалуйста, обратитесь к администратору для проверки, если <a href="http://www.graphviz.com">graphviz</a> установлен и \'pathtodot\' в опции корректен в Администрирование > Сервер > Системные пути';
$string['pluginname'] = 'Регулярное выражение';
$string['pluginname_help'] = '<p>Регулярные выражения - это форма записи шаблонов, совпадающих с разными строками. Вы можете использовать их для проверки ответов студентов двумя способамиs: для указания полностью правильных ответов или для отлова наиболее частых ошибок и выдачи соответствующих отзывов.</p><p>Этот тип вопросов по умолчанию использует синтаксис PCRE. Существует множество уроков по созданию регулярных выражений, например, <a href="http://www.phpfreaks.com/content/print/126">example</a>. Детальное описание вы можете найти здесь: <a href="http://www.nusphere.com/kb/phpmanual/reference.pcre.pattern.syntax.htm">php manual</a>. Вам не нужно заключать регулярные выражения в разделители или указывать модификаторы - Moodle сделает это сам.</p><p>Вы также можете использовать этот тип вопросов как улучшенный вариант shortanswer (с подсказками), даже если вы ничего не знаете про регулярные выражения! Просто выберите нотацию <b>Moodle shortanswer</b> для ваших вопросов.</p>';
$string['php_preg_matcher'] = 'расширение preg для PHP';
$string['pluginname_link'] = 'question/type/preg';
$string['pluginnameadding'] = 'Добавление вопроса с регулярными выражениями';
$string['pluginnameediting'] = 'Редактирование вопроса с регулярными выражениями';
$string['pluginnamesummary'] = 'Введите ответ в виде строки, который может быть сопоставлен с несколькими регулярными выражениями. Показываются правильные части ответов студентов. Используются поведения с несколькими попытками, которые могут дать подсказку следующего символа или лексемы.<br/>Вы можете использовать этот тип вопросов не зная регулярные выражения, но имея возможность подсказок с помощью использования нотации \'Moodle shortanswer\'.';
$string['questioneditingheading'] = 'Настройки редактирования вопроса';
$string['regex_handler'] = 'Обработчик регулярных выражений';
$string['subexpression'] = 'Подвыражение';
$string['syntax_tree_tool'] = 'Синтаксческое дерево';
$string['syntax_tree_tool_help'] = 'Здесь вы можете видеть синтаксическое дерево. Нажмите на узел дерева и выделится соответствующее поддерево, подграф и часть в словесном описании.';
$string['tobecontinued'] = '...';
$string['toolargequant'] = 'Слишком большой квантификатор';
$string['toomanyerrors'] = '.......и ещё {$a} ошибки(ошибок)';
$string['lazyquant'] = 'Ленивые квантификаторы';
$string['greedyquant'] = 'Жадные квантификаторы';
$string['possessivequant'] = 'Ревнивые квантификаторы';
$string['ungreedyquant'] = 'Нежадные квантификаторы';
$string['unsupported'] = '{$a->nodename} в позиции с {$a->linefirst}:{$a->indfirst} по {$a->linelast}:{$a->indlast} не поддерживается {$a->engine}.';
$string['unsupportedmodifier'] = 'Ошибка: модификатор {$a->modifier} не поддерживается {$a->classname}.';
$string['usehint_help'] = 'В поведениях, разрешающих несколько попыток, показывать студентам кнопку подсказки следующего символа или следующей лексемы. Не все движки поиска совпадений поддерживают подсказки.';
$string['usecharhint'] = 'Разрешить подсказку следующего символа';
$string['usecharhint_help'] = 'В поведениях, разрешающих несколько попыток, показывать студентам кнопку подсказки следующего символа, которая показывает один правильный символ после правильной части ответа, давая штраф за эту посказку. Не все движки поиска совпадений поддерживают подсказки.';
$string['uselexemhint'] = 'Разрешить подсказку следующей лексемы (слова, числа, знака пунктуации)';
$string['uselexemhint_help'] = '<p>В поведениях, разрешающих несколько попыток, показывать студентам кнопку подсказки следующей лексемы, которая позволяет либо завершить текущую лексему, либо показать следующую, давая штраф за эту посказку. Не все движки поиска совпадений поддерживают подсказки.</p><p><b>Лексема</b> - это атомарная часть языка: слово, число, знак препинания и т.д.</p>';

/******* Abstract syntax tree nodes descriptions *******/
// Types.
$string['leaf_charset']                = 'символьный класс';
$string['leaf_meta']                   = 'мета-символ или escape-последовательность';
$string['leaf_assert']                 = 'простой ассерт';
$string['leaf_backref']                = 'обратная ссылка';
$string['leaf_recursion']              = 'рекурсия';
$string['leaf_control']                = 'управляющая последовательность';
$string['leaf_options']                = 'модификатор';   // TODO: remove?
$string['node_finite_quant']           = 'конечный квантификатор';
$string['node_infinite_quant']         = 'бесконечный квантификатор';
$string['node_concat']                 = 'конкатенация';
$string['node_alt']                    = 'альтернатива';
$string['node_assert']                 = 'назад смотрящий ассерт';
$string['node_subexpr']                = 'подвыражение';
$string['node_cond_subexpr']           = 'условное подвыражение';
$string['node_error']                  = 'синтаксическая ошибка';

// Subtypes.
$string['empty_leaf_meta']             = 'ничего';
$string['esc_b_leaf_assert']           = 'граничные ассерты слова';
$string['esc_a_leaf_assert']           = 'начало объекта ассерта';
$string['esc_z_leaf_assert']           = 'конец объекта ассерта';
$string['esc_g_leaf_assert']           = '';
$string['circumflex_leaf_assert']      = 'начало объекта ассерта';
$string['dollar_leaf_assert']          = 'конец объекта ассерта';
$string['accept_leaf_control']         = '';   // TODO
$string['fail_leaf_control']           = '';
$string['mark_name_leaf_control']      = '';
$string['commit_leaf_control']         = '';
$string['prune_leaf_control']          = '';
$string['skip_leaf_control']           = '';
$string['skip_name_leaf_control']      = '';
$string['then_leaf_control']           = '';
$string['cr_leaf_control']             = '';
$string['lf_leaf_control']             = '';
$string['crlf_leaf_control']           = '';
$string['anycrlf_leaf_control']        = '';
$string['any_leaf_control']            = '';
$string['bsr_anycrlf_leaf_control']    = '';
$string['bsr_unicode_leaf_control']    = '';
$string['no_start_opt_leaf_control']   = '';
$string['utf8_leaf_control']           = '';
$string['utf16_leaf_control']          = '';
$string['ucp_leaf_control']            = '';
$string['pla_node_assert']             = 'положительный вперёд смотрящий ассерт';
$string['nla_node_assert']             = 'отрицательный вперёд смотрящий ассерт';
$string['plb_node_assert']             = 'положительный назад смотрящий ассерт';
$string['nlb_node_assert']             = 'отрицательный назад смотрящий ассерт';
$string['subexpr_node_subexpr']        = 'подвыражение';
$string['onceonly_node_subexpr']       = 'подвыражение захватываемое единажды';
$string['subexpr_node_cond_subexpr']   = '"условное"-подвыражение с проверкой захвата подвыражения';
$string['recursion_node_cond_subexpr'] = 'рекурсивное условное подвыражение';
$string['define_node_cond_subexpr']    = '"определение"-условного подвыражения';
$string['pla_node_cond_subexpr']       = 'положительное вперёдсмотрящее условное подвыражение';
$string['nla_node_cond_subexpr']       = 'отрицательное вперёдсмотрящее условное подвыражение';
$string['plb_node_cond_subexpr']       = 'положительное назадсмотрящее условное подвыражение';
$string['nlb_node_cond_subexpr']       = 'положительное назадсмотрящее условное подвыражение';

$string['unknown_error_node_error']                = 'неизвестная ошибка';
$string['missing_open_paren_node_error']           = 'Синтаксискская ошибка: ожидалась открывающая круглая скобка \'(\' для закрывающей круглой скобки в позиции {$a->indfirst}.';
$string['missing_close_paren_node_error']          = 'Синтаксискская ошибка: ожидалась закрывающая круглая скобка \')\' для открывающей круглой скобки в позиции {$a->indfirst}.';
$string['missing_comment_ending_node_error']       = 'Синтаксискская ошибка: ожидалась закрывающая круглая скобка для комментария в позиции с {$a->indfirst} по {$a->indlast}.';
$string['missing_condsubexpr_ending_node_error']   = 'Не завершённое имя условного подвыражения.';
$string['missing_callout_ending_node_error']       = 'Не закрытая скобка в callout.';
$string['missing_control_ending_node_error']       = 'Ожидалась закрывающая круглая скобка после управляющей последовательности.';
$string['missing_subexpr_name_ending_node_error']  = 'Синтаксискская ошибка в имени подвыражения';
$string['missing_brackets_for_g_node_error']       = 'за \g не стоит имя или номер в фигурных или угловых скобках, или в кавычках.';
$string['missing_brackets_for_k_node_error']       = 'за \k не стоит имя или номер в фигурных или угловых скобках, или в кавычках.';
$string['unclosed_charset_node_error']             = 'Синтаксискская ошибка: ожидалась закрывающая квадратная скобка \']\' для символьного класса начавшегося с позиции {$a->indfirst}.';
$string['posix_class_outside_charset_node_error']  = 'POSIX классы не допускаются за пределами символьного класса.';
$string['quantifier_without_parameter_node_error'] = 'Синтаксискская ошибка: квантификатор в позиции с {$a->indfirst} до {$a->indlast} не имеет операнда - нечего повторять.';
$string['incorrect_quant_range_node_error']        = 'Некорректные границы квантификатора в позиции с {$a->indfirst} до {$a->indlast}: левая граница больше правой.';
$string['incorrect_charset_range_node_error']      = 'Некорректные границы в символьном классе в позиции с {$a->indfirst} до {$a->indlast}: левый символ "больше" правого.';
$string['set_unset_same_modifier_node_error']      = 'Установка и снятие {$a->addinfo} модификатора в одно и тоже время в позиции с {$a->indfirst} до {$a->indlast}.';
$string['unsupported_modifier_node_error']         = 'Неизвестный, неправильный или не поддерживаемый модификатор(модификаторы): {$a->addinfo}.';
$string['unknown_unicode_property_node_error']     = 'Неизвестное unicode свойство: {$a->addinfo}.';
$string['unknown_posix_class_node_error']          = 'Неизвестный posix класс: {$a->addinfo}.';
$string['unknown_control_sequence_node_error']     = 'Неизвестная управляющая последовательность: {$a->addinfo}.';
$string['condsubexpr_too_much_alter_node_error']   = 'Синтаксискская ошибка: три или более альтернатив на верхнем уровне в условном подвыражении в позиции с {$-> indfirst} до {$-> indlast}. Используйте скобки, если вы хотите включить больше альтернатив.';
$string['condsubexpr_assert_expected_node_error']  = 'Ассерт или условие ожидалось.';
$string['condsubexpr_zero_condition_node_error']   = 'Неверное условие (?(0).';
$string['slash_at_end_of_pattern_node_error']      = 'Синтаксискская ошибка: \ в конце шаблона.';
$string['c_at_end_of_pattern_node_error']          = 'Синтаксискская ошибка: \c в конце шаблона.';
$string['cx_should_be_ascii_node_error']           = '\c должно сопровождаться символом ASCII.';
$string['unexisting_subexpr_node_error']           = 'Подвыражение "{$a->addinfo}" не найдено.';
$string['duplicate_subexpr_names_node_error']      = 'Два именованных подвыражения имеют одинаковые имена.';
$string['different_subexpr_names_node_error']      = 'Различные имена подвыражения у подвыражения с тем же номером.';
$string['subexpr_name_expected_node_error']        = 'ожидается имя подвыражения.';
$string['unrecognized_pqh_node_error']             = 'Неизвестный символ после (? или (?-';
$string['unrecognized_pqlt_node_error']            = 'Неизвестный символ после (?<';
$string['unrecognized_pqp_node_error']             = 'Неизвестный символ после (?P';
$string['char_code_too_big_node_error']            = 'Код символа {$a->addinfo} слишком большой.';
$string['char_code_disallowed_node_error']         = 'Unicode-коды 0xd800 ... 0xdfff не разрешены.';
$string['callout_big_number_node_error']           = 'Номер {$a->addinfo} callout слишком большой, не должен превышать 255.';
$string['lnu_unsupported_node_error']              = 'Последовательности \L, \l, \N{name}, \U, и \u не поддерживаются.';

// Types and subtypes needed for authoring tools
$string['leaf_charset_negative'] = 'отрицательный символьный класс';
$string['leaf_charset_error']    = 'ошибка в символьном классе';

/******* Error messages *******/
$string['error_PCREincorrectregex']              = 'Некорректное регулярное выражение - ошибка синтаксиса! Ознакомьтесь с <a href="http://pcre.org/pcre.txt">документацией PCRE</a> для получения информации.';
$string['error_duringauthoringtool']             = 'Ошибки при попытке построения {$a}:';

/******* DFA and NFA limitations *******/
$string['engine_heading_descriptions'] = 'Matching regular expressions can be time and memory consuming. These settings allow you to control limits of time and memory usage by the matching engines. Increase them when you get messages that the regular expression is too complex, but do mind your server\'s performance (you may also want to increase PHP time and memory limits). Decrease them if you get blank page when saving or running a preg question.';
$string['too_large_fa'] = 'Regular expression is too complex to be matched by {$a->engine} due to the time and/or memory limits. Please try another matching engine, ask your administrator to <a href="{$a->link}"> increase time and memory limits</a> or simplify you regular expression.';
$string['fa_state_limit'] = 'Automata size limit: states';
$string['fa_transition_limit'] = 'Automata size limit: transitions';
$string['dfa_settings_heading'] = 'Deterministic finite state automata engine settings';
$string['nfa_settings_heading'] = 'Nondeterministic finite state automata engine settings';
$string['dfa_state_limit_description'] = 'Allows you to tune time and memory limits for the DFA engine when matching complex regexes';
$string['nfa_state_limit_description'] = 'Allows you to tune time and memory limits for the NFA engine when matching complex regexes';
$string['dfa_transition_limit_description'] = 'Maximum number of transitions in DFA';
$string['nfa_transition_limit_description'] = 'Maximum number of transitions in NFA';

/********** Strings for authoring tools **********************/
$string['authoring_tool_page_header'] = 'Инструменты автора';
$string['authoring_form_charset_mode'] = 'Режим отображения для сложных символьных классов:';
$string['authoring_form_charset_flags'] = 'реальное значение (унифицированный формат)';
$string['authoring_form_charset_userinscription'] = 'как написано в регулярных выражениях';
$string['authoring_form_tree_horiz'] = 'горизонтально';
$string['authoring_form_tree_vert'] = 'вертикально';
$string['regex_edit_header'] = 'Регулярное выражение';
$string['regex_edit_header_help'] = 'Введите регулярное выражение здесь. Вы увидете соответствующее синтаксическое дерево, объясняющий графи и словесное описание. Нажмите "Обновить", чтобы принять изменения в регулярном выражении.';
$string['regex_edit_header_text'] = 'Регулярное выражение';
$string['regex_text_text'] = 'Введите регулярное выражение:';
$string['regex_update_text'] = 'Обновить';
$string['regex_save_text'] = 'Сохранить';
$string['regex_cancel_text'] = 'Отмена';
$string['regex_show_selection'] = 'Отобразить выделение';
$string['regex_tree_build'] = 'Построение дерева...';
$string['regex_graph_build'] = 'Построение графа...';
$string['regex_match_header'] = 'Тестирование регулярного выражения';
$string['regex_match_header_help'] = 'Здесь вы можете ввести некоторые строки (по одной на строку), чтобы проверить своё регулярное выражение. После нажатия кнопки "Проверить строку (строки)" результаты будут отображены справа: совпавшие части зеленые, не совпавшие части красные.';
$string['regex_match_textarea'] = 'Введите строки для проверки (по одной на строку)';
$string['regex_check_strings'] = 'Проверить строку (строки)';

// Strings for node description

// TYPE_LEAF_META
$string['description_empty'] = 'ничего';
// TYPE_LEAF_ASSERT
$string['description_circumflex'] = 'начало строки';
$string['description_dollar'] = 'конец строки';
$string['description_wordbreak'] = 'на границе слова';
$string['description_wordbreak_neg'] = 'не на границе слова';
$string['description_esc_a'] = 'в начале текста';
$string['description_esc_z'] = 'в конце текста';
// TYPE_LEAF_BACKREF
$string['description_backref'] = 'обратная ссылка на подвыражение #{$a->number}';
$string['description_backref_name'] = 'обратная ссылка на подвыражение "{$a->name}"';
// TYPE_LEAF_RECURSION
$string['description_recursion_all'] = 'рекурсивное совпадение со всем регулярным выражением';
$string['description_recursion'] = 'рекурсивное совпадение с подмаской  №{$a->number}';
$string['description_recursion_name'] = 'рекурсивное совпадение с подмаской "{$a->name}"';
// TYPE_LEAF_OPTIONS
$string['description_option_i'] = 'регистронезависимо:';
$string['description_unsetoption_i'] = 'регистрозависимо:';
$string['description_option_s'] = 'точка захватывает \n:';
$string['description_unsetoption_s'] = 'точка не захватывает \n:';
$string['description_option_m'] = 'многострочный режим:';
$string['description_unsetoption_m'] = 'не многострочный режим:';
$string['description_option_x'] = 'пробелы в выражении были проигнорированы:';
$string['description_unsetoption_x'] = 'пробелы в выражении не были проигнорированы:';
$string['description_option_U'] = 'квантификаторы не жадные:';
$string['description_unsetoption_U'] = 'квантификаторы жадные:';
$string['description_option_J'] = 'повторение имен разрешено:';
$string['description_unsetoption_J'] = 'повторение имен запрещено:';
// TYPE_NODE_FINITE_QUANT
$string['description_finite_quant'] = '{$a->firstoperand} повторяется от {$a->leftborder} до {$a->rightborder} раз(а){$a->greedy}';
$string['description_finite_quant_strict'] = '{$a->firstoperand} повторяется {$a->count} раз(а){$a->greedy}';
$string['description_finite_quant_0'] = '{$a->firstoperand} повторяется не более {$a->rightborder} раз или отсутствует{$a->greedy}';
$string['description_finite_quant_1'] = '{$a->firstoperand} повторяется не более {$a->rightborder} раз{$a->greedy}';
$string['description_finite_quant_01'] = '{$a->firstoperand} может отсутствовать{$a->greedy}';
$string['description_finite_quant_borders_err'] = ' (некорректные границы у квантификатора)';
// TYPE_NODE_INFINITE_QUANT
$string['description_infinite_quant'] = '{$a->firstoperand} повторяется хотябы {$a->leftborder} раз(а){$a->greedy}';
$string['description_infinite_quant_0'] = '{$a->firstoperand} повторяется любое количество раз или отсутствует{$a->greedy}';
$string['description_infinite_quant_1'] = '{$a->firstoperand} повторяется любое количество раз{$a->greedy}';
// {$a->greedy}
$string['description_quant_lazy'] = ' (ленивый квантификатор)';
$string['description_quant_greedy'] = '';
$string['description_quant_possessive'] = ' (сверхжадный квантификатор)';
// TYPE_NODE_CONCAT
$string['description_concat'] = '{$a->firstoperand} затем {$a->secondoperand}';
$string['description_concat_wcomma'] = '{$a->firstoperand}, затем {$a->secondoperand}';
$string['description_concat_space'] = '{$a->firstoperand} {$a->secondoperand}';
$string['description_concat_and'] = '{$a->firstoperand} и {$a->secondoperand}';
$string['description_concat_short'] = '{$a->firstoperand}{$a->secondoperand}';
// TYPE_NODE_ALT
$string['description_alt'] = '{$a->firstoperand} или {$a->secondoperand}';
$string['description_alt_wcomma'] = '{$a->firstoperand}, или {$a->secondoperand}';
// TYPE_NODE_ASSERT
$string['description_pla_node_assert'] = 'текст далее должен соответствовать: [{$a->firstoperand}]';
$string['description_nla_node_assert'] = 'текст далее не должен соответствовать: [{$a->firstoperand}]';
$string['description_plb_node_assert'] = 'предыдущий текст должен соответствовать: [{$a->firstoperand}]';
$string['description_nlb_node_assert'] = 'предыдущий текст не должен соответствовать: [{$a->firstoperand}]';
$string['description_pla_node_assert_cond'] = 'текст далее соответствует: [{$a->firstoperand}]';
$string['description_nla_node_assert_cond'] = 'текст далее не соответсвует: [{$a->firstoperand}]';
$string['description_plb_node_assert_cond'] = 'предшествующий текст соответсвует: [{$a->firstoperand}]';
$string['description_nlb_node_assert_cond'] = 'предшествующий текст не соответствует: [{$a->firstoperand}]';
// TYPE_NODE_SUBEXPR
$string['description_subexpression'] = 'подмаска №{$a->number}: [{$a->firstoperand}]';
$string['description_subexpression_once'] = 'однократная подмаска №{$a->number}: [{$a->firstoperand}]';
$string['description_subexpression_name'] = 'подмаска "{$a->name}": [{$a->firstoperand}]';
$string['description_subexpression_once_name'] = 'однократная подмаска "{$a->name}": [{$a->firstoperand}]';
$string['description_grouping'] = 'группировка: [{$a->firstoperand}]';
$string['description_grouping_duplicate'] = 'группировка (номера подмасок сбрасываются в каждой из альтернатив): [{$a->firstoperand}]';
// TYPE_NODE_COND_SUBEXPR ({$a->firstoperand} - first option; {$a->secondoperand} - second option; {$a->cond} - condition )
$string['description_node_cond_subexpr'] = 'если {$a->cond}, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_node_cond_subexpr_else'] = ' иначе проверить: [{$a->secondoperand}]';
$string['description_backref_node_cond_subexpr'] = 'если подмаска №{$a->number} была успешно сопоставлена, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_backref_node_cond_subexpr_name'] = 'если подмаска "{$a->name}" была успешно сопоставлена, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_recursive_node_cond_subexpr_all'] = 'если весь шаблон был рекурсивно сопоставлен тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_recursive_node_cond_subexpr'] = 'если подмаска №{$a->number} была успешно рекурсивно сопоставлена, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_recursive_node_cond_subexpr_name'] = 'если подмаска "{$a->name}" была успешно рекурсивно сопоставлена, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_define_node_cond_subexpr'] = 'описание {$a->firstoperand}';
// TYPE_LEAF_CONTROL
$string['description_accept_leaf_control'] = 'спровоцировать удачное совпадение';
$string['description_fail_leaf_control'] = 'спровоцировать неудачу';
$string['description_mark_name_leaf_control'] = 'задайте имя для {$a->name} которое будет возвращено';
$string['description_control_backtrack'] = 'если остальные шаблон не соответствует {$a->what}';
$string['description_commit_leaf_control'] = 'общие неудачи, нет предыдущей отправной точки';
$string['description_prune_leaf_control'] = 'перейти к следующему начальному символу';
$string['description_skip_leaf_control'] = 'перейти к текущей позиции поиска';
$string['description_skip_name_leaf_control'] = 'перейти к (*MARK:{$a->name})';
$string['description_then_leaf_control'] = 'возврат к следующему чередованию';
$string['description_control_newline'] = 'совпадение с новыми строками {$a->what}';
$string['description_cr_leaf_control'] = 'только возврат каретки';
$string['description_lf_leaf_control'] = 'только символ новой строки';
$string['description_crlf_leaf_control'] = 'возврат каретки, сопровождаемый переводом строки';
$string['description_anycrlf_leaf_control'] = 'возврат каретки, перевод строки или возврат каретки, сопровождаемый переводом строки';
$string['description_any_leaf_control'] = 'любой Unicode символ новой строки';
$string['description_control_r'] = '\R совпадает с {$a->what}';
$string['description_bsr_anycrlf_leaf_control'] = 'CR, LF, или CRLF';
$string['description_bsr_unicode_leaf_control'] = 'любой Unicode символ новой строки';
$string['description_no_start_opt_leaf_control'] = 'no start-match optimization';
$string['description_utf8_leaf_control'] = 'UTF-8 мод';
$string['description_utf16_leaf_control'] = 'UTF-16 мод';
$string['description_ucp_leaf_control'] = 'PCRE_UCP';
// TYPE_LEAF_CHARSET
$string['description_charset'] = 'один из следующих символов: {$a->characters};';
$string['description_charset_negative'] = 'любой из символов кроме следующих: {$a->characters};';
$string['description_charset_one_neg'] = 'не {$a->characters}';
$string['description_charset_range'] = 'любой символ от {$a->start} до {$a->end}';
$string['description_char'] = '<span style="color:blue">{$a->char}</span>';
$string['description_char_16value'] = 'символ с кодом 0x{$a->code}';
//$string['description_charset_one'] = '{$a->characters}';
// non-printing characters
$string['description_charflag_dot'] = 'любой символ';
$string['description_charflag_slashd'] = 'десятичная цифра';
$string['description_charflag_slashh'] = 'символ горизонтального белого разделителя';
$string['description_charflag_slashs'] = 'белый разделитель';
$string['description_charflag_slashv'] = 'символ вертикального белого разделителя';//TODO - third string for description \v is it good?
$string['description_charflag_slashw'] = 'символ слова';
$string['description_char0'] = 'ноль-символ(NUL)';
$string['description_char1'] = 'символ начала заголовка(SOH)';
$string['description_char2'] = 'символ начала такста(STX)';
$string['description_char3'] = 'символ конца текста(ETX)';
$string['description_char4'] = 'символ конца передачи(EOT)';
$string['description_char5'] = 'символ запроса подтверждения(ENQ)';
$string['description_char6'] = 'символ подтверждения(ACK)';// ?! ВОТАФАКИЗЗИС?!
$string['description_char7'] = 'вукового сигнала(BEL)';
$string['description_char8'] = 'символ удаления(BS)';
$string['description_char9'] = 'табуляция(HT)';
$string['description_charA'] = 'перевод строки(LF)';
$string['description_charB'] = 'вертикальная табуляция(VT)'; // TODO - \v already has a string but this string is used when user type \xb ?
$string['description_charC'] = 'символ новой страницы(FF)';
$string['description_charD'] = 'символ возврата каретки(CR)';
$string['description_charE'] = 'shift out символ(SO)';
$string['description_charF'] = 'shift in символ(SI)';
$string['description_char10'] = 'символ освобождения канала данных(DLE)';
$string['description_char11'] = 'символ управления устройством(DC1)';
$string['description_char12'] = 'символ управления устройством(DC2)';
$string['description_char13'] = 'символ управления устройством(DC3)';
$string['description_char14'] = 'символ управления устройством(DC4)';
$string['description_char15'] = 'символ неподтверждения(NAK)';
$string['description_char16'] = 'символ синхронизации(SYN)';
$string['description_char17'] = 'конца текстового блока(ETB)';
$string['description_char18'] = 'символ отмены(CAN)';
$string['description_char19'] = 'конец носителя(EM)';
$string['description_char1A'] = 'подставитель(SUB)';
$string['description_char1B'] = 'esc-символ(ESC)';
$string['description_char1C'] = 'разделитель файлов(FS)';
$string['description_char1D'] = 'разделитель групп(GS)';
$string['description_char1E'] = 'разделитель записей(RS)';
$string['description_char1F'] = 'разделитель юнитов(US)';
$string['description_char20'] = 'пробел';
$string['description_char7F'] = 'символ удаления(DEL)';
$string['description_charA0'] = 'неразрывный пробел';
$string['description_charAD'] = 'символ мягкого переноса';
$string['description_char2002'] = 'en пробел';
$string['description_char2003'] = 'em пробел';
$string['description_char2009'] = 'тонкий пробел';
$string['description_char200C'] = 'zero width non-joiner';
$string['description_char200D'] = 'zero width joiner';
//CHARSET FLAGS
$string['description_charflag_digit'] = 'десятичное число';
$string['description_charflag_xdigit'] = 'шестнадцатиричное число';
$string['description_charflag_space'] = 'пробел';
$string['description_charflag_word'] = 'символ-слово';
$string['description_charflag_alnum'] = 'буква или цифра';
$string['description_charflag_alpha'] = 'буква';
$string['description_charflag_ascii'] = 'символы с кодом 0-127';
$string['description_charflag_cntrl'] = 'служебный символ';
$string['description_charflag_graph'] = 'печатный символ';
$string['description_charflag_lower'] = 'строчная буква';
$string['description_charflag_upper'] = 'заглавная буква';
$string['description_charflag_print'] = 'печатный символ (включая пробел)';
$string['description_charflag_punct'] = 'печатный символ (исключая буквы, цифры и пробел)';
$string['description_charflag_hspace'] = 'горизонтальный пробел'; // ??
$string['description_charflag_vspace'] = 'вертикальный пробел';// ??!!
$string['description_charflag_Cc'] = 'ASCII или Latin-1 служебный символ';
$string['description_charflag_Cf'] = 'непечатные символы форматирования (Unicode)';
$string['description_charflag_Cn'] = 'символ, отсутствующий в юникоде,';// ??
$string['description_charflag_Co'] = 'символ с кодом, выделенным для приватного использования,';
$string['description_charflag_Cs'] = 'surrogate';
$string['description_charflag_C'] = 'непечатный символ или неиспользуемый код символа';
$string['description_charflag_Ll'] = 'буква в нижнем регистре';
$string['description_charflag_Lm'] = 'спец. символ, используемый как буква,';
$string['description_charflag_Lo'] = 'буква без заглавного варианта';
$string['description_charflag_Lt'] = 'буква в заглавном регистре';
$string['description_charflag_Lu'] = 'буква в верхнем регистре';
$string['description_charflag_L'] = 'буква';
$string['description_charflag_Mc'] = 'пробельный символ';
$string['description_charflag_Me'] = 'enclosing mark';
$string['description_charflag_Mn'] = 'не пробельный символ';
$string['description_charflag_M'] = 'метка';
$string['description_charflag_Nd'] = 'десятичное число';
$string['description_charflag_Nl'] = 'letter number';
$string['description_charflag_No'] = 'другое число';
$string['description_charflag_N'] = 'число';
$string['description_charflag_Pc'] = 'connector punctuation';
$string['description_charflag_Pd'] = 'тире';
$string['description_charflag_Pe'] = 'close punctuation';
$string['description_charflag_Pf'] = 'final punctuation';
$string['description_charflag_Pi'] = 'initial punctuation';
$string['description_charflag_Po'] = 'other punctuation';
$string['description_charflag_Ps'] = 'open punctuation';
$string['description_charflag_P'] = 'пунктуация';
$string['description_charflag_Sc'] = 'денежный символ';
$string['description_charflag_Sk'] = 'символ-модификатор';// ??
$string['description_charflag_Sm'] = 'математический символ';
$string['description_charflag_So'] = 'символ (не математический, денежный)';
$string['description_charflag_S'] = 'символ';
$string['description_charflag_Zl'] = 'разделитель строк';
$string['description_charflag_Zp'] = 'разделитель параграфов';
$string['description_charflag_Zs'] = 'пробельный разделитель';
$string['description_charflag_Z'] = 'разделитель';
$string['description_charflag_Xan'] = 'алфавитно-числовой символ';
$string['description_charflag_Xps'] = 'любой POSIX пробельный символ';
$string['description_charflag_Xsp'] = 'любой Perl пробельный символ';
$string['description_charflag_Xwd'] = 'любой Perl символ-слово';
$string['description_charflag_Arabic'] = 'Арабская символ';
$string['description_charflag_Armenian'] = 'Армянский символ';
$string['description_charflag_Avestan'] = 'Авестийский символ';
$string['description_charflag_Balinese'] = 'Balinese символ';
$string['description_charflag_Bamum'] = 'Bamum символ';
$string['description_charflag_Bengali'] = 'Bengali символ';
$string['description_charflag_Bopomofo'] = 'Bopomofo символ';
$string['description_charflag_Braille'] = 'Braille символ';
$string['description_charflag_Buginese'] = 'Buginese символ';
$string['description_charflag_Buhid'] = 'Buhid символ';
$string['description_charflag_Canadian_Aboriginal'] = 'Canadian Aboriginal символ';
$string['description_charflag_Carian'] = 'Carian символ';
$string['description_charflag_Cham'] = 'Cham символ';
$string['description_charflag_Cherokee'] = 'Cherokee символ';
$string['description_charflag_Common'] = 'Common символ';
$string['description_charflag_Coptic'] = 'Coptic символ';
$string['description_charflag_Cuneiform'] = 'Cuneiform символ';
$string['description_charflag_Cypriot'] = 'Cypriot символ';
$string['description_charflag_Cyrillic'] = 'Cyrillic символ';
$string['description_charflag_Deseret'] = 'Deseret символ';
$string['description_charflag_Devanagari'] = 'Devanagari символ';
$string['description_charflag_Egyptian_Hieroglyphs'] = 'Egyptian Hieroglyphs символ';
$string['description_charflag_Ethiopic'] = 'Ethiopic символ';
$string['description_charflag_Georgian'] = 'Georgian символ';
$string['description_charflag_Glagolitic'] = 'Glagolitic символ';
$string['description_charflag_Gothic'] = 'Gothic символ';
$string['description_charflag_Greek'] = 'Greek символ';
$string['description_charflag_Gujarati'] = 'Gujarati символ';
$string['description_charflag_Gurmukhi'] = 'Gurmukhi символ';
$string['description_charflag_Han'] = 'Han символ';
$string['description_charflag_Hangul'] = 'Hangul символ';
$string['description_charflag_Hanunoo'] = 'Hanunoo символ';
$string['description_charflag_Hebrew'] = 'Hebrew символ';
$string['description_charflag_Hiragana'] = 'Hiragana символ';
$string['description_charflag_Imperial_Aramaic'] = 'Imperial Aramaic символ';
$string['description_charflag_Inherited'] = 'Inherited символ';
$string['description_charflag_Inscriptional_Pahlavi'] = 'Inscriptional Pahlavi символ';
$string['description_charflag_Inscriptional_Parthian'] = 'Inscriptional Parthian символ';
$string['description_charflag_Javanese'] = 'Javanese символ';
$string['description_charflag_Kaithi'] = 'Kaithi символ';
$string['description_charflag_Kannada'] = 'Kannada символ';
$string['description_charflag_Katakana'] = 'Katakana символ';
$string['description_charflag_Kayah_Li'] = 'Kayah Li символ';
$string['description_charflag_Kharoshthi'] = 'Kharoshthi символ';
$string['description_charflag_Khmer'] = 'Khmer символ';
$string['description_charflag_Lao'] = 'Lao символ';
$string['description_charflag_Latin'] = 'Latin символ';
$string['description_charflag_Lepcha'] = 'Lepcha символ';
$string['description_charflag_Limbu'] = 'Limbu символ';
$string['description_charflag_Linear_B'] = 'Linear B символ';
$string['description_charflag_Lisu'] = 'Lisu символ';
$string['description_charflag_Lycian'] = 'Lycian символ';
$string['description_charflag_Lydian'] = 'Lydian символ';
$string['description_charflag_Malayalam'] = 'Malayalam символ';
$string['description_charflag_Meetei_Mayek'] = 'Meetei Mayek символ';
$string['description_charflag_Mongolian'] = 'Mongolian символ';
$string['description_charflag_Myanmar'] = 'Myanmar символ';
$string['description_charflag_New_Tai_Lue'] = 'New Tai Lue символ';
$string['description_charflag_Nko'] = 'Nko символ';
$string['description_charflag_Ogham'] = 'Ogham символ';
$string['description_charflag_Old_Italic'] = 'Old Italic символ';
$string['description_charflag_Old_Persian'] = 'Old Persian символ';
$string['description_charflag_Old_South_Arabian'] = 'Old South_Arabian символ';
$string['description_charflag_Old_Turkic'] = 'Old_Turkic символ';
$string['description_charflag_Ol_Chiki'] = 'Ol_Chiki символ';
$string['description_charflag_Oriya'] = 'Oriya символ';
$string['description_charflag_Osmanya'] = 'Osmanya символ';
$string['description_charflag_Phags_Pa'] = 'Phags_Pa символ';
$string['description_charflag_Phoenician'] = 'Phoenician символ';
$string['description_charflag_Rejang'] = 'Rejang символ';
$string['description_charflag_Runic'] = 'Runic символ';
$string['description_charflag_Samaritan'] = 'Samaritan символ';
$string['description_charflag_Saurashtra'] = 'Saurashtra символ';
$string['description_charflag_Shavian'] = 'Shavian символ';
$string['description_charflag_Sinhala'] = 'Sinhala символ';
$string['description_charflag_Sundanese'] = 'Sundanese символ';
$string['description_charflag_Syloti_Nagri'] = 'Syloti_Nagri символ';
$string['description_charflag_Syriac'] = 'Syriac символ';
$string['description_charflag_Tagalog'] = 'Tagalog символ';
$string['description_charflag_Tagbanwa'] = 'Tagbanwa символ';
$string['description_charflag_Tai_Le'] = 'Tai_Le символ';
$string['description_charflag_Tai_Tham'] = 'Tai_Tham символ';
$string['description_charflag_Tai_Viet'] = 'Tai_Viet символ';
$string['description_charflag_Tamil'] = 'Tamil символ';
$string['description_charflag_Telugu'] = 'Telugu символ';
$string['description_charflag_Thaana'] = 'Thaana символ';
$string['description_charflag_Thai'] = 'Thai символ';
$string['description_charflag_Tibetan'] = 'Tibetan символ';
$string['description_charflag_Tifinagh'] = 'Tifinagh символ';
$string['description_charflag_Ugaritic'] = 'Ugaritic символ';
$string['description_charflag_Vai'] = 'Vai символ';
$string['description_charflag_Yi'] = 'Yi символ';
// description errors
$string['description_errorbefore'] = '<span style="color:red">';
$string['description_errorafter'] = '</span>';
// for testing
$string['description_charflag_word_g'] = 'символ слова(form g)';//for testing only
$string['description_char_g'] = '<span style="color:blue">{$a->char}</span>(form g)';//for testing only
$string['description_dollar_g'] = 'конец строки(form g)';//for testing
$string['description_concat_g'] = '{$a->g1} затем {$a->g2}';
$string['description_concat_short_g'] = '{$a->g1}{$a->g2}';
$string['description_alt_g'] = '{$a->g1} или {$a->g2}';
$string['description_alt_wcomma_g'] = '{$a->g1} или {$a->g2}';
$string['description_empty_g'] = 'ничего(form g)';

// Strings for explaining graph

$string['authoring_tool_explain_graph'] = 'объясняющий граф';

$string['explain_subexpression'] = 'подвыражение №';
$string['explain_backref'] = 'результат подвыражения №';
$string['explain_recursion'] = 'рекурсия';
$string['explain_unknow_node'] = 'неизвестный узел';
$string['explain_unknow_meta'] = 'неизвестный мета-узел';
$string['explain_unknow_assert'] = 'неизвестное утверждение';
$string['explain_unknow_charset_flag'] = 'неизвестный флаг набора символов';
$string['explain_not'] = 'не ';
$string['explain_any_char'] = 'Любой символ из';
$string['explain_any_char_except'] = 'Любой символ кроме';
$string['explain_to'] = ' по ';
$string['explain_from'] = ' c ';
