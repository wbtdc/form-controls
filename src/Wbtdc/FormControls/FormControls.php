<?php
namespace Wbtdc\FormControls;
  
class FormControls {
    const REQUIRED = "/required/";
    const STRONG = '<strong> * </strong>';
    const CLS = 'class';
    const OPTSCB = 'optionsCallback';
    const VALCB = 'valueCallback';
    const DEF = 'default';
    const VAL = 'validation';
    
    public function __construct() {
        add_action('admin_print_styles', array($this, 'printStyles'));
    }
    
    public function update_option_setting($option, $newval) {
        update_option($option, $newval);
    }

    public function printStyles() { 
        ?>
    	<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->    
    	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
		<?php     
    }
    public function display_switch($args) {
        $name = $args['name'];

        $value = $args[self::VALCB][0]->{$args[self::VALCB][1]}($args[self::VALCB][2]);
        error_log("Display switch got value $value");
        $checked = $value === 'on' ? ' checked' : '';
        ?>
        <div class="custom-control custom-switch">
  			<input type="checkbox" class="custom-control-input <?php echo $args[self::CLS];?>" id="<?php echo $name;?>" name="<?php echo $name;?>"  <?php echo $checked;?>/>
  			<label class="custom-control-label" for="<?php echo $name;?>"><?php echo $args['label-text'];?></label>
		</div>
    <?php 
    }
    public function display_colorpicker($args) {
        $name = $args['name'];
        $value = get_option($name, '');
        $value = $value != '' ? $value : $args[self::DEF];
        $req = array_key_exists (self::VAL, $args) && preg_match(self::REQUIRED, $args[self::VAL]) ? self::STRONG : '';
        $validation = array_key_exists(self::VAL, $args) ? $args[self::VAL] : '';
    
        ?>
        	<?php echo $req;?><input id="<?php echo $name;?>" type="text" name="<?php echo $name;?>" class="al_funnel_colorpicker" value="<?php echo $value;?>" <?php echo $validation;?>/>
        <?php 
    }
    public function display_textarea($args) {
        $type = $args['name'];
        $settings = array(
            'media_buttons' => false,
            'textarea_rows' => 10,
            'textarea_name' => $type
        );
        $content = get_option($type, '');
        if ($content == '' && array_key_exists(self::DEF, $args)) {
            $content = $args[self::DEF];
        }
        wp_editor($content, $type, $settings);
    }
    public function display_img($args) { 
        $type = $args['name'];
        $al_funnel_img = get_option($type, null);
        $al_funnel_images = explode(',', $al_funnel_img);
        $img_html = '';
        $template = $this->al_funnel_img_template();
        if ($al_funnel_img) {
            foreach ($al_funnel_images as $img_id) {
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
    ?>
        <div class="image_holder" id="<?php echo $type;?>_image_holder"><?php echo $img_html; ?></div>
        <?php echo $req;?><input type="hidden" name="<?php echo $type;?>" id="<?php echo $type;?>_image_hidden" value="<?php echo $al_funnel_img;?>" <?php echo $validation;?>/>
        <input type="hidden" id="al_funnel_img_template" value='<?php echo $template;?>'/>
    <?php 
    } 
    public function display_input ($args) {
        $name = $args['name'];
        $req = array_key_exists (self::VAL, $args) && preg_match(self::REQUIRED, $args[self::VAL]) ? self::STRONG : '';        
        $type = array_key_exists('type', $args) ? $args['type'] : 'text';
        
        $value = null;
        if (array_key_exists(self::VALCB, $args)) {
            $value = $args[self::VALCB][0]->{$args[self::VALCB][1]}($args[self::VALCB][2]);
        }
        $validation = array_key_exists(self::VAL, $args) ? $args[self::VAL] : '';
        ?>
    	    <?php echo $req;?><input style="<?php echo $args['style'];?>" class="<?php echo $name;?> form-control <?php echo $args[self::CLS];?>" type="<?php echo $type; ?>" name="<?php echo $name;?>" value="<?php echo $value;?>" <?php echo $validation;?>/>
        <?php 
    }
    public function display_select($args) {
        $name = $args['name'];
        $req = array_key_exists (self::VAL, $args) && preg_match(self::REQUIRED, $args[self::VAL]) ? self::STRONG : '';
        $validation = array_key_exists(self::VAL, $args) ? $args[self::VAL] : '';
        ?>
    	<?php echo $req;?><select style="<?php echo $args['style'];?>" class="form-control <?php echo $args[self::CLS];?>" name="<?php echo $name;?>" <?php echo $validation;?>>
    		<?php 
    		  $method = $args[self::VALCB][1];

    		  $oCallObj = $args[self::OPTSCB][0];
    		  $method = $args[self::OPTSCB][1];
    		  $oArgs = $args[self::OPTSCB][2];
    		  
    		  $options = $oCallObj->$method($oArgs);
    		  
    		  echo $options;
              ?>
    	</select><?php 
    }
}