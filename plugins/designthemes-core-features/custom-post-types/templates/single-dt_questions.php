<?php 
get_header();
?>

<!-- ** Primary Section ** -->
<section id="primary" class="content-full-width">

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    	<?php
		if( have_posts() ): while( have_posts() ): the_post();
			$question_id = get_the_ID();
			?>
			
            <div id="dt-question-list">
                <div class="dt-question">
                    <div class="dt-title">
                        <h4><?php the_content(); ?></h4>
                    </div>	
                
                    <div class="dt-question-options">
                        <?php
                        
                        $out = '';
                        $question_type = get_post_meta ( $question_id, "question-type",true);
                        
                        if($question_type == 'multiple-choice') {
                            
                            $multichoice_answers = dttheme_wp_kses(get_post_meta ( $question_id, 'multichoice-answers', TRUE ));
                            
                            if(isset($multichoice_answers) && is_array($multichoice_answers)) {
                                $out .= '<ul>';
                                foreach($multichoice_answers as $answer) {
                                    $out .= '<li>';
                                    $out .= '<input type="radio" name="dt-question-'.$question_id.'" value="'.$answer.'" />  <label>'.$answer.'</label>';
                                    $out .= '</li>';
                                    $j++;
                                }
                                $out .= '</ul>';
                            }
                        
                        } else if($question_type == 'multiple-correct') {
                            
                            $multicorrect_answers = dttheme_wp_kses(get_post_meta ( $question_id, 'multicorrect-answers', TRUE ));
                            
                            if(isset($multicorrect_answers) && is_array($multicorrect_answers)) {
                                $out .= '<ul>';
                                foreach($multicorrect_answers as $answer) {
                                    $out .= '<li>';
                                    $out .= '<input type="checkbox" name="dt-question-'.$question_id.'[]" value="'.$answer.'" />  <label>'.$answer.'</label>';
                                    $out .= '</li>';
                                    $j++;
                                }
                                $out .= '</ul>';
                            }
                        
                        } else if($question_type == 'boolean') {
                            
							   $out .= '<div class="dt-boolean">';
                            $out .= '<input type="radio" name="dt-question-'.$question_id.'" value="true" />  <label>'.__('True', 'dt_themes').'</label>';
                            $out .= '<input type="radio" name="dt-question-'.$question_id.'" value="false" />  <label>'.__('False', 'dt_themes').'</label>';
                            $out .= '</div>';	            
                            
                        } else if($question_type == 'gap-fill') {
            
                            $text_before_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'text-before-gap', TRUE ));
                            $text_before_gap = !empty($text_before_gap) ? $text_before_gap : '';
                            $text_after_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'text-after-gap', TRUE ));
                            $text_after_gap = !empty($text_after_gap) ? $text_after_gap : '';
                            
							   $out .= '<div class="dt-gapfill">';
                            $out .= $text_before_gap.' <input type="text" name="dt-question-'.$question_id.'" value="" class="dt-gap" /> '.$text_after_gap;
							   $out .= '</div>';	
                        
                        } else if($question_type == 'single-line') {
                                        
                            $out .= '<input type="text" name="dt-question-'.$question_id.'" value="" />';			
            
                        } else if($question_type == 'multi-line') {
                                        
                            $out .= '<textarea name="dt-question-'.$question_id.'"></textarea>';
                            
                        }
                        
                        echo $out;
                        
                        ?>
                    </div>
                    <div class="dt-mark"><span><?php echo get_the_date('d');?></span><?php echo get_the_date('M');?></div>
                    
                </div>
            </div>
			
		<?php    
		endwhile; endif;	
        ?>
        
   </article>     

</section><!-- ** Primary Section End ** -->
<?php get_footer(); ?>