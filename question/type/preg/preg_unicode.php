<?php

class qtype_preg_unicode extends textlib {

    public static $ranges = array('Basic Latin'                               => array(0x0000, 0x007F),
                                  'C1 Controls and Latin-1 Supplement'        => array(0x0080, 0x00FF),
                                  'Latin Extended-A'                          => array(0x0100, 0x017F),
                                  'Latin Extended-B'                          => array(0x0180, 0x024F),
                                  'IPA Extensions'                            => array(0x0250, 0x02AF),
                                  'Spacing Modifier Letters'                  => array(0x02B0, 0x02FF),
                                  'Combining Diacritical Marks'               => array(0x0300, 0x036F),
                                  'Greek/Coptic'                              => array(0x0370, 0x03FF),
                                  'Cyrillic'                                  => array(0x0400, 0x04FF),
                                  'Cyrillic Supplement'                       => array(0x0500, 0x052F),
                                  'Armenian'                                  => array(0x0530, 0x058F),
                                  'Hebrew'                                    => array(0x0590, 0x05FF),
                                  'Arabic'                                    => array(0x0600, 0x06FF),
                                  'Syriac'                                    => array(0x0700, 0x074F),
                                  //'Undefined'                               => array(0x0750, 0x077F),
                                  'Thaana'                                    => array(0x0780, 0x07BF),
                                  //'Undefined'                               => array(0x07C0, 0x08FF),
                                  'Devanagari'                                => array(0x0900, 0x097F),
                                  'Bengali/Assamese'                          => array(0x0980, 0x09FF),
                                  'Gurmukhi'                                  => array(0x0A00, 0x0A7F),
                                  'Gujarati'                                  => array(0x0A80, 0x0AFF),
                                  'Oriya'                                     => array(0x0B00, 0x0B7F),
                                  'Tamil'                                     => array(0x0B80, 0x0BFF),
                                  'Telugu'                                    => array(0x0C00, 0x0C7F),
                                  'Kannada'                                   => array(0x0C80, 0x0CFF),
                                  'Malayalam'                                 => array(0x0D00, 0x0DFF),
                                  'Sinhala'                                   => array(0x0D80, 0x0DFF),
                                  'Thai'                                      => array(0x0E00, 0x0E7F),
                                  'Lao'                                       => array(0x0E80, 0x0EFF),
                                  'Tibetan'                                   => array(0x0F00, 0x0FFF),
                                  'Myanmar'                                   => array(0x1000, 0x109F),
                                  'Georgian'                                  => array(0x10A0, 0x10FF),
                                  'Hangul Jamo'                               => array(0x1100, 0x11FF),
                                  'Ethiopic'                                  => array(0x1200, 0x137F),
                                  //'Undefined'                               => array(0x1380, 0x139F),
                                  'Cherokee'                                  => array(0x13A0, 0x13FF),
                                  'Unified Canadian Aboriginal Syllabics'     => array(0x1400, 0x167F),
                                  'Ogham'                                     => array(0x1680, 0x169F),
                                  'Runic'                                     => array(0x16A0, 0x16FF),
                                  'Tagalog'                                   => array(0x1700, 0x171F),
                                  'Hanunoo'                                   => array(0x1720, 0x173F),
                                  'Buhid'                                     => array(0x1740, 0x175F),
                                  'Tagbanwa'                                  => array(0x1760, 0x177F),
                                  'Khmer'                                     => array(0x1780, 0x17FF),
                                  'Mongolian'                                 => array(0x1800, 0x18AF),
                                  //'Undefined'                               => array(0x18B0, 0x18FF),
                                  'Limbu'                                     => array(0x1900, 0x194F),
                                  'Tai Le'                                    => array(0x1950, 0x197F),
                                  //'Undefined'                               => array(0x1980, 0x19DF),
                                  'Khmer Symbols'                             => array(0x19E0, 0x19FF),
                                  //'Undefined'                               => array(0x1A00, 0x1CFF),
                                  'Phonetic Extensions'                       => array(0x1D00, 0x1D7F),
                                  //'Undefined'                               => array(0x1D80, 0x1DFF),
                                  'Latin Extended Additional'                 => array(0x1E00, 0x1EFF),
                                  'Greek Extended'                            => array(0x1F00, 0x1FFF),
                                  'General Punctuation'                       => array(0x2000, 0x206F),
                                  'Superscripts and Subscripts'               => array(0x2070, 0x209F),
                                  'Currency Symbols'                          => array(0x20A0, 0x20CF),
                                  'Combining Diacritical Marks for Symbols'   => array(0x20D0, 0x20FF),
                                  'Letterlike Symbols'                        => array(0x2100, 0x214F),
                                  'Number Forms'                              => array(0x2150, 0x218F),
                                  'Arrows'                                    => array(0x2190, 0x21FF),
                                  'Mathematical Operators'                    => array(0x2200, 0x22FF),
                                  'Miscellaneous Technical'                   => array(0x2300, 0x23FF),
                                  'Control Pictures'                          => array(0x2400, 0x243F),
                                  'Optical Character Recognition'             => array(0x2440, 0x245F),
                                  'Enclosed Alphanumerics'                    => array(0x2460, 0x24FF),
                                  'Box Drawing'                               => array(0x2500, 0x257F),
                                  'Block Elements'                            => array(0x2580, 0x259F),
                                  'Geometric Shapes'                          => array(0x25A0, 0x25FF),
                                  'Miscellaneous Symbols'                     => array(0x2600, 0x26FF),
                                  'Dingbats'                                  => array(0x2700, 0x27BF),
                                  'Miscellaneous Mathematical Symbols-A'      => array(0x27C0, 0x27EF),
                                  'Supplemental Arrows-A'                     => array(0x27F0, 0x27FF),
                                  'Braille Patterns'                          => array(0x2800, 0x28FF),
                                  'Supplemental Arrows-B'                     => array(0x2900, 0x297F),
                                  'Miscellaneous Mathematical Symbols-B'      => array(0x2980, 0x29FF),
                                  'Supplemental Mathematical Operators'       => array(0x2A00, 0x2AFF),
                                  'Miscellaneous Symbols and Arrows'          => array(0x2B00, 0x2BFF),
                                  //'Undefined'                               => array(0x2C00, 0x2E7F),
                                  'CJK Radicals Supplement'                   => array(0x2E80, 0x2EFF),
                                  'Kangxi Radicals'                           => array(0x2F00, 0x2FDF),
                                  //'Undefined'                               => array(0x2FE0, 0x2FEF),
                                  'Ideographic Description Characters'        => array(0x2FF0, 0x2FFF),
                                  'CJK Symbols and Punctuation'               => array(0x3000, 0x303F),
                                  'Hiragana'                                  => array(0x3040, 0x309F),
                                  'Katakana'                                  => array(0x30A0, 0x30FF),
                                  'Bopomofo'                                  => array(0x3100, 0x312F),
                                  'Hangul Compatibility Jamo'                 => array(0x3130, 0x318F),
                                  'Kanbun (0xKunten),'                        => array(0x3190, 0x319F),
                                  'Bopomofo Extended'                         => array(0x31A0, 0x31BF),
                                  //'Undefined'                               => array(0x31C0, 0x31EF),
                                  'Katakana Phonetic Extensions'              => array(0x31F0, 0x31FF),
                                  'Enclosed CJK Letters and Months'           => array(0x3200, 0x32FF),
                                  'CJK Compatibility'                         => array(0x3300, 0x33FF),
                                  'CJK Unified Ideographs Extension A'        => array(0x3400, 0x4DBF),
                                  'Yijing Hexagram Symbols'                   => array(0x4DC0, 0x4DFF),
                                  'CJK Unified Ideographs'                    => array(0x4E00, 0x9FAF),
                                  //'Undefined'                               => array(0x9FB0, 0x9FFF),
                                  'Yi Syllables'                              => array(0xA000, 0xA48F),
                                  'Yi Radicals'                               => array(0xA490, 0xA4CF),
                                  //'Undefined'                               => array(0xA4D0, 0xABFF),
                                  'Hangul Syllables'                          => array(0xAC00, 0xD7AF),
                                  //'Undefined'                               => array(0xD7B0, 0xD7FF),
                                  'High Surrogate Area'                       => array(0xD800, 0xDBFF),
                                  'Low Surrogate Area'                        => array(0xDC00, 0xDFFF),
                                  'Private Use Area'                          => array(0xE000, 0xF8FF),
                                  'CJK Compatibility Ideographs'              => array(0xF900, 0xFAFF),
                                  'Alphabetic Presentation Forms'             => array(0xFB00, 0xFB4F),
                                  'Arabic Presentation Forms-A'               => array(0xFB50, 0xFDFF),
                                  'Variation Selectors'                       => array(0xFE00, 0xFE0F),
                                  //'Undefined'                               => array(0xFE10, 0xFE1F),
                                  'Combining Half Marks'                      => array(0xFE20, 0xFE2F),
                                  'CJK Compatibility Forms'                   => array(0xFE30, 0xFE4F),
                                  'Small Form Variants'                       => array(0xFE50, 0xFE6F),
                                  'Arabic Presentation Forms-B'               => array(0xFE70, 0xFEFF),
                                  'Halfwidth and Fullwidth Forms'             => array(0xFF00, 0xFFEF),
                                  'Specials'                                  => array(0xFFF0, 0xFFFF),
                                  'Linear B Syllabary'                        => array(0x10000, 0x1007F),
                                  'Linear B Ideograms'                        => array(0x10080, 0x100FF),
                                  'Aegean Numbers'                            => array(0x10100, 0x1013F),
                                  //'Undefined'                               => array(0x10140, 0x102FF),
                                  'Old Italic'                                => array(0x10300, 0x1032F),
                                  'Gothic'                                    => array(0x10330, 0x1034F),
                                  'Ugaritic'                                  => array(0x10380, 0x1039F),
                                  'Deseret'                                   => array(0x10400, 0x1044F),
                                  'Shavian'                                   => array(0x10450, 0x1047F),
                                  'Osmanya'                                   => array(0x10480, 0x104AF),
                                  //'Undefined'                               => array(0x104B0, 0x107FF),
                                  'Cypriot Syllabary'                         => array(0x10800, 0x1083F),
                                  //'Undefined'                               => array(0x10840, 0x1CFFF),
                                  'Byzantine Musical Symbols'                 => array(0x1D000, 0x1D0FF),
                                  'Musical Symbols'                           => array(0x1D100, 0x1D1FF),
                                  //'Undefined'                               => array(0x1D200, 0x1D2FF),
                                  'Tai Xuan Jing Symbols'                     => array(0x1D300, 0x1D35F),
                                  //'Undefined'                               => array(0x1D360, 0x1D3FF),
                                  'Mathematical Alphanumeric Symbols'         => array(0x1D400, 0x1D7FF),
                                  //'Undefined'                               => array(0x1D800, 0x1FFFF),
                                  'CJK Unified Ideographs Extension B'        => array(0x20000, 0x2A6DF),
                                  //'Undefined'                               => array(0x2A6E0, 0x2F7FF),
                                  'CJK Compatibility Ideographs Supplement'   => array(0x2F800, 0x2FA1F),
                                  //'Unused'                                  => array(0x2FAB0, 0xDFFFF),
                                  'Tags'                                      => array(0xE0000, 0xE007F),
                                  //'Unused'                                  => array(0xE0080, 0xE00FF),
                                  'Variation Selectors Supplement'            => array(0xE0100, 0xE01EF),
                                  //'Unused'                                  => array(0xE01F0, 0xEFFFF),
                                  'Supplementary Private Use Area-A'          => array(0xF0000, 0xFFFFD),
                                  //'Unused'                                  => array(0xFFFFE, 0xFFFFF),
                                  'Supplementary Private Use Area-B'          => array(0x100000, 0x10FFFD)
                                  );

    public static $hspaces = array(0x0009,    // Horizontal tab.
                                   0x0020,    // Space.
                                   0x00A0,    // Non-break space.
                                   0x1680,    // Ogham space mark.
                                   0x180E,    // Mongolian vowel separator.
                                   0x2000,    // En quad.
                                   0x2001,    // Em quad.
                                   0x2002,    // En space.
                                   0x2003,    // Em space.
                                   0x2004,    // Three-per-em space.
                                   0x2005,    // Four-per-em space.
                                   0x2006,    // Six-per-em space.
                                   0x2007,    // Figure space.
                                   0x2008,    // Punctuation space.
                                   0x2009,    // Thin space.
                                   0x200A,    // Hair space.
                                   0x202F,    // Narrow no-break space.
                                   0x205F,    // Medium mathematical space.
                                   0x3000     // Ideographic space.
                                   );

    public static $vspaces = array(0x000A,    // Linefeed.
                                   0x000B,    // Vertical tab.
                                   0x000C,    // Formfeed.
                                   0x000D,    // Carriage return.
                                   0x0085,    // Next line.
                                   0x2028,    // Line separator.
                                   0x2029     // Paragraph separator.
                                   );

    /******************************************************************/
    public static function Cc_ranges() {
        return array(array('left'=>0x0000, 'right'=>0x001F),
                     array('left'=>0x007F, 'right'=>0x009F));
    }
    public static function Cf_ranges() {
        return array(array('left'=>0x00AD, 'right'=>0x00AD),
                     array('left'=>0x0600, 'right'=>0x0604),
                     array('left'=>0x06DD, 'right'=>0x06DD),
                     array('left'=>0x070F, 'right'=>0x070F),
                     array('left'=>0x200B, 'right'=>0x200F),
                     array('left'=>0x202A, 'right'=>0x202E),
                     array('left'=>0x2060, 'right'=>0x2064),
                     array('left'=>0x206A, 'right'=>0x206F),
                     array('left'=>0xFEFF, 'right'=>0xFEFF),
                     array('left'=>0xFFF9, 'right'=>0xFFFB),
                     array('left'=>0x110BD, 'right'=>0x110BD),
                     array('left'=>0x1D173, 'right'=>0x1D17A),
                     array('left'=>0xE0001, 'right'=>0xE0001),
                     array('left'=>0xE0020, 'right'=>0xE007F));
    }
    public static function Cn_ranges() {
        return array();
    }
    public static function Co_ranges() {
        return array(array('left'=>0xE000, 'right'=>0xE000),
                     array('left'=>0xF8FF, 'right'=>0xF8FF),
                     array('left'=>0xF0000, 'right'=>0xF0000),
                     array('left'=>0xFFFFD, 'right'=>0xFFFFD),
                     array('left'=>0x100000, 'right'=>0x100000),
                     array('left'=>0x10FFFD, 'right'=>0x10FFFD));
    }
    public static function Cs_ranges() {
        return array(array('left'=>0xD800, 'right'=>0xD800),
                     array('left'=>0xDB7F, 'right'=>0xDB80),
                     array('left'=>0xDBFF, 'right'=>0xDC00),
                     array('left'=>0xDFFF, 'right'=>0xDFFF));
    }
    public static function C_ranges() {
        return array_merge(self::Cc_ranges(), self::Cf_ranges(),
                           self::Co_ranges(), self::Cs_ranges());
    }
    /******************************************************************/
    public static function Ll_ranges() {
        return array(array('left'=>0x0061, 'right'=>0x007A),
                     array('left'=>0x00B5, 'right'=>0x00B5),
                     array('left'=>0x00DF, 'right'=>0x00F6),
                     array('left'=>0x00F8, 'right'=>0x00FF),
                     array('left'=>0x0101, 'right'=>0x0101),
                     array('left'=>0x0103, 'right'=>0x0103),
                     array('left'=>0x0105, 'right'=>0x0105),
                     array('left'=>0x0107, 'right'=>0x0107),
                     array('left'=>0x0109, 'right'=>0x0109),
                     array('left'=>0x010B, 'right'=>0x010B),
                     array('left'=>0x010D, 'right'=>0x010D),
                     array('left'=>0x010F, 'right'=>0x010F),
                     array('left'=>0x0111, 'right'=>0x0111),
                     array('left'=>0x0113, 'right'=>0x0113),
                     array('left'=>0x0115, 'right'=>0x0115),
                     array('left'=>0x0117, 'right'=>0x0117),
                     array('left'=>0x0119, 'right'=>0x0119),
                     array('left'=>0x011B, 'right'=>0x011B),
                     array('left'=>0x011D, 'right'=>0x011D),
                     array('left'=>0x011F, 'right'=>0x011F),
                     array('left'=>0x0121, 'right'=>0x0121),
                     array('left'=>0x0123, 'right'=>0x0123),
                     array('left'=>0x0125, 'right'=>0x0125),
                     array('left'=>0x0127, 'right'=>0x0127),
                     array('left'=>0x0129, 'right'=>0x0129),
                     array('left'=>0x012B, 'right'=>0x012B),
                     array('left'=>0x012D, 'right'=>0x012D),
                     array('left'=>0x012F, 'right'=>0x012F),
                     array('left'=>0x0131, 'right'=>0x0131),
                     array('left'=>0x0133, 'right'=>0x0133),
                     array('left'=>0x0135, 'right'=>0x0135),
                     array('left'=>0x0137, 'right'=>0x0138),
                     array('left'=>0x013A, 'right'=>0x013A),
                     array('left'=>0x013C, 'right'=>0x013C),
                     array('left'=>0x013E, 'right'=>0x013E),
                     array('left'=>0x0140, 'right'=>0x0140),
                     array('left'=>0x0142, 'right'=>0x0142),
                     array('left'=>0x0144, 'right'=>0x0144),
                     array('left'=>0x0146, 'right'=>0x0146),
                     array('left'=>0x0148, 'right'=>0x0149),
                     array('left'=>0x014B, 'right'=>0x014B),
                     array('left'=>0x014D, 'right'=>0x014D),
                     array('left'=>0x014F, 'right'=>0x014F),
                     array('left'=>0x0151, 'right'=>0x0151),
                     array('left'=>0x0153, 'right'=>0x0153),
                     array('left'=>0x0155, 'right'=>0x0155),
                     array('left'=>0x0157, 'right'=>0x0157),
                     array('left'=>0x0159, 'right'=>0x0159),
                     array('left'=>0x015B, 'right'=>0x015B),
                     array('left'=>0x015D, 'right'=>0x015D),
                     array('left'=>0x015F, 'right'=>0x015F),
                     array('left'=>0x0161, 'right'=>0x0161),
                     array('left'=>0x0163, 'right'=>0x0163),
                     array('left'=>0x0165, 'right'=>0x0165),
                     array('left'=>0x0167, 'right'=>0x0167),
                     array('left'=>0x0169, 'right'=>0x0169),
                     array('left'=>0x016B, 'right'=>0x016B),
                     array('left'=>0x016D, 'right'=>0x016D),
                     array('left'=>0x016F, 'right'=>0x016F),
                     array('left'=>0x0171, 'right'=>0x0171),
                     array('left'=>0x0173, 'right'=>0x0173),
                     array('left'=>0x0175, 'right'=>0x0175),
                     array('left'=>0x0177, 'right'=>0x0177),
                     array('left'=>0x017A, 'right'=>0x017A),
                     array('left'=>0x017C, 'right'=>0x017C),
                     array('left'=>0x017E, 'right'=>0x0180),
                     array('left'=>0x0183, 'right'=>0x0183),
                     array('left'=>0x0185, 'right'=>0x0185),
                     array('left'=>0x0188, 'right'=>0x0188),
                     array('left'=>0x018C, 'right'=>0x018D),
                     array('left'=>0x0192, 'right'=>0x0192),
                     array('left'=>0x0195, 'right'=>0x0195),
                     array('left'=>0x0199, 'right'=>0x019B),
                     array('left'=>0x019E, 'right'=>0x019E),
                     array('left'=>0x01A1, 'right'=>0x01A1),
                     array('left'=>0x01A3, 'right'=>0x01A3),
                     array('left'=>0x01A5, 'right'=>0x01A5),
                     array('left'=>0x01A8, 'right'=>0x01A8),
                     array('left'=>0x01AA, 'right'=>0x01AB),
                     array('left'=>0x01AD, 'right'=>0x01AD),
                     array('left'=>0x01B0, 'right'=>0x01B0),
                     array('left'=>0x01B4, 'right'=>0x01B4),
                     array('left'=>0x01B6, 'right'=>0x01B6),
                     array('left'=>0x01B9, 'right'=>0x01BA),
                     array('left'=>0x01BD, 'right'=>0x01BF),
                     array('left'=>0x01C6, 'right'=>0x01C6),
                     array('left'=>0x01C9, 'right'=>0x01C9),
                     array('left'=>0x01CC, 'right'=>0x01CC),
                     array('left'=>0x01CE, 'right'=>0x01CE),
                     array('left'=>0x01D0, 'right'=>0x01D0),
                     array('left'=>0x01D2, 'right'=>0x01D2),
                     array('left'=>0x01D4, 'right'=>0x01D4),
                     array('left'=>0x01D6, 'right'=>0x01D6),
                     array('left'=>0x01D8, 'right'=>0x01D8),
                     array('left'=>0x01DA, 'right'=>0x01DA),
                     array('left'=>0x01DC, 'right'=>0x01DD),
                     array('left'=>0x01DF, 'right'=>0x01DF),
                     array('left'=>0x01E1, 'right'=>0x01E1),
                     array('left'=>0x01E3, 'right'=>0x01E3),
                     array('left'=>0x01E5, 'right'=>0x01E5),
                     array('left'=>0x01E7, 'right'=>0x01E7),
                     array('left'=>0x01E9, 'right'=>0x01E9),
                     array('left'=>0x01EB, 'right'=>0x01EB),
                     array('left'=>0x01ED, 'right'=>0x01ED),
                     array('left'=>0x01EF, 'right'=>0x01F0),
                     array('left'=>0x01F3, 'right'=>0x01F3),
                     array('left'=>0x01F5, 'right'=>0x01F5),
                     array('left'=>0x01F9, 'right'=>0x01F9),
                     array('left'=>0x01FB, 'right'=>0x01FB),
                     array('left'=>0x01FD, 'right'=>0x01FD),
                     array('left'=>0x01FF, 'right'=>0x01FF),
                     array('left'=>0x0201, 'right'=>0x0201),
                     array('left'=>0x0203, 'right'=>0x0203),
                     array('left'=>0x0205, 'right'=>0x0205),
                     array('left'=>0x0207, 'right'=>0x0207),
                     array('left'=>0x0209, 'right'=>0x0209),
                     array('left'=>0x020B, 'right'=>0x020B),
                     array('left'=>0x020D, 'right'=>0x020D),
                     array('left'=>0x020F, 'right'=>0x020F),
                     array('left'=>0x0211, 'right'=>0x0211),
                     array('left'=>0x0213, 'right'=>0x0213),
                     array('left'=>0x0215, 'right'=>0x0215),
                     array('left'=>0x0217, 'right'=>0x0217),
                     array('left'=>0x0219, 'right'=>0x0219),
                     array('left'=>0x021B, 'right'=>0x021B),
                     array('left'=>0x021D, 'right'=>0x021D),
                     array('left'=>0x021F, 'right'=>0x021F),
                     array('left'=>0x0221, 'right'=>0x0221),
                     array('left'=>0x0223, 'right'=>0x0223),
                     array('left'=>0x0225, 'right'=>0x0225),
                     array('left'=>0x0227, 'right'=>0x0227),
                     array('left'=>0x0229, 'right'=>0x0229),
                     array('left'=>0x022B, 'right'=>0x022B),
                     array('left'=>0x022D, 'right'=>0x022D),
                     array('left'=>0x022F, 'right'=>0x022F),
                     array('left'=>0x0231, 'right'=>0x0231),
                     array('left'=>0x0233, 'right'=>0x0239),
                     array('left'=>0x023C, 'right'=>0x023C),
                     array('left'=>0x023F, 'right'=>0x0240),
                     array('left'=>0x0242, 'right'=>0x0242),
                     array('left'=>0x0247, 'right'=>0x0247),
                     array('left'=>0x0249, 'right'=>0x0249),
                     array('left'=>0x024B, 'right'=>0x024B),
                     array('left'=>0x024D, 'right'=>0x024D),
                     array('left'=>0x024F, 'right'=>0x0293),
                     array('left'=>0x0295, 'right'=>0x02AF),
                     array('left'=>0x0371, 'right'=>0x0371),
                     array('left'=>0x0373, 'right'=>0x0373),
                     array('left'=>0x0377, 'right'=>0x0377),
                     array('left'=>0x037B, 'right'=>0x037D),
                     array('left'=>0x0390, 'right'=>0x0390),
                     array('left'=>0x03AC, 'right'=>0x03CE),
                     array('left'=>0x03D0, 'right'=>0x03D1),
                     array('left'=>0x03D5, 'right'=>0x03D7),
                     array('left'=>0x03D9, 'right'=>0x03D9),
                     array('left'=>0x03DB, 'right'=>0x03DB),
                     array('left'=>0x03DD, 'right'=>0x03DD),
                     array('left'=>0x03DF, 'right'=>0x03DF),
                     array('left'=>0x03E1, 'right'=>0x03E1),
                     array('left'=>0x03E3, 'right'=>0x03E3),
                     array('left'=>0x03E5, 'right'=>0x03E5),
                     array('left'=>0x03E7, 'right'=>0x03E7),
                     array('left'=>0x03E9, 'right'=>0x03E9),
                     array('left'=>0x03EB, 'right'=>0x03EB),
                     array('left'=>0x03ED, 'right'=>0x03ED),
                     array('left'=>0x03EF, 'right'=>0x03F3),
                     array('left'=>0x03F5, 'right'=>0x03F5),
                     array('left'=>0x03F8, 'right'=>0x03F8),
                     array('left'=>0x03FB, 'right'=>0x03FC),
                     array('left'=>0x0430, 'right'=>0x045F),
                     array('left'=>0x0461, 'right'=>0x0461),
                     array('left'=>0x0463, 'right'=>0x0463),
                     array('left'=>0x0465, 'right'=>0x0465),
                     array('left'=>0x0467, 'right'=>0x0467),
                     array('left'=>0x0469, 'right'=>0x0469),
                     array('left'=>0x046B, 'right'=>0x046B),
                     array('left'=>0x046D, 'right'=>0x046D),
                     array('left'=>0x046F, 'right'=>0x046F),
                     array('left'=>0x0471, 'right'=>0x0471),
                     array('left'=>0x0473, 'right'=>0x0473),
                     array('left'=>0x0475, 'right'=>0x0475),
                     array('left'=>0x0477, 'right'=>0x0477),
                     array('left'=>0x0479, 'right'=>0x0479),
                     array('left'=>0x047B, 'right'=>0x047B),
                     array('left'=>0x047D, 'right'=>0x047D),
                     array('left'=>0x047F, 'right'=>0x047F),
                     array('left'=>0x0481, 'right'=>0x0481),
                     array('left'=>0x048B, 'right'=>0x048B),
                     array('left'=>0x048D, 'right'=>0x048D),
                     array('left'=>0x048F, 'right'=>0x048F),
                     array('left'=>0x0491, 'right'=>0x0491),
                     array('left'=>0x0493, 'right'=>0x0493),
                     array('left'=>0x0495, 'right'=>0x0495),
                     array('left'=>0x0497, 'right'=>0x0497),
                     array('left'=>0x0499, 'right'=>0x0499),
                     array('left'=>0x049B, 'right'=>0x049B),
                     array('left'=>0x049D, 'right'=>0x049D),
                     array('left'=>0x049F, 'right'=>0x049F),
                     array('left'=>0x04A1, 'right'=>0x04A1),
                     array('left'=>0x04A3, 'right'=>0x04A3),
                     array('left'=>0x04A5, 'right'=>0x04A5),
                     array('left'=>0x04A7, 'right'=>0x04A7),
                     array('left'=>0x04A9, 'right'=>0x04A9),
                     array('left'=>0x04AB, 'right'=>0x04AB),
                     array('left'=>0x04AD, 'right'=>0x04AD),
                     array('left'=>0x04AF, 'right'=>0x04AF),
                     array('left'=>0x04B1, 'right'=>0x04B1),
                     array('left'=>0x04B3, 'right'=>0x04B3),
                     array('left'=>0x04B5, 'right'=>0x04B5),
                     array('left'=>0x04B7, 'right'=>0x04B7),
                     array('left'=>0x04B9, 'right'=>0x04B9),
                     array('left'=>0x04BB, 'right'=>0x04BB),
                     array('left'=>0x04BD, 'right'=>0x04BD),
                     array('left'=>0x04BF, 'right'=>0x04BF),
                     array('left'=>0x04C2, 'right'=>0x04C2),
                     array('left'=>0x04C4, 'right'=>0x04C4),
                     array('left'=>0x04C6, 'right'=>0x04C6),
                     array('left'=>0x04C8, 'right'=>0x04C8),
                     array('left'=>0x04CA, 'right'=>0x04CA),
                     array('left'=>0x04CC, 'right'=>0x04CC),
                     array('left'=>0x04CE, 'right'=>0x04CF),
                     array('left'=>0x04D1, 'right'=>0x04D1),
                     array('left'=>0x04D3, 'right'=>0x04D3),
                     array('left'=>0x04D5, 'right'=>0x04D5),
                     array('left'=>0x04D7, 'right'=>0x04D7),
                     array('left'=>0x04D9, 'right'=>0x04D9),
                     array('left'=>0x04DB, 'right'=>0x04DB),
                     array('left'=>0x04DD, 'right'=>0x04DD),
                     array('left'=>0x04DF, 'right'=>0x04DF),
                     array('left'=>0x04E1, 'right'=>0x04E1),
                     array('left'=>0x04E3, 'right'=>0x04E3),
                     array('left'=>0x04E5, 'right'=>0x04E5),
                     array('left'=>0x04E7, 'right'=>0x04E7),
                     array('left'=>0x04E9, 'right'=>0x04E9),
                     array('left'=>0x04EB, 'right'=>0x04EB),
                     array('left'=>0x04ED, 'right'=>0x04ED),
                     array('left'=>0x04EF, 'right'=>0x04EF),
                     array('left'=>0x04F1, 'right'=>0x04F1),
                     array('left'=>0x04F3, 'right'=>0x04F3),
                     array('left'=>0x04F5, 'right'=>0x04F5),
                     array('left'=>0x04F7, 'right'=>0x04F7),
                     array('left'=>0x04F9, 'right'=>0x04F9),
                     array('left'=>0x04FB, 'right'=>0x04FB),
                     array('left'=>0x04FD, 'right'=>0x04FD),
                     array('left'=>0x04FF, 'right'=>0x04FF),
                     array('left'=>0x0501, 'right'=>0x0501),
                     array('left'=>0x0503, 'right'=>0x0503),
                     array('left'=>0x0505, 'right'=>0x0505),
                     array('left'=>0x0507, 'right'=>0x0507),
                     array('left'=>0x0509, 'right'=>0x0509),
                     array('left'=>0x050B, 'right'=>0x050B),
                     array('left'=>0x050D, 'right'=>0x050D),
                     array('left'=>0x050F, 'right'=>0x050F),
                     array('left'=>0x0511, 'right'=>0x0511),
                     array('left'=>0x0513, 'right'=>0x0513),
                     array('left'=>0x0515, 'right'=>0x0515),
                     array('left'=>0x0517, 'right'=>0x0517),
                     array('left'=>0x0519, 'right'=>0x0519),
                     array('left'=>0x051B, 'right'=>0x051B),
                     array('left'=>0x051D, 'right'=>0x051D),
                     array('left'=>0x051F, 'right'=>0x051F),
                     array('left'=>0x0521, 'right'=>0x0521),
                     array('left'=>0x0523, 'right'=>0x0523),
                     array('left'=>0x0525, 'right'=>0x0525),
                     array('left'=>0x0527, 'right'=>0x0527),
                     array('left'=>0x0561, 'right'=>0x0587),
                     array('left'=>0x1D00, 'right'=>0x1D2B),
                     array('left'=>0x1D6B, 'right'=>0x1D77),
                     array('left'=>0x1D79, 'right'=>0x1D9A),
                     array('left'=>0x1E01, 'right'=>0x1E01),
                     array('left'=>0x1E03, 'right'=>0x1E03),
                     array('left'=>0x1E05, 'right'=>0x1E05),
                     array('left'=>0x1E07, 'right'=>0x1E07),
                     array('left'=>0x1E09, 'right'=>0x1E09),
                     array('left'=>0x1E0B, 'right'=>0x1E0B),
                     array('left'=>0x1E0D, 'right'=>0x1E0D),
                     array('left'=>0x1E0F, 'right'=>0x1E0F),
                     array('left'=>0x1E11, 'right'=>0x1E11),
                     array('left'=>0x1E13, 'right'=>0x1E13),
                     array('left'=>0x1E15, 'right'=>0x1E15),
                     array('left'=>0x1E17, 'right'=>0x1E17),
                     array('left'=>0x1E19, 'right'=>0x1E19),
                     array('left'=>0x1E1B, 'right'=>0x1E1B),
                     array('left'=>0x1E1D, 'right'=>0x1E1D),
                     array('left'=>0x1E1F, 'right'=>0x1E1F),
                     array('left'=>0x1E21, 'right'=>0x1E21),
                     array('left'=>0x1E23, 'right'=>0x1E23),
                     array('left'=>0x1E25, 'right'=>0x1E25),
                     array('left'=>0x1E27, 'right'=>0x1E27),
                     array('left'=>0x1E29, 'right'=>0x1E29),
                     array('left'=>0x1E2B, 'right'=>0x1E2B),
                     array('left'=>0x1E2D, 'right'=>0x1E2D),
                     array('left'=>0x1E2F, 'right'=>0x1E2F),
                     array('left'=>0x1E31, 'right'=>0x1E31),
                     array('left'=>0x1E33, 'right'=>0x1E33),
                     array('left'=>0x1E35, 'right'=>0x1E35),
                     array('left'=>0x1E37, 'right'=>0x1E37),
                     array('left'=>0x1E39, 'right'=>0x1E39),
                     array('left'=>0x1E3B, 'right'=>0x1E3B),
                     array('left'=>0x1E3D, 'right'=>0x1E3D),
                     array('left'=>0x1E3F, 'right'=>0x1E3F),
                     array('left'=>0x1E41, 'right'=>0x1E41),
                     array('left'=>0x1E43, 'right'=>0x1E43),
                     array('left'=>0x1E45, 'right'=>0x1E45),
                     array('left'=>0x1E47, 'right'=>0x1E47),
                     array('left'=>0x1E49, 'right'=>0x1E49),
                     array('left'=>0x1E4B, 'right'=>0x1E4B),
                     array('left'=>0x1E4D, 'right'=>0x1E4D),
                     array('left'=>0x1E4F, 'right'=>0x1E4F),
                     array('left'=>0x1E51, 'right'=>0x1E51),
                     array('left'=>0x1E53, 'right'=>0x1E53),
                     array('left'=>0x1E55, 'right'=>0x1E55),
                     array('left'=>0x1E57, 'right'=>0x1E57),
                     array('left'=>0x1E59, 'right'=>0x1E59),
                     array('left'=>0x1E5B, 'right'=>0x1E5B),
                     array('left'=>0x1E5D, 'right'=>0x1E5D),
                     array('left'=>0x1E5F, 'right'=>0x1E5F),
                     array('left'=>0x1E61, 'right'=>0x1E61),
                     array('left'=>0x1E63, 'right'=>0x1E63),
                     array('left'=>0x1E65, 'right'=>0x1E65),
                     array('left'=>0x1E67, 'right'=>0x1E67),
                     array('left'=>0x1E69, 'right'=>0x1E69),
                     array('left'=>0x1E6B, 'right'=>0x1E6B),
                     array('left'=>0x1E6D, 'right'=>0x1E6D),
                     array('left'=>0x1E6F, 'right'=>0x1E6F),
                     array('left'=>0x1E71, 'right'=>0x1E71),
                     array('left'=>0x1E73, 'right'=>0x1E73),
                     array('left'=>0x1E75, 'right'=>0x1E75),
                     array('left'=>0x1E77, 'right'=>0x1E77),
                     array('left'=>0x1E79, 'right'=>0x1E79),
                     array('left'=>0x1E7B, 'right'=>0x1E7B),
                     array('left'=>0x1E7D, 'right'=>0x1E7D),
                     array('left'=>0x1E7F, 'right'=>0x1E7F),
                     array('left'=>0x1E81, 'right'=>0x1E81),
                     array('left'=>0x1E83, 'right'=>0x1E83),
                     array('left'=>0x1E85, 'right'=>0x1E85),
                     array('left'=>0x1E87, 'right'=>0x1E87),
                     array('left'=>0x1E89, 'right'=>0x1E89),
                     array('left'=>0x1E8B, 'right'=>0x1E8B),
                     array('left'=>0x1E8D, 'right'=>0x1E8D),
                     array('left'=>0x1E8F, 'right'=>0x1E8F),
                     array('left'=>0x1E91, 'right'=>0x1E91),
                     array('left'=>0x1E93, 'right'=>0x1E93),
                     array('left'=>0x1E95, 'right'=>0x1E9D),
                     array('left'=>0x1E9F, 'right'=>0x1E9F),
                     array('left'=>0x1EA1, 'right'=>0x1EA1),
                     array('left'=>0x1EA3, 'right'=>0x1EA3),
                     array('left'=>0x1EA5, 'right'=>0x1EA5),
                     array('left'=>0x1EA7, 'right'=>0x1EA7),
                     array('left'=>0x1EA9, 'right'=>0x1EA9),
                     array('left'=>0x1EAB, 'right'=>0x1EAB),
                     array('left'=>0x1EAD, 'right'=>0x1EAD),
                     array('left'=>0x1EAF, 'right'=>0x1EAF),
                     array('left'=>0x1EB1, 'right'=>0x1EB1),
                     array('left'=>0x1EB3, 'right'=>0x1EB3),
                     array('left'=>0x1EB5, 'right'=>0x1EB5),
                     array('left'=>0x1EB7, 'right'=>0x1EB7),
                     array('left'=>0x1EB9, 'right'=>0x1EB9),
                     array('left'=>0x1EBB, 'right'=>0x1EBB),
                     array('left'=>0x1EBD, 'right'=>0x1EBD),
                     array('left'=>0x1EBF, 'right'=>0x1EBF),
                     array('left'=>0x1EC1, 'right'=>0x1EC1),
                     array('left'=>0x1EC3, 'right'=>0x1EC3),
                     array('left'=>0x1EC5, 'right'=>0x1EC5),
                     array('left'=>0x1EC7, 'right'=>0x1EC7),
                     array('left'=>0x1EC9, 'right'=>0x1EC9),
                     array('left'=>0x1ECB, 'right'=>0x1ECB),
                     array('left'=>0x1ECD, 'right'=>0x1ECD),
                     array('left'=>0x1ECF, 'right'=>0x1ECF),
                     array('left'=>0x1ED1, 'right'=>0x1ED1),
                     array('left'=>0x1ED3, 'right'=>0x1ED3),
                     array('left'=>0x1ED5, 'right'=>0x1ED5),
                     array('left'=>0x1ED7, 'right'=>0x1ED7),
                     array('left'=>0x1ED9, 'right'=>0x1ED9),
                     array('left'=>0x1EDB, 'right'=>0x1EDB),
                     array('left'=>0x1EDD, 'right'=>0x1EDD),
                     array('left'=>0x1EDF, 'right'=>0x1EDF),
                     array('left'=>0x1EE1, 'right'=>0x1EE1),
                     array('left'=>0x1EE3, 'right'=>0x1EE3),
                     array('left'=>0x1EE5, 'right'=>0x1EE5),
                     array('left'=>0x1EE7, 'right'=>0x1EE7),
                     array('left'=>0x1EE9, 'right'=>0x1EE9),
                     array('left'=>0x1EEB, 'right'=>0x1EEB),
                     array('left'=>0x1EED, 'right'=>0x1EED),
                     array('left'=>0x1EEF, 'right'=>0x1EEF),
                     array('left'=>0x1EF1, 'right'=>0x1EF1),
                     array('left'=>0x1EF3, 'right'=>0x1EF3),
                     array('left'=>0x1EF5, 'right'=>0x1EF5),
                     array('left'=>0x1EF7, 'right'=>0x1EF7),
                     array('left'=>0x1EF9, 'right'=>0x1EF9),
                     array('left'=>0x1EFB, 'right'=>0x1EFB),
                     array('left'=>0x1EFD, 'right'=>0x1EFD),
                     array('left'=>0x1EFF, 'right'=>0x1F07),
                     array('left'=>0x1F10, 'right'=>0x1F15),
                     array('left'=>0x1F20, 'right'=>0x1F27),
                     array('left'=>0x1F30, 'right'=>0x1F37),
                     array('left'=>0x1F40, 'right'=>0x1F45),
                     array('left'=>0x1F50, 'right'=>0x1F57),
                     array('left'=>0x1F60, 'right'=>0x1F67),
                     array('left'=>0x1F70, 'right'=>0x1F7D),
                     array('left'=>0x1F80, 'right'=>0x1F87),
                     array('left'=>0x1F90, 'right'=>0x1F97),
                     array('left'=>0x1FA0, 'right'=>0x1FA7),
                     array('left'=>0x1FB0, 'right'=>0x1FB4),
                     array('left'=>0x1FB6, 'right'=>0x1FB7),
                     array('left'=>0x1FBE, 'right'=>0x1FBE),
                     array('left'=>0x1FC2, 'right'=>0x1FC4),
                     array('left'=>0x1FC6, 'right'=>0x1FC7),
                     array('left'=>0x1FD0, 'right'=>0x1FD3),
                     array('left'=>0x1FD6, 'right'=>0x1FD7),
                     array('left'=>0x1FE0, 'right'=>0x1FE7),
                     array('left'=>0x1FF2, 'right'=>0x1FF4),
                     array('left'=>0x1FF6, 'right'=>0x1FF7),
                     array('left'=>0x210A, 'right'=>0x210A),
                     array('left'=>0x210E, 'right'=>0x210F),
                     array('left'=>0x2113, 'right'=>0x2113),
                     array('left'=>0x212F, 'right'=>0x212F),
                     array('left'=>0x2134, 'right'=>0x2134),
                     array('left'=>0x2139, 'right'=>0x2139),
                     array('left'=>0x213C, 'right'=>0x213D),
                     array('left'=>0x2146, 'right'=>0x2149),
                     array('left'=>0x214E, 'right'=>0x214E),
                     array('left'=>0x2184, 'right'=>0x2184),
                     array('left'=>0x2C30, 'right'=>0x2C5E),
                     array('left'=>0x2C61, 'right'=>0x2C61),
                     array('left'=>0x2C65, 'right'=>0x2C66),
                     array('left'=>0x2C68, 'right'=>0x2C68),
                     array('left'=>0x2C6A, 'right'=>0x2C6A),
                     array('left'=>0x2C6C, 'right'=>0x2C6C),
                     array('left'=>0x2C71, 'right'=>0x2C71),
                     array('left'=>0x2C73, 'right'=>0x2C74),
                     array('left'=>0x2C76, 'right'=>0x2C7B),
                     array('left'=>0x2C81, 'right'=>0x2C81),
                     array('left'=>0x2C83, 'right'=>0x2C83),
                     array('left'=>0x2C85, 'right'=>0x2C85),
                     array('left'=>0x2C87, 'right'=>0x2C87),
                     array('left'=>0x2C89, 'right'=>0x2C89),
                     array('left'=>0x2C8B, 'right'=>0x2C8B),
                     array('left'=>0x2C8D, 'right'=>0x2C8D),
                     array('left'=>0x2C8F, 'right'=>0x2C8F),
                     array('left'=>0x2C91, 'right'=>0x2C91),
                     array('left'=>0x2C93, 'right'=>0x2C93),
                     array('left'=>0x2C95, 'right'=>0x2C95),
                     array('left'=>0x2C97, 'right'=>0x2C97),
                     array('left'=>0x2C99, 'right'=>0x2C99),
                     array('left'=>0x2C9B, 'right'=>0x2C9B),
                     array('left'=>0x2C9D, 'right'=>0x2C9D),
                     array('left'=>0x2C9F, 'right'=>0x2C9F),
                     array('left'=>0x2CA1, 'right'=>0x2CA1),
                     array('left'=>0x2CA3, 'right'=>0x2CA3),
                     array('left'=>0x2CA5, 'right'=>0x2CA5),
                     array('left'=>0x2CA7, 'right'=>0x2CA7),
                     array('left'=>0x2CA9, 'right'=>0x2CA9),
                     array('left'=>0x2CAB, 'right'=>0x2CAB),
                     array('left'=>0x2CAD, 'right'=>0x2CAD),
                     array('left'=>0x2CAF, 'right'=>0x2CAF),
                     array('left'=>0x2CB1, 'right'=>0x2CB1),
                     array('left'=>0x2CB3, 'right'=>0x2CB3),
                     array('left'=>0x2CB5, 'right'=>0x2CB5),
                     array('left'=>0x2CB7, 'right'=>0x2CB7),
                     array('left'=>0x2CB9, 'right'=>0x2CB9),
                     array('left'=>0x2CBB, 'right'=>0x2CBB),
                     array('left'=>0x2CBD, 'right'=>0x2CBD),
                     array('left'=>0x2CBF, 'right'=>0x2CBF),
                     array('left'=>0x2CC1, 'right'=>0x2CC1),
                     array('left'=>0x2CC3, 'right'=>0x2CC3),
                     array('left'=>0x2CC5, 'right'=>0x2CC5),
                     array('left'=>0x2CC7, 'right'=>0x2CC7),
                     array('left'=>0x2CC9, 'right'=>0x2CC9),
                     array('left'=>0x2CCB, 'right'=>0x2CCB),
                     array('left'=>0x2CCD, 'right'=>0x2CCD),
                     array('left'=>0x2CCF, 'right'=>0x2CCF),
                     array('left'=>0x2CD1, 'right'=>0x2CD1),
                     array('left'=>0x2CD3, 'right'=>0x2CD3),
                     array('left'=>0x2CD5, 'right'=>0x2CD5),
                     array('left'=>0x2CD7, 'right'=>0x2CD7),
                     array('left'=>0x2CD9, 'right'=>0x2CD9),
                     array('left'=>0x2CDB, 'right'=>0x2CDB),
                     array('left'=>0x2CDD, 'right'=>0x2CDD),
                     array('left'=>0x2CDF, 'right'=>0x2CDF),
                     array('left'=>0x2CE1, 'right'=>0x2CE1),
                     array('left'=>0x2CE3, 'right'=>0x2CE4),
                     array('left'=>0x2CEC, 'right'=>0x2CEC),
                     array('left'=>0x2CEE, 'right'=>0x2CEE),
                     array('left'=>0x2CF3, 'right'=>0x2CF3),
                     array('left'=>0x2D00, 'right'=>0x2D25),
                     array('left'=>0x2D27, 'right'=>0x2D27),
                     array('left'=>0x2D2D, 'right'=>0x2D2D),
                     array('left'=>0xA641, 'right'=>0xA641),
                     array('left'=>0xA643, 'right'=>0xA643),
                     array('left'=>0xA645, 'right'=>0xA645),
                     array('left'=>0xA647, 'right'=>0xA647),
                     array('left'=>0xA649, 'right'=>0xA649),
                     array('left'=>0xA64B, 'right'=>0xA64B),
                     array('left'=>0xA64D, 'right'=>0xA64D),
                     array('left'=>0xA64F, 'right'=>0xA64F),
                     array('left'=>0xA651, 'right'=>0xA651),
                     array('left'=>0xA653, 'right'=>0xA653),
                     array('left'=>0xA655, 'right'=>0xA655),
                     array('left'=>0xA657, 'right'=>0xA657),
                     array('left'=>0xA659, 'right'=>0xA659),
                     array('left'=>0xA65B, 'right'=>0xA65B),
                     array('left'=>0xA65D, 'right'=>0xA65D),
                     array('left'=>0xA65F, 'right'=>0xA65F),
                     array('left'=>0xA661, 'right'=>0xA661),
                     array('left'=>0xA663, 'right'=>0xA663),
                     array('left'=>0xA665, 'right'=>0xA665),
                     array('left'=>0xA667, 'right'=>0xA667),
                     array('left'=>0xA669, 'right'=>0xA669),
                     array('left'=>0xA66B, 'right'=>0xA66B),
                     array('left'=>0xA66D, 'right'=>0xA66D),
                     array('left'=>0xA681, 'right'=>0xA681),
                     array('left'=>0xA683, 'right'=>0xA683),
                     array('left'=>0xA685, 'right'=>0xA685),
                     array('left'=>0xA687, 'right'=>0xA687),
                     array('left'=>0xA689, 'right'=>0xA689),
                     array('left'=>0xA68B, 'right'=>0xA68B),
                     array('left'=>0xA68D, 'right'=>0xA68D),
                     array('left'=>0xA68F, 'right'=>0xA68F),
                     array('left'=>0xA691, 'right'=>0xA691),
                     array('left'=>0xA693, 'right'=>0xA693),
                     array('left'=>0xA695, 'right'=>0xA695),
                     array('left'=>0xA697, 'right'=>0xA697),
                     array('left'=>0xA723, 'right'=>0xA723),
                     array('left'=>0xA725, 'right'=>0xA725),
                     array('left'=>0xA727, 'right'=>0xA727),
                     array('left'=>0xA729, 'right'=>0xA729),
                     array('left'=>0xA72B, 'right'=>0xA72B),
                     array('left'=>0xA72D, 'right'=>0xA72D),
                     array('left'=>0xA72F, 'right'=>0xA731),
                     array('left'=>0xA733, 'right'=>0xA733),
                     array('left'=>0xA735, 'right'=>0xA735),
                     array('left'=>0xA737, 'right'=>0xA737),
                     array('left'=>0xA739, 'right'=>0xA739),
                     array('left'=>0xA73B, 'right'=>0xA73B),
                     array('left'=>0xA73D, 'right'=>0xA73D),
                     array('left'=>0xA73F, 'right'=>0xA73F),
                     array('left'=>0xA741, 'right'=>0xA741),
                     array('left'=>0xA743, 'right'=>0xA743),
                     array('left'=>0xA745, 'right'=>0xA745),
                     array('left'=>0xA747, 'right'=>0xA747),
                     array('left'=>0xA749, 'right'=>0xA749),
                     array('left'=>0xA74B, 'right'=>0xA74B),
                     array('left'=>0xA74D, 'right'=>0xA74D),
                     array('left'=>0xA74F, 'right'=>0xA74F),
                     array('left'=>0xA751, 'right'=>0xA751),
                     array('left'=>0xA753, 'right'=>0xA753),
                     array('left'=>0xA755, 'right'=>0xA755),
                     array('left'=>0xA757, 'right'=>0xA757),
                     array('left'=>0xA759, 'right'=>0xA759),
                     array('left'=>0xA75B, 'right'=>0xA75B),
                     array('left'=>0xA75D, 'right'=>0xA75D),
                     array('left'=>0xA75F, 'right'=>0xA75F),
                     array('left'=>0xA761, 'right'=>0xA761),
                     array('left'=>0xA763, 'right'=>0xA763),
                     array('left'=>0xA765, 'right'=>0xA765),
                     array('left'=>0xA767, 'right'=>0xA767),
                     array('left'=>0xA769, 'right'=>0xA769),
                     array('left'=>0xA76B, 'right'=>0xA76B),
                     array('left'=>0xA76D, 'right'=>0xA76D),
                     array('left'=>0xA76F, 'right'=>0xA76F),
                     array('left'=>0xA771, 'right'=>0xA778),
                     array('left'=>0xA77A, 'right'=>0xA77A),
                     array('left'=>0xA77C, 'right'=>0xA77C),
                     array('left'=>0xA77F, 'right'=>0xA77F),
                     array('left'=>0xA781, 'right'=>0xA781),
                     array('left'=>0xA783, 'right'=>0xA783),
                     array('left'=>0xA785, 'right'=>0xA785),
                     array('left'=>0xA787, 'right'=>0xA787),
                     array('left'=>0xA78C, 'right'=>0xA78C),
                     array('left'=>0xA78E, 'right'=>0xA78E),
                     array('left'=>0xA791, 'right'=>0xA791),
                     array('left'=>0xA793, 'right'=>0xA793),
                     array('left'=>0xA7A1, 'right'=>0xA7A1),
                     array('left'=>0xA7A3, 'right'=>0xA7A3),
                     array('left'=>0xA7A5, 'right'=>0xA7A5),
                     array('left'=>0xA7A7, 'right'=>0xA7A7),
                     array('left'=>0xA7A9, 'right'=>0xA7A9),
                     array('left'=>0xA7FA, 'right'=>0xA7FA),
                     array('left'=>0xFB00, 'right'=>0xFB06),
                     array('left'=>0xFB13, 'right'=>0xFB17),
                     array('left'=>0xFF41, 'right'=>0xFF5A),
                     array('left'=>0x10428, 'right'=>0x1044F),
                     array('left'=>0x1D41A, 'right'=>0x1D433),
                     array('left'=>0x1D44E, 'right'=>0x1D454),
                     array('left'=>0x1D456, 'right'=>0x1D467),
                     array('left'=>0x1D482, 'right'=>0x1D49B),
                     array('left'=>0x1D4B6, 'right'=>0x1D4B9),
                     array('left'=>0x1D4BB, 'right'=>0x1D4BB),
                     array('left'=>0x1D4BD, 'right'=>0x1D4C3),
                     array('left'=>0x1D4C5, 'right'=>0x1D4CF),
                     array('left'=>0x1D4EA, 'right'=>0x1D503),
                     array('left'=>0x1D51E, 'right'=>0x1D537),
                     array('left'=>0x1D552, 'right'=>0x1D56B),
                     array('left'=>0x1D586, 'right'=>0x1D59F),
                     array('left'=>0x1D5BA, 'right'=>0x1D5D3),
                     array('left'=>0x1D5EE, 'right'=>0x1D607),
                     array('left'=>0x1D622, 'right'=>0x1D63B),
                     array('left'=>0x1D656, 'right'=>0x1D66F),
                     array('left'=>0x1D68A, 'right'=>0x1D6A5),
                     array('left'=>0x1D6C2, 'right'=>0x1D6DA),
                     array('left'=>0x1D6DC, 'right'=>0x1D6E1),
                     array('left'=>0x1D6FC, 'right'=>0x1D714),
                     array('left'=>0x1D716, 'right'=>0x1D71B),
                     array('left'=>0x1D736, 'right'=>0x1D74E),
                     array('left'=>0x1D750, 'right'=>0x1D755),
                     array('left'=>0x1D770, 'right'=>0x1D788),
                     array('left'=>0x1D78A, 'right'=>0x1D78F),
                     array('left'=>0x1D7AA, 'right'=>0x1D7C2),
                     array('left'=>0x1D7C4, 'right'=>0x1D7C9),
                     array('left'=>0x1D7CB, 'right'=>0x1D7CB));
    }
    public static function Lm_ranges() {
        return array(array('left'=>0x02B0, 'right'=>0x02C1),
                     array('left'=>0x02C6, 'right'=>0x02D1),
                     array('left'=>0x02E0, 'right'=>0x02E4),
                     array('left'=>0x02EC, 'right'=>0x02EC),
                     array('left'=>0x02EE, 'right'=>0x02EE),
                     array('left'=>0x0374, 'right'=>0x0374),
                     array('left'=>0x037A, 'right'=>0x037A),
                     array('left'=>0x0559, 'right'=>0x0559),
                     array('left'=>0x0640, 'right'=>0x0640),
                     array('left'=>0x06E5, 'right'=>0x06E6),
                     array('left'=>0x07F4, 'right'=>0x07F5),
                     array('left'=>0x07FA, 'right'=>0x07FA),
                     array('left'=>0x081A, 'right'=>0x081A),
                     array('left'=>0x0824, 'right'=>0x0824),
                     array('left'=>0x0828, 'right'=>0x0828),
                     array('left'=>0x0971, 'right'=>0x0971),
                     array('left'=>0x0E46, 'right'=>0x0E46),
                     array('left'=>0x0EC6, 'right'=>0x0EC6),
                     array('left'=>0x10FC, 'right'=>0x10FC),
                     array('left'=>0x17D7, 'right'=>0x17D7),
                     array('left'=>0x1843, 'right'=>0x1843),
                     array('left'=>0x1AA7, 'right'=>0x1AA7),
                     array('left'=>0x1C78, 'right'=>0x1C7D),
                     array('left'=>0x1D2C, 'right'=>0x1D6A),
                     array('left'=>0x1D78, 'right'=>0x1D78),
                     array('left'=>0x1D9B, 'right'=>0x1DBF),
                     array('left'=>0x2071, 'right'=>0x2071),
                     array('left'=>0x207F, 'right'=>0x207F),
                     array('left'=>0x2090, 'right'=>0x209C),
                     array('left'=>0x2C7C, 'right'=>0x2C7D),
                     array('left'=>0x2D6F, 'right'=>0x2D6F),
                     array('left'=>0x2E2F, 'right'=>0x2E2F),
                     array('left'=>0x3005, 'right'=>0x3005),
                     array('left'=>0x3031, 'right'=>0x3035),
                     array('left'=>0x303B, 'right'=>0x303B),
                     array('left'=>0x309D, 'right'=>0x309E),
                     array('left'=>0x30FC, 'right'=>0x30FE),
                     array('left'=>0xA015, 'right'=>0xA015),
                     array('left'=>0xA4F8, 'right'=>0xA4FD),
                     array('left'=>0xA60C, 'right'=>0xA60C),
                     array('left'=>0xA67F, 'right'=>0xA67F),
                     array('left'=>0xA717, 'right'=>0xA71F),
                     array('left'=>0xA770, 'right'=>0xA770),
                     array('left'=>0xA788, 'right'=>0xA788),
                     array('left'=>0xA7F8, 'right'=>0xA7F9),
                     array('left'=>0xA9CF, 'right'=>0xA9CF),
                     array('left'=>0xAA70, 'right'=>0xAA70),
                     array('left'=>0xAADD, 'right'=>0xAADD),
                     array('left'=>0xAAF3, 'right'=>0xAAF4),
                     array('left'=>0xFF70, 'right'=>0xFF70),
                     array('left'=>0xFF9E, 'right'=>0xFF9F),
                     array('left'=>0x16F93, 'right'=>0x16F9F));
    }
    public static function Lo_ranges() {
        return array(array('left'=>0x00AA, 'right'=>0x00AA),
                     array('left'=>0x00BA, 'right'=>0x00BA),
                     array('left'=>0x01BB, 'right'=>0x01BB),
                     array('left'=>0x01C0, 'right'=>0x01C3),
                     array('left'=>0x0294, 'right'=>0x0294),
                     array('left'=>0x05D0, 'right'=>0x05EA),
                     array('left'=>0x05F0, 'right'=>0x05F2),
                     array('left'=>0x0620, 'right'=>0x063F),
                     array('left'=>0x0641, 'right'=>0x064A),
                     array('left'=>0x066E, 'right'=>0x066F),
                     array('left'=>0x0671, 'right'=>0x06D3),
                     array('left'=>0x06D5, 'right'=>0x06D5),
                     array('left'=>0x06EE, 'right'=>0x06EF),
                     array('left'=>0x06FA, 'right'=>0x06FC),
                     array('left'=>0x06FF, 'right'=>0x06FF),
                     array('left'=>0x0710, 'right'=>0x0710),
                     array('left'=>0x0712, 'right'=>0x072F),
                     array('left'=>0x074D, 'right'=>0x07A5),
                     array('left'=>0x07B1, 'right'=>0x07B1),
                     array('left'=>0x07CA, 'right'=>0x07EA),
                     array('left'=>0x0800, 'right'=>0x0815),
                     array('left'=>0x0840, 'right'=>0x0858),
                     array('left'=>0x08A0, 'right'=>0x08A0),
                     array('left'=>0x08A2, 'right'=>0x08AC),
                     array('left'=>0x0904, 'right'=>0x0939),
                     array('left'=>0x093D, 'right'=>0x093D),
                     array('left'=>0x0950, 'right'=>0x0950),
                     array('left'=>0x0958, 'right'=>0x0961),
                     array('left'=>0x0972, 'right'=>0x0977),
                     array('left'=>0x0979, 'right'=>0x097F),
                     array('left'=>0x0985, 'right'=>0x098C),
                     array('left'=>0x098F, 'right'=>0x0990),
                     array('left'=>0x0993, 'right'=>0x09A8),
                     array('left'=>0x09AA, 'right'=>0x09B0),
                     array('left'=>0x09B2, 'right'=>0x09B2),
                     array('left'=>0x09B6, 'right'=>0x09B9),
                     array('left'=>0x09BD, 'right'=>0x09BD),
                     array('left'=>0x09CE, 'right'=>0x09CE),
                     array('left'=>0x09DC, 'right'=>0x09DD),
                     array('left'=>0x09DF, 'right'=>0x09E1),
                     array('left'=>0x09F0, 'right'=>0x09F1),
                     array('left'=>0x0A05, 'right'=>0x0A0A),
                     array('left'=>0x0A0F, 'right'=>0x0A10),
                     array('left'=>0x0A13, 'right'=>0x0A28),
                     array('left'=>0x0A2A, 'right'=>0x0A30),
                     array('left'=>0x0A32, 'right'=>0x0A33),
                     array('left'=>0x0A35, 'right'=>0x0A36),
                     array('left'=>0x0A38, 'right'=>0x0A39),
                     array('left'=>0x0A59, 'right'=>0x0A5C),
                     array('left'=>0x0A5E, 'right'=>0x0A5E),
                     array('left'=>0x0A72, 'right'=>0x0A74),
                     array('left'=>0x0A85, 'right'=>0x0A8D),
                     array('left'=>0x0A8F, 'right'=>0x0A91),
                     array('left'=>0x0A93, 'right'=>0x0AA8),
                     array('left'=>0x0AAA, 'right'=>0x0AB0),
                     array('left'=>0x0AB2, 'right'=>0x0AB3),
                     array('left'=>0x0AB5, 'right'=>0x0AB9),
                     array('left'=>0x0ABD, 'right'=>0x0ABD),
                     array('left'=>0x0AD0, 'right'=>0x0AD0),
                     array('left'=>0x0AE0, 'right'=>0x0AE1),
                     array('left'=>0x0B05, 'right'=>0x0B0C),
                     array('left'=>0x0B0F, 'right'=>0x0B10),
                     array('left'=>0x0B13, 'right'=>0x0B28),
                     array('left'=>0x0B2A, 'right'=>0x0B30),
                     array('left'=>0x0B32, 'right'=>0x0B33),
                     array('left'=>0x0B35, 'right'=>0x0B39),
                     array('left'=>0x0B3D, 'right'=>0x0B3D),
                     array('left'=>0x0B5C, 'right'=>0x0B5D),
                     array('left'=>0x0B5F, 'right'=>0x0B61),
                     array('left'=>0x0B71, 'right'=>0x0B71),
                     array('left'=>0x0B83, 'right'=>0x0B83),
                     array('left'=>0x0B85, 'right'=>0x0B8A),
                     array('left'=>0x0B8E, 'right'=>0x0B90),
                     array('left'=>0x0B92, 'right'=>0x0B95),
                     array('left'=>0x0B99, 'right'=>0x0B9A),
                     array('left'=>0x0B9C, 'right'=>0x0B9C),
                     array('left'=>0x0B9E, 'right'=>0x0B9F),
                     array('left'=>0x0BA3, 'right'=>0x0BA4),
                     array('left'=>0x0BA8, 'right'=>0x0BAA),
                     array('left'=>0x0BAE, 'right'=>0x0BB9),
                     array('left'=>0x0BD0, 'right'=>0x0BD0),
                     array('left'=>0x0C05, 'right'=>0x0C0C),
                     array('left'=>0x0C0E, 'right'=>0x0C10),
                     array('left'=>0x0C12, 'right'=>0x0C28),
                     array('left'=>0x0C2A, 'right'=>0x0C33),
                     array('left'=>0x0C35, 'right'=>0x0C39),
                     array('left'=>0x0C3D, 'right'=>0x0C3D),
                     array('left'=>0x0C58, 'right'=>0x0C59),
                     array('left'=>0x0C60, 'right'=>0x0C61),
                     array('left'=>0x0C85, 'right'=>0x0C8C),
                     array('left'=>0x0C8E, 'right'=>0x0C90),
                     array('left'=>0x0C92, 'right'=>0x0CA8),
                     array('left'=>0x0CAA, 'right'=>0x0CB3),
                     array('left'=>0x0CB5, 'right'=>0x0CB9),
                     array('left'=>0x0CBD, 'right'=>0x0CBD),
                     array('left'=>0x0CDE, 'right'=>0x0CDE),
                     array('left'=>0x0CE0, 'right'=>0x0CE1),
                     array('left'=>0x0CF1, 'right'=>0x0CF2),
                     array('left'=>0x0D05, 'right'=>0x0D0C),
                     array('left'=>0x0D0E, 'right'=>0x0D10),
                     array('left'=>0x0D12, 'right'=>0x0D3A),
                     array('left'=>0x0D3D, 'right'=>0x0D3D),
                     array('left'=>0x0D4E, 'right'=>0x0D4E),
                     array('left'=>0x0D60, 'right'=>0x0D61),
                     array('left'=>0x0D7A, 'right'=>0x0D7F),
                     array('left'=>0x0D85, 'right'=>0x0D96),
                     array('left'=>0x0D9A, 'right'=>0x0DB1),
                     array('left'=>0x0DB3, 'right'=>0x0DBB),
                     array('left'=>0x0DBD, 'right'=>0x0DBD),
                     array('left'=>0x0DC0, 'right'=>0x0DC6),
                     array('left'=>0x0E01, 'right'=>0x0E30),
                     array('left'=>0x0E32, 'right'=>0x0E33),
                     array('left'=>0x0E40, 'right'=>0x0E45),
                     array('left'=>0x0E81, 'right'=>0x0E82),
                     array('left'=>0x0E84, 'right'=>0x0E84),
                     array('left'=>0x0E87, 'right'=>0x0E88),
                     array('left'=>0x0E8A, 'right'=>0x0E8A),
                     array('left'=>0x0E8D, 'right'=>0x0E8D),
                     array('left'=>0x0E94, 'right'=>0x0E97),
                     array('left'=>0x0E99, 'right'=>0x0E9F),
                     array('left'=>0x0EA1, 'right'=>0x0EA3),
                     array('left'=>0x0EA5, 'right'=>0x0EA5),
                     array('left'=>0x0EA7, 'right'=>0x0EA7),
                     array('left'=>0x0EAA, 'right'=>0x0EAB),
                     array('left'=>0x0EAD, 'right'=>0x0EB0),
                     array('left'=>0x0EB2, 'right'=>0x0EB3),
                     array('left'=>0x0EBD, 'right'=>0x0EBD),
                     array('left'=>0x0EC0, 'right'=>0x0EC4),
                     array('left'=>0x0EDC, 'right'=>0x0EDF),
                     array('left'=>0x0F00, 'right'=>0x0F00),
                     array('left'=>0x0F40, 'right'=>0x0F47),
                     array('left'=>0x0F49, 'right'=>0x0F6C),
                     array('left'=>0x0F88, 'right'=>0x0F8C),
                     array('left'=>0x1000, 'right'=>0x102A),
                     array('left'=>0x103F, 'right'=>0x103F),
                     array('left'=>0x1050, 'right'=>0x1055),
                     array('left'=>0x105A, 'right'=>0x105D),
                     array('left'=>0x1061, 'right'=>0x1061),
                     array('left'=>0x1065, 'right'=>0x1066),
                     array('left'=>0x106E, 'right'=>0x1070),
                     array('left'=>0x1075, 'right'=>0x1081),
                     array('left'=>0x108E, 'right'=>0x108E),
                     array('left'=>0x10D0, 'right'=>0x10FA),
                     array('left'=>0x10FD, 'right'=>0x1248),
                     array('left'=>0x124A, 'right'=>0x124D),
                     array('left'=>0x1250, 'right'=>0x1256),
                     array('left'=>0x1258, 'right'=>0x1258),
                     array('left'=>0x125A, 'right'=>0x125D),
                     array('left'=>0x1260, 'right'=>0x1288),
                     array('left'=>0x128A, 'right'=>0x128D),
                     array('left'=>0x1290, 'right'=>0x12B0),
                     array('left'=>0x12B2, 'right'=>0x12B5),
                     array('left'=>0x12B8, 'right'=>0x12BE),
                     array('left'=>0x12C0, 'right'=>0x12C0),
                     array('left'=>0x12C2, 'right'=>0x12C5),
                     array('left'=>0x12C8, 'right'=>0x12D6),
                     array('left'=>0x12D8, 'right'=>0x1310),
                     array('left'=>0x1312, 'right'=>0x1315),
                     array('left'=>0x1318, 'right'=>0x135A),
                     array('left'=>0x1380, 'right'=>0x138F),
                     array('left'=>0x13A0, 'right'=>0x13F4),
                     array('left'=>0x1401, 'right'=>0x166C),
                     array('left'=>0x166F, 'right'=>0x167F),
                     array('left'=>0x1681, 'right'=>0x169A),
                     array('left'=>0x16A0, 'right'=>0x16EA),
                     array('left'=>0x1700, 'right'=>0x170C),
                     array('left'=>0x170E, 'right'=>0x1711),
                     array('left'=>0x1720, 'right'=>0x1731),
                     array('left'=>0x1740, 'right'=>0x1751),
                     array('left'=>0x1760, 'right'=>0x176C),
                     array('left'=>0x176E, 'right'=>0x1770),
                     array('left'=>0x1780, 'right'=>0x17B3),
                     array('left'=>0x17DC, 'right'=>0x17DC),
                     array('left'=>0x1820, 'right'=>0x1842),
                     array('left'=>0x1844, 'right'=>0x1877),
                     array('left'=>0x1880, 'right'=>0x18A8),
                     array('left'=>0x18AA, 'right'=>0x18AA),
                     array('left'=>0x18B0, 'right'=>0x18F5),
                     array('left'=>0x1900, 'right'=>0x191C),
                     array('left'=>0x1950, 'right'=>0x196D),
                     array('left'=>0x1970, 'right'=>0x1974),
                     array('left'=>0x1980, 'right'=>0x19AB),
                     array('left'=>0x19C1, 'right'=>0x19C7),
                     array('left'=>0x1A00, 'right'=>0x1A16),
                     array('left'=>0x1A20, 'right'=>0x1A54),
                     array('left'=>0x1B05, 'right'=>0x1B33),
                     array('left'=>0x1B45, 'right'=>0x1B4B),
                     array('left'=>0x1B83, 'right'=>0x1BA0),
                     array('left'=>0x1BAE, 'right'=>0x1BAF),
                     array('left'=>0x1BBA, 'right'=>0x1BE5),
                     array('left'=>0x1C00, 'right'=>0x1C23),
                     array('left'=>0x1C4D, 'right'=>0x1C4F),
                     array('left'=>0x1C5A, 'right'=>0x1C77),
                     array('left'=>0x1CE9, 'right'=>0x1CEC),
                     array('left'=>0x1CEE, 'right'=>0x1CF1),
                     array('left'=>0x1CF5, 'right'=>0x1CF6),
                     array('left'=>0x2135, 'right'=>0x2138),
                     array('left'=>0x2D30, 'right'=>0x2D67),
                     array('left'=>0x2D80, 'right'=>0x2D96),
                     array('left'=>0x2DA0, 'right'=>0x2DA6),
                     array('left'=>0x2DA8, 'right'=>0x2DAE),
                     array('left'=>0x2DB0, 'right'=>0x2DB6),
                     array('left'=>0x2DB8, 'right'=>0x2DBE),
                     array('left'=>0x2DC0, 'right'=>0x2DC6),
                     array('left'=>0x2DC8, 'right'=>0x2DCE),
                     array('left'=>0x2DD0, 'right'=>0x2DD6),
                     array('left'=>0x2DD8, 'right'=>0x2DDE),
                     array('left'=>0x3006, 'right'=>0x3006),
                     array('left'=>0x303C, 'right'=>0x303C),
                     array('left'=>0x3041, 'right'=>0x3096),
                     array('left'=>0x309F, 'right'=>0x309F),
                     array('left'=>0x30A1, 'right'=>0x30FA),
                     array('left'=>0x30FF, 'right'=>0x30FF),
                     array('left'=>0x3105, 'right'=>0x312D),
                     array('left'=>0x3131, 'right'=>0x318E),
                     array('left'=>0x31A0, 'right'=>0x31BA),
                     array('left'=>0x31F0, 'right'=>0x31FF),
                     array('left'=>0x3400, 'right'=>0x3400),
                     array('left'=>0x4DB5, 'right'=>0x4DB5),
                     array('left'=>0x4E00, 'right'=>0x4E00),
                     array('left'=>0x9FCC, 'right'=>0x9FCC),
                     array('left'=>0xA000, 'right'=>0xA014),
                     array('left'=>0xA016, 'right'=>0xA48C),
                     array('left'=>0xA4D0, 'right'=>0xA4F7),
                     array('left'=>0xA500, 'right'=>0xA60B),
                     array('left'=>0xA610, 'right'=>0xA61F),
                     array('left'=>0xA62A, 'right'=>0xA62B),
                     array('left'=>0xA66E, 'right'=>0xA66E),
                     array('left'=>0xA6A0, 'right'=>0xA6E5),
                     array('left'=>0xA7FB, 'right'=>0xA801),
                     array('left'=>0xA803, 'right'=>0xA805),
                     array('left'=>0xA807, 'right'=>0xA80A),
                     array('left'=>0xA80C, 'right'=>0xA822),
                     array('left'=>0xA840, 'right'=>0xA873),
                     array('left'=>0xA882, 'right'=>0xA8B3),
                     array('left'=>0xA8F2, 'right'=>0xA8F7),
                     array('left'=>0xA8FB, 'right'=>0xA8FB),
                     array('left'=>0xA90A, 'right'=>0xA925),
                     array('left'=>0xA930, 'right'=>0xA946),
                     array('left'=>0xA960, 'right'=>0xA97C),
                     array('left'=>0xA984, 'right'=>0xA9B2),
                     array('left'=>0xAA00, 'right'=>0xAA28),
                     array('left'=>0xAA40, 'right'=>0xAA42),
                     array('left'=>0xAA44, 'right'=>0xAA4B),
                     array('left'=>0xAA60, 'right'=>0xAA6F),
                     array('left'=>0xAA71, 'right'=>0xAA76),
                     array('left'=>0xAA7A, 'right'=>0xAA7A),
                     array('left'=>0xAA80, 'right'=>0xAAAF),
                     array('left'=>0xAAB1, 'right'=>0xAAB1),
                     array('left'=>0xAAB5, 'right'=>0xAAB6),
                     array('left'=>0xAAB9, 'right'=>0xAABD),
                     array('left'=>0xAAC0, 'right'=>0xAAC0),
                     array('left'=>0xAAC2, 'right'=>0xAAC2),
                     array('left'=>0xAADB, 'right'=>0xAADC),
                     array('left'=>0xAAE0, 'right'=>0xAAEA),
                     array('left'=>0xAAF2, 'right'=>0xAAF2),
                     array('left'=>0xAB01, 'right'=>0xAB06),
                     array('left'=>0xAB09, 'right'=>0xAB0E),
                     array('left'=>0xAB11, 'right'=>0xAB16),
                     array('left'=>0xAB20, 'right'=>0xAB26),
                     array('left'=>0xAB28, 'right'=>0xAB2E),
                     array('left'=>0xABC0, 'right'=>0xABE2),
                     array('left'=>0xAC00, 'right'=>0xAC00),
                     array('left'=>0xD7A3, 'right'=>0xD7A3),
                     array('left'=>0xD7B0, 'right'=>0xD7C6),
                     array('left'=>0xD7CB, 'right'=>0xD7FB),
                     array('left'=>0xF900, 'right'=>0xFA6D),
                     array('left'=>0xFA70, 'right'=>0xFAD9),
                     array('left'=>0xFB1D, 'right'=>0xFB1D),
                     array('left'=>0xFB1F, 'right'=>0xFB28),
                     array('left'=>0xFB2A, 'right'=>0xFB36),
                     array('left'=>0xFB38, 'right'=>0xFB3C),
                     array('left'=>0xFB3E, 'right'=>0xFB3E),
                     array('left'=>0xFB40, 'right'=>0xFB41),
                     array('left'=>0xFB43, 'right'=>0xFB44),
                     array('left'=>0xFB46, 'right'=>0xFBB1),
                     array('left'=>0xFBD3, 'right'=>0xFD3D),
                     array('left'=>0xFD50, 'right'=>0xFD8F),
                     array('left'=>0xFD92, 'right'=>0xFDC7),
                     array('left'=>0xFDF0, 'right'=>0xFDFB),
                     array('left'=>0xFE70, 'right'=>0xFE74),
                     array('left'=>0xFE76, 'right'=>0xFEFC),
                     array('left'=>0xFF66, 'right'=>0xFF6F),
                     array('left'=>0xFF71, 'right'=>0xFF9D),
                     array('left'=>0xFFA0, 'right'=>0xFFBE),
                     array('left'=>0xFFC2, 'right'=>0xFFC7),
                     array('left'=>0xFFCA, 'right'=>0xFFCF),
                     array('left'=>0xFFD2, 'right'=>0xFFD7),
                     array('left'=>0xFFDA, 'right'=>0xFFDC),
                     array('left'=>0x10000, 'right'=>0x1000B),
                     array('left'=>0x1000D, 'right'=>0x10026),
                     array('left'=>0x10028, 'right'=>0x1003A),
                     array('left'=>0x1003C, 'right'=>0x1003D),
                     array('left'=>0x1003F, 'right'=>0x1004D),
                     array('left'=>0x10050, 'right'=>0x1005D),
                     array('left'=>0x10080, 'right'=>0x100FA),
                     array('left'=>0x10280, 'right'=>0x1029C),
                     array('left'=>0x102A0, 'right'=>0x102D0),
                     array('left'=>0x10300, 'right'=>0x1031E),
                     array('left'=>0x10330, 'right'=>0x10340),
                     array('left'=>0x10342, 'right'=>0x10349),
                     array('left'=>0x10380, 'right'=>0x1039D),
                     array('left'=>0x103A0, 'right'=>0x103C3),
                     array('left'=>0x103C8, 'right'=>0x103CF),
                     array('left'=>0x10450, 'right'=>0x1049D),
                     array('left'=>0x10800, 'right'=>0x10805),
                     array('left'=>0x10808, 'right'=>0x10808),
                     array('left'=>0x1080A, 'right'=>0x10835),
                     array('left'=>0x10837, 'right'=>0x10838),
                     array('left'=>0x1083C, 'right'=>0x1083C),
                     array('left'=>0x1083F, 'right'=>0x10855),
                     array('left'=>0x10900, 'right'=>0x10915),
                     array('left'=>0x10920, 'right'=>0x10939),
                     array('left'=>0x10980, 'right'=>0x109B7),
                     array('left'=>0x109BE, 'right'=>0x109BF),
                     array('left'=>0x10A00, 'right'=>0x10A00),
                     array('left'=>0x10A10, 'right'=>0x10A13),
                     array('left'=>0x10A15, 'right'=>0x10A17),
                     array('left'=>0x10A19, 'right'=>0x10A33),
                     array('left'=>0x10A60, 'right'=>0x10A7C),
                     array('left'=>0x10B00, 'right'=>0x10B35),
                     array('left'=>0x10B40, 'right'=>0x10B55),
                     array('left'=>0x10B60, 'right'=>0x10B72),
                     array('left'=>0x10C00, 'right'=>0x10C48),
                     array('left'=>0x11003, 'right'=>0x11037),
                     array('left'=>0x11083, 'right'=>0x110AF),
                     array('left'=>0x110D0, 'right'=>0x110E8),
                     array('left'=>0x11103, 'right'=>0x11126),
                     array('left'=>0x11183, 'right'=>0x111B2),
                     array('left'=>0x111C1, 'right'=>0x111C4),
                     array('left'=>0x11680, 'right'=>0x116AA),
                     array('left'=>0x12000, 'right'=>0x1236E),
                     array('left'=>0x13000, 'right'=>0x1342E),
                     array('left'=>0x16800, 'right'=>0x16A38),
                     array('left'=>0x16F00, 'right'=>0x16F44),
                     array('left'=>0x16F50, 'right'=>0x16F50),
                     array('left'=>0x1B000, 'right'=>0x1B001),
                     array('left'=>0x1EE00, 'right'=>0x1EE03),
                     array('left'=>0x1EE05, 'right'=>0x1EE1F),
                     array('left'=>0x1EE21, 'right'=>0x1EE22),
                     array('left'=>0x1EE24, 'right'=>0x1EE24),
                     array('left'=>0x1EE27, 'right'=>0x1EE27),
                     array('left'=>0x1EE29, 'right'=>0x1EE32),
                     array('left'=>0x1EE34, 'right'=>0x1EE37),
                     array('left'=>0x1EE39, 'right'=>0x1EE39),
                     array('left'=>0x1EE3B, 'right'=>0x1EE3B),
                     array('left'=>0x1EE42, 'right'=>0x1EE42),
                     array('left'=>0x1EE47, 'right'=>0x1EE47),
                     array('left'=>0x1EE49, 'right'=>0x1EE49),
                     array('left'=>0x1EE4B, 'right'=>0x1EE4B),
                     array('left'=>0x1EE4D, 'right'=>0x1EE4F),
                     array('left'=>0x1EE51, 'right'=>0x1EE52),
                     array('left'=>0x1EE54, 'right'=>0x1EE54),
                     array('left'=>0x1EE57, 'right'=>0x1EE57),
                     array('left'=>0x1EE59, 'right'=>0x1EE59),
                     array('left'=>0x1EE5B, 'right'=>0x1EE5B),
                     array('left'=>0x1EE5D, 'right'=>0x1EE5D),
                     array('left'=>0x1EE5F, 'right'=>0x1EE5F),
                     array('left'=>0x1EE61, 'right'=>0x1EE62),
                     array('left'=>0x1EE64, 'right'=>0x1EE64),
                     array('left'=>0x1EE67, 'right'=>0x1EE6A),
                     array('left'=>0x1EE6C, 'right'=>0x1EE72),
                     array('left'=>0x1EE74, 'right'=>0x1EE77),
                     array('left'=>0x1EE79, 'right'=>0x1EE7C),
                     array('left'=>0x1EE7E, 'right'=>0x1EE7E),
                     array('left'=>0x1EE80, 'right'=>0x1EE89),
                     array('left'=>0x1EE8B, 'right'=>0x1EE9B),
                     array('left'=>0x1EEA1, 'right'=>0x1EEA3),
                     array('left'=>0x1EEA5, 'right'=>0x1EEA9),
                     array('left'=>0x1EEAB, 'right'=>0x1EEBB),
                     array('left'=>0x20000, 'right'=>0x20000),
                     array('left'=>0x2A6D6, 'right'=>0x2A6D6),
                     array('left'=>0x2A700, 'right'=>0x2A700),
                     array('left'=>0x2B734, 'right'=>0x2B734),
                     array('left'=>0x2B740, 'right'=>0x2B740),
                     array('left'=>0x2B81D, 'right'=>0x2B81D),
                     array('left'=>0x2F800, 'right'=>0x2FA1D));
    }
    public static function Lt_ranges() {
        return array(array('left'=>0x01C5, 'right'=>0x01C5),
                     array('left'=>0x01C8, 'right'=>0x01C8),
                     array('left'=>0x01CB, 'right'=>0x01CB),
                     array('left'=>0x01F2, 'right'=>0x01F2),
                     array('left'=>0x1F88, 'right'=>0x1F8F),
                     array('left'=>0x1F98, 'right'=>0x1F9F),
                     array('left'=>0x1FA8, 'right'=>0x1FAF),
                     array('left'=>0x1FBC, 'right'=>0x1FBC),
                     array('left'=>0x1FCC, 'right'=>0x1FCC),
                     array('left'=>0x1FFC, 'right'=>0x1FFC));
    }
    public static function Lu_ranges() {
        return array(array('left'=>0x0041, 'right'=>0x005A),
                     array('left'=>0x00C0, 'right'=>0x00D6),
                     array('left'=>0x00D8, 'right'=>0x00DE),
                     array('left'=>0x0100, 'right'=>0x0100),
                     array('left'=>0x0102, 'right'=>0x0102),
                     array('left'=>0x0104, 'right'=>0x0104),
                     array('left'=>0x0106, 'right'=>0x0106),
                     array('left'=>0x0108, 'right'=>0x0108),
                     array('left'=>0x010A, 'right'=>0x010A),
                     array('left'=>0x010C, 'right'=>0x010C),
                     array('left'=>0x010E, 'right'=>0x010E),
                     array('left'=>0x0110, 'right'=>0x0110),
                     array('left'=>0x0112, 'right'=>0x0112),
                     array('left'=>0x0114, 'right'=>0x0114),
                     array('left'=>0x0116, 'right'=>0x0116),
                     array('left'=>0x0118, 'right'=>0x0118),
                     array('left'=>0x011A, 'right'=>0x011A),
                     array('left'=>0x011C, 'right'=>0x011C),
                     array('left'=>0x011E, 'right'=>0x011E),
                     array('left'=>0x0120, 'right'=>0x0120),
                     array('left'=>0x0122, 'right'=>0x0122),
                     array('left'=>0x0124, 'right'=>0x0124),
                     array('left'=>0x0126, 'right'=>0x0126),
                     array('left'=>0x0128, 'right'=>0x0128),
                     array('left'=>0x012A, 'right'=>0x012A),
                     array('left'=>0x012C, 'right'=>0x012C),
                     array('left'=>0x012E, 'right'=>0x012E),
                     array('left'=>0x0130, 'right'=>0x0130),
                     array('left'=>0x0132, 'right'=>0x0132),
                     array('left'=>0x0134, 'right'=>0x0134),
                     array('left'=>0x0136, 'right'=>0x0136),
                     array('left'=>0x0139, 'right'=>0x0139),
                     array('left'=>0x013B, 'right'=>0x013B),
                     array('left'=>0x013D, 'right'=>0x013D),
                     array('left'=>0x013F, 'right'=>0x013F),
                     array('left'=>0x0141, 'right'=>0x0141),
                     array('left'=>0x0143, 'right'=>0x0143),
                     array('left'=>0x0145, 'right'=>0x0145),
                     array('left'=>0x0147, 'right'=>0x0147),
                     array('left'=>0x014A, 'right'=>0x014A),
                     array('left'=>0x014C, 'right'=>0x014C),
                     array('left'=>0x014E, 'right'=>0x014E),
                     array('left'=>0x0150, 'right'=>0x0150),
                     array('left'=>0x0152, 'right'=>0x0152),
                     array('left'=>0x0154, 'right'=>0x0154),
                     array('left'=>0x0156, 'right'=>0x0156),
                     array('left'=>0x0158, 'right'=>0x0158),
                     array('left'=>0x015A, 'right'=>0x015A),
                     array('left'=>0x015C, 'right'=>0x015C),
                     array('left'=>0x015E, 'right'=>0x015E),
                     array('left'=>0x0160, 'right'=>0x0160),
                     array('left'=>0x0162, 'right'=>0x0162),
                     array('left'=>0x0164, 'right'=>0x0164),
                     array('left'=>0x0166, 'right'=>0x0166),
                     array('left'=>0x0168, 'right'=>0x0168),
                     array('left'=>0x016A, 'right'=>0x016A),
                     array('left'=>0x016C, 'right'=>0x016C),
                     array('left'=>0x016E, 'right'=>0x016E),
                     array('left'=>0x0170, 'right'=>0x0170),
                     array('left'=>0x0172, 'right'=>0x0172),
                     array('left'=>0x0174, 'right'=>0x0174),
                     array('left'=>0x0176, 'right'=>0x0176),
                     array('left'=>0x0178, 'right'=>0x0179),
                     array('left'=>0x017B, 'right'=>0x017B),
                     array('left'=>0x017D, 'right'=>0x017D),
                     array('left'=>0x0181, 'right'=>0x0182),
                     array('left'=>0x0184, 'right'=>0x0184),
                     array('left'=>0x0186, 'right'=>0x0187),
                     array('left'=>0x0189, 'right'=>0x018B),
                     array('left'=>0x018E, 'right'=>0x0191),
                     array('left'=>0x0193, 'right'=>0x0194),
                     array('left'=>0x0196, 'right'=>0x0198),
                     array('left'=>0x019C, 'right'=>0x019D),
                     array('left'=>0x019F, 'right'=>0x01A0),
                     array('left'=>0x01A2, 'right'=>0x01A2),
                     array('left'=>0x01A4, 'right'=>0x01A4),
                     array('left'=>0x01A6, 'right'=>0x01A7),
                     array('left'=>0x01A9, 'right'=>0x01A9),
                     array('left'=>0x01AC, 'right'=>0x01AC),
                     array('left'=>0x01AE, 'right'=>0x01AF),
                     array('left'=>0x01B1, 'right'=>0x01B3),
                     array('left'=>0x01B5, 'right'=>0x01B5),
                     array('left'=>0x01B7, 'right'=>0x01B8),
                     array('left'=>0x01BC, 'right'=>0x01BC),
                     array('left'=>0x01C4, 'right'=>0x01C4),
                     array('left'=>0x01C7, 'right'=>0x01C7),
                     array('left'=>0x01CA, 'right'=>0x01CA),
                     array('left'=>0x01CD, 'right'=>0x01CD),
                     array('left'=>0x01CF, 'right'=>0x01CF),
                     array('left'=>0x01D1, 'right'=>0x01D1),
                     array('left'=>0x01D3, 'right'=>0x01D3),
                     array('left'=>0x01D5, 'right'=>0x01D5),
                     array('left'=>0x01D7, 'right'=>0x01D7),
                     array('left'=>0x01D9, 'right'=>0x01D9),
                     array('left'=>0x01DB, 'right'=>0x01DB),
                     array('left'=>0x01DE, 'right'=>0x01DE),
                     array('left'=>0x01E0, 'right'=>0x01E0),
                     array('left'=>0x01E2, 'right'=>0x01E2),
                     array('left'=>0x01E4, 'right'=>0x01E4),
                     array('left'=>0x01E6, 'right'=>0x01E6),
                     array('left'=>0x01E8, 'right'=>0x01E8),
                     array('left'=>0x01EA, 'right'=>0x01EA),
                     array('left'=>0x01EC, 'right'=>0x01EC),
                     array('left'=>0x01EE, 'right'=>0x01EE),
                     array('left'=>0x01F1, 'right'=>0x01F1),
                     array('left'=>0x01F4, 'right'=>0x01F4),
                     array('left'=>0x01F6, 'right'=>0x01F8),
                     array('left'=>0x01FA, 'right'=>0x01FA),
                     array('left'=>0x01FC, 'right'=>0x01FC),
                     array('left'=>0x01FE, 'right'=>0x01FE),
                     array('left'=>0x0200, 'right'=>0x0200),
                     array('left'=>0x0202, 'right'=>0x0202),
                     array('left'=>0x0204, 'right'=>0x0204),
                     array('left'=>0x0206, 'right'=>0x0206),
                     array('left'=>0x0208, 'right'=>0x0208),
                     array('left'=>0x020A, 'right'=>0x020A),
                     array('left'=>0x020C, 'right'=>0x020C),
                     array('left'=>0x020E, 'right'=>0x020E),
                     array('left'=>0x0210, 'right'=>0x0210),
                     array('left'=>0x0212, 'right'=>0x0212),
                     array('left'=>0x0214, 'right'=>0x0214),
                     array('left'=>0x0216, 'right'=>0x0216),
                     array('left'=>0x0218, 'right'=>0x0218),
                     array('left'=>0x021A, 'right'=>0x021A),
                     array('left'=>0x021C, 'right'=>0x021C),
                     array('left'=>0x021E, 'right'=>0x021E),
                     array('left'=>0x0220, 'right'=>0x0220),
                     array('left'=>0x0222, 'right'=>0x0222),
                     array('left'=>0x0224, 'right'=>0x0224),
                     array('left'=>0x0226, 'right'=>0x0226),
                     array('left'=>0x0228, 'right'=>0x0228),
                     array('left'=>0x022A, 'right'=>0x022A),
                     array('left'=>0x022C, 'right'=>0x022C),
                     array('left'=>0x022E, 'right'=>0x022E),
                     array('left'=>0x0230, 'right'=>0x0230),
                     array('left'=>0x0232, 'right'=>0x0232),
                     array('left'=>0x023A, 'right'=>0x023B),
                     array('left'=>0x023D, 'right'=>0x023E),
                     array('left'=>0x0241, 'right'=>0x0241),
                     array('left'=>0x0243, 'right'=>0x0246),
                     array('left'=>0x0248, 'right'=>0x0248),
                     array('left'=>0x024A, 'right'=>0x024A),
                     array('left'=>0x024C, 'right'=>0x024C),
                     array('left'=>0x024E, 'right'=>0x024E),
                     array('left'=>0x0370, 'right'=>0x0370),
                     array('left'=>0x0372, 'right'=>0x0372),
                     array('left'=>0x0376, 'right'=>0x0376),
                     array('left'=>0x0386, 'right'=>0x0386),
                     array('left'=>0x0388, 'right'=>0x038A),
                     array('left'=>0x038C, 'right'=>0x038C),
                     array('left'=>0x038E, 'right'=>0x038F),
                     array('left'=>0x0391, 'right'=>0x03A1),
                     array('left'=>0x03A3, 'right'=>0x03AB),
                     array('left'=>0x03CF, 'right'=>0x03CF),
                     array('left'=>0x03D2, 'right'=>0x03D4),
                     array('left'=>0x03D8, 'right'=>0x03D8),
                     array('left'=>0x03DA, 'right'=>0x03DA),
                     array('left'=>0x03DC, 'right'=>0x03DC),
                     array('left'=>0x03DE, 'right'=>0x03DE),
                     array('left'=>0x03E0, 'right'=>0x03E0),
                     array('left'=>0x03E2, 'right'=>0x03E2),
                     array('left'=>0x03E4, 'right'=>0x03E4),
                     array('left'=>0x03E6, 'right'=>0x03E6),
                     array('left'=>0x03E8, 'right'=>0x03E8),
                     array('left'=>0x03EA, 'right'=>0x03EA),
                     array('left'=>0x03EC, 'right'=>0x03EC),
                     array('left'=>0x03EE, 'right'=>0x03EE),
                     array('left'=>0x03F4, 'right'=>0x03F4),
                     array('left'=>0x03F7, 'right'=>0x03F7),
                     array('left'=>0x03F9, 'right'=>0x03FA),
                     array('left'=>0x03FD, 'right'=>0x042F),
                     array('left'=>0x0460, 'right'=>0x0460),
                     array('left'=>0x0462, 'right'=>0x0462),
                     array('left'=>0x0464, 'right'=>0x0464),
                     array('left'=>0x0466, 'right'=>0x0466),
                     array('left'=>0x0468, 'right'=>0x0468),
                     array('left'=>0x046A, 'right'=>0x046A),
                     array('left'=>0x046C, 'right'=>0x046C),
                     array('left'=>0x046E, 'right'=>0x046E),
                     array('left'=>0x0470, 'right'=>0x0470),
                     array('left'=>0x0472, 'right'=>0x0472),
                     array('left'=>0x0474, 'right'=>0x0474),
                     array('left'=>0x0476, 'right'=>0x0476),
                     array('left'=>0x0478, 'right'=>0x0478),
                     array('left'=>0x047A, 'right'=>0x047A),
                     array('left'=>0x047C, 'right'=>0x047C),
                     array('left'=>0x047E, 'right'=>0x047E),
                     array('left'=>0x0480, 'right'=>0x0480),
                     array('left'=>0x048A, 'right'=>0x048A),
                     array('left'=>0x048C, 'right'=>0x048C),
                     array('left'=>0x048E, 'right'=>0x048E),
                     array('left'=>0x0490, 'right'=>0x0490),
                     array('left'=>0x0492, 'right'=>0x0492),
                     array('left'=>0x0494, 'right'=>0x0494),
                     array('left'=>0x0496, 'right'=>0x0496),
                     array('left'=>0x0498, 'right'=>0x0498),
                     array('left'=>0x049A, 'right'=>0x049A),
                     array('left'=>0x049C, 'right'=>0x049C),
                     array('left'=>0x049E, 'right'=>0x049E),
                     array('left'=>0x04A0, 'right'=>0x04A0),
                     array('left'=>0x04A2, 'right'=>0x04A2),
                     array('left'=>0x04A4, 'right'=>0x04A4),
                     array('left'=>0x04A6, 'right'=>0x04A6),
                     array('left'=>0x04A8, 'right'=>0x04A8),
                     array('left'=>0x04AA, 'right'=>0x04AA),
                     array('left'=>0x04AC, 'right'=>0x04AC),
                     array('left'=>0x04AE, 'right'=>0x04AE),
                     array('left'=>0x04B0, 'right'=>0x04B0),
                     array('left'=>0x04B2, 'right'=>0x04B2),
                     array('left'=>0x04B4, 'right'=>0x04B4),
                     array('left'=>0x04B6, 'right'=>0x04B6),
                     array('left'=>0x04B8, 'right'=>0x04B8),
                     array('left'=>0x04BA, 'right'=>0x04BA),
                     array('left'=>0x04BC, 'right'=>0x04BC),
                     array('left'=>0x04BE, 'right'=>0x04BE),
                     array('left'=>0x04C0, 'right'=>0x04C1),
                     array('left'=>0x04C3, 'right'=>0x04C3),
                     array('left'=>0x04C5, 'right'=>0x04C5),
                     array('left'=>0x04C7, 'right'=>0x04C7),
                     array('left'=>0x04C9, 'right'=>0x04C9),
                     array('left'=>0x04CB, 'right'=>0x04CB),
                     array('left'=>0x04CD, 'right'=>0x04CD),
                     array('left'=>0x04D0, 'right'=>0x04D0),
                     array('left'=>0x04D2, 'right'=>0x04D2),
                     array('left'=>0x04D4, 'right'=>0x04D4),
                     array('left'=>0x04D6, 'right'=>0x04D6),
                     array('left'=>0x04D8, 'right'=>0x04D8),
                     array('left'=>0x04DA, 'right'=>0x04DA),
                     array('left'=>0x04DC, 'right'=>0x04DC),
                     array('left'=>0x04DE, 'right'=>0x04DE),
                     array('left'=>0x04E0, 'right'=>0x04E0),
                     array('left'=>0x04E2, 'right'=>0x04E2),
                     array('left'=>0x04E4, 'right'=>0x04E4),
                     array('left'=>0x04E6, 'right'=>0x04E6),
                     array('left'=>0x04E8, 'right'=>0x04E8),
                     array('left'=>0x04EA, 'right'=>0x04EA),
                     array('left'=>0x04EC, 'right'=>0x04EC),
                     array('left'=>0x04EE, 'right'=>0x04EE),
                     array('left'=>0x04F0, 'right'=>0x04F0),
                     array('left'=>0x04F2, 'right'=>0x04F2),
                     array('left'=>0x04F4, 'right'=>0x04F4),
                     array('left'=>0x04F6, 'right'=>0x04F6),
                     array('left'=>0x04F8, 'right'=>0x04F8),
                     array('left'=>0x04FA, 'right'=>0x04FA),
                     array('left'=>0x04FC, 'right'=>0x04FC),
                     array('left'=>0x04FE, 'right'=>0x04FE),
                     array('left'=>0x0500, 'right'=>0x0500),
                     array('left'=>0x0502, 'right'=>0x0502),
                     array('left'=>0x0504, 'right'=>0x0504),
                     array('left'=>0x0506, 'right'=>0x0506),
                     array('left'=>0x0508, 'right'=>0x0508),
                     array('left'=>0x050A, 'right'=>0x050A),
                     array('left'=>0x050C, 'right'=>0x050C),
                     array('left'=>0x050E, 'right'=>0x050E),
                     array('left'=>0x0510, 'right'=>0x0510),
                     array('left'=>0x0512, 'right'=>0x0512),
                     array('left'=>0x0514, 'right'=>0x0514),
                     array('left'=>0x0516, 'right'=>0x0516),
                     array('left'=>0x0518, 'right'=>0x0518),
                     array('left'=>0x051A, 'right'=>0x051A),
                     array('left'=>0x051C, 'right'=>0x051C),
                     array('left'=>0x051E, 'right'=>0x051E),
                     array('left'=>0x0520, 'right'=>0x0520),
                     array('left'=>0x0522, 'right'=>0x0522),
                     array('left'=>0x0524, 'right'=>0x0524),
                     array('left'=>0x0526, 'right'=>0x0526),
                     array('left'=>0x0531, 'right'=>0x0556),
                     array('left'=>0x10A0, 'right'=>0x10C5),
                     array('left'=>0x10C7, 'right'=>0x10C7),
                     array('left'=>0x10CD, 'right'=>0x10CD),
                     array('left'=>0x1E00, 'right'=>0x1E00),
                     array('left'=>0x1E02, 'right'=>0x1E02),
                     array('left'=>0x1E04, 'right'=>0x1E04),
                     array('left'=>0x1E06, 'right'=>0x1E06),
                     array('left'=>0x1E08, 'right'=>0x1E08),
                     array('left'=>0x1E0A, 'right'=>0x1E0A),
                     array('left'=>0x1E0C, 'right'=>0x1E0C),
                     array('left'=>0x1E0E, 'right'=>0x1E0E),
                     array('left'=>0x1E10, 'right'=>0x1E10),
                     array('left'=>0x1E12, 'right'=>0x1E12),
                     array('left'=>0x1E14, 'right'=>0x1E14),
                     array('left'=>0x1E16, 'right'=>0x1E16),
                     array('left'=>0x1E18, 'right'=>0x1E18),
                     array('left'=>0x1E1A, 'right'=>0x1E1A),
                     array('left'=>0x1E1C, 'right'=>0x1E1C),
                     array('left'=>0x1E1E, 'right'=>0x1E1E),
                     array('left'=>0x1E20, 'right'=>0x1E20),
                     array('left'=>0x1E22, 'right'=>0x1E22),
                     array('left'=>0x1E24, 'right'=>0x1E24),
                     array('left'=>0x1E26, 'right'=>0x1E26),
                     array('left'=>0x1E28, 'right'=>0x1E28),
                     array('left'=>0x1E2A, 'right'=>0x1E2A),
                     array('left'=>0x1E2C, 'right'=>0x1E2C),
                     array('left'=>0x1E2E, 'right'=>0x1E2E),
                     array('left'=>0x1E30, 'right'=>0x1E30),
                     array('left'=>0x1E32, 'right'=>0x1E32),
                     array('left'=>0x1E34, 'right'=>0x1E34),
                     array('left'=>0x1E36, 'right'=>0x1E36),
                     array('left'=>0x1E38, 'right'=>0x1E38),
                     array('left'=>0x1E3A, 'right'=>0x1E3A),
                     array('left'=>0x1E3C, 'right'=>0x1E3C),
                     array('left'=>0x1E3E, 'right'=>0x1E3E),
                     array('left'=>0x1E40, 'right'=>0x1E40),
                     array('left'=>0x1E42, 'right'=>0x1E42),
                     array('left'=>0x1E44, 'right'=>0x1E44),
                     array('left'=>0x1E46, 'right'=>0x1E46),
                     array('left'=>0x1E48, 'right'=>0x1E48),
                     array('left'=>0x1E4A, 'right'=>0x1E4A),
                     array('left'=>0x1E4C, 'right'=>0x1E4C),
                     array('left'=>0x1E4E, 'right'=>0x1E4E),
                     array('left'=>0x1E50, 'right'=>0x1E50),
                     array('left'=>0x1E52, 'right'=>0x1E52),
                     array('left'=>0x1E54, 'right'=>0x1E54),
                     array('left'=>0x1E56, 'right'=>0x1E56),
                     array('left'=>0x1E58, 'right'=>0x1E58),
                     array('left'=>0x1E5A, 'right'=>0x1E5A),
                     array('left'=>0x1E5C, 'right'=>0x1E5C),
                     array('left'=>0x1E5E, 'right'=>0x1E5E),
                     array('left'=>0x1E60, 'right'=>0x1E60),
                     array('left'=>0x1E62, 'right'=>0x1E62),
                     array('left'=>0x1E64, 'right'=>0x1E64),
                     array('left'=>0x1E66, 'right'=>0x1E66),
                     array('left'=>0x1E68, 'right'=>0x1E68),
                     array('left'=>0x1E6A, 'right'=>0x1E6A),
                     array('left'=>0x1E6C, 'right'=>0x1E6C),
                     array('left'=>0x1E6E, 'right'=>0x1E6E),
                     array('left'=>0x1E70, 'right'=>0x1E70),
                     array('left'=>0x1E72, 'right'=>0x1E72),
                     array('left'=>0x1E74, 'right'=>0x1E74),
                     array('left'=>0x1E76, 'right'=>0x1E76),
                     array('left'=>0x1E78, 'right'=>0x1E78),
                     array('left'=>0x1E7A, 'right'=>0x1E7A),
                     array('left'=>0x1E7C, 'right'=>0x1E7C),
                     array('left'=>0x1E7E, 'right'=>0x1E7E),
                     array('left'=>0x1E80, 'right'=>0x1E80),
                     array('left'=>0x1E82, 'right'=>0x1E82),
                     array('left'=>0x1E84, 'right'=>0x1E84),
                     array('left'=>0x1E86, 'right'=>0x1E86),
                     array('left'=>0x1E88, 'right'=>0x1E88),
                     array('left'=>0x1E8A, 'right'=>0x1E8A),
                     array('left'=>0x1E8C, 'right'=>0x1E8C),
                     array('left'=>0x1E8E, 'right'=>0x1E8E),
                     array('left'=>0x1E90, 'right'=>0x1E90),
                     array('left'=>0x1E92, 'right'=>0x1E92),
                     array('left'=>0x1E94, 'right'=>0x1E94),
                     array('left'=>0x1E9E, 'right'=>0x1E9E),
                     array('left'=>0x1EA0, 'right'=>0x1EA0),
                     array('left'=>0x1EA2, 'right'=>0x1EA2),
                     array('left'=>0x1EA4, 'right'=>0x1EA4),
                     array('left'=>0x1EA6, 'right'=>0x1EA6),
                     array('left'=>0x1EA8, 'right'=>0x1EA8),
                     array('left'=>0x1EAA, 'right'=>0x1EAA),
                     array('left'=>0x1EAC, 'right'=>0x1EAC),
                     array('left'=>0x1EAE, 'right'=>0x1EAE),
                     array('left'=>0x1EB0, 'right'=>0x1EB0),
                     array('left'=>0x1EB2, 'right'=>0x1EB2),
                     array('left'=>0x1EB4, 'right'=>0x1EB4),
                     array('left'=>0x1EB6, 'right'=>0x1EB6),
                     array('left'=>0x1EB8, 'right'=>0x1EB8),
                     array('left'=>0x1EBA, 'right'=>0x1EBA),
                     array('left'=>0x1EBC, 'right'=>0x1EBC),
                     array('left'=>0x1EBE, 'right'=>0x1EBE),
                     array('left'=>0x1EC0, 'right'=>0x1EC0),
                     array('left'=>0x1EC2, 'right'=>0x1EC2),
                     array('left'=>0x1EC4, 'right'=>0x1EC4),
                     array('left'=>0x1EC6, 'right'=>0x1EC6),
                     array('left'=>0x1EC8, 'right'=>0x1EC8),
                     array('left'=>0x1ECA, 'right'=>0x1ECA),
                     array('left'=>0x1ECC, 'right'=>0x1ECC),
                     array('left'=>0x1ECE, 'right'=>0x1ECE),
                     array('left'=>0x1ED0, 'right'=>0x1ED0),
                     array('left'=>0x1ED2, 'right'=>0x1ED2),
                     array('left'=>0x1ED4, 'right'=>0x1ED4),
                     array('left'=>0x1ED6, 'right'=>0x1ED6),
                     array('left'=>0x1ED8, 'right'=>0x1ED8),
                     array('left'=>0x1EDA, 'right'=>0x1EDA),
                     array('left'=>0x1EDC, 'right'=>0x1EDC),
                     array('left'=>0x1EDE, 'right'=>0x1EDE),
                     array('left'=>0x1EE0, 'right'=>0x1EE0),
                     array('left'=>0x1EE2, 'right'=>0x1EE2),
                     array('left'=>0x1EE4, 'right'=>0x1EE4),
                     array('left'=>0x1EE6, 'right'=>0x1EE6),
                     array('left'=>0x1EE8, 'right'=>0x1EE8),
                     array('left'=>0x1EEA, 'right'=>0x1EEA),
                     array('left'=>0x1EEC, 'right'=>0x1EEC),
                     array('left'=>0x1EEE, 'right'=>0x1EEE),
                     array('left'=>0x1EF0, 'right'=>0x1EF0),
                     array('left'=>0x1EF2, 'right'=>0x1EF2),
                     array('left'=>0x1EF4, 'right'=>0x1EF4),
                     array('left'=>0x1EF6, 'right'=>0x1EF6),
                     array('left'=>0x1EF8, 'right'=>0x1EF8),
                     array('left'=>0x1EFA, 'right'=>0x1EFA),
                     array('left'=>0x1EFC, 'right'=>0x1EFC),
                     array('left'=>0x1EFE, 'right'=>0x1EFE),
                     array('left'=>0x1F08, 'right'=>0x1F0F),
                     array('left'=>0x1F18, 'right'=>0x1F1D),
                     array('left'=>0x1F28, 'right'=>0x1F2F),
                     array('left'=>0x1F38, 'right'=>0x1F3F),
                     array('left'=>0x1F48, 'right'=>0x1F4D),
                     array('left'=>0x1F59, 'right'=>0x1F59),
                     array('left'=>0x1F5B, 'right'=>0x1F5B),
                     array('left'=>0x1F5D, 'right'=>0x1F5D),
                     array('left'=>0x1F5F, 'right'=>0x1F5F),
                     array('left'=>0x1F68, 'right'=>0x1F6F),
                     array('left'=>0x1FB8, 'right'=>0x1FBB),
                     array('left'=>0x1FC8, 'right'=>0x1FCB),
                     array('left'=>0x1FD8, 'right'=>0x1FDB),
                     array('left'=>0x1FE8, 'right'=>0x1FEC),
                     array('left'=>0x1FF8, 'right'=>0x1FFB),
                     array('left'=>0x2102, 'right'=>0x2102),
                     array('left'=>0x2107, 'right'=>0x2107),
                     array('left'=>0x210B, 'right'=>0x210D),
                     array('left'=>0x2110, 'right'=>0x2112),
                     array('left'=>0x2115, 'right'=>0x2115),
                     array('left'=>0x2119, 'right'=>0x211D),
                     array('left'=>0x2124, 'right'=>0x2124),
                     array('left'=>0x2126, 'right'=>0x2126),
                     array('left'=>0x2128, 'right'=>0x2128),
                     array('left'=>0x212A, 'right'=>0x212D),
                     array('left'=>0x2130, 'right'=>0x2133),
                     array('left'=>0x213E, 'right'=>0x213F),
                     array('left'=>0x2145, 'right'=>0x2145),
                     array('left'=>0x2183, 'right'=>0x2183),
                     array('left'=>0x2C00, 'right'=>0x2C2E),
                     array('left'=>0x2C60, 'right'=>0x2C60),
                     array('left'=>0x2C62, 'right'=>0x2C64),
                     array('left'=>0x2C67, 'right'=>0x2C67),
                     array('left'=>0x2C69, 'right'=>0x2C69),
                     array('left'=>0x2C6B, 'right'=>0x2C6B),
                     array('left'=>0x2C6D, 'right'=>0x2C70),
                     array('left'=>0x2C72, 'right'=>0x2C72),
                     array('left'=>0x2C75, 'right'=>0x2C75),
                     array('left'=>0x2C7E, 'right'=>0x2C80),
                     array('left'=>0x2C82, 'right'=>0x2C82),
                     array('left'=>0x2C84, 'right'=>0x2C84),
                     array('left'=>0x2C86, 'right'=>0x2C86),
                     array('left'=>0x2C88, 'right'=>0x2C88),
                     array('left'=>0x2C8A, 'right'=>0x2C8A),
                     array('left'=>0x2C8C, 'right'=>0x2C8C),
                     array('left'=>0x2C8E, 'right'=>0x2C8E),
                     array('left'=>0x2C90, 'right'=>0x2C90),
                     array('left'=>0x2C92, 'right'=>0x2C92),
                     array('left'=>0x2C94, 'right'=>0x2C94),
                     array('left'=>0x2C96, 'right'=>0x2C96),
                     array('left'=>0x2C98, 'right'=>0x2C98),
                     array('left'=>0x2C9A, 'right'=>0x2C9A),
                     array('left'=>0x2C9C, 'right'=>0x2C9C),
                     array('left'=>0x2C9E, 'right'=>0x2C9E),
                     array('left'=>0x2CA0, 'right'=>0x2CA0),
                     array('left'=>0x2CA2, 'right'=>0x2CA2),
                     array('left'=>0x2CA4, 'right'=>0x2CA4),
                     array('left'=>0x2CA6, 'right'=>0x2CA6),
                     array('left'=>0x2CA8, 'right'=>0x2CA8),
                     array('left'=>0x2CAA, 'right'=>0x2CAA),
                     array('left'=>0x2CAC, 'right'=>0x2CAC),
                     array('left'=>0x2CAE, 'right'=>0x2CAE),
                     array('left'=>0x2CB0, 'right'=>0x2CB0),
                     array('left'=>0x2CB2, 'right'=>0x2CB2),
                     array('left'=>0x2CB4, 'right'=>0x2CB4),
                     array('left'=>0x2CB6, 'right'=>0x2CB6),
                     array('left'=>0x2CB8, 'right'=>0x2CB8),
                     array('left'=>0x2CBA, 'right'=>0x2CBA),
                     array('left'=>0x2CBC, 'right'=>0x2CBC),
                     array('left'=>0x2CBE, 'right'=>0x2CBE),
                     array('left'=>0x2CC0, 'right'=>0x2CC0),
                     array('left'=>0x2CC2, 'right'=>0x2CC2),
                     array('left'=>0x2CC4, 'right'=>0x2CC4),
                     array('left'=>0x2CC6, 'right'=>0x2CC6),
                     array('left'=>0x2CC8, 'right'=>0x2CC8),
                     array('left'=>0x2CCA, 'right'=>0x2CCA),
                     array('left'=>0x2CCC, 'right'=>0x2CCC),
                     array('left'=>0x2CCE, 'right'=>0x2CCE),
                     array('left'=>0x2CD0, 'right'=>0x2CD0),
                     array('left'=>0x2CD2, 'right'=>0x2CD2),
                     array('left'=>0x2CD4, 'right'=>0x2CD4),
                     array('left'=>0x2CD6, 'right'=>0x2CD6),
                     array('left'=>0x2CD8, 'right'=>0x2CD8),
                     array('left'=>0x2CDA, 'right'=>0x2CDA),
                     array('left'=>0x2CDC, 'right'=>0x2CDC),
                     array('left'=>0x2CDE, 'right'=>0x2CDE),
                     array('left'=>0x2CE0, 'right'=>0x2CE0),
                     array('left'=>0x2CE2, 'right'=>0x2CE2),
                     array('left'=>0x2CEB, 'right'=>0x2CEB),
                     array('left'=>0x2CED, 'right'=>0x2CED),
                     array('left'=>0x2CF2, 'right'=>0x2CF2),
                     array('left'=>0xA640, 'right'=>0xA640),
                     array('left'=>0xA642, 'right'=>0xA642),
                     array('left'=>0xA644, 'right'=>0xA644),
                     array('left'=>0xA646, 'right'=>0xA646),
                     array('left'=>0xA648, 'right'=>0xA648),
                     array('left'=>0xA64A, 'right'=>0xA64A),
                     array('left'=>0xA64C, 'right'=>0xA64C),
                     array('left'=>0xA64E, 'right'=>0xA64E),
                     array('left'=>0xA650, 'right'=>0xA650),
                     array('left'=>0xA652, 'right'=>0xA652),
                     array('left'=>0xA654, 'right'=>0xA654),
                     array('left'=>0xA656, 'right'=>0xA656),
                     array('left'=>0xA658, 'right'=>0xA658),
                     array('left'=>0xA65A, 'right'=>0xA65A),
                     array('left'=>0xA65C, 'right'=>0xA65C),
                     array('left'=>0xA65E, 'right'=>0xA65E),
                     array('left'=>0xA660, 'right'=>0xA660),
                     array('left'=>0xA662, 'right'=>0xA662),
                     array('left'=>0xA664, 'right'=>0xA664),
                     array('left'=>0xA666, 'right'=>0xA666),
                     array('left'=>0xA668, 'right'=>0xA668),
                     array('left'=>0xA66A, 'right'=>0xA66A),
                     array('left'=>0xA66C, 'right'=>0xA66C),
                     array('left'=>0xA680, 'right'=>0xA680),
                     array('left'=>0xA682, 'right'=>0xA682),
                     array('left'=>0xA684, 'right'=>0xA684),
                     array('left'=>0xA686, 'right'=>0xA686),
                     array('left'=>0xA688, 'right'=>0xA688),
                     array('left'=>0xA68A, 'right'=>0xA68A),
                     array('left'=>0xA68C, 'right'=>0xA68C),
                     array('left'=>0xA68E, 'right'=>0xA68E),
                     array('left'=>0xA690, 'right'=>0xA690),
                     array('left'=>0xA692, 'right'=>0xA692),
                     array('left'=>0xA694, 'right'=>0xA694),
                     array('left'=>0xA696, 'right'=>0xA696),
                     array('left'=>0xA722, 'right'=>0xA722),
                     array('left'=>0xA724, 'right'=>0xA724),
                     array('left'=>0xA726, 'right'=>0xA726),
                     array('left'=>0xA728, 'right'=>0xA728),
                     array('left'=>0xA72A, 'right'=>0xA72A),
                     array('left'=>0xA72C, 'right'=>0xA72C),
                     array('left'=>0xA72E, 'right'=>0xA72E),
                     array('left'=>0xA732, 'right'=>0xA732),
                     array('left'=>0xA734, 'right'=>0xA734),
                     array('left'=>0xA736, 'right'=>0xA736),
                     array('left'=>0xA738, 'right'=>0xA738),
                     array('left'=>0xA73A, 'right'=>0xA73A),
                     array('left'=>0xA73C, 'right'=>0xA73C),
                     array('left'=>0xA73E, 'right'=>0xA73E),
                     array('left'=>0xA740, 'right'=>0xA740),
                     array('left'=>0xA742, 'right'=>0xA742),
                     array('left'=>0xA744, 'right'=>0xA744),
                     array('left'=>0xA746, 'right'=>0xA746),
                     array('left'=>0xA748, 'right'=>0xA748),
                     array('left'=>0xA74A, 'right'=>0xA74A),
                     array('left'=>0xA74C, 'right'=>0xA74C),
                     array('left'=>0xA74E, 'right'=>0xA74E),
                     array('left'=>0xA750, 'right'=>0xA750),
                     array('left'=>0xA752, 'right'=>0xA752),
                     array('left'=>0xA754, 'right'=>0xA754),
                     array('left'=>0xA756, 'right'=>0xA756),
                     array('left'=>0xA758, 'right'=>0xA758),
                     array('left'=>0xA75A, 'right'=>0xA75A),
                     array('left'=>0xA75C, 'right'=>0xA75C),
                     array('left'=>0xA75E, 'right'=>0xA75E),
                     array('left'=>0xA760, 'right'=>0xA760),
                     array('left'=>0xA762, 'right'=>0xA762),
                     array('left'=>0xA764, 'right'=>0xA764),
                     array('left'=>0xA766, 'right'=>0xA766),
                     array('left'=>0xA768, 'right'=>0xA768),
                     array('left'=>0xA76A, 'right'=>0xA76A),
                     array('left'=>0xA76C, 'right'=>0xA76C),
                     array('left'=>0xA76E, 'right'=>0xA76E),
                     array('left'=>0xA779, 'right'=>0xA779),
                     array('left'=>0xA77B, 'right'=>0xA77B),
                     array('left'=>0xA77D, 'right'=>0xA77E),
                     array('left'=>0xA780, 'right'=>0xA780),
                     array('left'=>0xA782, 'right'=>0xA782),
                     array('left'=>0xA784, 'right'=>0xA784),
                     array('left'=>0xA786, 'right'=>0xA786),
                     array('left'=>0xA78B, 'right'=>0xA78B),
                     array('left'=>0xA78D, 'right'=>0xA78D),
                     array('left'=>0xA790, 'right'=>0xA790),
                     array('left'=>0xA792, 'right'=>0xA792),
                     array('left'=>0xA7A0, 'right'=>0xA7A0),
                     array('left'=>0xA7A2, 'right'=>0xA7A2),
                     array('left'=>0xA7A4, 'right'=>0xA7A4),
                     array('left'=>0xA7A6, 'right'=>0xA7A6),
                     array('left'=>0xA7A8, 'right'=>0xA7A8),
                     array('left'=>0xA7AA, 'right'=>0xA7AA),
                     array('left'=>0xFF21, 'right'=>0xFF3A),
                     array('left'=>0x10400, 'right'=>0x10427),
                     array('left'=>0x1D400, 'right'=>0x1D419),
                     array('left'=>0x1D434, 'right'=>0x1D44D),
                     array('left'=>0x1D468, 'right'=>0x1D481),
                     array('left'=>0x1D49C, 'right'=>0x1D49C),
                     array('left'=>0x1D49E, 'right'=>0x1D49F),
                     array('left'=>0x1D4A2, 'right'=>0x1D4A2),
                     array('left'=>0x1D4A5, 'right'=>0x1D4A6),
                     array('left'=>0x1D4A9, 'right'=>0x1D4AC),
                     array('left'=>0x1D4AE, 'right'=>0x1D4B5),
                     array('left'=>0x1D4D0, 'right'=>0x1D4E9),
                     array('left'=>0x1D504, 'right'=>0x1D505),
                     array('left'=>0x1D507, 'right'=>0x1D50A),
                     array('left'=>0x1D50D, 'right'=>0x1D514),
                     array('left'=>0x1D516, 'right'=>0x1D51C),
                     array('left'=>0x1D538, 'right'=>0x1D539),
                     array('left'=>0x1D53B, 'right'=>0x1D53E),
                     array('left'=>0x1D540, 'right'=>0x1D544),
                     array('left'=>0x1D546, 'right'=>0x1D546),
                     array('left'=>0x1D54A, 'right'=>0x1D550),
                     array('left'=>0x1D56C, 'right'=>0x1D585),
                     array('left'=>0x1D5A0, 'right'=>0x1D5B9),
                     array('left'=>0x1D5D4, 'right'=>0x1D5ED),
                     array('left'=>0x1D608, 'right'=>0x1D621),
                     array('left'=>0x1D63C, 'right'=>0x1D655),
                     array('left'=>0x1D670, 'right'=>0x1D689),
                     array('left'=>0x1D6A8, 'right'=>0x1D6C0),
                     array('left'=>0x1D6E2, 'right'=>0x1D6FA),
                     array('left'=>0x1D71C, 'right'=>0x1D734),
                     array('left'=>0x1D756, 'right'=>0x1D76E),
                     array('left'=>0x1D790, 'right'=>0x1D7A8),
                     array('left'=>0x1D7CA, 'right'=>0x1D7CA));
    }
    public static function L_ranges() {
        return array_merge(self::Ll_ranges(), self::Lm_ranges(), self::Lo_ranges(),
                           self::Lt_ranges(), self::Lu_ranges());
    }
    /******************************************************************/
    public static function Mc_ranges() {
        return array(array('left'=>0x0903, 'right'=>0x0903),
                     array('left'=>0x093B, 'right'=>0x093B),
                     array('left'=>0x093E, 'right'=>0x0940),
                     array('left'=>0x0949, 'right'=>0x094C),
                     array('left'=>0x094E, 'right'=>0x094F),
                     array('left'=>0x0982, 'right'=>0x0983),
                     array('left'=>0x09BE, 'right'=>0x09C0),
                     array('left'=>0x09C7, 'right'=>0x09C8),
                     array('left'=>0x09CB, 'right'=>0x09CC),
                     array('left'=>0x09D7, 'right'=>0x09D7),
                     array('left'=>0x0A03, 'right'=>0x0A03),
                     array('left'=>0x0A3E, 'right'=>0x0A40),
                     array('left'=>0x0A83, 'right'=>0x0A83),
                     array('left'=>0x0ABE, 'right'=>0x0AC0),
                     array('left'=>0x0AC9, 'right'=>0x0AC9),
                     array('left'=>0x0ACB, 'right'=>0x0ACC),
                     array('left'=>0x0B02, 'right'=>0x0B03),
                     array('left'=>0x0B3E, 'right'=>0x0B3E),
                     array('left'=>0x0B40, 'right'=>0x0B40),
                     array('left'=>0x0B47, 'right'=>0x0B48),
                     array('left'=>0x0B4B, 'right'=>0x0B4C),
                     array('left'=>0x0B57, 'right'=>0x0B57),
                     array('left'=>0x0BBE, 'right'=>0x0BBF),
                     array('left'=>0x0BC1, 'right'=>0x0BC2),
                     array('left'=>0x0BC6, 'right'=>0x0BC8),
                     array('left'=>0x0BCA, 'right'=>0x0BCC),
                     array('left'=>0x0BD7, 'right'=>0x0BD7),
                     array('left'=>0x0C01, 'right'=>0x0C03),
                     array('left'=>0x0C41, 'right'=>0x0C44),
                     array('left'=>0x0C82, 'right'=>0x0C83),
                     array('left'=>0x0CBE, 'right'=>0x0CBE),
                     array('left'=>0x0CC0, 'right'=>0x0CC4),
                     array('left'=>0x0CC7, 'right'=>0x0CC8),
                     array('left'=>0x0CCA, 'right'=>0x0CCB),
                     array('left'=>0x0CD5, 'right'=>0x0CD6),
                     array('left'=>0x0D02, 'right'=>0x0D03),
                     array('left'=>0x0D3E, 'right'=>0x0D40),
                     array('left'=>0x0D46, 'right'=>0x0D48),
                     array('left'=>0x0D4A, 'right'=>0x0D4C),
                     array('left'=>0x0D57, 'right'=>0x0D57),
                     array('left'=>0x0D82, 'right'=>0x0D83),
                     array('left'=>0x0DCF, 'right'=>0x0DD1),
                     array('left'=>0x0DD8, 'right'=>0x0DDF),
                     array('left'=>0x0DF2, 'right'=>0x0DF3),
                     array('left'=>0x0F3E, 'right'=>0x0F3F),
                     array('left'=>0x0F7F, 'right'=>0x0F7F),
                     array('left'=>0x102B, 'right'=>0x102C),
                     array('left'=>0x1031, 'right'=>0x1031),
                     array('left'=>0x1038, 'right'=>0x1038),
                     array('left'=>0x103B, 'right'=>0x103C),
                     array('left'=>0x1056, 'right'=>0x1057),
                     array('left'=>0x1062, 'right'=>0x1064),
                     array('left'=>0x1067, 'right'=>0x106D),
                     array('left'=>0x1083, 'right'=>0x1084),
                     array('left'=>0x1087, 'right'=>0x108C),
                     array('left'=>0x108F, 'right'=>0x108F),
                     array('left'=>0x109A, 'right'=>0x109C),
                     array('left'=>0x17B6, 'right'=>0x17B6),
                     array('left'=>0x17BE, 'right'=>0x17C5),
                     array('left'=>0x17C7, 'right'=>0x17C8),
                     array('left'=>0x1923, 'right'=>0x1926),
                     array('left'=>0x1929, 'right'=>0x192B),
                     array('left'=>0x1930, 'right'=>0x1931),
                     array('left'=>0x1933, 'right'=>0x1938),
                     array('left'=>0x19B0, 'right'=>0x19C0),
                     array('left'=>0x19C8, 'right'=>0x19C9),
                     array('left'=>0x1A19, 'right'=>0x1A1B),
                     array('left'=>0x1A55, 'right'=>0x1A55),
                     array('left'=>0x1A57, 'right'=>0x1A57),
                     array('left'=>0x1A61, 'right'=>0x1A61),
                     array('left'=>0x1A63, 'right'=>0x1A64),
                     array('left'=>0x1A6D, 'right'=>0x1A72),
                     array('left'=>0x1B04, 'right'=>0x1B04),
                     array('left'=>0x1B35, 'right'=>0x1B35),
                     array('left'=>0x1B3B, 'right'=>0x1B3B),
                     array('left'=>0x1B3D, 'right'=>0x1B41),
                     array('left'=>0x1B43, 'right'=>0x1B44),
                     array('left'=>0x1B82, 'right'=>0x1B82),
                     array('left'=>0x1BA1, 'right'=>0x1BA1),
                     array('left'=>0x1BA6, 'right'=>0x1BA7),
                     array('left'=>0x1BAA, 'right'=>0x1BAA),
                     array('left'=>0x1BAC, 'right'=>0x1BAD),
                     array('left'=>0x1BE7, 'right'=>0x1BE7),
                     array('left'=>0x1BEA, 'right'=>0x1BEC),
                     array('left'=>0x1BEE, 'right'=>0x1BEE),
                     array('left'=>0x1BF2, 'right'=>0x1BF3),
                     array('left'=>0x1C24, 'right'=>0x1C2B),
                     array('left'=>0x1C34, 'right'=>0x1C35),
                     array('left'=>0x1CE1, 'right'=>0x1CE1),
                     array('left'=>0x1CF2, 'right'=>0x1CF3),
                     array('left'=>0x302E, 'right'=>0x302F),
                     array('left'=>0xA823, 'right'=>0xA824),
                     array('left'=>0xA827, 'right'=>0xA827),
                     array('left'=>0xA880, 'right'=>0xA881),
                     array('left'=>0xA8B4, 'right'=>0xA8C3),
                     array('left'=>0xA952, 'right'=>0xA953),
                     array('left'=>0xA983, 'right'=>0xA983),
                     array('left'=>0xA9B4, 'right'=>0xA9B5),
                     array('left'=>0xA9BA, 'right'=>0xA9BB),
                     array('left'=>0xA9BD, 'right'=>0xA9C0),
                     array('left'=>0xAA2F, 'right'=>0xAA30),
                     array('left'=>0xAA33, 'right'=>0xAA34),
                     array('left'=>0xAA4D, 'right'=>0xAA4D),
                     array('left'=>0xAA7B, 'right'=>0xAA7B),
                     array('left'=>0xAAEB, 'right'=>0xAAEB),
                     array('left'=>0xAAEE, 'right'=>0xAAEF),
                     array('left'=>0xAAF5, 'right'=>0xAAF5),
                     array('left'=>0xABE3, 'right'=>0xABE4),
                     array('left'=>0xABE6, 'right'=>0xABE7),
                     array('left'=>0xABE9, 'right'=>0xABEA),
                     array('left'=>0xABEC, 'right'=>0xABEC),
                     array('left'=>0x11000, 'right'=>0x11000),
                     array('left'=>0x11002, 'right'=>0x11002),
                     array('left'=>0x11082, 'right'=>0x11082),
                     array('left'=>0x110B0, 'right'=>0x110B2),
                     array('left'=>0x110B7, 'right'=>0x110B8),
                     array('left'=>0x1112C, 'right'=>0x1112C),
                     array('left'=>0x11182, 'right'=>0x11182),
                     array('left'=>0x111B3, 'right'=>0x111B5),
                     array('left'=>0x111BF, 'right'=>0x111C0),
                     array('left'=>0x116AC, 'right'=>0x116AC),
                     array('left'=>0x116AE, 'right'=>0x116AF),
                     array('left'=>0x116B6, 'right'=>0x116B6),
                     array('left'=>0x16F51, 'right'=>0x16F7E),
                     array('left'=>0x1D165, 'right'=>0x1D166),
                     array('left'=>0x1D16D, 'right'=>0x1D172));
    }
    public static function Me_ranges() {
        return array(array('left'=>0x0488, 'right'=>0x0489),
                     array('left'=>0x20DD, 'right'=>0x20E0),
                     array('left'=>0x20E2, 'right'=>0x20E4),
                     array('left'=>0xA670, 'right'=>0xA672));
    }
    public static function Mn_ranges() {
        return array(array('left'=>0x0300, 'right'=>0x036F),
                     array('left'=>0x0483, 'right'=>0x0487),
                     array('left'=>0x0591, 'right'=>0x05BD),
                     array('left'=>0x05BF, 'right'=>0x05BF),
                     array('left'=>0x05C1, 'right'=>0x05C2),
                     array('left'=>0x05C4, 'right'=>0x05C5),
                     array('left'=>0x05C7, 'right'=>0x05C7),
                     array('left'=>0x0610, 'right'=>0x061A),
                     array('left'=>0x064B, 'right'=>0x065F),
                     array('left'=>0x0670, 'right'=>0x0670),
                     array('left'=>0x06D6, 'right'=>0x06DC),
                     array('left'=>0x06DF, 'right'=>0x06E4),
                     array('left'=>0x06E7, 'right'=>0x06E8),
                     array('left'=>0x06EA, 'right'=>0x06ED),
                     array('left'=>0x0711, 'right'=>0x0711),
                     array('left'=>0x0730, 'right'=>0x074A),
                     array('left'=>0x07A6, 'right'=>0x07B0),
                     array('left'=>0x07EB, 'right'=>0x07F3),
                     array('left'=>0x0816, 'right'=>0x0819),
                     array('left'=>0x081B, 'right'=>0x0823),
                     array('left'=>0x0825, 'right'=>0x0827),
                     array('left'=>0x0829, 'right'=>0x082D),
                     array('left'=>0x0859, 'right'=>0x085B),
                     array('left'=>0x08E4, 'right'=>0x08FE),
                     array('left'=>0x0900, 'right'=>0x0902),
                     array('left'=>0x093A, 'right'=>0x093A),
                     array('left'=>0x093C, 'right'=>0x093C),
                     array('left'=>0x0941, 'right'=>0x0948),
                     array('left'=>0x094D, 'right'=>0x094D),
                     array('left'=>0x0951, 'right'=>0x0957),
                     array('left'=>0x0962, 'right'=>0x0963),
                     array('left'=>0x0981, 'right'=>0x0981),
                     array('left'=>0x09BC, 'right'=>0x09BC),
                     array('left'=>0x09C1, 'right'=>0x09C4),
                     array('left'=>0x09CD, 'right'=>0x09CD),
                     array('left'=>0x09E2, 'right'=>0x09E3),
                     array('left'=>0x0A01, 'right'=>0x0A02),
                     array('left'=>0x0A3C, 'right'=>0x0A3C),
                     array('left'=>0x0A41, 'right'=>0x0A42),
                     array('left'=>0x0A47, 'right'=>0x0A48),
                     array('left'=>0x0A4B, 'right'=>0x0A4D),
                     array('left'=>0x0A51, 'right'=>0x0A51),
                     array('left'=>0x0A70, 'right'=>0x0A71),
                     array('left'=>0x0A75, 'right'=>0x0A75),
                     array('left'=>0x0A81, 'right'=>0x0A82),
                     array('left'=>0x0ABC, 'right'=>0x0ABC),
                     array('left'=>0x0AC1, 'right'=>0x0AC5),
                     array('left'=>0x0AC7, 'right'=>0x0AC8),
                     array('left'=>0x0ACD, 'right'=>0x0ACD),
                     array('left'=>0x0AE2, 'right'=>0x0AE3),
                     array('left'=>0x0B01, 'right'=>0x0B01),
                     array('left'=>0x0B3C, 'right'=>0x0B3C),
                     array('left'=>0x0B3F, 'right'=>0x0B3F),
                     array('left'=>0x0B41, 'right'=>0x0B44),
                     array('left'=>0x0B4D, 'right'=>0x0B4D),
                     array('left'=>0x0B56, 'right'=>0x0B56),
                     array('left'=>0x0B62, 'right'=>0x0B63),
                     array('left'=>0x0B82, 'right'=>0x0B82),
                     array('left'=>0x0BC0, 'right'=>0x0BC0),
                     array('left'=>0x0BCD, 'right'=>0x0BCD),
                     array('left'=>0x0C3E, 'right'=>0x0C40),
                     array('left'=>0x0C46, 'right'=>0x0C48),
                     array('left'=>0x0C4A, 'right'=>0x0C4D),
                     array('left'=>0x0C55, 'right'=>0x0C56),
                     array('left'=>0x0C62, 'right'=>0x0C63),
                     array('left'=>0x0CBC, 'right'=>0x0CBC),
                     array('left'=>0x0CBF, 'right'=>0x0CBF),
                     array('left'=>0x0CC6, 'right'=>0x0CC6),
                     array('left'=>0x0CCC, 'right'=>0x0CCD),
                     array('left'=>0x0CE2, 'right'=>0x0CE3),
                     array('left'=>0x0D41, 'right'=>0x0D44),
                     array('left'=>0x0D4D, 'right'=>0x0D4D),
                     array('left'=>0x0D62, 'right'=>0x0D63),
                     array('left'=>0x0DCA, 'right'=>0x0DCA),
                     array('left'=>0x0DD2, 'right'=>0x0DD4),
                     array('left'=>0x0DD6, 'right'=>0x0DD6),
                     array('left'=>0x0E31, 'right'=>0x0E31),
                     array('left'=>0x0E34, 'right'=>0x0E3A),
                     array('left'=>0x0E47, 'right'=>0x0E4E),
                     array('left'=>0x0EB1, 'right'=>0x0EB1),
                     array('left'=>0x0EB4, 'right'=>0x0EB9),
                     array('left'=>0x0EBB, 'right'=>0x0EBC),
                     array('left'=>0x0EC8, 'right'=>0x0ECD),
                     array('left'=>0x0F18, 'right'=>0x0F19),
                     array('left'=>0x0F35, 'right'=>0x0F35),
                     array('left'=>0x0F37, 'right'=>0x0F37),
                     array('left'=>0x0F39, 'right'=>0x0F39),
                     array('left'=>0x0F71, 'right'=>0x0F7E),
                     array('left'=>0x0F80, 'right'=>0x0F84),
                     array('left'=>0x0F86, 'right'=>0x0F87),
                     array('left'=>0x0F8D, 'right'=>0x0F97),
                     array('left'=>0x0F99, 'right'=>0x0FBC),
                     array('left'=>0x0FC6, 'right'=>0x0FC6),
                     array('left'=>0x102D, 'right'=>0x1030),
                     array('left'=>0x1032, 'right'=>0x1037),
                     array('left'=>0x1039, 'right'=>0x103A),
                     array('left'=>0x103D, 'right'=>0x103E),
                     array('left'=>0x1058, 'right'=>0x1059),
                     array('left'=>0x105E, 'right'=>0x1060),
                     array('left'=>0x1071, 'right'=>0x1074),
                     array('left'=>0x1082, 'right'=>0x1082),
                     array('left'=>0x1085, 'right'=>0x1086),
                     array('left'=>0x108D, 'right'=>0x108D),
                     array('left'=>0x109D, 'right'=>0x109D),
                     array('left'=>0x135D, 'right'=>0x135F),
                     array('left'=>0x1712, 'right'=>0x1714),
                     array('left'=>0x1732, 'right'=>0x1734),
                     array('left'=>0x1752, 'right'=>0x1753),
                     array('left'=>0x1772, 'right'=>0x1773),
                     array('left'=>0x17B4, 'right'=>0x17B5),
                     array('left'=>0x17B7, 'right'=>0x17BD),
                     array('left'=>0x17C6, 'right'=>0x17C6),
                     array('left'=>0x17C9, 'right'=>0x17D3),
                     array('left'=>0x17DD, 'right'=>0x17DD),
                     array('left'=>0x180B, 'right'=>0x180D),
                     array('left'=>0x18A9, 'right'=>0x18A9),
                     array('left'=>0x1920, 'right'=>0x1922),
                     array('left'=>0x1927, 'right'=>0x1928),
                     array('left'=>0x1932, 'right'=>0x1932),
                     array('left'=>0x1939, 'right'=>0x193B),
                     array('left'=>0x1A17, 'right'=>0x1A18),
                     array('left'=>0x1A56, 'right'=>0x1A56),
                     array('left'=>0x1A58, 'right'=>0x1A5E),
                     array('left'=>0x1A60, 'right'=>0x1A60),
                     array('left'=>0x1A62, 'right'=>0x1A62),
                     array('left'=>0x1A65, 'right'=>0x1A6C),
                     array('left'=>0x1A73, 'right'=>0x1A7C),
                     array('left'=>0x1A7F, 'right'=>0x1A7F),
                     array('left'=>0x1B00, 'right'=>0x1B03),
                     array('left'=>0x1B34, 'right'=>0x1B34),
                     array('left'=>0x1B36, 'right'=>0x1B3A),
                     array('left'=>0x1B3C, 'right'=>0x1B3C),
                     array('left'=>0x1B42, 'right'=>0x1B42),
                     array('left'=>0x1B6B, 'right'=>0x1B73),
                     array('left'=>0x1B80, 'right'=>0x1B81),
                     array('left'=>0x1BA2, 'right'=>0x1BA5),
                     array('left'=>0x1BA8, 'right'=>0x1BA9),
                     array('left'=>0x1BAB, 'right'=>0x1BAB),
                     array('left'=>0x1BE6, 'right'=>0x1BE6),
                     array('left'=>0x1BE8, 'right'=>0x1BE9),
                     array('left'=>0x1BED, 'right'=>0x1BED),
                     array('left'=>0x1BEF, 'right'=>0x1BF1),
                     array('left'=>0x1C2C, 'right'=>0x1C33),
                     array('left'=>0x1C36, 'right'=>0x1C37),
                     array('left'=>0x1CD0, 'right'=>0x1CD2),
                     array('left'=>0x1CD4, 'right'=>0x1CE0),
                     array('left'=>0x1CE2, 'right'=>0x1CE8),
                     array('left'=>0x1CED, 'right'=>0x1CED),
                     array('left'=>0x1CF4, 'right'=>0x1CF4),
                     array('left'=>0x1DC0, 'right'=>0x1DE6),
                     array('left'=>0x1DFC, 'right'=>0x1DFF),
                     array('left'=>0x20D0, 'right'=>0x20DC),
                     array('left'=>0x20E1, 'right'=>0x20E1),
                     array('left'=>0x20E5, 'right'=>0x20F0),
                     array('left'=>0x2CEF, 'right'=>0x2CF1),
                     array('left'=>0x2D7F, 'right'=>0x2D7F),
                     array('left'=>0x2DE0, 'right'=>0x2DFF),
                     array('left'=>0x302A, 'right'=>0x302D),
                     array('left'=>0x3099, 'right'=>0x309A),
                     array('left'=>0xA66F, 'right'=>0xA66F),
                     array('left'=>0xA674, 'right'=>0xA67D),
                     array('left'=>0xA69F, 'right'=>0xA69F),
                     array('left'=>0xA6F0, 'right'=>0xA6F1),
                     array('left'=>0xA802, 'right'=>0xA802),
                     array('left'=>0xA806, 'right'=>0xA806),
                     array('left'=>0xA80B, 'right'=>0xA80B),
                     array('left'=>0xA825, 'right'=>0xA826),
                     array('left'=>0xA8C4, 'right'=>0xA8C4),
                     array('left'=>0xA8E0, 'right'=>0xA8F1),
                     array('left'=>0xA926, 'right'=>0xA92D),
                     array('left'=>0xA947, 'right'=>0xA951),
                     array('left'=>0xA980, 'right'=>0xA982),
                     array('left'=>0xA9B3, 'right'=>0xA9B3),
                     array('left'=>0xA9B6, 'right'=>0xA9B9),
                     array('left'=>0xA9BC, 'right'=>0xA9BC),
                     array('left'=>0xAA29, 'right'=>0xAA2E),
                     array('left'=>0xAA31, 'right'=>0xAA32),
                     array('left'=>0xAA35, 'right'=>0xAA36),
                     array('left'=>0xAA43, 'right'=>0xAA43),
                     array('left'=>0xAA4C, 'right'=>0xAA4C),
                     array('left'=>0xAAB0, 'right'=>0xAAB0),
                     array('left'=>0xAAB2, 'right'=>0xAAB4),
                     array('left'=>0xAAB7, 'right'=>0xAAB8),
                     array('left'=>0xAABE, 'right'=>0xAABF),
                     array('left'=>0xAAC1, 'right'=>0xAAC1),
                     array('left'=>0xAAEC, 'right'=>0xAAED),
                     array('left'=>0xAAF6, 'right'=>0xAAF6),
                     array('left'=>0xABE5, 'right'=>0xABE5),
                     array('left'=>0xABE8, 'right'=>0xABE8),
                     array('left'=>0xABED, 'right'=>0xABED),
                     array('left'=>0xFB1E, 'right'=>0xFB1E),
                     array('left'=>0xFE00, 'right'=>0xFE0F),
                     array('left'=>0xFE20, 'right'=>0xFE26),
                     array('left'=>0x101FD, 'right'=>0x101FD),
                     array('left'=>0x10A01, 'right'=>0x10A03),
                     array('left'=>0x10A05, 'right'=>0x10A06),
                     array('left'=>0x10A0C, 'right'=>0x10A0F),
                     array('left'=>0x10A38, 'right'=>0x10A3A),
                     array('left'=>0x10A3F, 'right'=>0x10A3F),
                     array('left'=>0x11001, 'right'=>0x11001),
                     array('left'=>0x11038, 'right'=>0x11046),
                     array('left'=>0x11080, 'right'=>0x11081),
                     array('left'=>0x110B3, 'right'=>0x110B6),
                     array('left'=>0x110B9, 'right'=>0x110BA),
                     array('left'=>0x11100, 'right'=>0x11102),
                     array('left'=>0x11127, 'right'=>0x1112B),
                     array('left'=>0x1112D, 'right'=>0x11134),
                     array('left'=>0x11180, 'right'=>0x11181),
                     array('left'=>0x111B6, 'right'=>0x111BE),
                     array('left'=>0x116AB, 'right'=>0x116AB),
                     array('left'=>0x116AD, 'right'=>0x116AD),
                     array('left'=>0x116B0, 'right'=>0x116B5),
                     array('left'=>0x116B7, 'right'=>0x116B7),
                     array('left'=>0x16F8F, 'right'=>0x16F92),
                     array('left'=>0x1D167, 'right'=>0x1D169),
                     array('left'=>0x1D17B, 'right'=>0x1D182),
                     array('left'=>0x1D185, 'right'=>0x1D18B),
                     array('left'=>0x1D1AA, 'right'=>0x1D1AD),
                     array('left'=>0x1D242, 'right'=>0x1D244),
                     array('left'=>0xE0100, 'right'=>0xE01EF));
    }
    public static function M_ranges() {
        return array_merge(self::Mc_ranges(), self::Me_ranges(), self::Mn_ranges());
    }
    /******************************************************************/
    public static function Nd_ranges() {
        return array(array('left'=>0x0030, 'right'=>0x0039),
                     array('left'=>0x0660, 'right'=>0x0669),
                     array('left'=>0x06F0, 'right'=>0x06F9),
                     array('left'=>0x07C0, 'right'=>0x07C9),
                     array('left'=>0x0966, 'right'=>0x096F),
                     array('left'=>0x09E6, 'right'=>0x09EF),
                     array('left'=>0x0A66, 'right'=>0x0A6F),
                     array('left'=>0x0AE6, 'right'=>0x0AEF),
                     array('left'=>0x0B66, 'right'=>0x0B6F),
                     array('left'=>0x0BE6, 'right'=>0x0BEF),
                     array('left'=>0x0C66, 'right'=>0x0C6F),
                     array('left'=>0x0CE6, 'right'=>0x0CEF),
                     array('left'=>0x0D66, 'right'=>0x0D6F),
                     array('left'=>0x0E50, 'right'=>0x0E59),
                     array('left'=>0x0ED0, 'right'=>0x0ED9),
                     array('left'=>0x0F20, 'right'=>0x0F29),
                     array('left'=>0x1040, 'right'=>0x1049),
                     array('left'=>0x1090, 'right'=>0x1099),
                     array('left'=>0x17E0, 'right'=>0x17E9),
                     array('left'=>0x1810, 'right'=>0x1819),
                     array('left'=>0x1946, 'right'=>0x194F),
                     array('left'=>0x19D0, 'right'=>0x19D9),
                     array('left'=>0x1A80, 'right'=>0x1A89),
                     array('left'=>0x1A90, 'right'=>0x1A99),
                     array('left'=>0x1B50, 'right'=>0x1B59),
                     array('left'=>0x1BB0, 'right'=>0x1BB9),
                     array('left'=>0x1C40, 'right'=>0x1C49),
                     array('left'=>0x1C50, 'right'=>0x1C59),
                     array('left'=>0xA620, 'right'=>0xA629),
                     array('left'=>0xA8D0, 'right'=>0xA8D9),
                     array('left'=>0xA900, 'right'=>0xA909),
                     array('left'=>0xA9D0, 'right'=>0xA9D9),
                     array('left'=>0xAA50, 'right'=>0xAA59),
                     array('left'=>0xABF0, 'right'=>0xABF9),
                     array('left'=>0xFF10, 'right'=>0xFF19),
                     array('left'=>0x104A0, 'right'=>0x104A9),
                     array('left'=>0x11066, 'right'=>0x1106F),
                     array('left'=>0x110F0, 'right'=>0x110F9),
                     array('left'=>0x11136, 'right'=>0x1113F),
                     array('left'=>0x111D0, 'right'=>0x111D9),
                     array('left'=>0x116C0, 'right'=>0x116C9),
                     array('left'=>0x1D7CE, 'right'=>0x1D7FF));
    }
    public static function Nl_ranges() {
        return array(array('left'=>0x16EE, 'right'=>0x16F0),
                     array('left'=>0x2160, 'right'=>0x2182),
                     array('left'=>0x2185, 'right'=>0x2188),
                     array('left'=>0x3007, 'right'=>0x3007),
                     array('left'=>0x3021, 'right'=>0x3029),
                     array('left'=>0x3038, 'right'=>0x303A),
                     array('left'=>0xA6E6, 'right'=>0xA6EF),
                     array('left'=>0x10140, 'right'=>0x10174),
                     array('left'=>0x10341, 'right'=>0x10341),
                     array('left'=>0x1034A, 'right'=>0x1034A),
                     array('left'=>0x103D1, 'right'=>0x103D5),
                     array('left'=>0x12400, 'right'=>0x12462));
    }
    public static function No_ranges() {
        return array(array('left'=>0x00B2, 'right'=>0x00B3),
                     array('left'=>0x00B9, 'right'=>0x00B9),
                     array('left'=>0x00BC, 'right'=>0x00BE),
                     array('left'=>0x09F4, 'right'=>0x09F9),
                     array('left'=>0x0B72, 'right'=>0x0B77),
                     array('left'=>0x0BF0, 'right'=>0x0BF2),
                     array('left'=>0x0C78, 'right'=>0x0C7E),
                     array('left'=>0x0D70, 'right'=>0x0D75),
                     array('left'=>0x0F2A, 'right'=>0x0F33),
                     array('left'=>0x1369, 'right'=>0x137C),
                     array('left'=>0x17F0, 'right'=>0x17F9),
                     array('left'=>0x19DA, 'right'=>0x19DA),
                     array('left'=>0x2070, 'right'=>0x2070),
                     array('left'=>0x2074, 'right'=>0x2079),
                     array('left'=>0x2080, 'right'=>0x2089),
                     array('left'=>0x2150, 'right'=>0x215F),
                     array('left'=>0x2189, 'right'=>0x2189),
                     array('left'=>0x2460, 'right'=>0x249B),
                     array('left'=>0x24EA, 'right'=>0x24FF),
                     array('left'=>0x2776, 'right'=>0x2793),
                     array('left'=>0x2CFD, 'right'=>0x2CFD),
                     array('left'=>0x3192, 'right'=>0x3195),
                     array('left'=>0x3220, 'right'=>0x3229),
                     array('left'=>0x3248, 'right'=>0x324F),
                     array('left'=>0x3251, 'right'=>0x325F),
                     array('left'=>0x3280, 'right'=>0x3289),
                     array('left'=>0x32B1, 'right'=>0x32BF),
                     array('left'=>0xA830, 'right'=>0xA835),
                     array('left'=>0x10107, 'right'=>0x10133),
                     array('left'=>0x10175, 'right'=>0x10178),
                     array('left'=>0x1018A, 'right'=>0x1018A),
                     array('left'=>0x10320, 'right'=>0x10323),
                     array('left'=>0x10858, 'right'=>0x1085F),
                     array('left'=>0x10916, 'right'=>0x1091B),
                     array('left'=>0x10A40, 'right'=>0x10A47),
                     array('left'=>0x10A7D, 'right'=>0x10A7E),
                     array('left'=>0x10B58, 'right'=>0x10B5F),
                     array('left'=>0x10B78, 'right'=>0x10B7F),
                     array('left'=>0x10E60, 'right'=>0x10E7E),
                     array('left'=>0x11052, 'right'=>0x11065),
                     array('left'=>0x1D360, 'right'=>0x1D371),
                     array('left'=>0x1F100, 'right'=>0x1F10A));
    }
    public static function N_ranges() {
        return array_merge(self::Nd_ranges(), self::Nl_ranges(), self::No_ranges());
    }
    /******************************************************************/
    public static function Pc_ranges() {
        return array(array('left'=>0x005F, 'right'=>0x005F),
                     array('left'=>0x203F, 'right'=>0x2040),
                     array('left'=>0x2054, 'right'=>0x2054),
                     array('left'=>0xFE33, 'right'=>0xFE34),
                     array('left'=>0xFE4D, 'right'=>0xFE4F),
                     array('left'=>0xFF3F, 'right'=>0xFF3F));
    }
    public static function Pd_ranges() {
        return array(array('left'=>0x002D, 'right'=>0x002D),
                     array('left'=>0x058A, 'right'=>0x058A),
                     array('left'=>0x05BE, 'right'=>0x05BE),
                     array('left'=>0x1400, 'right'=>0x1400),
                     array('left'=>0x1806, 'right'=>0x1806),
                     array('left'=>0x2010, 'right'=>0x2015),
                     array('left'=>0x2E17, 'right'=>0x2E17),
                     array('left'=>0x2E1A, 'right'=>0x2E1A),
                     array('left'=>0x2E3A, 'right'=>0x2E3B),
                     array('left'=>0x301C, 'right'=>0x301C),
                     array('left'=>0x3030, 'right'=>0x3030),
                     array('left'=>0x30A0, 'right'=>0x30A0),
                     array('left'=>0xFE31, 'right'=>0xFE32),
                     array('left'=>0xFE58, 'right'=>0xFE58),
                     array('left'=>0xFE63, 'right'=>0xFE63),
                     array('left'=>0xFF0D, 'right'=>0xFF0D));
    }
    public static function Pe_ranges() {
        return array(array('left'=>0x0029, 'right'=>0x0029),
                     array('left'=>0x005D, 'right'=>0x005D),
                     array('left'=>0x007D, 'right'=>0x007D),
                     array('left'=>0x0F3B, 'right'=>0x0F3B),
                     array('left'=>0x0F3D, 'right'=>0x0F3D),
                     array('left'=>0x169C, 'right'=>0x169C),
                     array('left'=>0x2046, 'right'=>0x2046),
                     array('left'=>0x207E, 'right'=>0x207E),
                     array('left'=>0x208E, 'right'=>0x208E),
                     array('left'=>0x232A, 'right'=>0x232A),
                     array('left'=>0x2769, 'right'=>0x2769),
                     array('left'=>0x276B, 'right'=>0x276B),
                     array('left'=>0x276D, 'right'=>0x276D),
                     array('left'=>0x276F, 'right'=>0x276F),
                     array('left'=>0x2771, 'right'=>0x2771),
                     array('left'=>0x2773, 'right'=>0x2773),
                     array('left'=>0x2775, 'right'=>0x2775),
                     array('left'=>0x27C6, 'right'=>0x27C6),
                     array('left'=>0x27E7, 'right'=>0x27E7),
                     array('left'=>0x27E9, 'right'=>0x27E9),
                     array('left'=>0x27EB, 'right'=>0x27EB),
                     array('left'=>0x27ED, 'right'=>0x27ED),
                     array('left'=>0x27EF, 'right'=>0x27EF),
                     array('left'=>0x2984, 'right'=>0x2984),
                     array('left'=>0x2986, 'right'=>0x2986),
                     array('left'=>0x2988, 'right'=>0x2988),
                     array('left'=>0x298A, 'right'=>0x298A),
                     array('left'=>0x298C, 'right'=>0x298C),
                     array('left'=>0x298E, 'right'=>0x298E),
                     array('left'=>0x2990, 'right'=>0x2990),
                     array('left'=>0x2992, 'right'=>0x2992),
                     array('left'=>0x2994, 'right'=>0x2994),
                     array('left'=>0x2996, 'right'=>0x2996),
                     array('left'=>0x2998, 'right'=>0x2998),
                     array('left'=>0x29D9, 'right'=>0x29D9),
                     array('left'=>0x29DB, 'right'=>0x29DB),
                     array('left'=>0x29FD, 'right'=>0x29FD),
                     array('left'=>0x2E23, 'right'=>0x2E23),
                     array('left'=>0x2E25, 'right'=>0x2E25),
                     array('left'=>0x2E27, 'right'=>0x2E27),
                     array('left'=>0x2E29, 'right'=>0x2E29),
                     array('left'=>0x3009, 'right'=>0x3009),
                     array('left'=>0x300B, 'right'=>0x300B),
                     array('left'=>0x300D, 'right'=>0x300D),
                     array('left'=>0x300F, 'right'=>0x300F),
                     array('left'=>0x3011, 'right'=>0x3011),
                     array('left'=>0x3015, 'right'=>0x3015),
                     array('left'=>0x3017, 'right'=>0x3017),
                     array('left'=>0x3019, 'right'=>0x3019),
                     array('left'=>0x301B, 'right'=>0x301B),
                     array('left'=>0x301E, 'right'=>0x301F),
                     array('left'=>0xFD3F, 'right'=>0xFD3F),
                     array('left'=>0xFE18, 'right'=>0xFE18),
                     array('left'=>0xFE36, 'right'=>0xFE36),
                     array('left'=>0xFE38, 'right'=>0xFE38),
                     array('left'=>0xFE3A, 'right'=>0xFE3A),
                     array('left'=>0xFE3C, 'right'=>0xFE3C),
                     array('left'=>0xFE3E, 'right'=>0xFE3E),
                     array('left'=>0xFE40, 'right'=>0xFE40),
                     array('left'=>0xFE42, 'right'=>0xFE42),
                     array('left'=>0xFE44, 'right'=>0xFE44),
                     array('left'=>0xFE48, 'right'=>0xFE48),
                     array('left'=>0xFE5A, 'right'=>0xFE5A),
                     array('left'=>0xFE5C, 'right'=>0xFE5C),
                     array('left'=>0xFE5E, 'right'=>0xFE5E),
                     array('left'=>0xFF09, 'right'=>0xFF09),
                     array('left'=>0xFF3D, 'right'=>0xFF3D),
                     array('left'=>0xFF5D, 'right'=>0xFF5D),
                     array('left'=>0xFF60, 'right'=>0xFF60),
                     array('left'=>0xFF63, 'right'=>0xFF63));
    }
    public static function Pf_ranges() {
        return array(array('left'=>0x00BB, 'right'=>0x00BB),
                     array('left'=>0x2019, 'right'=>0x2019),
                     array('left'=>0x201D, 'right'=>0x201D),
                     array('left'=>0x203A, 'right'=>0x203A),
                     array('left'=>0x2E03, 'right'=>0x2E03),
                     array('left'=>0x2E05, 'right'=>0x2E05),
                     array('left'=>0x2E0A, 'right'=>0x2E0A),
                     array('left'=>0x2E0D, 'right'=>0x2E0D),
                     array('left'=>0x2E1D, 'right'=>0x2E1D),
                     array('left'=>0x2E21, 'right'=>0x2E21));
    }
    public static function Pi_ranges() {
        return array(array('left'=>0x00AB, 'right'=>0x00AB),
                     array('left'=>0x2018, 'right'=>0x2018),
                     array('left'=>0x201B, 'right'=>0x201C),
                     array('left'=>0x201F, 'right'=>0x201F),
                     array('left'=>0x2039, 'right'=>0x2039),
                     array('left'=>0x2E02, 'right'=>0x2E02),
                     array('left'=>0x2E04, 'right'=>0x2E04),
                     array('left'=>0x2E09, 'right'=>0x2E09),
                     array('left'=>0x2E0C, 'right'=>0x2E0C),
                     array('left'=>0x2E1C, 'right'=>0x2E1C),
                     array('left'=>0x2E20, 'right'=>0x2E20));
    }
    public static function Po_ranges() {
        return array(array('left'=>0x0021, 'right'=>0x0023),
                     array('left'=>0x0025, 'right'=>0x0027),
                     array('left'=>0x002A, 'right'=>0x002A),
                     array('left'=>0x002C, 'right'=>0x002C),
                     array('left'=>0x002E, 'right'=>0x002F),
                     array('left'=>0x003A, 'right'=>0x003B),
                     array('left'=>0x003F, 'right'=>0x0040),
                     array('left'=>0x005C, 'right'=>0x005C),
                     array('left'=>0x00A1, 'right'=>0x00A1),
                     array('left'=>0x00A7, 'right'=>0x00A7),
                     array('left'=>0x00B6, 'right'=>0x00B7),
                     array('left'=>0x00BF, 'right'=>0x00BF),
                     array('left'=>0x037E, 'right'=>0x037E),
                     array('left'=>0x0387, 'right'=>0x0387),
                     array('left'=>0x055A, 'right'=>0x055F),
                     array('left'=>0x0589, 'right'=>0x0589),
                     array('left'=>0x05C0, 'right'=>0x05C0),
                     array('left'=>0x05C3, 'right'=>0x05C3),
                     array('left'=>0x05C6, 'right'=>0x05C6),
                     array('left'=>0x05F3, 'right'=>0x05F4),
                     array('left'=>0x0609, 'right'=>0x060A),
                     array('left'=>0x060C, 'right'=>0x060D),
                     array('left'=>0x061B, 'right'=>0x061B),
                     array('left'=>0x061E, 'right'=>0x061F),
                     array('left'=>0x066A, 'right'=>0x066D),
                     array('left'=>0x06D4, 'right'=>0x06D4),
                     array('left'=>0x0700, 'right'=>0x070D),
                     array('left'=>0x07F7, 'right'=>0x07F9),
                     array('left'=>0x0830, 'right'=>0x083E),
                     array('left'=>0x085E, 'right'=>0x085E),
                     array('left'=>0x0964, 'right'=>0x0965),
                     array('left'=>0x0970, 'right'=>0x0970),
                     array('left'=>0x0AF0, 'right'=>0x0AF0),
                     array('left'=>0x0DF4, 'right'=>0x0DF4),
                     array('left'=>0x0E4F, 'right'=>0x0E4F),
                     array('left'=>0x0E5A, 'right'=>0x0E5B),
                     array('left'=>0x0F04, 'right'=>0x0F12),
                     array('left'=>0x0F14, 'right'=>0x0F14),
                     array('left'=>0x0F85, 'right'=>0x0F85),
                     array('left'=>0x0FD0, 'right'=>0x0FD4),
                     array('left'=>0x0FD9, 'right'=>0x0FDA),
                     array('left'=>0x104A, 'right'=>0x104F),
                     array('left'=>0x10FB, 'right'=>0x10FB),
                     array('left'=>0x1360, 'right'=>0x1368),
                     array('left'=>0x166D, 'right'=>0x166E),
                     array('left'=>0x16EB, 'right'=>0x16ED),
                     array('left'=>0x1735, 'right'=>0x1736),
                     array('left'=>0x17D4, 'right'=>0x17D6),
                     array('left'=>0x17D8, 'right'=>0x17DA),
                     array('left'=>0x1800, 'right'=>0x1805),
                     array('left'=>0x1807, 'right'=>0x180A),
                     array('left'=>0x1944, 'right'=>0x1945),
                     array('left'=>0x1A1E, 'right'=>0x1A1F),
                     array('left'=>0x1AA0, 'right'=>0x1AA6),
                     array('left'=>0x1AA8, 'right'=>0x1AAD),
                     array('left'=>0x1B5A, 'right'=>0x1B60),
                     array('left'=>0x1BFC, 'right'=>0x1BFF),
                     array('left'=>0x1C3B, 'right'=>0x1C3F),
                     array('left'=>0x1C7E, 'right'=>0x1C7F),
                     array('left'=>0x1CC0, 'right'=>0x1CC7),
                     array('left'=>0x1CD3, 'right'=>0x1CD3),
                     array('left'=>0x2016, 'right'=>0x2017),
                     array('left'=>0x2020, 'right'=>0x2027),
                     array('left'=>0x2030, 'right'=>0x2038),
                     array('left'=>0x203B, 'right'=>0x203E),
                     array('left'=>0x2041, 'right'=>0x2043),
                     array('left'=>0x2047, 'right'=>0x2051),
                     array('left'=>0x2053, 'right'=>0x2053),
                     array('left'=>0x2055, 'right'=>0x205E),
                     array('left'=>0x2CF9, 'right'=>0x2CFC),
                     array('left'=>0x2CFE, 'right'=>0x2CFF),
                     array('left'=>0x2D70, 'right'=>0x2D70),
                     array('left'=>0x2E00, 'right'=>0x2E01),
                     array('left'=>0x2E06, 'right'=>0x2E08),
                     array('left'=>0x2E0B, 'right'=>0x2E0B),
                     array('left'=>0x2E0E, 'right'=>0x2E16),
                     array('left'=>0x2E18, 'right'=>0x2E19),
                     array('left'=>0x2E1B, 'right'=>0x2E1B),
                     array('left'=>0x2E1E, 'right'=>0x2E1F),
                     array('left'=>0x2E2A, 'right'=>0x2E2E),
                     array('left'=>0x2E30, 'right'=>0x2E39),
                     array('left'=>0x3001, 'right'=>0x3003),
                     array('left'=>0x303D, 'right'=>0x303D),
                     array('left'=>0x30FB, 'right'=>0x30FB),
                     array('left'=>0xA4FE, 'right'=>0xA4FF),
                     array('left'=>0xA60D, 'right'=>0xA60F),
                     array('left'=>0xA673, 'right'=>0xA673),
                     array('left'=>0xA67E, 'right'=>0xA67E),
                     array('left'=>0xA6F2, 'right'=>0xA6F7),
                     array('left'=>0xA874, 'right'=>0xA877),
                     array('left'=>0xA8CE, 'right'=>0xA8CF),
                     array('left'=>0xA8F8, 'right'=>0xA8FA),
                     array('left'=>0xA92E, 'right'=>0xA92F),
                     array('left'=>0xA95F, 'right'=>0xA95F),
                     array('left'=>0xA9C1, 'right'=>0xA9CD),
                     array('left'=>0xA9DE, 'right'=>0xA9DF),
                     array('left'=>0xAA5C, 'right'=>0xAA5F),
                     array('left'=>0xAADE, 'right'=>0xAADF),
                     array('left'=>0xAAF0, 'right'=>0xAAF1),
                     array('left'=>0xABEB, 'right'=>0xABEB),
                     array('left'=>0xFE10, 'right'=>0xFE16),
                     array('left'=>0xFE19, 'right'=>0xFE19),
                     array('left'=>0xFE30, 'right'=>0xFE30),
                     array('left'=>0xFE45, 'right'=>0xFE46),
                     array('left'=>0xFE49, 'right'=>0xFE4C),
                     array('left'=>0xFE50, 'right'=>0xFE52),
                     array('left'=>0xFE54, 'right'=>0xFE57),
                     array('left'=>0xFE5F, 'right'=>0xFE61),
                     array('left'=>0xFE68, 'right'=>0xFE68),
                     array('left'=>0xFE6A, 'right'=>0xFE6B),
                     array('left'=>0xFF01, 'right'=>0xFF03),
                     array('left'=>0xFF05, 'right'=>0xFF07),
                     array('left'=>0xFF0A, 'right'=>0xFF0A),
                     array('left'=>0xFF0C, 'right'=>0xFF0C),
                     array('left'=>0xFF0E, 'right'=>0xFF0F),
                     array('left'=>0xFF1A, 'right'=>0xFF1B),
                     array('left'=>0xFF1F, 'right'=>0xFF20),
                     array('left'=>0xFF3C, 'right'=>0xFF3C),
                     array('left'=>0xFF61, 'right'=>0xFF61),
                     array('left'=>0xFF64, 'right'=>0xFF65),
                     array('left'=>0x10100, 'right'=>0x10102),
                     array('left'=>0x1039F, 'right'=>0x1039F),
                     array('left'=>0x103D0, 'right'=>0x103D0),
                     array('left'=>0x10857, 'right'=>0x10857),
                     array('left'=>0x1091F, 'right'=>0x1091F),
                     array('left'=>0x1093F, 'right'=>0x1093F),
                     array('left'=>0x10A50, 'right'=>0x10A58),
                     array('left'=>0x10A7F, 'right'=>0x10A7F),
                     array('left'=>0x10B39, 'right'=>0x10B3F),
                     array('left'=>0x11047, 'right'=>0x1104D),
                     array('left'=>0x110BB, 'right'=>0x110BC),
                     array('left'=>0x110BE, 'right'=>0x110C1),
                     array('left'=>0x11140, 'right'=>0x11143),
                     array('left'=>0x111C5, 'right'=>0x111C8),
                     array('left'=>0x12470, 'right'=>0x12473));
    }
    public static function Ps_ranges() {
        return array(array('left'=>0x0028, 'right'=>0x0028),
                     array('left'=>0x005B, 'right'=>0x005B),
                     array('left'=>0x007B, 'right'=>0x007B),
                     array('left'=>0x0F3A, 'right'=>0x0F3A),
                     array('left'=>0x0F3C, 'right'=>0x0F3C),
                     array('left'=>0x169B, 'right'=>0x169B),
                     array('left'=>0x201A, 'right'=>0x201A),
                     array('left'=>0x201E, 'right'=>0x201E),
                     array('left'=>0x2045, 'right'=>0x2045),
                     array('left'=>0x207D, 'right'=>0x207D),
                     array('left'=>0x208D, 'right'=>0x208D),
                     array('left'=>0x2329, 'right'=>0x2329),
                     array('left'=>0x2768, 'right'=>0x2768),
                     array('left'=>0x276A, 'right'=>0x276A),
                     array('left'=>0x276C, 'right'=>0x276C),
                     array('left'=>0x276E, 'right'=>0x276E),
                     array('left'=>0x2770, 'right'=>0x2770),
                     array('left'=>0x2772, 'right'=>0x2772),
                     array('left'=>0x2774, 'right'=>0x2774),
                     array('left'=>0x27C5, 'right'=>0x27C5),
                     array('left'=>0x27E6, 'right'=>0x27E6),
                     array('left'=>0x27E8, 'right'=>0x27E8),
                     array('left'=>0x27EA, 'right'=>0x27EA),
                     array('left'=>0x27EC, 'right'=>0x27EC),
                     array('left'=>0x27EE, 'right'=>0x27EE),
                     array('left'=>0x2983, 'right'=>0x2983),
                     array('left'=>0x2985, 'right'=>0x2985),
                     array('left'=>0x2987, 'right'=>0x2987),
                     array('left'=>0x2989, 'right'=>0x2989),
                     array('left'=>0x298B, 'right'=>0x298B),
                     array('left'=>0x298D, 'right'=>0x298D),
                     array('left'=>0x298F, 'right'=>0x298F),
                     array('left'=>0x2991, 'right'=>0x2991),
                     array('left'=>0x2993, 'right'=>0x2993),
                     array('left'=>0x2995, 'right'=>0x2995),
                     array('left'=>0x2997, 'right'=>0x2997),
                     array('left'=>0x29D8, 'right'=>0x29D8),
                     array('left'=>0x29DA, 'right'=>0x29DA),
                     array('left'=>0x29FC, 'right'=>0x29FC),
                     array('left'=>0x2E22, 'right'=>0x2E22),
                     array('left'=>0x2E24, 'right'=>0x2E24),
                     array('left'=>0x2E26, 'right'=>0x2E26),
                     array('left'=>0x2E28, 'right'=>0x2E28),
                     array('left'=>0x3008, 'right'=>0x3008),
                     array('left'=>0x300A, 'right'=>0x300A),
                     array('left'=>0x300C, 'right'=>0x300C),
                     array('left'=>0x300E, 'right'=>0x300E),
                     array('left'=>0x3010, 'right'=>0x3010),
                     array('left'=>0x3014, 'right'=>0x3014),
                     array('left'=>0x3016, 'right'=>0x3016),
                     array('left'=>0x3018, 'right'=>0x3018),
                     array('left'=>0x301A, 'right'=>0x301A),
                     array('left'=>0x301D, 'right'=>0x301D),
                     array('left'=>0xFD3E, 'right'=>0xFD3E),
                     array('left'=>0xFE17, 'right'=>0xFE17),
                     array('left'=>0xFE35, 'right'=>0xFE35),
                     array('left'=>0xFE37, 'right'=>0xFE37),
                     array('left'=>0xFE39, 'right'=>0xFE39),
                     array('left'=>0xFE3B, 'right'=>0xFE3B),
                     array('left'=>0xFE3D, 'right'=>0xFE3D),
                     array('left'=>0xFE3F, 'right'=>0xFE3F),
                     array('left'=>0xFE41, 'right'=>0xFE41),
                     array('left'=>0xFE43, 'right'=>0xFE43),
                     array('left'=>0xFE47, 'right'=>0xFE47),
                     array('left'=>0xFE59, 'right'=>0xFE59),
                     array('left'=>0xFE5B, 'right'=>0xFE5B),
                     array('left'=>0xFE5D, 'right'=>0xFE5D),
                     array('left'=>0xFF08, 'right'=>0xFF08),
                     array('left'=>0xFF3B, 'right'=>0xFF3B),
                     array('left'=>0xFF5B, 'right'=>0xFF5B),
                     array('left'=>0xFF5F, 'right'=>0xFF5F),
                     array('left'=>0xFF62, 'right'=>0xFF62));
    }
    public static function P_ranges() {
        return array_merge(self::Pc_ranges(), self::Pd_ranges(), self::Pe_ranges(), self::Pf_ranges(),
                           self::Pi_ranges(), self::Po_ranges(), self::Ps_ranges());
    }
    /******************************************************************/
    public static function Sc_ranges() {
        return array(array('left'=>0x0024, 'right'=>0x0024),
                     array('left'=>0x00A2, 'right'=>0x00A5),
                     array('left'=>0x058F, 'right'=>0x058F),
                     array('left'=>0x060B, 'right'=>0x060B),
                     array('left'=>0x09F2, 'right'=>0x09F3),
                     array('left'=>0x09FB, 'right'=>0x09FB),
                     array('left'=>0x0AF1, 'right'=>0x0AF1),
                     array('left'=>0x0BF9, 'right'=>0x0BF9),
                     array('left'=>0x0E3F, 'right'=>0x0E3F),
                     array('left'=>0x17DB, 'right'=>0x17DB),
                     array('left'=>0x20A0, 'right'=>0x20B9),
                     array('left'=>0xA838, 'right'=>0xA838),
                     array('left'=>0xFDFC, 'right'=>0xFDFC),
                     array('left'=>0xFE69, 'right'=>0xFE69),
                     array('left'=>0xFF04, 'right'=>0xFF04),
                     array('left'=>0xFFE0, 'right'=>0xFFE1),
                     array('left'=>0xFFE5, 'right'=>0xFFE6));
    }
    public static function Sk_ranges() {
        return array(array('left'=>0x005E, 'right'=>0x005E),
                     array('left'=>0x0060, 'right'=>0x0060),
                     array('left'=>0x00A8, 'right'=>0x00A8),
                     array('left'=>0x00AF, 'right'=>0x00AF),
                     array('left'=>0x00B4, 'right'=>0x00B4),
                     array('left'=>0x00B8, 'right'=>0x00B8),
                     array('left'=>0x02C2, 'right'=>0x02C5),
                     array('left'=>0x02D2, 'right'=>0x02DF),
                     array('left'=>0x02E5, 'right'=>0x02EB),
                     array('left'=>0x02ED, 'right'=>0x02ED),
                     array('left'=>0x02EF, 'right'=>0x02FF),
                     array('left'=>0x0375, 'right'=>0x0375),
                     array('left'=>0x0384, 'right'=>0x0385),
                     array('left'=>0x1FBD, 'right'=>0x1FBD),
                     array('left'=>0x1FBF, 'right'=>0x1FC1),
                     array('left'=>0x1FCD, 'right'=>0x1FCF),
                     array('left'=>0x1FDD, 'right'=>0x1FDF),
                     array('left'=>0x1FED, 'right'=>0x1FEF),
                     array('left'=>0x1FFD, 'right'=>0x1FFE),
                     array('left'=>0x309B, 'right'=>0x309C),
                     array('left'=>0xA700, 'right'=>0xA716),
                     array('left'=>0xA720, 'right'=>0xA721),
                     array('left'=>0xA789, 'right'=>0xA78A),
                     array('left'=>0xFBB2, 'right'=>0xFBC1),
                     array('left'=>0xFF3E, 'right'=>0xFF3E),
                     array('left'=>0xFF40, 'right'=>0xFF40),
                     array('left'=>0xFFE3, 'right'=>0xFFE3));
    }
    public static function Sm_ranges() {
        return array(array('left'=>0x002B, 'right'=>0x002B),
                     array('left'=>0x003C, 'right'=>0x003E),
                     array('left'=>0x007C, 'right'=>0x007C),
                     array('left'=>0x007E, 'right'=>0x007E),
                     array('left'=>0x00AC, 'right'=>0x00AC),
                     array('left'=>0x00B1, 'right'=>0x00B1),
                     array('left'=>0x00D7, 'right'=>0x00D7),
                     array('left'=>0x00F7, 'right'=>0x00F7),
                     array('left'=>0x03F6, 'right'=>0x03F6),
                     array('left'=>0x0606, 'right'=>0x0608),
                     array('left'=>0x2044, 'right'=>0x2044),
                     array('left'=>0x2052, 'right'=>0x2052),
                     array('left'=>0x207A, 'right'=>0x207C),
                     array('left'=>0x208A, 'right'=>0x208C),
                     array('left'=>0x2118, 'right'=>0x2118),
                     array('left'=>0x2140, 'right'=>0x2144),
                     array('left'=>0x214B, 'right'=>0x214B),
                     array('left'=>0x2190, 'right'=>0x2194),
                     array('left'=>0x219A, 'right'=>0x219B),
                     array('left'=>0x21A0, 'right'=>0x21A0),
                     array('left'=>0x21A3, 'right'=>0x21A3),
                     array('left'=>0x21A6, 'right'=>0x21A6),
                     array('left'=>0x21AE, 'right'=>0x21AE),
                     array('left'=>0x21CE, 'right'=>0x21CF),
                     array('left'=>0x21D2, 'right'=>0x21D2),
                     array('left'=>0x21D4, 'right'=>0x21D4),
                     array('left'=>0x21F4, 'right'=>0x22FF),
                     array('left'=>0x2308, 'right'=>0x230B),
                     array('left'=>0x2320, 'right'=>0x2321),
                     array('left'=>0x237C, 'right'=>0x237C),
                     array('left'=>0x239B, 'right'=>0x23B3),
                     array('left'=>0x23DC, 'right'=>0x23E1),
                     array('left'=>0x25B7, 'right'=>0x25B7),
                     array('left'=>0x25C1, 'right'=>0x25C1),
                     array('left'=>0x25F8, 'right'=>0x25FF),
                     array('left'=>0x266F, 'right'=>0x266F),
                     array('left'=>0x27C0, 'right'=>0x27C4),
                     array('left'=>0x27C7, 'right'=>0x27E5),
                     array('left'=>0x27F0, 'right'=>0x27FF),
                     array('left'=>0x2900, 'right'=>0x2982),
                     array('left'=>0x2999, 'right'=>0x29D7),
                     array('left'=>0x29DC, 'right'=>0x29FB),
                     array('left'=>0x29FE, 'right'=>0x2AFF),
                     array('left'=>0x2B30, 'right'=>0x2B44),
                     array('left'=>0x2B47, 'right'=>0x2B4C),
                     array('left'=>0xFB29, 'right'=>0xFB29),
                     array('left'=>0xFE62, 'right'=>0xFE62),
                     array('left'=>0xFE64, 'right'=>0xFE66),
                     array('left'=>0xFF0B, 'right'=>0xFF0B),
                     array('left'=>0xFF1C, 'right'=>0xFF1E),
                     array('left'=>0xFF5C, 'right'=>0xFF5C),
                     array('left'=>0xFF5E, 'right'=>0xFF5E),
                     array('left'=>0xFFE2, 'right'=>0xFFE2),
                     array('left'=>0xFFE9, 'right'=>0xFFEC),
                     array('left'=>0x1D6C1, 'right'=>0x1D6C1),
                     array('left'=>0x1D6DB, 'right'=>0x1D6DB),
                     array('left'=>0x1D6FB, 'right'=>0x1D6FB),
                     array('left'=>0x1D715, 'right'=>0x1D715),
                     array('left'=>0x1D735, 'right'=>0x1D735),
                     array('left'=>0x1D74F, 'right'=>0x1D74F),
                     array('left'=>0x1D76F, 'right'=>0x1D76F),
                     array('left'=>0x1D789, 'right'=>0x1D789),
                     array('left'=>0x1D7A9, 'right'=>0x1D7A9),
                     array('left'=>0x1D7C3, 'right'=>0x1D7C3),
                     array('left'=>0x1EEF0, 'right'=>0x1EEF1));
    }
    public static function So_ranges() {
        return array(array('left'=>0x00A6, 'right'=>0x00A6),
                     array('left'=>0x00A9, 'right'=>0x00A9),
                     array('left'=>0x00AE, 'right'=>0x00AE),
                     array('left'=>0x00B0, 'right'=>0x00B0),
                     array('left'=>0x0482, 'right'=>0x0482),
                     array('left'=>0x060E, 'right'=>0x060F),
                     array('left'=>0x06DE, 'right'=>0x06DE),
                     array('left'=>0x06E9, 'right'=>0x06E9),
                     array('left'=>0x06FD, 'right'=>0x06FE),
                     array('left'=>0x07F6, 'right'=>0x07F6),
                     array('left'=>0x09FA, 'right'=>0x09FA),
                     array('left'=>0x0B70, 'right'=>0x0B70),
                     array('left'=>0x0BF3, 'right'=>0x0BF8),
                     array('left'=>0x0BFA, 'right'=>0x0BFA),
                     array('left'=>0x0C7F, 'right'=>0x0C7F),
                     array('left'=>0x0D79, 'right'=>0x0D79),
                     array('left'=>0x0F01, 'right'=>0x0F03),
                     array('left'=>0x0F13, 'right'=>0x0F13),
                     array('left'=>0x0F15, 'right'=>0x0F17),
                     array('left'=>0x0F1A, 'right'=>0x0F1F),
                     array('left'=>0x0F34, 'right'=>0x0F34),
                     array('left'=>0x0F36, 'right'=>0x0F36),
                     array('left'=>0x0F38, 'right'=>0x0F38),
                     array('left'=>0x0FBE, 'right'=>0x0FC5),
                     array('left'=>0x0FC7, 'right'=>0x0FCC),
                     array('left'=>0x0FCE, 'right'=>0x0FCF),
                     array('left'=>0x0FD5, 'right'=>0x0FD8),
                     array('left'=>0x109E, 'right'=>0x109F),
                     array('left'=>0x1390, 'right'=>0x1399),
                     array('left'=>0x1940, 'right'=>0x1940),
                     array('left'=>0x19DE, 'right'=>0x19FF),
                     array('left'=>0x1B61, 'right'=>0x1B6A),
                     array('left'=>0x1B74, 'right'=>0x1B7C),
                     array('left'=>0x2100, 'right'=>0x2101),
                     array('left'=>0x2103, 'right'=>0x2106),
                     array('left'=>0x2108, 'right'=>0x2109),
                     array('left'=>0x2114, 'right'=>0x2114),
                     array('left'=>0x2116, 'right'=>0x2117),
                     array('left'=>0x211E, 'right'=>0x2123),
                     array('left'=>0x2125, 'right'=>0x2125),
                     array('left'=>0x2127, 'right'=>0x2127),
                     array('left'=>0x2129, 'right'=>0x2129),
                     array('left'=>0x212E, 'right'=>0x212E),
                     array('left'=>0x213A, 'right'=>0x213B),
                     array('left'=>0x214A, 'right'=>0x214A),
                     array('left'=>0x214C, 'right'=>0x214D),
                     array('left'=>0x214F, 'right'=>0x214F),
                     array('left'=>0x2195, 'right'=>0x2199),
                     array('left'=>0x219C, 'right'=>0x219F),
                     array('left'=>0x21A1, 'right'=>0x21A2),
                     array('left'=>0x21A4, 'right'=>0x21A5),
                     array('left'=>0x21A7, 'right'=>0x21AD),
                     array('left'=>0x21AF, 'right'=>0x21CD),
                     array('left'=>0x21D0, 'right'=>0x21D1),
                     array('left'=>0x21D3, 'right'=>0x21D3),
                     array('left'=>0x21D5, 'right'=>0x21F3),
                     array('left'=>0x2300, 'right'=>0x2307),
                     array('left'=>0x230C, 'right'=>0x231F),
                     array('left'=>0x2322, 'right'=>0x2328),
                     array('left'=>0x232B, 'right'=>0x237B),
                     array('left'=>0x237D, 'right'=>0x239A),
                     array('left'=>0x23B4, 'right'=>0x23DB),
                     array('left'=>0x23E2, 'right'=>0x23F3),
                     array('left'=>0x2400, 'right'=>0x2426),
                     array('left'=>0x2440, 'right'=>0x244A),
                     array('left'=>0x249C, 'right'=>0x24E9),
                     array('left'=>0x2500, 'right'=>0x25B6),
                     array('left'=>0x25B8, 'right'=>0x25C0),
                     array('left'=>0x25C2, 'right'=>0x25F7),
                     array('left'=>0x2600, 'right'=>0x266E),
                     array('left'=>0x2670, 'right'=>0x26FF),
                     array('left'=>0x2701, 'right'=>0x2767),
                     array('left'=>0x2794, 'right'=>0x27BF),
                     array('left'=>0x2800, 'right'=>0x28FF),
                     array('left'=>0x2B00, 'right'=>0x2B2F),
                     array('left'=>0x2B45, 'right'=>0x2B46),
                     array('left'=>0x2B50, 'right'=>0x2B59),
                     array('left'=>0x2CE5, 'right'=>0x2CEA),
                     array('left'=>0x2E80, 'right'=>0x2E99),
                     array('left'=>0x2E9B, 'right'=>0x2EF3),
                     array('left'=>0x2F00, 'right'=>0x2FD5),
                     array('left'=>0x2FF0, 'right'=>0x2FFB),
                     array('left'=>0x3004, 'right'=>0x3004),
                     array('left'=>0x3012, 'right'=>0x3013),
                     array('left'=>0x3020, 'right'=>0x3020),
                     array('left'=>0x3036, 'right'=>0x3037),
                     array('left'=>0x303E, 'right'=>0x303F),
                     array('left'=>0x3190, 'right'=>0x3191),
                     array('left'=>0x3196, 'right'=>0x319F),
                     array('left'=>0x31C0, 'right'=>0x31E3),
                     array('left'=>0x3200, 'right'=>0x321E),
                     array('left'=>0x322A, 'right'=>0x3247),
                     array('left'=>0x3250, 'right'=>0x3250),
                     array('left'=>0x3260, 'right'=>0x327F),
                     array('left'=>0x328A, 'right'=>0x32B0),
                     array('left'=>0x32C0, 'right'=>0x32FE),
                     array('left'=>0x3300, 'right'=>0x33FF),
                     array('left'=>0x4DC0, 'right'=>0x4DFF),
                     array('left'=>0xA490, 'right'=>0xA4C6),
                     array('left'=>0xA828, 'right'=>0xA82B),
                     array('left'=>0xA836, 'right'=>0xA837),
                     array('left'=>0xA839, 'right'=>0xA839),
                     array('left'=>0xAA77, 'right'=>0xAA79),
                     array('left'=>0xFDFD, 'right'=>0xFDFD),
                     array('left'=>0xFFE4, 'right'=>0xFFE4),
                     array('left'=>0xFFE8, 'right'=>0xFFE8),
                     array('left'=>0xFFED, 'right'=>0xFFEE),
                     array('left'=>0xFFFC, 'right'=>0xFFFD),
                     array('left'=>0x10137, 'right'=>0x1013F),
                     array('left'=>0x10179, 'right'=>0x10189),
                     array('left'=>0x10190, 'right'=>0x1019B),
                     array('left'=>0x101D0, 'right'=>0x101FC),
                     array('left'=>0x1D000, 'right'=>0x1D0F5),
                     array('left'=>0x1D100, 'right'=>0x1D126),
                     array('left'=>0x1D129, 'right'=>0x1D164),
                     array('left'=>0x1D16A, 'right'=>0x1D16C),
                     array('left'=>0x1D183, 'right'=>0x1D184),
                     array('left'=>0x1D18C, 'right'=>0x1D1A9),
                     array('left'=>0x1D1AE, 'right'=>0x1D1DD),
                     array('left'=>0x1D200, 'right'=>0x1D241),
                     array('left'=>0x1D245, 'right'=>0x1D245),
                     array('left'=>0x1D300, 'right'=>0x1D356),
                     array('left'=>0x1F000, 'right'=>0x1F02B),
                     array('left'=>0x1F030, 'right'=>0x1F093),
                     array('left'=>0x1F0A0, 'right'=>0x1F0AE),
                     array('left'=>0x1F0B1, 'right'=>0x1F0BE),
                     array('left'=>0x1F0C1, 'right'=>0x1F0CF),
                     array('left'=>0x1F0D1, 'right'=>0x1F0DF),
                     array('left'=>0x1F110, 'right'=>0x1F12E),
                     array('left'=>0x1F130, 'right'=>0x1F16B),
                     array('left'=>0x1F170, 'right'=>0x1F19A),
                     array('left'=>0x1F1E6, 'right'=>0x1F202),
                     array('left'=>0x1F210, 'right'=>0x1F23A),
                     array('left'=>0x1F240, 'right'=>0x1F248),
                     array('left'=>0x1F250, 'right'=>0x1F251),
                     array('left'=>0x1F300, 'right'=>0x1F320),
                     array('left'=>0x1F330, 'right'=>0x1F335),
                     array('left'=>0x1F337, 'right'=>0x1F37C),
                     array('left'=>0x1F380, 'right'=>0x1F393),
                     array('left'=>0x1F3A0, 'right'=>0x1F3C4),
                     array('left'=>0x1F3C6, 'right'=>0x1F3CA),
                     array('left'=>0x1F3E0, 'right'=>0x1F3F0),
                     array('left'=>0x1F400, 'right'=>0x1F43E),
                     array('left'=>0x1F440, 'right'=>0x1F440),
                     array('left'=>0x1F442, 'right'=>0x1F4F7),
                     array('left'=>0x1F4F9, 'right'=>0x1F4FC),
                     array('left'=>0x1F500, 'right'=>0x1F53D),
                     array('left'=>0x1F540, 'right'=>0x1F543),
                     array('left'=>0x1F550, 'right'=>0x1F567),
                     array('left'=>0x1F5FB, 'right'=>0x1F640),
                     array('left'=>0x1F645, 'right'=>0x1F64F),
                     array('left'=>0x1F680, 'right'=>0x1F6C5),
                     array('left'=>0x1F700, 'right'=>0x1F773));
    }
    public static function S_ranges() {
        return array_merge(self::Sc_ranges(), self::Sk_ranges(),
                           self::Sm_ranges(), So_ranges());
    }
    /******************************************************************/
    public static function Zl_ranges() {
        return array(array('left'=>0x2028, 'right'=>0x2028));
    }
    public static function Zp_ranges() {
        return array(array('left'=>0x2029, 'right'=>0x2029));
    }
    public static function Zs_ranges() {
        return array(array('left'=>0x0020, 'right'=>0x0020),
                     array('left'=>0x00A0, 'right'=>0x00A0),
                     array('left'=>0x1680, 'right'=>0x1680),
                     array('left'=>0x180E, 'right'=>0x180E),
                     array('left'=>0x2000, 'right'=>0x200A),
                     array('left'=>0x202F, 'right'=>0x202F),
                     array('left'=>0x205F, 'right'=>0x205F),
                     array('left'=>0x3000, 'right'=>0x3000));
    }
    public static function Z_ranges() {
        return array_merge(self::Zl_ranges(), self::Zp_ranges(), self::Zs_ranges());
    }
    /******************************************************************/

    /**
     * Returns unicode ranges which $utf8str characters belongs to.
     * @param utf8str UTF-8 string.
     * @return array of arrays['left'=>int, 'right'=>int] containing ranges.
     */
    public static function get_ranges($utf8str) {
        $result = array();
        for ($i = 0; $i < self::strlen($utf8str); $i++) {
            $code = self::ord(self::substr($utf8str, $i, 1));
            foreach (self::$ranges as $name => $range) {
                if ($code >= $range[0] && $code < $range[1]) {
                    if (!array_key_exists($name, $result)) {
                        $result[$name] = array('left' => $range[0], 'right' => $range[1]);
                    }
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Intersects ranges to get a one whole range.
     * @param ranges an array of ranges by "OR". Every range is represented as array('negative'=>bool, 'left'=>int, 'right'=>int).
     * @return an array of ranges where ranges represented as array ('left'=>int, 'right'=>int).
     */
    public static function intersect_ranges($ranges) {
        $result = array();
        foreach ($ranges as $tointersect) {
            $curresult = array(array('left' => 0, 'right' => 0x10FFFD));
            for ($i = 0; count($curresult) > 0 && $i < count($tointersect); $i++) {
                // $curresult is updated every iteration of this loop.
                $tmp = $tointersect[$i];
                // A negative range turns into two positive ranges.
                if (!$tmp['negative']) {
                    $currange = array(array('left' => $tmp['left'], 'right' => $tmp['right']));
                } else {
                    $currange = array();
                    if ($tmp['left'] > 0) {
                        $currange[] = array('left' => 0, 'right' => $tmp['left']);
                    }
                    if ($tmp['right'] < 0x10FFFD) {
                        $currange[] = array('left' => $tmp['right'], 'right' => 0x10FFFD);
                    }
                }

                // Process two current ranges.
                $tmp = array();
                //echo 'intersecting '; print_r($curresult); echo ' with '; print_r($currange); echo '<br/>';
                foreach ($curresult as $curresultpart) {
                    foreach ($currange as $currangepart) {
                        if ($curresultpart['left'] < $currangepart['left']) {
                            $left = $curresultpart;
                            $right = $currangepart;
                        } else {
                            $left = $currangepart;
                            $right = $curresultpart;
                        }
                        //echo $left['right'].'<br/>';
                        if ($right['left'] <= $left['right'] && $left['right'] >= $right['left']) {
                            $tmp[] = array('left' => $right['left'], 'right' => min($left['right'], $right['right']));
                        }
                    }
                }
                //echo 'result: '; print_r($tmp); echo '<br/><br/>';
                $curresult = $tmp;
            }
            if (count($curresult) > 0) {
                foreach ($curresult as $tmp) {
                    $result[] = $tmp;
                }
            }
        }
        return $result;
    }

    /**
     * Returns the code of a UTF-8 character.
     * @param utf8chr - a UTF-8 character.
     * @return its code.
     */
    public static function ord($utf8chr) {
        if ($utf8chr === '') {
            return 0;
        }

        $ord0 = ord($utf8chr{0});
        if ($ord0 >= 0 && $ord0 <= 127) {
            return $ord0;
        }

        $ord1 = ord($utf8chr{1});
        if ($ord0 >= 192 && $ord0 <= 223) {
            return ($ord0 - 192) * 64 + ($ord1 - 128);
        }

        $ord2 = ord($utf8chr{2});
        if ($ord0 >= 224 && $ord0 <= 239) {
            return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
        }

        $ord3 = ord($utf8chr{3});
        if ($ord0 >= 240 && $ord0 <= 247) {
            return ($ord0 - 240) * 262144 + ($ord1 - 128 )* 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
        }

        return false;
    }

    /**
     * Checks if a character is an ascii character.
     */
    public static function is_ascii($utf8chr) {
        $ord = self::ord($utf8chr);
        return $ord >= 0 && $ord <= 127;
    }

    // TODO: unicode support for the functions below!

    /**
     * Checks if a character is a digit.
     */
    public static function is_digit($utf8chr) {
        return ctype_digit($utf8chr);
    }

    /**
     * Checks if a character is an xdigit.
     */
    public static function is_xdigit($utf8chr) {
        return ctype_xdigit($utf8chr);
    }

    /**
     * Checks if a character is a space.
     */
    public static function is_space($utf8chr) {
        return ctype_space($utf8chr);
    }

    /**
     * Checks if a character is a cntrl.
     */
    public static function is_cntrl($utf8chr) {
        return ctype_cntrl($utf8chr);
    }

    /**
     * Checks if a character is a graph.
     */
    public static function is_graph($utf8chr) {
        return ctype_graph($utf8chr);
    }

    /**
     * Checks if a character is lowercase.
     */
    public static function is_lower($utf8chr) {
        return ctype_lower($utf8chr);
    }

    /**
     * Checks if a character is uppercase.
     */
    public static function is_upper($utf8chr) {
        return ctype_upper($utf8chr);
    }

    /**
     * Checks if a character is printable.
     */
    public static function is_print($utf8chr) {
        return ctype_print($utf8chr);
    }

    /**
     * Checks if a character is non-space or alnum.
     */
    public static function is_punct($utf8chr) {
        return ctype_punct($utf8chr);
    }

    /**
     * Checks if a character is alphabetic.
     */
    public static function is_alpha($utf8chr) {
        return ctype_alpha($utf8chr);
    }

    /**
     * Checks if a character is alphanumeric.
     */
    public static function is_alnum($utf8chr) {
        return ctype_alnum($utf8chr);
    }

    /**
     * Checks if a character is alphabetic or '_'.
     */
    public static function is_wordchar($utf8chr) {
        return $utf8chr === '_' || self::is_alnum($utf8chr);
    }

    /**
     * Checks if a character is a horizontal space.
     */
    public static function is_hspace($utf8chr) {
        return in_array(self::ord($utf8chr), self::$hspaces);
    }

    /**
     * Checks if a character is a vertical space.
     */
    public static function is_vspace($utf8chr) {
        return in_array(self::ord($utf8chr), self::$vspaces);
    }

    /******************************************************************/

    public static function is_Cc($utf8chr) {    // Control
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cf($utf8chr) {    // Format
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cn($utf8chr) {    // Unassigned
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Co($utf8chr) {    // Private use
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cs($utf8chr) {    // Surrogate
        throw new Exception('Unicode properties support is not implemented yet');
    }

    /******************************************************************/

    public static function is_Ll($utf8chr) {    // Lower case letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lm($utf8chr) {    // Modifier letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lo($utf8chr) {    // Other letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lt($utf8chr) {    // Title case letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lu($utf8chr) {    // Upper case letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_L($utf8chr) {     // Letter
        return self::is_Ll($utf8chr) || self::is_Lm($utf8chr) || self::is_Lo($utf8chr) ||
               self::is_Lt($utf8chr) || self::is_Lu($utf8chr);
    }

    /******************************************************************/

    public static function is_Mc($utf8chr) {    // Spacing mark
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Me($utf8chr) {    // Enclosing mark
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Mn($utf8chr) {    // Non-spacing mark
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_M($utf8chr) {     // Mark
        return self::is_Mc($utf8chr) || self::is_Me($utf8chr) || self::is_Mn($utf8chr);
    }

    /******************************************************************/

    public static function is_Nd($utf8chr) {    // Decimal number
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Nl($utf8chr) {    // Letter number
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_No($utf8chr) {    // Other number
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_N($utf8chr) {     // Number
        return self::is_Nd($utf8chr) || self::is_Nl($utf8chr) || self::is_No($utf8chr);
    }

    /******************************************************************/

    public static function is_Pc($utf8chr) {    // Connector punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Pd($utf8chr) {    // Dash punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Pe($utf8chr) {    // Close punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Pf($utf8chr) {    // Final punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Pi($utf8chr) {    // Initial punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Po($utf8chr) {    // Other punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ps($utf8chr) {    // Open punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_P($utf8chr) {     // Punctuation
        return self::is_Pc($utf8chr) || self::is_Pd($utf8chr) || self::is_Pe($utf8chr) ||
               self::is_Pf($utf8chr) || self::is_Pi($utf8chr) || self::is_Po($utf8chr) ||
               self::is_Ps($utf8chr);
    }

    /******************************************************************/

    public static function is_Sc($utf8chr) {    // Currency symbol
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Sk($utf8chr) {    // Modifier symbol
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Sm($utf8chr) {    // Mathematical symbol
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_So($utf8chr) {    // Other symbol
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_S($utf8chr) {     // Symbol
        return self::is_Sc($utf8chr) || self::is_Sk($utf8chr) ||
               self::is_Sm($utf8chr) || self::is_So($utf8chr);
    }

    /******************************************************************/

    public static function is_Zl($utf8chr) {    // Line separator
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Zp($utf8chr) {    // Paragraph separator
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Zs($utf8chr) {    // Space separator
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Z($utf8chr) {     // Separator
        return self::is_Zl($utf8chr) || self::is_Zp($utf8chr) || self::is_Zs($utf8chr);
    }

    /******************************************************************/

    public static function is_C($utf8chr) {     // Other
        return !self::is_cC($utf8chr) && !self::is_Cf($utf8chr) && !self::is_Cn($utf8chr) &&
               !self::is_Co($utf8chr) && !self::is_Cs($utf8chr) && !self::is_L($utf8chr) &&
               !self::is_M($utf8chr) && !self::is_N($utf8chr) && !self::is_P($utf8chr) &&
               !self::is_S($utf8chr) && !self::is_Z($utf8chr);
    }

    /******************************************************************/

    public static function is_Arabic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Armenian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Avestan($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Balinese($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Bamum($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Bengali($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Bopomofo($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Braille($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Buginese($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Buhid($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Canadian_Aboriginal($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Carian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cham($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cherokee($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Common($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Coptic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cuneiform($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cypriot($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cyrillic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Deseret($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Devanagari($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Egyptian_Hieroglyphs($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ethiopic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Georgian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Glagolitic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Gothic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Greek($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Gujarati($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Gurmukhi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Han($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Hangul($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Hanunoo($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Hebrew($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Hiragana($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Imperial_Aramaic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Inherited($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Inscriptional_Pahlavi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Inscriptional_Parthian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Javanese($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Kaithi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Kannada($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Katakana($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Kayah_Li($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Kharoshthi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Khmer($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lao($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Latin($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lepcha($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Limbu($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Linear_B($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lisu($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lycian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lydian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Malayalam($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Meetei_Mayek($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Mongolian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Myanmar($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_New_Tai_Lue($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Nko($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ogham($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Old_Italic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Old_Persian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Old_South_Arabian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Old_Turkic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ol_Chiki($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Oriya($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Osmanya($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Phags_Pa($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Phoenician($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Rejang($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Runic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Samaritan($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Saurashtra($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Shavian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Sinhala($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Sundanese($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Syloti_Nagri($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Syriac($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tagalog($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tagbanwa($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tai_Le($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tai_Tham($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tai_Viet($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tamil($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Telugu($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Thaana($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Thai($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tibetan($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tifinagh($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ugaritic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Vai($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Yi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

}
