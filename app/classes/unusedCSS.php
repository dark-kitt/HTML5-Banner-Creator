<?php
    /**
     * check for unused CSS in HTML, SCSS and JS
     */
    class unusedCSS
    {

        public $unusedCSS;
        public $selectors;

        function __construct(stdClass $selectors = null, array $html = [], array $scss = [], array $js = [], string $project_dir = '', string $directory = '')
        {
            $unusedCSS = [];

            if ($selectors === null)
            {
                $html_length = count($html);
                $html_content = '';
                while ($html_length--)
                {
                    $html_content .= file_get_contents( $directory . $html[$html_length] );
                }
                $scss_length = count($scss);
                $scss_content = '';
                while ($scss_length--)
                {
                    $scss_content .= file_get_contents( $directory . '/scss' . $scss[$scss_length] );
                }
                $js_length = count($js);
                $js_content = '';
                while ($js_length--)
                {
                    $js_content .= file_get_contents( $directory . '/js' . $js[$js_length] );
                }

                $selectors = $this->find_selectors($html_content, $scss_content, $js_content);
            }

            foreach ($html as $html_file)
            {
                foreach ( $scss as $scss_file )
                {
                    foreach ( $js as $js_file )
                    {
                        array_push(
                            $unusedCSS,
                            $this->find_unused_CSS($selectors, $html_file, $scss_file, $js_file, $project_dir, $directory)
                        );
                    }
                }
            }

            $unusedCSS = $this->flatten_array($unusedCSS);
            $unusedCSS = array_map('json_encode', $unusedCSS);
            $unusedCSS = array_values(array_unique($unusedCSS));
            $unusedCSS = array_map('json_decode', $unusedCSS);
            $unusedCSS = json_decode(json_encode($unusedCSS), True);

            $this->unusedCSS = $unusedCSS;
            $this->selectors = $selectors;
        }

        /*
         * find all unused CSS selectors
         */
        private function find_unused_CSS(stdClass $selectors, string $html = '', string $scss = '', string $js = '', string $project_dir = '', string $directory)
        {

            $unusedCSS = [];
            $html = $directory . $html;
            $scss = $directory . '/scss' . $scss;
            $js = $directory . '/js' . $js;

            $html_ids = $selectors->html->id;
            $html_classes = $selectors->html->class;
            $html_tags = $selectors->html->tag;

            $scss_ids = $selectors->scss->id;
            $scss_classes = $selectors->scss->class;
            $scss_tags = $selectors->scss->tag;

            $js_ids = [];
            foreach ($selectors->js->id as $value) {
                $js_ids[] = $this->flatten_array( $value );
            }
            $js_ids = $this->flatten_array( $js_ids );

            $js_classes = [];
            foreach ($selectors->js->class as $value) {
                $js_classes[] = $this->flatten_array( $value );
            }
            $js_classes = $this->flatten_array( $js_classes );

            $html_content = file_get_contents( $html );
            $scss_content = file_get_contents( $scss );
            $js_content = file_get_contents( $js );
            $curr_selectors = $this->find_selectors($html_content, $scss_content, $js_content);

            $curr_html_ids = $curr_selectors->html->id;
            $curr_html_classes = $curr_selectors->html->class;
            $curr_html_tags = $curr_selectors->html->tag;

            $curr_scss_ids = $curr_selectors->scss->id;
            $curr_scss_classes = $curr_selectors->scss->class;
            $curr_scss_tags = $curr_selectors->scss->tag;

            $curr_js_ids = [];
            foreach ($curr_selectors->js->id as $value) {
                $curr_js_ids[] = $this->flatten_array( $value );
            }
            $curr_js_ids = $this->flatten_array( $curr_js_ids );

            $curr_js_classes = [];
            foreach ($curr_selectors->js->class as $value) {
                $curr_js_classes[] = $this->flatten_array( $value );
            }
            $curr_js_classes = $this->flatten_array( $curr_js_classes );

            $js_id_obj = (object) [
                'get_id' => [
                    '.getElementById("', '")',
                    '\.getElementById\b\(', ''
                ],
                'id_jQuery' => [
                    '$("#', '")',
                    '\$\(', '\#'
                ],
                'id_obj' => [
                    'id: "', '"',
                    'id\b\:', '(?:\'|\")'
                ],
                'gid' => [
                    'gid("', '")',
                    'gid\b\(', '(?:\'|\")'
                ]
            ];

            $js_class_obj = (object) [
                'get_class' => [
                    '.getElementsByClassName("', '")',
                    '\.getElementsByClassName\b\(', ''
                ],
                'class_jQuery' => [
                    '$(".', '")',
                    '\$\(', '\.'
                ],
                'arh_jQuery' => [
                    '.addClass | .hasClass | .removeClass("', '")',
                    '(?:\.addClass\b|\.hasClass\b|\.removeClass\b)', ''
                ],
                'rhClass' => [
                    'removeClass | hasClass("', '")',
                    '(?:hasClass\b|removeClass\b)', ''
                ],
                'addClass' => [
                    'class: "', '"',
                    'class\b\:', ''
                ],
                'class_obj' => [
                    'cl: "', '"',
                    'cl\b\:', ''
                ],
                'gcl' => [
                    'gcl("', '")',
                    'gcl\b\(', ''
                ]
            ];

            if ( count($html_ids) > 0 )
            {
                foreach ( $html_ids as $value )
                {
                    if ( !in_array( $value, $scss_ids ) &&
                         !in_array( $value, $js_ids ) &&
                          in_array( $value, $curr_html_ids ) )
                    {
                        array_push( $unusedCSS, (object) [
                            'identifier' => 'id="' . $value . '"',
                            'message' => 'HTML id not found in SCSS or JS file.',
                            'line' => $this->find_row('id\b', $value, $html),
                            'directory' => str_replace( $project_dir , '', $html )
                        ]);
                    }
                }
            }

            if ( count($html_classes) > 0 )
            {
                foreach ( $html_classes as $value )
                {
                    if ( !in_array( $value, $scss_classes ) &&
                         !in_array( $value, $js_classes ) &&
                          in_array( $value, $curr_html_classes ) )
                    {
                        array_push( $unusedCSS, (object) [
                            'identifier' => 'class="' . $value . '"',
                            'message' => 'HTML class not found in SCSS or JS file.',
                            'line' => $this->find_row('class\b', $value, $html),
                            'directory' => str_replace( $project_dir , '', $html )
                        ]);
                    }
                }
            }

            if ( count($scss_ids) > 0 )
            {
                foreach ( $scss_ids as $value )
                {
                    if ( !in_array( $value, $html_ids ) &&
                         !in_array( $value, $js_ids ) &&
                          in_array( $value, $curr_scss_ids ) )
                    {
                        array_push( $unusedCSS, (object) [
                            'identifier' => '#' . $value,
                            'message' => 'CSS id not found in HTML or JS file.',
                            'line' => $this->find_row('\#', $value, $scss),
                            'directory' => str_replace( $project_dir , '', $scss )
                        ]);
                    }
                }
            }

            if ( count($scss_classes) > 0 )
            {
                foreach ( $scss_classes as $value )
                {
                    if ( !in_array( $value, $html_classes ) &&
                         !in_array( $value, $js_classes )  &&
                          in_array( $value, $curr_scss_classes ) )
                    {
                        array_push( $unusedCSS, (object) [
                            'identifier' => '.' . $value,
                            'message' => 'CSS class not found in HTML or JS file.',
                            'line' => $this->find_row('\.', $value, $scss),
                            'directory' => str_replace( $project_dir , '', $scss )
                        ]);
                    }
                }
            }

            if ( count($scss_tags) > 0 )
            {
                foreach ( $scss_tags as $value )
                {
                    if ( !in_array( $value, $html_tags ) &&
                          $value !== 'body' &&
                          $value !== 'html'  &&
                          in_array( $value, $curr_scss_tags ) )
                    {
                        array_push( $unusedCSS, (object) [
                            'identifier' => $value,
                            'message' => 'CSS tag not found in HTML file.',
                            'line' => $this->find_row('\<', $value, $scss),
                            'directory' => str_replace( $project_dir , '', $scss )
                        ]);
                    }
                }
            }

            if ( count($js_ids) > 0 )
            {
                foreach ( $selectors->js->id as $key => $par_value )
                {
                    foreach ($selectors->js->id->$key as $chi_value) {
                        if ( !in_array( $chi_value, $scss_ids ) &&
                             !in_array( $chi_value, $html_ids ) &&
                              in_array( $chi_value, $curr_js_ids ))
                        {
                            array_push( $unusedCSS, (object) [
                                'identifier' => $js_id_obj->$key[0] . $chi_value . $js_id_obj->$key[1],
                                'message' => 'CSS id in JS file not found in HTML or SCSS file.',
                                'line' => $this->find_row($js_id_obj->$key[2], $js_id_obj->$key[3] . $chi_value, $js),
                                'directory' => str_replace( $project_dir , '', $js )
                            ]);
                        }
                    }
                }
            }

            if ( count($js_classes) > 0 )
            {
                foreach ( $selectors->js->class as $key => $par_value )
                {
                    foreach ($selectors->js->class->$key as $chi_value) {
                        if ( !in_array( $chi_value, $scss_classes ) &&
                             !in_array( $chi_value, $html_classes ) &&
                              in_array( $chi_value, $curr_js_classes ))
                        {
                            array_push( $unusedCSS, (object) [
                                'identifier' => $js_class_obj->$key[0] . $chi_value . $js_class_obj->$key[1],
                                'message' => 'CSS class in JS file not found in HTML or SCSS file.',
                                'line' => $this->find_row($js_class_obj->$key[2], $js_class_obj->$key[3] . $chi_value, $js),
                                'directory' => str_replace( $project_dir , '', $js )
                            ]);
                        }
                    }
                }
            }

            return $unusedCSS;
        }

        /*
         * find row number of match
         */
        private function find_row(string $identifier, string $search, string $inputFile )
        {
            $line_number = false;
            $lines = [];

            if ( $identifier === '\#' || $identifier === '\.' )
            {
                $pattern = '/' . $identifier . $search . '\b/';
            }
            elseif ( $identifier === '\<' )
            {
                $pattern = '/(?(?=\#' . $search . '\b)\#' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=\.' . $search . '\b)\.' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=\-' . $search . '\b)\-' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=' . $search . '\b\-)' . $search . '\b\-|\0)(*SKIP)(*FAIL)|(?(?=\_' . $search . '\b)\_' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=\w' . $search . '\b)\w' . $search . '\b|\0)(*SKIP)(*FAIL)|(?(?=\:' . $search . '\b)\:' . $search . '\b|\0)(*SKIP)(*FAIL)|' . $search . '\b/';
            }
            else
            {
                $pattern = '/' . $identifier . '.*?' . $search . '\b/';
            }

            if ($handle = fopen($inputFile, "r"))
            {
                $count = 0;

                while (($line = fgets($handle)))
                {
                    $count++;
                    $line_number = (preg_match( $pattern, $line ) !== 0) ? $count : $line_number;

                    if ($line_number !== false && $line_number === $count)
                    {
                        array_push($lines, $line_number);
                    }
                }
                fclose($handle);

                return $lines;
            }

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
    }

?>
