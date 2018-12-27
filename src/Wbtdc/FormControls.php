<?php
namespace Wbtdc;
  
class FormControls {
    private static $instance;
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Form_Controls();
        }
        return self::$instance;
    }
    private function __construct() {
        
    }
    
    public function update_option_setting($option, $oldval, $newval) {
        update_option($option, $newval);
    }
    public function display_colorpicker($args) {
        $name = $args['name'];
        $value = get_option($name, '');
        $value = $value != '' ? $value : $args['default'];
        $req = array_key_exists ('validation', $args) && preg_match("/required/", $args['validation']) ? '<strong> * </strong>' : '';
        $validation = array_key_exists('validation', $args) ? $args['validation'] : '';
    
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
        if ($content == '') {
            if (array_key_exists('default', $args)) {
                $content = $args['default'];
            }
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
        $req = array_key_exists ('validation', $args) && preg_match("/required/", $args['validation']) ? '<strong> * </strong>' : '';
        $validation = array_key_exists('validation', $args) ? $args['validation'] : '';
    ?>
        <div class="image_holder" id="<?php echo $type;?>_image_holder"><?php echo $img_html; ?></div>
        <?php echo $req;?><input type="hidden" name="<?php echo $type;?>" id="<?php echo $type;?>_image_hidden" value="<?php echo $al_funnel_img;?>" <?php echo $validation;?>/>
        <input type="hidden" id="al_funnel_img_template" value='<?php echo $template;?>'/>
    <?php 
    } 
    public function display_input ($args) {
        $name = $args['name'];
        $req = array_key_exists ('validation', $args) && preg_match("/required/", $args['validation']) ? '<strong> * </strong>' : '';        
        $type = array_key_exists('type', $args) ? $args['type'] : 'text';
        
        $value = $args['valueCallback'][0]->{$args['valueCallback'][1]}($args['valueCallback'][2]);
        $validation = array_key_exists('validation', $args) ? $args['validation'] : '';
        ?>
    	    <?php echo $req;?><input style="<?php echo $args['style'];?>" class="<?php echo $name;?> form-control <?php echo $args['class'];?>" type="<?php echo $type; ?>" name="<?php echo $name;?>" value="<?php echo $value;?>" <?php echo $validation;?>/>
        <?php 
    }
    public function display_select($args) {
        $name = $args['name'];
        $req = array_key_exists ('validation', $args) && preg_match("/required/", $args['validation']) ? '<strong> * </strong>' : '';
        $validation = array_key_exists('validation', $args) ? $args['validation'] : '';
        ?>
        	<?php echo $req;?><select style="<?php echo $args['style'];?>" class="form-control <?php echo $args['class'];?>" name="<?php echo $name;?>" <?php echo $validation;?>>
        		<?php 
        		  $vCallObj = $args['valueCallback'][0];
        		  $method = $args['valueCallback'][1];
        		  $cbArgs = $args['valueCallback'][2];
        		  $value = $vCallObj->$method($cbArgs);

        		  $oCallObj = $args['optionsCallback'][0];
        		  $method = $args['optionsCallback'][1];
        		  $oArgs = $args['optionsCallback'][2];
        		  
        		  $options = $oCallObj->$method($oArgs);
        		  
        		  echo $options;
                  ?>
        	</select><?php 
    }
}