<?php
defined('WP_PLUGIN_URL') or die('Restricted access');
if (!current_user_can('publish_posts')) wp_die( __('You do not have sufficient permissions to access this page.') );

#$object = new WordPress_Plugin_Model(null, null, 'edit', $_GET['id']);
$object = new WordPress_Plugin_Model(null, null, $_GET['action'], $_GET['id']);

?>
<div class="wrap wprmm">
  <div id="icon-tools" class="icon32"></div><h2 class="left">Edit <?php echo $object->name;?></h2>
  <div class="clear"></div>
  <hr />

  <?php #wprmm_get_help(array('main'=>true));?>

  <form method="post" action="<?php echo $object->form_post_url;?>">
    <table class="form-table">    
      <tbody>

        <?php foreach($object->structure as $field): ?>
          <?php $label = ucwords(str_replace(array('_'), array(' '), $field->Field));?>
          <?php $class = strtolower($field->Field);?>
          <tr valign="top">
             
            <!-- Label -->
            <th scope="row">
              <label for="<?php echo $class;?>"><?php echo $label;?></label>
            </th>


            <!-- Field Edit with dynamic type -->
            <td>
              <!-- Not Editable -->
              <?php if(in_array($field->Field, array('id', 'updated_at'))):?>
                <input name="<?php echo "$object->class_name[$field->Field]";?>" type="text" id="<?php echo $class;?>" value="<?php echo $object->get_val($field->Field);?>" class="regular-text" readonly="readonly" />


              <!-- Rich Text -->
              <?php elseif($field->Type == "text"): ?>

                  <?php if(function_exists('wp_editor')): ?>
                    <?php wp_editor($object->get_val($field->Field), "$object->class_name[$field->Field]", array('textarea_rows'=>5)); ?>
                  <?php else: ?>
                    <textarea name="<?php echo "$object->class_name[$field->Field]";?>" id="<?php echo $class;?>"><?php echo $class;?></textarea>
                 <?php endif;?>


              <!-- Boolean -->
              <?php elseif($field->Type == "tinyint(1)"): ?>
                <input type="checkbox" name="<?php echo "$object->class_name[$field->Field]"; ?>" value="1" <?php echo ($object->get_val($field->Field) == 1)? "checked" : ""; ?> />



              <!-- String and default -->
              <?php else: ?>
                <input name="<?php echo "$object->class_name[$field->Field]"; ?>" type="text" id="<?php echo $class;?>" value="<?php echo $object->get_val($field->Field);?>" class="regular-text">
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>


    <p class="submit">
      <a href="<?php echo $object->admin_url;?>" class="button-secondary">&laquo; Back</a>
      <input type="hidden" name="<?php echo "$object->class_name[action]";?>" value="<?php echo "$this->action"; ?>" />
      <input type="hidden" name="wppb_model_name" value="<?php echo "$this->class_name"; ?>" />
      <input type="submit" name="<?php echo "$object->class_name[submit]"; ?>" id="submit" class="button-primary" value="Save" />
      
    </p>

  </form>



</div>      
