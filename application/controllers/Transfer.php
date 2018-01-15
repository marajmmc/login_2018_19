<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function index()
    {
        /*$this->load->dbforge();
        $tables = $this->db->list_tables();
        foreach ($tables as $i=>$table)
        {
            $this->dbforge->rename_table($table, 'ems_'.$table);
        }*/
    }
    private function insert($table_name,$data)
    {
        $this->db->insert($table_name,$data);
        $id=$this->db->insert_id();
        if($id>0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function users()
    {
        $source_tables=array(
            'setup_user'=>'arm_login.setup_user',
            'setup_user_info'=>'arm_login.setup_user_info',
            'setup_user_area'=>'arm_ems.ems_system_assigned_area',
            'setup_users_other_sites'=>'arm_login.setup_users_other_sites',
            'setup_users_company'=>'arm_login.login_setup_users_company'
        );
        $destination_tables=array(
            'setup_user'=>$this->config->item('table_login_setup_user'),
            'setup_user_info'=>$this->config->item('table_login_setup_user_info'),
            'setup_user_area'=>$this->config->item('table_login_setup_user_area'),
            'setup_users_other_sites'=>$this->config->item('table_login_setup_users_other_sites'),
            'setup_users_company'=>$this->config->item('table_login_setup_users_company')
        );

        $users=Query_helper::get_info($source_tables['setup_user'],'*',array());

        $results=Query_helper::get_info($source_tables['setup_user_info'],'*',array('revision=1'));
        $user_infos=array();
        foreach($results as $result)
        {
            $user_infos[$result['user_id']]=$result;
        }

        $results=Query_helper::get_info($source_tables['setup_user_area'],'*',array('revision=1'));
        $user_areas=array();
        foreach($results as $result)
        {
            $user_areas[$result['user_id']]=$result;
        }

        $results=Query_helper::get_info($source_tables['setup_users_company'],'*',array('revision=1'));
        $user_companies=array();
        foreach($results as $result)
        {
            $user_companies[$result['user_id']][]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($users as $user)
        {
            if($user['id']==1)
            {
                $user['password']=md5("Arm!@#$");
            }

            if(!($this->insert($destination_tables['setup_user'],$user)))
            {
                $this->db->trans_complete();
                echo 'Failed';
                exit();
            }
            else
            {
                $data_user_info=array();
                if(isset($user_infos[$user['id']]))
                {
                    $data_user_info=$user_infos[$user['id']];
                    unset($data_user_info['id']);
                    if($data_user_info['picture_profile']!==null)
                    {
                        $data_user_info['image_name']=basename($data_user_info['picture_profile']);
                        $data_user_info['image_location']=str_replace('http://50.116.76.180/login/','',$data_user_info['picture_profile']);
                    }
                    unset($data_user_info['picture_profile']);
                }
                else
                {
                    $data_user_info['user_id']=$user['id'];
                    $data_user_info['revision']=1;
                }

                if(!($this->insert($destination_tables['setup_user_info'],$data_user_info)))
                {
                    $this->db->trans_complete();
                    echo 'Failed';
                    exit();
                }
                //HAVE TO CHECK
                $data_user_area=array();
                if(isset($user_areas[$user['id']]))
                {
                    $data_user_area=$user_areas[$user['id']];
                    unset($data_user_area['id']);
                }
                else
                {
                    $data_user_area['user_id']=$user['id'];
                    $data_user_area['revision']=1;
                }  //HAVE TO CHECK
                if(!($this->insert($destination_tables['setup_user_area'],$data_user_area)))
                {
                    $this->db->trans_complete();
                    echo 'Failed';
                    exit();
                }

                if(isset($user_companies[$user['id']]))
                {
                    $data_user_companies_array=$user_companies[$user['id']];

                    foreach($data_user_companies_array  as $data_user_companies)
                    {
                        unset($data_user_companies['id']);
                        if(!($this->insert($destination_tables['setup_users_company'],$data_user_companies)))
                        {
                            $this->db->trans_complete();
                            echo 'Failed';
                            exit();
                        }
                    }
                }
            }
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success';
        }
        else
        {
            echo 'Failed';
        }
    }

    public function customers()
    {
        $source_tables=array(
            'ems_customers'=>'arm_ems.ems_csetup_customers'
        );
        $destination_tables=array(
            'customers'=>$this->config->item('table_login_csetup_customer'),
            'customers_info'=>$this->config->item('table_login_csetup_cus_info')
        );

        $results=Query_helper::get_info($source_tables['ems_customers'],'*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            $data=array();
            $data['id']=$result['id'];
            $data['status']=$result['status'];
            $data['date_created']=$result['date_created'];
            $data['user_created']=1;
            $customer_result=$this->db->insert($destination_tables['customers'],$data);
            //$customer_id = $this->db->insert_id();

            if(!$customer_result)
            {
                $this->db->trans_complete();
                echo 'failed';
                die();
            }
            else
            {
                $data=array();
                $data['customer_id']=$result['id'];
                $data['name']=$result['name'];

                if(strtolower($result['type'])=='outlet')
                {
                    $data['type']=1;
                }
                else if(strtolower($result['type'])=='customer')
                {
                    $data['type']=2;
                }
                else
                {
                    $data['type']=null;
                }

                if(strtolower($result['incharge'])=='arm')
                {
                    $data['incharge']=1;
                }
                else if(strtolower($result['incharge'])=='customer')
                {
                    $data['incharge']=2;
                }
                else
                {
                    $data['incharge']=null;
                }

                $data['name_short']=$result['name_short'];
                $data['district_id']=$result['district_id'];
                $data['customer_code']=$result['customer_code'];
                $data['credit_limit']=$result['credit_limit'];
                $data['name_owner']=$result['name_owner'];
                $data['name_market']=$result['name_market'];
                $data['address']=$result['address'];
                $data['phone']=$result['phone'];
                $data['email']=$result['email'];
                $data['status_agreement']=$result['status_agreement'];
                $data['ordering']=$result['ordering'];
                $data['date_created']=$result['date_created'];
                $data['user_created']=1;
                $data['old_cs_id']=$result['old_cs_id'];
                $this->db->insert($destination_tables['customers_info'],$data);
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
        }
        /*
         customer in-charge update query
                UPDATE
        arm_login_2018_19.login_csetup_customer_info cci
        INNER JOIN arm_ems.ems_csetup_customers ecc ON ecc.id=cci.id
        SET cci.incharge=CASE WHEN ecc.incharge='Arm' THEN 1 WHEN ecc.incharge='Customer' THEN 2 END
         * */
    }

    public function variety()
    {
        $source_tables=array(
            'varieties'=>'arm_ems.ems_varieties',
            'varietiy_price_kg'=>'arm_ems.ems_variety_price_kg'
        );
        $destination_tables=array(
            'varieties'=>$this->config->item('table_login_setup_classification_varieties'),
            'variety_principals'=>$this->config->item('table_login_setup_variety_principals')
        );
        $varieties_kg_price=array();
        $results=Query_helper::get_info($source_tables['varietiy_price_kg'],'*',array());
        foreach($results as $result)
        {
            $varieties_kg_price[$result['variety_id']]=$result['price_net'];
        }
        $results=Query_helper::get_info('arm_ems.ems_varieties','*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            $principal_id=$result['principal_id'];
            $name_import=$result['name_import'];
            unset($result['principal_id']);
            unset($result['name_import']);
            
            if(strtolower($result['hybrid'])=='f1 hybrid')
            {
                $result['hybrid']=1;
            }
            else if(strtolower($result['hybrid'])=='op')
            {
                $result['hybrid']=2;
            }

            if(isset($varieties_kg_price[$result['id']]))
            {
                $result['price_kg']=$varieties_kg_price[$result['id']];
                $result['revision_price_kg']=1;
            }

            if(!($this->insert($destination_tables['varieties'],$result)))
            {
                $this->db->trans_complete();
                echo 'Failed';
                exit();
            }
            else
            {
                if($principal_id>0)
                {
                    $data=array();
                    $data['variety_id']=$result['id'];
                    $data['principal_id']=$principal_id;
                    $data['name_import']=$name_import;
                    $data['date_created']=$result['date_created'];
                    $data['user_created']=$result['user_created'];
                    if(!($this->insert($destination_tables['variety_principals'],$data)))
                    {
                        $this->db->trans_complete();
                        echo 'Failed';
                        exit();
                    }
                }
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success';
        }
        else
        {
            echo 'Failed';
        }
    }

    public function user_role_transfer()
    {
        $source_tables=array
        (
            'system_task'=>$this->config->item('table_system_task'),
            'system_other_sites'=>$this->config->item('table_login_system_other_sites')
        );
        $destination_tables=array
        (
            'user_group_role'=>$this->config->item('table_system_user_group_role'),
            'setup_users_other_sites'=>$this->config->item('table_login_setup_users_other_sites')
        );

        $this->db->trans_start();  //DB Transaction Handle START

        $results=Query_helper::get_info($source_tables['system_task'],'*',array("type ='TASK'"));
        foreach($results as $result)
        {
            $data=array();
            $data['user_group_id']=1;
            $data['task_id']=$result['id'];
            $data['action0']=1;
            $data['action1']=1;
            $data['action2']=1;
            $data['action3']=1;
            $data['action4']=1;
            $data['action5']=1;
            $data['action6']=1;
            $data['revision']=1;
            $data['date_created']=$result['date_created'];
            $data['user_created']=$result['user_created'];
            if(!(Query_helper::add($destination_tables['user_group_role'],$data, false)))
            {
                $this->db->trans_complete();
                echo 'Failed';
                exit();
            }
            if($result['id']!=2)
            {
                $data=array();
                $data['user_group_id']=2;
                $data['task_id']=$result['id'];
                $data['action0']=1;
                $data['action1']=1;
                $data['action2']=1;
                $data['action3']=1;
                $data['action4']=1;
                $data['action5']=1;
                $data['action6']=1;
                $data['revision']=1;
                $data['date_created']=$result['date_created'];
                $data['user_created']=$result['user_created'];
                if(!(Query_helper::add($destination_tables['user_group_role'],$data, false)))
                {
                    $this->db->trans_complete();
                    echo 'Failed';
                    exit();
                }
            }
        }

        $results=Query_helper::get_info($source_tables['system_other_sites'],'*',array());
        foreach($results as $result)
        {
            $data=array();
            $data['user_id']=1;
            $data['site_id']=$result['id'];
            $data['revision']=1;
            $data['date_created']=time();
            $data['user_created']=1;
            if(!(Query_helper::add($destination_tables['setup_users_other_sites'],$data, false)))
            {
                $this->db->trans_complete();
                echo 'Failed';
                exit();
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success';
        }
        else
        {
            echo 'Failed';
        }
    }

    /*public function clean_user_images()
    {
        $folder=FCPATH.'images/profiles/';
        $counter_deleted=0;
        if(file_exists($folder))
        {
            $results=Query_helper::get_info($this->config->item('table_login_setup_user_info'),array('user_id','image_location'),array('revision=1'));
            foreach($results as $result)
            {
                if(file_exists($folder.$result['user_id']))
                {
                    $folder_contents=scandir($folder.$result['user_id']);
                    foreach($folder_contents as $content)
                    {
                        if($folder.$result['user_id'].'/'.$content=='.')
                        {
                            continue;
                        }
                        if($folder.$result['user_id'].'/'.$content=='..')
                        {
                            continue;
                        }
                        if(is_dir($folder.$result['user_id'].'/'.$content))
                        {
                            continue;
                        }
                        if($folder.$result['user_id'].'/'.$content!=FCPATH.$result['image_location'])
                        {
                            unlink($folder.$result['user_id'].'/'.$content);
                            $counter_deleted++;
                        }
                    }
                }
            }
            echo $counter_deleted.' files has been deleted.';
        }
        else
        {
            echo 'Expected folder is not exists.';
        }
    }*/

    /*public function copy_profile_image($limit=0,$start=0)
    {

        $remote_server_url=$this->config->item('system_base_url_profile_picture');
        $source_file='';
        $folder_location='';
        $create_file='';
        $results=Query_helper::get_info($this->config->item('table_login_setup_user_info'),array('user_id','image_name','image_location'),array('revision=1'),$limit,$start);
        if(empty($results))
        {
            echo "Completely transfer profile images.";
            die();
        }

        foreach($results as $result)
        {
            $source_file = $remote_server_url.$result['image_location'];
            $folder_location = $_SERVER['DOCUMENT_ROOT'] . '/login_2018_19/images/profiles/'.$result['user_id'].'/';
            $create_file = $folder_location .$result['image_name'];
            if (!file_exists($folder_location))
            {
                mkdir($folder_location, 0777, true);
            }
            $url=@getimagesize($source_file);
            if(!is_array($url))
            {
                echo "invalid image: $source_file<br />";
            }
            else
            {
                echo "<img src='".$source_file."'> $source_file<br />";
                if(!file_exists($create_file))
                {
                    if ( copy($source_file, $create_file) )
                    {
                        echo "Copy success!";
                    }
                    else
                    {
                        echo "Copy failed.";
                    }
                }
                else
                {
                    echo "Already file exist";
                }
            }

        }

    }*/
}
