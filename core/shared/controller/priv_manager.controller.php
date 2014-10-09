<?php
	#TODO remove password from List, Edit
	#add action: list no pass, add action edit no pass.

	#Title: Untitled App	
	#Author: f3l	
	#Generated Date: 2006-05-16 15:52:06	
	#Description: f3l	
	#Generator Version: 0.2	
	
class priv_manager_model extends std{
		function create_user($user_name,$email,$password){
			#this function creates a user.
			#it does no checking whatsoever, so be carefull on what you send to it.
			#double check everything.
			#password should not be encrypted.
			global $i18n;
			$password=md5($password);
			$this->log("<br/>".$i18n['priv_manager']['created_user'].$user_name,'PRIV');
			$this->sql("INSERT INTO {$this->table_prefix}usr VALUES(0,'$user_name','$email','$password',0,'2000-01-01 :00:00:00','".date('Y-m-d H:i:s')."','".$this->get_ip()."')");
			return($this->last_id());
		}

		function create_role($role_name){
			#this function creates a role.
			global $i18n;
			$this->log("<br/>".$i18n['priv_manager']['created_role'].$role_name,'PRIV');
			$this->sql("INSERT INTO {$this->table_prefix}role VALUES(0,'$role_name')");
			return($this->last_id());
		}
		function find_role($role_name){
			#this function find's a role, with a given name.
			global $i18n;
			$l=$this->q2obj("SELECT id FROM {$this->table_prefix}role WHERE name='$role_name'");
			return($l[0]['id']);
		}
		
		function create_privilege($privilege_name,$action){
			#this function creates a privilege.
			global $i18n;
			$this->log("<br/>".$i18n['priv_manager']['created_privilege'].$privilege_name,'PRIV');
			$this->sql("INSERT INTO {$this->table_prefix}privilege VALUES(0,'$privilege_name','$action')");
			return($this->last_id());
		}

		function allow($privilege_id,$role_id){
			#this function gives a centarin user PRIV_ID to a certain role RID.
			global $i18n;
			$this->log("<br/>".$i18n['priv_manager']['allowed'].$privilege_id.":".$role_id,'PRIV');
			$this->sql("INSERT INTO {$this->table_prefix}role2priv VALUES(0,'$role_id','$privilege_id')");
			return($this->last_id());
		}

		function allow_by_action_name($privilege_name,$role_name){
			#this function gives a centarin user PRIV_ID to a certain role RID.
			global $i18n;
			$this->log("<br/>".$i18n['priv_manager']['allowed'].$privilege_name.":".$role_name,'PRIV');
			$role = $this->q2obj("SELECT id FROM {$this->table_prefix}role WHERE name='".$this->remove_strange_chars($role_name)."'");
			$priv = $this->q2obj("SELECT id FROM {$this->table_prefix}privilege WHERE action='".$this->remove_strange_chars($privilege_name)."'");

			if(count($role) == 0){
				echo('Missing Role:'.$role_name);
			}
			if(count($priv) == 0){
				echo('Missing privilege:'.$privilege_name);
			}
			$role_id=$role[0]['id'];
			$priv_id=$priv[0]['id'];
			return($this->allow($priv_id,$role_id));
		}
		function grant_by_name($user_name,$role_name){
			#this function gives a centarin user PRIV_ID to a certain role RID.
			global $i18n;
			$this->log("<br/>///////".$i18n['priv_manager']['granted'].$user_name.":".$role_name,'PRIV');
			$role = $this->q2obj("SELECT id FROM {$this->table_prefix}role WHERE name='".$this->remove_strange_chars($role_name)."'");
			$user = $this->q2obj("SELECT id FROM {$this->table_prefix}usr WHERE name='".$this->remove_strange_chars($user_name)."'");
			if(count($role) == 0){
				echo('Missing Role:'.$role_name);
			}
			if(count($user) == 0){
				echo('Missing User:'.$user_name);
			}

			$role_id=$role[0]['id'];
			$user_id=$user[0]['id'];
			return($this->grant($user_id,$role_id));
		}

		function grant($user_id,$role_id){
			#this function gives a centarin user UID a certain role RID.
			global $i18n;
			$this->log("<br/>".$i18n['priv_manager']['granted'].$user_id.":".$role_id,'PRIV');
			$this->sql("INSERT INTO {$this->table_prefix}usr2role VALUES(0,'$user_id','$role_id')");
			return($this->last_id());
		}

		function ac_none(){
		//	echo("this is not a table manager.");
		}
		function priv_manager_model(){
			$this->std();
		}
		var $use_table = 0;
		var $default_action='none';
		var $table="none";
		var $id="id";
		var $name='name';
		var $table_prefix='';
	}
?>
