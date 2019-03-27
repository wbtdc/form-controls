<?php
namespace Wbtdc\FormControls;
  
class FormControls {
    const REQUIRED = "/required/";
    const STRONG = '<strong> * </strong>';
    const CLS = 'class';
    const OPTSCB = 'optionsCallback';
    const VALCB = 'valueCallback';
    const DEFAULT = 'default';
    const VAL = 'validation';
    
    public function __construct() {
        add_action('admin_print_styles', array($this, 'printStyles'));
    }
    
    public function update_option_setting($option, $newval) {
        update_option($option, $newval);
    }

    public function printStyles() { 
        ?>
    	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>		
		<?php     
    }
    public function display_switch($args) {
        $name = $args['name'];

        $value = $args[self::VALCB][0]->{$args[self::VALCB][1]}($args[self::VALCB][2]);
        $checked = $value === 'on' ? ' checked' : '';
        $label = $args['label-text'] ? $args['label-text'] : '';
        $onText = $args['on-text'] ? $args['on-text'] : 'On';
        $offText = $args['off-text'] ? $args['off-text'] : 'Off';   
        $colClass = array_key_exists('colClass', $args) ? $args['colClass'] : 'col-m-12';
        $helpText = $args['help-text'];
        
        ?>
        <div class="row">
        <?php if ($label !== '') { ?>
        	<div class="<?php echo $colClass;?> labelCol">
				<label for="<?php echo $name;?>"><?php echo $label;?></label>   		        		
        	</div>
        <?php }?>
        	<div class="<?php echo $colClass;?> fieldCol">
    			<label style="margin-left:20px;"><?php echo $offText;?></label> 
        		<div style="position:relative;top:-5px;display:inline-block;margin-left:10px;" class="custom-control custom-switch">
  					<input type="checkbox" class="wbtdcSwitch custom-control-input <?php echo $args[self::CLS];?>" id="<?php echo $name;?>" name="<?php echo $name;?>"  <?php echo $checked;?>/>
					<label class="custom-control-label" for="<?php echo $name;?>"></label>
				</div>
				<label><?php echo $onText;?></label>
				<?php if ($helpText) {
				    $helpText = preg_replace("/(?<=\\\)'/", "\\\'", $helpText);
				    error_log("PregReplace ended up with $helpText");
				    echo "<i style='margin-left:10px;' class='fas fa-question-circle' title='$helpText'></i>";
				}?>
        	</div>
        </div>

    <?php 
    }
    public function display_colorpicker($args) {
        $name = $args['name'];
        $value = get_option($name, '');
        $value = $value != '' ? $value : $args[self::DEFAULT];
        $req = array_key_exists (self::VAL, $args) && preg_match(self::REQUIRED, $args[self::VAL]) ? self::STRONG : '';
        $validation = array_key_exists(self::VAL, $args) ? $args[self::VAL] : '';    
        ?>
        	<?php if (array_key_exists('label-text', $args)) { ?>
        		<label for="<?php echo $name;?>"><?php echo $args['label-text'];?></label>        		
        	<?php }
        	echo $req;?><input id="<?php echo $name;?>" type="text" name="<?php echo $name;?>" class="colorpicker" value="<?php echo $value;?>" <?php echo $validation;?>/>
        <?php 
    }
    public function display_textarea($args) {
        $type = $args['name'];
        $tinymce = array_key_exists('tinymce', $args) ? $args['tinymce'] : true; 
        $settings = array(
            'media_buttons' => false,
            'textarea_rows' => 10,
            'textarea_name' => $type,
            'tinymce' => $tinymce
        );
        $content = $args['content'];
        if ($content == '' && array_key_exists(self::DEFAULT, $args)) {
            $content = $args[self::DEFAULT];
        }
        ?>
        <div class="row"> 
    	<?php 
        if (array_key_exists('label-text', $args)) { ?>
        	<div class="col-sm">
    			<label for="<?php echo $type;?>"><?php echo $args['label-text'];?></label>
    		</div>        		
    	<?php 
        } ?>
			<div class="col-sm">
			<?php wp_editor($content, $type, $settings);?>
			</div>
		</div>
		<?php 
    }
    /*public function display_img($args) { 
        $type = $args['name'];
        $img = get_option($type, null);
        $images = explode(',', $img);
        $img_html = '';
        $template = $this->img_template();
        if ($img) {
            foreach ($images as $img_id) {
                $url = wp_get_attachment_image_src($img_id, 'thumbnail')[0];  
                $tmp = $template;
                $tmp = preg_replace("/\[img_id\]/", $img_id, $tmp);
                $tmp = preg_replace("/\[img_url\]/", $url, $tmp);
                $tmp = preg_replace("/\[type\]/", $type, $tmp);
                $img_html .= $tmp;
            }
        }
        $req = array_key_exists (self::VAL, $args) && preg_match(self::REQUIRED, $args[self::VAL]) ? self::STRONG : '';
        $validation = array_key_exists(self::VAL, $args) ? $args[self::VAL] : '';

        if (array_key_exists('label-text', $args)) { ?>
    		<label for="<?php echo $type;?>"><?php echo $args['label-text'];?></label>        		
    	<?php } ?>
    	<div class="image_holder" id="<?php echo $type;?>_image_holder"><?php echo $img_html; ?></div>
        <?php echo $req;?><input type="hidden" name="<?php echo $type;?>" id="<?php echo $type;?>_image_hidden" value="<?php echo $img;?>" <?php echo $validation;?>/>
        <input type="hidden" id="img_template" value='<?php echo $template;?>'/>
    <?php 
    }*/
    public function display_input ($args) {
        $name = $args['name'];
        $req = array_key_exists (self::VAL, $args) && preg_match(self::REQUIRED, $args[self::VAL]) ? self::STRONG : '';        
        $type = array_key_exists('type', $args) ? $args['type'] : 'text';
        $colClass = array_key_exists('colClass', $args) ? $args['colClass'] : 'col-m-12';
        $value = null;
        if (array_key_exists(self::VALCB, $args)) {
            $value = $args[self::VALCB][0]->{$args[self::VALCB][1]}($args[self::VALCB][2]);
        }
        if (!$value && array_key_exists(SELF::DEFAULT, $args)) {
            $value = $args[SELF::DEFAULT];
        }
        $validation = array_key_exists(self::VAL, $args) ? $args[self::VAL] : '';
        if (array_key_exists('label-text', $args)) { ?>
        <div class="row">
            <div class="<?php echo $colClass;?> labelCol">
        		<label class="formLabel" style="display:inline;" for="<?php echo $type;?>"><?php echo $args['label-text'];?></label>
        	</div>        		
        	<?php } ?>
        	<div class="<?php echo $colClass;?> fieldCol" style="text-align:right;">
            <?php  echo $req;?><input style="<?php echo $args['style'];?>;display:inline;" class="<?php echo $name;?> form-control <?php echo array_key_exists(self::CLS, $args) ? $args[self::CLS] : '';?>" type="<?php echo $type; ?>" name="<?php echo $name;?>" value="<?php echo $value;?>" <?php echo $validation;?>/>
             </div>
         </div>
        <?php 
    }
    public function display_select($args) {
        $name = $args['name'];
        $req = array_key_exists (self::VAL, $args) && preg_match(self::REQUIRED, $args[self::VAL]) ? self::STRONG : '';
        $validation = array_key_exists(self::VAL, $args) ? $args[self::VAL] : '';
        $colClass = array_key_exists('colClass', $args) ? $args['colClass'] : 'col-m-12';
        
        ?>
        <div class="row"> 
            <?php 
            if (array_key_exists('label-text', $args)) { ?>                
            	<div class="<?php echo $colClass;?>">
    				<label for="<?php echo $name;?>"><?php echo $args['label-text'];?></label>        		       	
            	</div>
        		<?php 
            }
            ?>
    		<div class="<?php echo $colClass;?>">
            	<?php echo $req;?>&nbsp;<select style="<?php echo $args['style'];?>" class="form-control <?php echo $args[self::CLS];?>" name="<?php echo $name;?>" <?php echo $validation;?>>
        		<?php 
            		  $method = $args[self::VALCB][1];
        
            		  $oCallObj = $args[self::OPTSCB][0];
            		  $method = $args[self::OPTSCB][1];
            		  $oArgs = $args[self::OPTSCB][2];
            		  
            		  $options = $oCallObj->$method($oArgs);
            		  
            		  echo $options;
                    ?>   
                  </select>		
    		</div>
        </div><!-- end div.row -->

    	<?php 
    }
}