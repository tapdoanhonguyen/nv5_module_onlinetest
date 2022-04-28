<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );
 

define( 'NV_IS_FILE_ADMIN', true );

define( 'TABLE_ONLINETEST_NAME', NV_PREFIXLANG . '_' . $module_data ); 

define( 'ACTION_METHOD', $nv_Request->get_string( 'action', 'get,post', '' ) ); 
 
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php'; 
 
$onlineTestInhome = array( '0'=> $lang_module['no'], '1'=> $lang_module['yes'] );

function nv_test_read_msword($examid, $fileword)
{
    global $module_data, $nv_Request, $lang_module;

    $temp_file = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $module_data . '_import_' . $examid . '.html';
    require_once (NV_ROOTDIR . '/modules/test/simple_html_dom.php');
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($fileword);
    $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
    $htmlWriter->save($temp_file);
    $html = file_get_html($temp_file);
    $all_p_tags = $html->find('p');
	//var_dump( $all_p_tags);die;
	
	
    $array_question = array(
        'data' => array(),
        'error' => 0
    );
    $images = array();
    $count_image = 1;
    $index_image = $count_elemt = 0;
    $str = '';

    foreach ($phpWord->getSections() as $section) {
        $arrays = $section->getElements();
		var_dump($arrays);die;
        foreach ($arrays as $e) {
            if (get_class($e) === 'PhpOffice\PhpWord\Element\TextRun') {
                foreach ($e->getElements() as $text) {
                    if (get_class($text) === 'PhpOffice\PhpWord\Element\Text') {
                        $str .= $text->getText();
                    } elseif (get_class($text) === 'PhpOffice\PhpWord\Element\Image') {
                        $data = base64_decode($text->getImageStringData(true));
                        $file_name = md5($text->getName() . time());
                        file_put_contents(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name . '.png', $data);
                        array_push($images, NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $file_name . '.png');
                        $count_image++;
                    }
                }
                $str .= "\n";
            }

            if (get_class($e) === 'PhpOffice\PhpWord\Element\TextBreak') {
                $array = preg_split("/\r\n|\n|\r/", $str);
                if (is_array($array) && count($array) > 0) {
                    $index_image = 0;
                    $question = array(
                        'question' => '',
                        'answer' => array(),
                        'useguide' => '',
                        'count_true' => 0,
                        'error' => array()
                    );
                    if ($all_p_tags[$count_elemt]->find('img')) {
                        $all_p_tags[$count_elemt]->find('img')[0]->src = $images[$index_image++];
                    }
                    $question['question'] = nv_test_content_format($all_p_tags[$count_elemt++]);
                    for ($i = 1; $i < count($array); $i++) {
                        // phát hiện lời giải
                        $is_userguide = 0;
                        if (substr(nv_unhtmlspecialchars($array[$i]), 0, 1) === '>') {
                            $is_userguide = 1;
                        } elseif (substr($array[$i], 0, 1) === '*') {
                            $is_true = 1;
                            $question['count_true']++;
                        } else {
                            $is_true = 0;
                        }

                        if (isset($all_p_tags[$count_elemt]) && $all_p_tags[$count_elemt]->find('img')) {
                            $all_p_tags[$count_elemt]->find('img')[0]->src = $images[$index_image++];
                        }

                        // loại bỏ các ký tự đánh dấu
                        if (isset($all_p_tags[$count_elemt])) {
                            if (count($all_p_tags[$count_elemt]->find('span')) > 0) {
                                $type = 'span';
                                $answerText = nv_unhtmlspecialchars($all_p_tags[$count_elemt]->find('span')[0]->plaintext);
                            } else {
                                $type = 'p';
                                $answerText = $all_p_tags[$count_elemt]->plaintext;
                            }
                            if (substr($answerText, 0, 1) === '*' || substr($answerText, 0, 1) === '>') {
                                if ($type == 'span') {
                                    $all_p_tags[$count_elemt]->find('span')[0]->innertext = substr($answerText, 1);
                                } else {
                                    $all_p_tags[$count_elemt]->innertext = substr($answerText, 1);
                                }
                            }
                        }

                        if ($is_userguide) {
                            $question['useguide'] = nv_test_content_format($all_p_tags[$count_elemt++]);
                        } elseif ($i < count($array) - 1) {
                            $question['answer'][$i] = array(
                                'id' => $i,
                                'content' => nv_test_content_format($all_p_tags[$count_elemt++]),
                                'is_true' => $is_true
                            );
                        } else {
                            $count_elemt++;
                        }
                    }

                    // kiểm tra lỗi
                    if (count($question['answer']) < 2) {
                        $question['error'][] = $lang_module['error_required_answer'];
                    } elseif (empty($question['count_true'])) {
                        $question['error'][] = $lang_module['error_required_answer_is_true'];
                    }

                    if (!empty($question['error'])) {
                        $array_question['error'] = 1;
                    }

                    $array_question['data'][] = $question;
                }
                $str = '';
                $images = array();
            }
        }
    }

    // insert last question
    $array = preg_split("/\r\n|\n|\r/", $str);
    if ($str != '' && is_array($array) && count($array) > 0) {
        $index_image = 0;
        $question = array(
            'question' => '',
            'answer' => array(),
            'count_true' => 0,
            'error' => array()
        );
        if ($all_p_tags[$count_elemt]->find('img')) {
            $all_p_tags[$count_elemt]->find('img')[0]->src = $images[$index_image++];
        }
        $question['question'] = nv_test_content_format($all_p_tags[$count_elemt++]);
        for ($i = 1; $i < count($array); $i++) {

            // phát hiện lời giải
            $is_userguide = 0;
            if (substr(nv_unhtmlspecialchars($array[$i]), 0, 1) === '>') {
                $is_userguide = 1;
                $question['useguide'] = nv_test_content_format($all_p_tags[$count_elemt]);
            } elseif (substr($array[$i], 0, 1) === '*') {
                $is_true = 1;
                $question['count_true']++;
            } else {
                $is_true = 0;
            }

            if (isset($all_p_tags[$count_elemt]) && $all_p_tags[$count_elemt]->find('img')) {
                $all_p_tags[$count_elemt]->find('img')[0]->src = $images[$index_image++];
            }

            // loại bỏ các ký tự đánh dấu
            if (isset($all_p_tags[$count_elemt]) && count($all_p_tags[$count_elemt]->find('span')) > 0) {
                $answerText = nv_unhtmlspecialchars($all_p_tags[$count_elemt]->find('span')[0]->plaintext);
                if (substr($answerText, 0, 1) === '*' || substr($answerText, 0, 1) === '>') {
                    $all_p_tags[$count_elemt]->find('span')[0]->innertext = substr($answerText, 1);
                }
            }

            if ($is_userguide) {
                $question['useguide'] = nv_test_content_format($all_p_tags[$count_elemt++]);
            } elseif ($i < count($array) - 1) {
                $question['answer'][$i] = array(
                    'id' => $i,
                    'content' => nv_test_content_format($all_p_tags[$count_elemt++]),
                    'is_true' => $is_true
                );
            } else {
                $count_elemt++;
            }
        }

        // kiểm tra lỗi
        if (count($question['answer']) < 2) {
            $question['error'][] = $lang_module['error_required_answer'];
        } elseif (empty($question['count_true'])) {
            $question['error'][] = $lang_module['error_required_answer_is_true'];
        }

        if (!empty($question['error'])) {
            $array_question['error'] = 1;
        }

        $array_question['data'][] = $question;
    }
    $nv_Request->set_Session($module_data . '_array_question', serialize($array_question));

    return $array_question;
}
