<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$user=User_helper::get_user();
$CI = & get_instance();
//$sites=User_helper::get_accessed_sites();

?>
<div class="row widget">
    <?php
    if($user->user_group==0)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_NOT_ASSIGNED_GROUP');?></h3>

        </div>
    <?php
    }
    ?>
    <?php
    if($CI->is_site_offline())
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_SITE_OFFLINE');?></h3>
        </div>
    <?php
    }
    ?>
    <div class="col-sm-12 ">
        <!--<h1><?php /*echo $user->name;*/?></h1>
        <img style="max-width: 250px;" src="<?php /*echo $CI->config->item('system_base_url_profile_picture').$user->image_location; */?>" alt="<?php /*echo $user->name; */?>">-->

        <div class="jumbotron">
            <div class="row">
                <div class="col-md-4 col-xs-12 col-sm-6 col-lg-4">
                    <img class="img img-responsive img-thumbnail" src="<?php echo $CI->config->item('system_base_url_profile_picture').$user->image_location; ?>" alt="<?php echo $user->name; ?>" class="img">
                </div>
                <div class="col-md-8 col-xs-12 col-sm-6 col-lg-8">
                    <div class="container" style="border-bottom:1px solid black">
                        <h2><?php echo $user->name;?></h2>
                    </div>
                    <hr>
                    <ul class="container details">
                        <li><p><span class="glyphicon glyphicon-earphone one" style="width:50px;"></span><?php echo !empty($user->mobile_no)?$user->mobile_no:'Mobile number not set'?></p></li>
                        <li><p><span class="glyphicon glyphicon-envelope one" style="width:50px;"></span><?php echo !empty($user->email)?$user->email:'Email not set'?></p></li>
                        <li><p><span class="glyphicon glyphicon-user one" style="width:50px;"></span><?php echo !empty($user->designation)?$user->designation:'Designation not set'?></p></li>
                        <li>
                            <p>
                                <a href="<?php echo base_url()?>profile_info/index/details/<?php echo $user->id;?>" class="btn btn-success">
                                    <span class="glyphicon glyphicon-edit one"></span>
                                    Profile View
                                </a>
                                <a href="<?php echo base_url()?>profile_password/" class="btn btn-success">
                                    <span class="glyphicon glyphicon-edit one"></span>
                                    Change Password
                                </a>
                                <a href="<?php echo base_url()?>profile_picture/index/edit/<?php echo $user->id;?>" class="btn btn-success">
                                    <span class="glyphicon glyphicon-edit one"></span>
                                    Change Profile Picture
                                </a>
                            </p>
                        </li>
                    </ul>
                    <style>
                        .details li
                        {
                            list-style: none;
                        }
                    </style>
                </div>
            </div>
        </div>

    </div>
    <?php
        /*if(sizeof($sites)>0)
        {
            */?><!--
            <div class="widget-header">
                <div class="title">
                    Other Sites
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
/*            foreach($sites as $site)
            {
                */?>
                <div style="" class="row show-grid">
                    <div class="col-xs-12">
                        <label class="control-label"><?php /*echo strtoupper($site['short_name']);*/?></label>- - -
                        <a class="external" target="_blank" href="<?php /*echo site_url('other_sites_visit/visit_site/'.$site['id']); */?>">Visit Site</a>
                    </div>
                </div>
                --><?php
/*            }
        }*/
    ?>

</div>
<div class="clearfix"></div>
