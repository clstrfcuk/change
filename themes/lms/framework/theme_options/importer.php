<!-- #import -->
<div id="importer" class="bpanel-content">

    <!-- .bpanel-main-content -->
    <div class="bpanel-main-content">
        <ul class="sub-panel"> 
            <li><a href="#tab1"><?php esc_html_e('Import Demo', 'dt_themes');?></a></li>
        </ul>
        
        <!-- #tab1-import-demo -->
        <div id="tab1" class="tab-content">
            <!-- .bpanel-box -->
            <div class="bpanel-box">
                <div class="box-title">
                    <h3><?php esc_html_e('Import Demo', 'dt_themes');?></h3>
                </div>
                
                <div class="box-content dttheme-import">
					<p class="note"><?php esc_html_e('Before starting the import, you need to install all plugins that you want to use.<br />If you are planning to use the shop, please install WooCommerce plugin.', 'dt_themes');?></p>
                    <div class="hr_invisible"> </div>
                    <div class="column one-third"><label><?php esc_html_e('Demo', 'dt_themes');?></label></div>
                    <div class="column two-third last">
                        <select name="demo" class="demo medium">
                            <option value="">-- <?php esc_html_e('Select', 'dt_themes');?> --</option>
                            <option value="default"><?php esc_html_e('Theme Default', 'dt_themes') ?></option>
                            <option value="sensei"><?php esc_html_e('Sensei', 'dt_themes') ?></option>
                        </select>
                    </div>
                    <div class="hr_invisible"> </div>

					<div class="default-demo hide">
                    
                        <div class="column one-third"><label><?php esc_html_e('Import', 'dt_themes');?></label></div>
                        <div class="column two-third last">
                            <select name="import" class="import medium">
                                <option value="">-- <?php esc_html_e('Select', 'dt_themes');?> --</option>
                                <option value="all"><?php esc_html_e('All', 'dt_themes') ?></option>
                                <option value="content"><?php esc_html_e('Content', 'dt_themes') ?></option>
                                <option value="menu"><?php esc_html_e('Menu', 'dt_themes') ?></option>
                                <option value="options"><?php esc_html_e('Options', 'dt_themes') ?></option>
                                <option value="widgets"><?php esc_html_e('Widgets', 'dt_themes') ?></option>
                            </select>
                        </div>
                        <div class="hr_invisible"> </div>
    
                        <div class="row-content hide">
                            <div class="column one-third">
                                <label for="content">Content</label>
                            </div>
                            <div class="column two-third last">
                                <select name="content" class="medium">
                                    <option value="">-- <?php esc_html_e('All', 'dt_themes');?> --</option>
                                    <option value="pages"><?php esc_html_e('Pages', 'dt_themes');?></option>
                                    <option value="posts"><?php esc_html_e('Posts', 'dt_themes');?></option>
                                    <option value="portfolios"><?php esc_html_e('Portfolio', 'dt_themes');?></option>
                                    <option value="contactforms"><?php esc_html_e('Contact Forms', 'dt_themes');?></option>
                                    <option value="courses"><?php esc_html_e('Courses', 'dt_themes');?></option>
                                    <option value="lessons"><?php esc_html_e('Lessons', 'dt_themes');?></option>
                                </select>
                            </div>
                            <div class="hr_invisible"> </div>
                        </div>
                        
					</div>
                    
					<div class="sensei-demo hide">
                    
                        <div class="column one-third"><label><?php esc_html_e('Import', 'dt_themes');?></label></div>
                        <div class="column two-third last">
                            <select name="import" class="import medium">
                                <option value="">-- <?php esc_html_e('Select', 'dt_themes');?> --</option>
                                <option value="all"><?php esc_html_e('All', 'dt_themes') ?></option>
                                <option value="content"><?php esc_html_e('Content', 'dt_themes') ?></option>
                                <option value="menu"><?php esc_html_e('Menu', 'dt_themes') ?></option>
                                <option value="options"><?php esc_html_e('Options', 'dt_themes') ?></option>
                                <option value="widgets"><?php esc_html_e('Widgets', 'dt_themes') ?></option>
                            </select>
                        </div>
                        <div class="hr_invisible"> </div>
    
                        <div class="row-content hide">
                            <div class="column one-third">
                                <label for="content">Content</label>
                            </div>
                            <div class="column two-third last">
                                <select name="content" class="medium">
                                    <option value="">-- <?php esc_html_e('All', 'dt_themes');?> --</option>
                                    <option value="pages"><?php esc_html_e('Pages', 'dt_themes');?></option>
                                    <option value="posts"><?php esc_html_e('Posts', 'dt_themes');?></option>
                                    <option value="portfolios"><?php esc_html_e('Portfolio', 'dt_themes');?></option>
                                    <option value="contactforms"><?php esc_html_e('Contact Forms', 'dt_themes');?></option>
                                    <option value="courses"><?php esc_html_e('Courses', 'dt_themes');?></option>
                                    <option value="lessons"><?php esc_html_e('Lessons', 'dt_themes');?></option>
                                </select>
                            </div>
                            <div class="hr_invisible"> </div>
                        </div>
                        
					</div>
                    
					<div class="row-attachments hide">
						<div class="column one-third"><?php esc_html_e('Attachments', 'dt_themes');?></div>
						<div class="column two-third last">
							<fieldset>
								<label for="attachments"><input type="checkbox" value="0" id="attachments" name="attachments"><?php esc_html_e('Import attachments', 'dt_themes');?></label>
								<p class="description"><?php esc_html_e('Download all attachments from the demo may take a while. Please be patient.', 'dt_themes');?></p>
							</fieldset>
						</div>
						<div class="hr_invisible"> </div>
					</div>
                    <div class="column one-column">
						<div class="hr_invisible"> </div>
						<div class="column one-third">&nbsp;</div>
						<div class="column two-third last">
		                    <a href="#" class="dttheme-import-button bpanel-button black-btn" title="<?php esc_html_e('Import demo data', 'dt_themes');?>"><?php esc_html_e('Import demo data', 'dt_themes');?></a>
                        </div>
                    </div>
                    <div class="hr"></div>
                </div><!-- .box-content -->
            </div><!-- .bpanel-box end -->            
        </div><!--#tab1-import-demo end-->

    </div><!-- .bpanel-main-content end-->
</div><!-- #import end-->