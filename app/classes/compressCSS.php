<?php
    /**
     * compress CSS
     */
    class compressCSS
    {

        public $html;
        public $scss;
        public $js;
        public $selectors;
        public $new_selectors;

        function __construct(stdClass $selectors = null, array $html = [], array $scss = [], array $js = [], string $directory = '')
        {
            $html_length = count($html);
            $html_content = '';
            while ($html_length--)
            {
                if ( is_file( $directory . $html[$html_length] ) )
                {
                    $html_content .= file_get_contents( $directory . $html[$html_length] );
                }
                else
                {
                    $html_content .= $html[$html_length];
                }
            }
            $scss_length = count($scss);
            $scss_content = '';
            while ($scss_length--)
            {
                if ( is_file( $directory . '/scss' . $scss[$scss_length] ) )
                {
                    $scss_content .= file_get_contents( $directory . '/scss' . $scss[$scss_length] );
                }
                else
                {
                    $scss_content .= $scss[$scss_length];
                }
            }
            $js_length = count($js);
            $js_content = '';
            while ($js_length--)
            {
                if ( is_file( $directory . '/js' . $js[$js_length] ) )
                {
                    $js_content .= file_get_contents( $directory . '/js' . $js[$js_length] );
                }
                else
                {
                    $js_content .= $js[$js_length];
                }
            }

            if ($selectors === null)
            {
                $selectors = $this->find_selectors($html_content, $scss_content, $js_content);
            }

            if ($selectors !== null)
            {
                $return = $this->compress($selectors, $html_content,  $scss_content,  $js_content);
            }

            $this->html = $return->html;
            $this->scss = $return->scss;
            $this->js = $return->js;
            $this->selectors = $selectors;
            $this->new_selectors = $return->new_selectors;
        }

        private function compress(stdClass $selectors, string $html = '', string $scss = '', string $js = '')
        {
            $html = $html;
            $scss = $scss;
            $js = $js;

            $scss_ids = $selectors->scss->id;
            $scss_classes = $selectors->scss->class;

            $js_ids = [
                [
                    '/((?:getElementById\b)\(.*?)(',
                    '\b)/'
                ],
                [
                    '/((?:\$)\(.*?(?=\#)\#)(',
                    '\b)/'
                ],
                [
                    '/((?:id\b)\:.*?)(',
                    '\b)/'
                ],
                [
                    '/((?:gid\b)\(.*?)(',
                    '\b)/'
                ]
            ];

            $js_classes = [
                [
                    '/((?:getElementsByClassName\b)\(.*?)(',
                    '\b)/'
                ],
                [
                    '/((?:\$)\(.*?(?=\.)\.)(',
                    '\b)/'
                ],
                [
                    '/((?:\.addClass\b|\.hasClass\b|\.removeClass\b)\(.*?)(',
                    '\b)/'
                ],
                [
                    '/((?:removeClass\b|hasClass\b)\(.*?(?=\,).*?(?=\'|\")(?:\'|\").*?)(',
                    '\b)/'
                ],
                [
                    '/((?:class\b)\:.*?)(',
                    '\b)/'
                ],
                [
                    '/((?:cl\b)\:.*?)(',
                    '\b)/'
                ],
                [
                    '/((?:gcl\b)\(.*?)(',
                    '\b)/'
                ]
            ];

            $count = 'aa';
            foreach ($scss_ids as $id)
            {
                $scss = $this->set_selector($scss, '/(\#)' . $id . '\b/', $count);
                $html = $this->set_selector($html, '/(id(?(?=\s+)\s+)\=(?(?=\s+)\s+)\"[^"]*?)((?<!\-|\w)' . $id . '\b)/', $count);
                foreach ($js_ids as $js_id) {
                    $js = $this->set_selector($js, $js_id[0] . $id . $js_id[1], $count);
                }
                $this->replace_value($selectors, $id ,$count);

                $count++;
            }

            foreach ($scss_classes as $class)
            {
                $scss = $this->set_selector($scss, '/(\.)' . $class . '\b/', $count);
                $html = $this->set_selector($html, '/(class\b(?(?=\s+)\s+)\=(?(?=\s+)\s+).*?(?|(?:(?=\s+)\s+)|(?:(?=\")\")|(?:(?=\')\')))(' . $class . '\b.*?(?=\"))/', $count);
                foreach ($js_classes as $js_class) {
                    $js = $this->set_selector($js, $js_class[0] . $class . $js_class[1], $count);
                }
                $this->replace_value($selectors, $class ,$count);

                $count++;
            }

            return (object) [
                'html' => $html,
                'scss' => $scss,
                'js' => $js,
                'new_selectors' => $selectors
            ];
        }

        /*
         * set new selector
         */
        private function set_selector(string $content, $pattern, string $replace)
        {
            $content = preg_replace_callback(
                            $pattern,
                            function($match) use (&$replace) {
                                return $match[1] . $replace;
                            },
                            $content
                        );
            return $content;
        }

        /*
         * find all selectors
         */
        private function find_selectors(string $html = '', string $scss = '', string $js = '')
        {
            $pattern_id_html = '/' . REGEX_ID_HTML . '/';
            $pattern_class_html = '/' . REGEX_CLASS_HTML . '/';
            $pattern_tag_html = '/' . REGEX_TAG_HTML . '/';

            $pattern_all_scss = '/' . REGEX_ALL_SCSS . '/';
            $pattern_id_scss = '/' . REGEX_ID_SCSS . '/';
            $pattern_class_scss = '/' . REGEX_CLASS_SCSS . '/';
            $pattern_tag_scss = '/' . REGEX_TAG_SCSS . '/';

            $pattern_get_id_js = '/' . REGEX_GET_ID_JS . '/';
            $pattern_get_class_js = '/' . REGEX_GET_CLASS_JS . '/';

            $pattern_add_rem_hasClass_jQuery = '/' . REGEX_ADD_REM_HASCLASS_JQUERY . '/';
            $pattern_selectors_jQuery = '/' . REGEX_SELECTORS_JQUERY . '/';
            $pattern_id_selectors_jQuery = '/' . REGEX_ID_SELECTORS_JQUERY . '/';
            $pattern_class_selectors_jQuery = '/' . REGEX_CLASS_SELECTORS_JQUERY . '/';

            $pattern_rem_hasClass_ksjs = '/' . REGEX_REM_HASCLASS_KSJS . '/';
            $pattern_addClass_ksjs = '/' . REGEX_ADDCLASS_KSJS . '/';
            $pattern_id_obj_ksjs = '/' . REGEX_ID_OBJ_KSJS . '/';
            $pattern_class_obj_ksjs = '/' . REGEX_CLASS_OBJ_KSJS . '/';
            $pattern_gid_ksjs = '/' . REGEX_GID_KSJS . '/';
            $pattern_gcl_ksjs = '/' . REGEX_GCL_KSJS . '/';


            /* filter html match result */
            preg_match_all( $pattern_id_html, $html, $html_ids );

            preg_match_all( $pattern_class_html, $html, $html_class_match );
            /* WORKAROUND: for multiple whitespaces in html e.g. class="class    class class" */
            preg_match_all('/(?|(.+?)(?:\,)|(.+))/', preg_replace( '/' . REGEX_SPACES . '/', ',', join(',',array_filter($html_class_match[1], function($value) { return $value !== ''; } ))), $html_classes);

            preg_match_all( $pattern_tag_html, $html, $html_tags );

            $html_ids = $this->filter_unique_flatten_array($html_ids[1]);
            $html_classes = array_unique( $html_classes[1] );
            $html_tags = $this->filter_unique_flatten_array($html_tags[1]);


            /* filter scss match result */
            preg_match_all( $pattern_all_scss, $scss, $scss_matches );
            $scss_result = str_replace(',,', ',', preg_replace( '/' . REGEX_SPACES . '/', ',', join( ',', $scss_matches[1] )));

            preg_match_all( $pattern_id_scss, $scss_result, $scss_ids );
            foreach( $scss_ids as $key => $id )
            {
                $scss_ids[$key] = str_replace( '#', '', $id );
            }
            $scss_ids = $this->filter_unique_flatten_array($scss_ids);

            preg_match_all( $pattern_class_scss, $scss_result, $scss_classes );
            foreach( $scss_classes as $key => $class )
            {
                $scss_classes[$key] = str_replace( '.', '', $class );
            }
            $scss_classes = $this->filter_unique_flatten_array($scss_classes);

            preg_match_all( $pattern_tag_scss, $scss_result, $scss_tags );
            $scss_tags = $this->filter_unique_flatten_array($scss_tags);


            /* filter js match result */
            preg_match_all( $pattern_get_id_js, $js, $js_get_ids );
            $js_get_ids = $this->filter_unique_flatten_array($js_get_ids[1]);

            preg_match_all( $pattern_get_class_js, $js, $js_get_classes );
            $js_get_classes = $this->filter_unique_flatten_array($js_get_classes[1]);

            preg_match_all( $pattern_add_rem_hasClass_jQuery, $js, $js_add_rem_hasClass_match_jQuery );
            preg_match_all( '/[\w\-]+/', join(',', $js_add_rem_hasClass_match_jQuery[1]), $js_add_rem_hasClass_jQuery);
            $js_add_rem_hasClass_jQuery = $this->filter_unique_flatten_array($js_add_rem_hasClass_jQuery[0]);

            preg_match_all( $pattern_selectors_jQuery, $js, $js_selectors_jQuery );

            preg_match_all( $pattern_id_selectors_jQuery, join(',', $js_selectors_jQuery[1]), $js_id_selectors_jQuery );
            foreach( $js_id_selectors_jQuery as $key => $id )
            {
                $js_id_selectors_jQuery[$key] = str_replace( '#', '', $id );
            }
            $js_id_selectors_jQuery = $this->filter_unique_flatten_array($js_id_selectors_jQuery[0]);

            preg_match_all( $pattern_class_selectors_jQuery, join(',', $js_selectors_jQuery[1]), $js_class_selectors_jQuery );
            foreach( $js_class_selectors_jQuery as $key => $class )
            {
                $js_class_selectors_jQuery[$key] = str_replace( '#', '', $class );
            }
            $js_class_selectors_jQuery = $this->filter_unique_flatten_array($js_class_selectors_jQuery[0]);

            preg_match_all( $pattern_rem_hasClass_ksjs, $js, $js_rem_hasClass_ksjs );
            $js_rem_hasClass_ksjs = $this->filter_unique_flatten_array($js_rem_hasClass_ksjs[1]);
            preg_match_all( $pattern_addClass_ksjs, $js, $js_addClass_ksjs );
            $js_addClass_ksjs = $this->filter_unique_flatten_array($js_addClass_ksjs[1]);

            preg_match_all( $pattern_id_obj_ksjs, $js, $js_id_obj_ksjs );
            $js_id_obj_ksjs = $this->filter_unique_flatten_array($js_id_obj_ksjs[1]);
            preg_match_all( $pattern_class_obj_ksjs, $js, $js_class_obj_ksjs );
            $js_class_obj_ksjs = $this->filter_unique_flatten_array($js_class_obj_ksjs[1]);

            preg_match_all( $pattern_gid_ksjs, $js, $js_gid_ksjs );
            $js_gid_ksjs = $this->filter_unique_flatten_array($js_gid_ksjs[1]);
            preg_match_all( $pattern_gcl_ksjs, $js, $js_gcl_ksjs );
            $js_gcl_ksjs = $this->filter_unique_flatten_array($js_gcl_ksjs[1]);

            return (object) [
                'html' => (object) [
                    'id' => $html_ids,
                    'class' => $html_classes,
                    'tag' => $html_tags
                ],
                'scss' => (object) [
                    'id' => $scss_ids,
                    'class' => $scss_classes,
                    'tag' => $scss_tags
                ],
                'js' => (object) [
                    'id' => (object) [
                        'get_id' => $js_get_ids,
                        'id_jQuery' => $js_id_selectors_jQuery,
                        'id_obj' => $js_id_obj_ksjs,
                        'gid' => $js_gid_ksjs
                    ],
                    'class' => (object) [
                        'get_class' => $js_get_classes,
                        'class_jQuery' => $js_class_selectors_jQuery,
                        'arh_jQuery' => $js_add_rem_hasClass_jQuery,
                        'rhClass' => $js_rem_hasClass_ksjs,
                        'addClass' => $js_addClass_ksjs,
                        'class_obj' => $js_class_obj_ksjs,
                        'gcl' => $js_gcl_ksjs
                    ]
                ]
            ];

        }

        /*
         * flatten array
         */
        private function flatten_array(array $array)
        {
            $return = [];
            array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
            return $return;
        }

        /*
         * filter, unique and flatten array
         */
        private function filter_unique_flatten_array(array $array)
        {
            return array_filter( array_unique( $this->flatten_array( $array ) ), function($value) { return $value !== ''; } );
        }

        /*
         * search and replace value in array
         */
        private function replace_value($item, $search ,$replacement)
        {
            array_walk_recursive(
                $item,
                function (&$value) use ($search, $replacement) {
                    if ($value instanceof stdClass)
                    {
                        $this->replace_value($value, $search, $replacement);
                    }
                    elseif (preg_match('/'.$value.'\b/', $search))
                    {
                        $value = $replacement;
                    }
                }
            );
            return $item;
        }

    }

?>
